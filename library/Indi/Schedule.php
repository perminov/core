<?php
class Indi_Schedule {

    /**
     * Schedule's left bound (e.g. beginning)
     *
     * @var int
     */
    protected $_since;

    /**
     * Schedule's right bound (e.g. ending)
     *
     * @var int
     */
    protected $_until;

    /**
     * @var array
     */
    protected $_shift = array(
        'system' => 86400,
        'inject' => 0
    );

    /**
     * Spaces, that schedule is consists of
     *
     * @var array
     */
    protected $_spaces = array();

    /**
     * Constructor. Possible usage:
     * 1. new Indi_Schedule(int|string $since, int|string $until), both arguments should be unixtimes
     *    or dates in format, recognizable with strtotime()
     *    This will create schedule instance having exact specified bounds
     * 2. new Indi_Schedule(string $wrap, int|string|null $date),
     *    - $wrap arg can be either 'month' or 'week'
     *    - $date arg can be either unixtime or date in format, recognizable with strtotime()
     *    if $wrap arg is 'week', newly created instance will have left bound as a monday of a week,
     *      that date specified by $date arg is within. If not specified, current date will be used
     *    if $wrap arg is 'month', newly created instance will have left bound as first date of a month,
     *      that date specified by $date arg is within. If not specified, current date will be used
     * 3.new Indi_Schedule(int|string $since, string $append)
     *    - $append arg can be a strtotime-compatible expression like '+1 week', or '+2 days'
     *   new Indi_Schedule(time(), '+2 months') will create schedule starting now and ending after 2 months
     *
     * @param $since
     * @param $until
     */
    public function __construct($since = 'month', $until = null) {

        // If $since arg is either 'week' or 'month'
        if (in($since, 'week,month')) extract($this->bounds($since, $until));

        // Set `_since`
        $this->_since = is_numeric($since) ? $since : strtotime($since);

        // If $until arg contains strtotime()-compatible expression, such as '+1 week' etc
        // Set `_until` by appending the interval, specified by $until arg, to `_since`
        if (preg_match('/^\+[0-9]+ (week|month|day|hour|second)s?/', $until))
            $this->_until = strtotime(date('Y-m-d H:i:s', $this->_since) . ' ' . $until);

        // Else use exact value as a schedule's right bound
        else $this->_until = is_numeric($until) ? $until : strtotime($until);

        // Create free space, that match whole schedule, initially
        $this->_spaces[] = new Indi_Schedule_Space($this->_since, $this->_until, 'free');
    }

    /**
     * Create busy space within the current schedule, or check whether it's possible to create it
     *
     * @param int $since Unix-timestamp or a string representation, recognizable by strtotime()
     * @param int $duration
     * @param bool $checkOnly
     * @return bool
     */
    public function busy($since, $duration, $checkOnly = false) {

        // Default value for $isBusy flag
        $isBusy = true;

        // Convert $since arg into timestamp
        $since = is_numeric($since) ? $since : strtotime($since);

        // If part, that we want to mark as busy - is starting earlier schedule's left bound
        if ($since < $this->_since) {

            // Deduct duration
            $duration -= $this->_since - $since;

            // Shift $since right, for it to match schedule left bound
            $since = $this->_since;
        }

        // If part, that we want to mark as busy - is ending later schedule's right bound
        if ($since + $duration > $this->_until) {

            // Deduct duration (so, part's right bound will be shifted left and will match schedule's right bound)
            $duration = $this->_until - $since;
        }

        // Foreach spaces within datetime-schedule
        foreach ($this->_spaces as $i => $space) {

            // Try to find a free space, having enough duration
            if ($space->avail == 'free' && $space->since <= $since && $space->until >= $since + $duration) {

                // Setup $found flag
                $isBusy = false;

                // If we needed only to check schedule availability - break the loop
                if ($checkOnly) break;

                // If free space starts earlier than busy space we want to inject
                if ($space->since < $since) {

                    // Prepare array for busy-space injection into $this->_spaces
                    $inject = array($busy = new Indi_Schedule_Space($since, $since + $duration, 'busy'));

                    // If free schedule will remain after busy-part injection
                    if ($space->until > $since + $duration)

                        // Wrap that free schedule into free-space, and append to 2be-injected parts
                        array_push($inject, $free = new Indi_Schedule_Space($since + $duration, $space->until, 'free'));

                    // Move current free part's ending mark
                    $space->until = $since;

                    // Inject
                    array_splice($this->_spaces, $i + 1, 0, $inject);

                // Else if found free space starts at the exact same moment as busy part we want to inject
                } else if ($space->since == $since) {

                    // If free space ends later than busy part
                    if ($space->until > $since + $duration) {

                        // Create busy space starting at the exact same moment as current free space
                        $busy = new Indi_Schedule_Space($since, $since + $duration, 'busy');

                        // Move current free space's starting mark to the ending of busy space
                        $space->since = $since + $duration;

                        // Inject busy part BEFORE current free part
                        array_splice($this->_spaces, $i, 0, array($busy));

                    // Else if current free space is going to became busy in it's whole bounds
                    } else if ($space->until == $since + $duration)

                        // Change it's 'avail' prop to 'busy'
                        $space->avail = 'busy';
                }
            }
        }

        // Return
        return $isBusy;
    }

    /**
     * Detect calendar bounds for a given date
     *
     * @static
     * @param string $wrap
     * @param $date
     * @return array
     */
    public function bounds($wrap = 'month', $date = null) {

        // If $date arg is not given - use current date by default
        if (!$date) $date = date('Y-m-d H:i:s');

        // Config for different wrap kinds
        $wrapCfg = array(
            'month' => array('format' => 'Y-m-01', 'days' => 7 * 6),
            'week'  => array('format' => 'Y-m-d',  'days' => 7 * 1)
        );

        // Get unix-timestamp
        $ts = strtotime(date($wrapCfg[$wrap]['format'], is_numeric($date) ? $date : strtotime($date)));

        // Get date, that is a monday in the same week as $ts
        $since = date('Y-m-d', $ts - (date('N', $ts) - 1) * $this->_shift['system']);

        // Get bottom right date of pseudo-calendar
        $until = date('Y-m-d', strtotime($since . '+' . $wrapCfg[$wrap]['days'] . ' days'));

        // Return
        return array('since' => $since, 'until' => $until);
    }

    /**
     * Return schedule's left bound as a unix-timestamp, or as a date, formatted according to $format arg
     *
     * @param null $format
     * @param bool $ldate
     * @return int|string
     */
    public function since($format = null, $ldate = false) {

        // If $format arg is not given - return `_since` prop
        if (!$format) return $this->_since;

        // If $ldate arg is given - return localized date
        if ($ldate) return ldate($format, $this->_since, $ldate);

        // Else return date, formatted without locale
        return strtolower(date($format, $this->_since));
    }

    /**
     * Return schedule's left bound as a unix-timestamp, or as a date, formatted according to $format arg
     * Note that if any of args is given, function will assume that the aim of the call is to retrieve schedule's
     * right bound in some kind of a human-readable format, so, 86400 seconds will be deducted before returning
     * formatted right bound, as right bound without such a deduction is used for internal purposes only
     *
     * @param null $format
     * @param bool $ldate
     * @return int|string
     */
    public function until($format = null, $ldate = false) {

        // If $format arg is not given - return `_since` prop
        if (!$format) return $this->_until;

        // If $ldate arg is given - return localized date
        if ($ldate) return ldate($format, $this->_until - $this->_shift['system'], $ldate);

        // Else return date, formatted without locale
        return strtolower(date($format, $this->_until - $this->_shift['system']));
    }

    /**
     * Load existing busy spaces into schedule
     *
     * @param $table
     * @param array $where
     * @param callable $pre
     * @return Indi_Schedule
     */
    public function load($table, $where = array(), $pre = null) {

        // Get model
        $model = Indi::model($table);

        // Normalize $where arg
        if (is_array($where)) $where = un($where, array(null, ''));
        else if (is_string($where) && strlen($where)) $where = array($where);
        else $where = array();

        // Convert `_since` and `_until` timestamps into datetime format
        $since = date('Y-m-d H:i:s', $this->_since);
        $until = date('Y-m-d H:i:s', $this->_until);

        // Schedule-related field names
        $srf = array(
            'since' => $model->sinceField(),
            'until' => $model->untilField(),
            'frame' => $model->frameField()
        );

        // Append WHERE clause part, responsible for fetching entries
        // that are within schedule bounds (fully or partially)
        $where[] = self::where($since, $until);

        // Get schedule's busy spaces
        $rs = $model->fetchAll($where);

        // Load existing busy spaces into schedule
        foreach ($rs as $r) {

            // If $pre arg is callable - call it, passing row, and schedule-related fields
            if (is_callable($pre)) $pre($r, $srf);

            // Use row for creating busy space
            if ($this->busy($r->{$srf['since']}, $r->{$srf['frame']}))
                jflush(false, 'Не удалось загрузить ' . Indi::model($table)->title() . ' ' . $rs->id . ' в раcписание');
        }

        // Return schedule itself
        return $this;
    }

    /**
     * Method for building WHERE clause, needed for fetching events that
     * are fully/partially within a schedule bounds
     *
     * @static
     * @param $since
     * @param $until
     * @return string
     */
    public static function where($since, $until) {
        return '(' . im(array(
            '(`spaceSince` <= "' . $since . '" AND `spaceUntil` >  "' . $since . '")',
            '(`spaceSince` <  "' . $until . '" AND `spaceUntil` >= "' . $until . '")',
            '(`spaceSince` >= "' . $since . '" AND `spaceUntil` <= "' . $until . '")'), ' OR ') . ')';
    }

    /**
     * Get spaces
     *
     * @return array
     */
    public function spaces() {
        return $this->_spaces;
    }

    /**
     * Set daily spaces that are not between $since and $until - as not available.
     * This can be useful when there is a need to setup working hours
     *
     * @param $since
     * @param $until
     * @return Indi_Schedule
     */
    public function daily($since = false, $until = false) {

        // Ensure $since arg to be in 'hh:mm:ss' format
        if ($since && !Indi::rexm('time', $since)) jflush(false, 'Argument $since should be a time in format hh:mm:ss');

        // Ensure $until arg to be in 'hh:mm:ss' format
        if ($until && !Indi::rexm('time', $until)) jflush(false, 'Argument $until should be a time in format hh:mm:ss');

        // Set initial mark to be the beginning of space left bound's date
        $mark = strtotime(date('Y-m-d', $this->_since));

        // Get daily number of seconds
        $daily = $this->_2sec('1d');

        // Convert $since and $until args to number of seconds
        if ($since) $since = $this->_2sec($since);
        if ($until) $until = $this->_2sec($until);

        // While $mark does not exceed space's right bound
        if ($since || $until) while ($mark < $this->_until) {

            // Try to set space between 00:00:00 and $since of each day - as not available
            if ($since && $this->busy($mark, $since)) jflush(false, 'Can\'t set opening hours');

            // Try to set space between $until and 00:00:00 of next day - as not available
            if ($until && $this->busy($mark + $until, $daily - $until)) jflush(false, 'Can\'t set closing hours');

            // Jump to next date
            $mark += $daily;
        }

        // Return itself
        return $this;
    }

    /**
     * Get array of dates within current space, where given $frame can't be injected as a NEW busy space
     *
     * @param $frame
     * @return array
     */
    public function busyDates($frame) {

        // Convert to seconds
        $frame = $this->_2sec($frame);

        // Array of busy dates
        $busy = array();

        // Set initial mark to be same as schedule's left bound
        $mark = $this->_since;

        // While $mark does not exceed schedule's right bound
        while ($mark < $this->_until) {

            // Foreach parts within datetime-space - find the space part, that mark is belonging to
            foreach ($this->_spaces as $i => $space) if ($mark >= $space->since && $mark < $space->until) break;

            // Set/reset $free flag
            $free = false;

            // Do
            do {

                // If $mark belongs to 'free' space, and given $frame CAN
                // be injected as NEW busy space - set $free flag as `true`
                if ($space->avail == 'free' && !$this->busy($space->since, $frame, true)) $free = true;

                // Else try next space
                else $space = $this->_spaces[++$i];

            // While $free flag is not true and $space's date is same as $mark's date
            } while (!$free && (date('Y-m-d', $space->until - (int)(date('H:i:s', $space->until) == '00:00:00')) == date('Y-m-d', $mark)));

            // If $free flag is still false - append $mark's date to busy dates array
            if (!$free) $busy[] = date('Y-m-d', $mark);

            // Jump to next date
            $mark += $this->_2sec('1d');
        }

        // Return busy dates
        return $busy;
    }

    /**
     * Get array of hours within a given date in current space, where given $frame can't be injected as a NEW busy space
     *
     * @param $frame
     * @param $date
     * @param string $step
     * @return array
     */
    public function busyHours($frame, $date, $step = '1h') {

        // Convert $frame arg to seconds
        $frame = $this->_2sec($frame);

        // Array of busy hours
        $busy = array();

        // Set initial mark
        $mark = strtotime($date);

        // Convert $step arg to seconds
        $step = $this->_2sec($step);

        // While $mark is within $date
        while ($date == date('Y-m-d', $mark)) {

            // If given $frame can't be injected as NEW busy space - append $mark's date in busy time-steps array
            if ($this->busy($mark, $frame, true)) $busy[] = date('H:i', $mark);

            // Jump to next time-step
            $mark += $step;
        }

        // Return busy time-steps
        return $busy;
    }

    /**
     * Convert duration, given as string in format 'xy', to number of seconds
     * where 'x' - is the number, and 'y' - is the measure. 'y' can be:
     * s - second
     * m - minute
     * h - hour
     * d - day
     * w - week
     *
     * Example usage:
     * $seconds = $this->_2sec('2m'); // $seconds will be = 120
     *
     * @param $expr
     * @return int
     */
    protected function _2sec($expr) {

        // If $expr is given in 'hh:mm:ss' format
        if (Indi::rexm('time', $expr)) {

            // Prepare type mapping
            $type = array('h', 'm', 's'); $s = 0;

            // Foreach type append it's value converted to seconds
            foreach (explode(':', $expr) as $index => $value) $s += $this->_2sec($value . $type[$index]);

            // Return
            return $s;
        }

        // Check format for $for argument
        if (!preg_match('~^([0-9]+)(s|m|h|d|w)$~', $expr, $m)) jflush(false, 'Incorrect $expr arg format');

        // Multipliers for $expr conversion
        $frame2sec = array(
            's' => 1,
            'm' => 60,
            'h' => 60 * 60,
            'd' => 60 * 60 * 24,
            'w' => 60 * 60 * 24 * 7
        );

        // Return number of seconds
        return $m[1] * $frame2sec[$m[2]];
    }

    /**
     * Convert schedule to a number of strings, each representing certain space
     *
     * @return string
     */
    public function __toString() {
        ob_start(); foreach ($this->_spaces as $space) echo $space . "\n"; return ob_get_clean();
    }
}
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
        'inject' => 0,
        'gap' => 0
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
     * @param $gap
     */
    public function __construct($since = 'month', $until = null, $gap = false) {

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

        // Set gap
        if ($gap) $this->_shift['gap'] = _2sec($gap);
    }

    /**
     * Create busy space within the current schedule, or check whether it's possible to create it
     *
     * @param int $since Unix-timestamp or a string representation, recognizable by strtotime()
     * @param int $duration
     * @param bool $checkOnly
     * @param bool $includeGap
     * @param string $avail
     * @return bool
     */
    public function busy($since, $duration, $checkOnly = false, $includeGap = true, $avail = 'busy') {

        // Default value for $isBusy flag
        $isBusy = true;

        // Convert $since arg into timestamp
        $since = is_numeric($since) ? $since : strtotime($since);

        // Include gap
        if ($includeGap) $duration += $this->_shift['gap'];

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
                    $inject = array($busy = new Indi_Schedule_Space($since, $since + $duration, $avail));

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
                        $busy = new Indi_Schedule_Space($since, $since + $duration, $avail);

                        // Move current free space's starting mark to the ending of busy space
                        $space->since = $since + $duration;

                        // Inject busy part BEFORE current free part
                        array_splice($this->_spaces, $i, 0, array($busy));

                    // Else if current free space is going to became busy in it's whole bounds
                    } else if ($space->until == $since + $duration)

                        // Change it's 'avail' prop to $avail
                        $space->avail = $avail;
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

        // Append WHERE clause part, responsible for fetching entries
        // that are within schedule bounds (fully or partially)
        $where[] = self::where($since, $until);

        // Get schedule's busy spaces
        $rs = $model->fetchAll($where);

        // Load existing busy spaces into schedule
        foreach ($rs as $r) {

            // If $pre arg is callable - call it, passing row, and schedule-related fields
            if (is_callable($pre)) $pre($r);

            // Use row for creating busy space
            if ($this->busy($r->spaceSince, $r->spaceFrame))
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
     * For EACH day within schedule, create spaces starting at midnight and ending at time,
     * specified by $until arg, and set availability of those spaces as 'early', or, if such
     * exact spaces is not possible to create because of already existing non-free spaces, that
     * would intersect with the desired spaces - create spaces among existing non-free spaces,
     * so the result is that the given period of time (within ech day) will be anyway be non-free,
     * as some parts of that period will be used for already existing non-free spaces, and remaining
     * parts will be filled with 'early' spaces
     *
     * @param bool $until
     * @return Indi_Schedule
     */
    public function early($until = false) {

        // If no $since arg given, or it is `false` - return
        if (!$until) return $this;

        // Ensure $since arg to be in 'hh:mm:ss' format
        if (!Indi::rexm('time', $until)) jflush(false, 'Argument $until should be a time in format hh:mm:ss');

        // Fill schedule with 'early' spaces, where possible
        $this->fillwp(0, _2sec($until), 'early');

        // Return itself
        return $this;
    }

    /**
     * For EACH day within schedule, create spaces starting at time, according to given $since arg
     * and ending at midnight, and set availability of those spaces as 'late', or, if such
     * exact spaces is not possible to create because of already existing non-free spaces, that
     * would intersect with the desired spaces - create spaces among existing non-free spaces,
     * so the result is that the given period of time (within ech day) will be anyway be non-free,
     * as some parts of that period will be used for already existing non-free spaces, and remaining
     * parts will be filled with 'late' spaces
     *
     * @param bool $since
     * @return Indi_Schedule
     */
    public function late($since = false) {

        // If no $since arg given, or it is `false` - return
        if (!$since) return $this;

        // Ensure $since arg to be in 'hh:mm:ss' format
        if (!Indi::rexm('time', $since)) jflush(false, 'Argument $since should be a time in format hh:mm:ss');

        // Fill schedule with 'late' spaces, where possible
        $this->fillwp(_2sec($since) + $this->_shift['gap'], _2sec('1d'), 'late');

        // Return itself
        return $this;
    }

    /**
     * For EACH day within schedule, fill (where possible) the period
     * between $since and $until with spaces marked as $avail
     *
     * @param $since
     * @param $until
     * @param $avail
     */
    public function fillwp($since, $until, $avail) {

        // Set initial day-timestamp to be the date of the schedule left bound
        $daystamp = strtotime(date('Y-m-d', $this->_since));

        // While $mark does not exceed schedule's right bound
        while ($daystamp < $this->_until) {

            // Get date for
            $date = date('Y-m-d', $daystamp);

            // Set absolute timestamps
            $_since = $daystamp + $since;
            $_until = $daystamp + $until;

            // Foreach space within schedule
            foreach ($this->_spaces as $space) {

                // If current space's start date is one of the next days to $_since - break;
                if (date('Y-m-d', $space->since) > $date) break;

                // If current space ends earlier than $_since - skip
                if ($space->until <= $_since) continue;

                // If current space is not 'free'
                if ($space->avail != 'free') continue;

                // Calculate the intersection of what we have and what we need
                $frame = min($space->until, $_until) - max($space->since, $_since);

                // If no current space's part, suitable for marking as 'late' found - break
                if ($frame <= 0) break;

                // Mark found part as 'late'
                if ($this->busy(max($space->since, $_since), $frame, false, false, $avail))
                    jflush(false, 'Can\'t set ' . $avail . ' space');
            }

            // Jump to next day
            $daystamp += _2sec('1d');
        }
    }

    /**
     * Set daily spaces that are not between $since and $until - as not available.
     * This can be useful when there is a need to setup working hours
     *
     * @param bool|string $opened
     * @param bool|string $closed
     * @return Indi_Schedule
     */
    public function daily($opened = false, $closed = false) {
        return $this->early($opened)->late($closed);
    }

    /**
     * Get array of dates within current space, where given $frame can't be injected as a NEW busy space
     *
     * @param $frame
     * @return array
     */
    public function busyDates($frame) {

        // Convert to seconds
        $frame = _2sec($frame);

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
            $mark += _2sec('1d');
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
        $frame = _2sec($frame);

        // Array of busy hours
        $busy = array();

        // Set initial mark
        $mark = strtotime($date);

        // Convert $step arg to seconds
        $step = _2sec($step);

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
     * Convert schedule to a number of strings, each representing certain space
     *
     * @return string
     */
    public function __toString() {
        ob_start(); foreach ($this->_spaces as $space) echo $space . "\n"; return ob_get_clean();
    }
}
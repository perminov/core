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
     * Params
     *
     * @var array
     */
    protected $_shift = array(

        // This param is used within $this->bounds() method as a number of seconds within 24 hours
        // Also, it's used in $this->until() method, for deducting from actual value of $this->_until,
        // because $this->until() method is for formatting a schedule's right bound's date, but actual
        // timestamp containing in $this->_until points to next date
        'system' => 86400,

        // Gap between busy spaces
        'gap' => 0,

        // This param is used in cases when our aim is to check the possibility of new busy space to be injected,
        // starting at most possible late date (e.g schedule's right bound's date), so this param will be used for
        // expanding schedule's right bound. Example: we have schedule covering a certain week starting at monday's
        // midnight and ending at next monday's midnight. So, if we need to check availability of 6 hours, starting
        // at sunday's 11PM, we'll not be able to check it, as our schedule ends in 1 hour so we have no info about
        // remaining 5 hours. That is why we need to expand schedule's right bound by 6 hours, and it will lead to
        // that WHERE clause, built for fetching events (converted into busy spaces) from db - will cover as more wider
        // time-range as we need to have the info.
        'frame' => 0
    );

    /**
     * Spaces, that schedule is consists of
     *
     * @var array
     */
    protected $_spaces = array();

    /**
     * Rowset, that will be used for refilling the schedule.
     * You should use $this->preload() call for initial setup,
     * and $this->refill() calls for refilling the schedule using
     * entries that match selection criteria, specified by $this->refill()
     * call args. This is useful for cases when you do not want execute
     * MySQL query each time you want to fulfil the schedule
     *
     * @var Indi_Db_Table_Rowset
     */
    public $_rs = null;

    /**
     * Array of distinct values of props, faced within $this->_rs.
     * You should use $this->distinct(['roomId', 'teacherId']) call for
     * walking through $this->_rs and collecting distinct values,
     * For obtaining already collected values of certain prop - use $this->distinct($prop)
     *
     * @var array
     */
    public $_distinct = [];

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
    public function busy($since, $duration = null, $checkOnly = false, $includeGap = true, $avail = 'busy') {

        // If $since arg in an instance of Indi_Db_Table_Row
        if ($since instanceof Indi_Db_Table_Row) {

            // Back it up
            $entry = clone $since;

            // Overwrite $since and $duration args with $row's `spaceSince` and `spaceFrame` props respectively
            $since = $entry->spaceSince; $duration = $entry->spaceFrame;
        }

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

            // Pick `avail` prop
            if ($isBusy && $space->since <= $since && $space->until >= $since + $duration) $isBusy = $space;

            // Try to find a free space, having enough duration
            if ($space->avail == 'free' && $space->since <= $since && $space->until >= $since + $duration) {

                // Setup $found flag
                $isBusy = false;

                // If we needed only to check schedule availability - break the loop
                if ($checkOnly) break;

                // If free space starts earlier than busy space we want to inject
                if ($space->since < $since) {

                    // Prepare array for busy-space injection into $this->_spaces
                    $inject = array($busy = new Indi_Schedule_Space($since, $since + $duration, $avail, $entry ?: null));

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
                        $busy = new Indi_Schedule_Space($since, $since + $duration, $avail, $entry ?: null);

                        // Move current free space's starting mark to the ending of busy space
                        $space->since = $since + $duration;

                        // Inject busy part BEFORE current free part
                        array_splice($this->_spaces, $i, 0, array($busy));

                    // Else if current free space is going to became busy in it's whole bounds
                    } else if ($space->until == $since + $duration) {

                        // Change it's 'avail' prop to $avail
                        $space->avail = $avail;

                        // Set entry
                        if ($entry) $space->$entry = $entry;
                    }
                }

                // Break
                break;
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
        return compact('since', 'until');
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

        // Get rowset
        $rs = $this->rowset($table, $where, $pre);

        // Load existing busy spaces into schedule
        foreach ($rs as $r)
            if ($this->busy($r))
                jflush(false, 'Не удалось загрузить ' . Indi::model($table)->title() . ' ' . $r->id . ' в раcписание');

        // Return schedule itself
        return $this;
    }

    /**
     * Preload db entries, for later selection and conversion to busy spaces
     *
     * @param $table
     * @param array $where
     * @param null $pre
     * @return Indi_Schedule
     */
    public function preload($table, $where = array(), $pre = null) {

        // Get rowset
        $this->_rs = $this->rowset($table, $where, $pre);

        // Return schedule itself
        return $this;
    }

    /**
     * Get rowset of entries, that current schedule is in intersection with
     */
    public function rowset($table, $where = array(), $pre = null) {

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

        // If $pre arg is callable - call it, passing row, and schedule-related fields
        if (is_callable($pre)) foreach ($rs as $r) $pre($r);

        // Return rowset
        return $rs;
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
     * Get array of dates within current schedule, where given $frame can't be injected as a NEW busy space.
     *
     * @param string $frame Amount of time. Possible values: '10:23:50', '1h', '30m', etc
     * @param bool|array $both If is an array - it will be fufilled with dates having at least one busy and one free spaces
     * @return array
     */
    public function busyDates($frame, &$both = false) {

        // Convert to seconds
        $frame = _2sec($frame);

        // Array of busy dates
        $busy = array();

        // Set initial mark to be same as schedule's left bound
        $mark = $this->_since;

        // Set $stop flag, indicating whether we'll stop checking $mark's date once desired free space was found there.
        // If $stop is `false` - we'll use $both arg as an array for fulfilling it with dates, having both free and busy
        // spaces(s), e.g. having not only at least one free space suitable for desired frame injection, but also
        // at least one busy space, so there will be a collection of dates that are partially busy
        $stop = $both === false; if (!$stop) $both = array();

        // Set index of space, that we should start searching from within each $mark's date
        $idx = 0;

        // While $mark does not exceed schedule's right bound
        while ($mark < $this->_until - $this->_shift['frame']) {

            // Foreach parts within datetime-space - find the space part, that mark is belonging to
            for ($i = $idx; $i < count($this->_spaces); $i++) {
                if ($mark >= $this->_spaces[$i]->since && $mark < $this->_spaces[$i]->until) {
                    $space = $this->_spaces[$idx = $i];
                    break;
                }
            }

            // Set/reset $hasFree and $hasBusy flags
            $gotFree = $gotBusy = false;

            // Shortcut to $mark's date
            $date = date('Y-m-d', $mark);

            // Do
            do {

                // Setup the start point of a frame, that we will check whether it's busy
                // Here we use max() fn, as $space may start at earlier date than $mark's date
                // and in that case usage of $space->since as a start point will produce incorrect results,
                // because our aim is to check whether it's possible to inject new busy space starting
                // at $mark's date, not starting yesterday or at earlier date
                $since = max($space->since, $mark);

                // If $mark belongs to 'free' space, and given $frame CAN
                // be injected as NEW busy space - set $free flag as `true`
                if ($space->avail == 'free') {
                    if (!$this->busy($since, $frame, true)) {
                        $gotFree = true;
                    }
                } else if ($space->avail == 'busy') {
                    $gotBusy = true;
                }

                // Jump to next space
                $space = $this->_spaces[++$i];

            // While  $space's date is same as $mark's date
            } while ((!$gotFree || !$stop) && date('Y-m-d', $space->since) == $date);

            // If $free flag is still false - append $mark's date to busy dates array
            if (!$gotFree) $busy[] = $date;

            // If partially busy dates should be collected, and current $date is such a date - collect
            if (!$stop && $gotFree && $gotBusy) $both[] = $date;

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

    /**
     * Shift schedule's right bound.
     * Use this method BEFORE $this->preload() and $this->load() calls,
     * as schedule's right bound is involved in building WHERE clause
     * for fetching event-entries from database
     */
    public function frame($frame) {

        // Revert previous shift
        $this->_until -= $this->_shift['frame'];

        // Setup new value for $this->_shift['frame'] prop
        $this->_shift['frame'] = is_string($frame) ? _2sec($frame) : $frame;

        // Apply new shift
        $this->_until += $this->_shift['frame'];

        // Apply new shift to schedule's single space.
        $this->_spaces[0]->until = $this->_until;
    }

    /**
     * Empty the schedule and fill it with another collection of spaces,
     * based on entries, selected from $this->_rs according to criteria,
     * given by $keys and $type args
     *
     * Example usage: $schedule->refill('1', 'teacherId', ['since' => '10:00:00', 'until' => '20:00:00']);
     *
     * @param $keys
     * @param string $type
     * @param array $daily
     * @param callable $pre
     * @return Indi_Schedule
     */
    public function refill($keys, $type = 'id', $daily = array(), $pre = null) {

        // Drop all existing spaces and insert a new free one, matching schedule bounds
        $this->_spaces = array(new Indi_Schedule_Space($this->_since, $this->_until, 'free'));

        // Select certain entries from preloaded rowset and add them as busy spaces into schedule
        foreach ($this->_rs->select($keys, $type) as $r) {

            // If $pre arg is callable - call it, passing row as a 1st arg
            if (is_callable($pre)) $pre($r);

            // Flush failure
            if ($this->busy($r)) jflush(false, 'Не удалось загрузить '
                . Indi::model($this->_rs->table())->title() . ' ' . $r->id . ' в раcписание');
        }

        // Setup daily hours
        if ($daily) $this->daily($daily['since'], $daily['until']);

        // Return schedule instance itself
        return $this;
    }

    /**
     * Collect distinct values per given props within preloaded rowset
     *
     * @param $prop
     * @return array
     */
    public function distinct($prop = null) {

        // If no args given - return $this->_distinct as is
        if (!func_num_args() || (!is_string($prop) && !is_array($prop))) return $this->_distinct;

        // If $prop arg is given and it's a string
        if (is_string($prop)) return array_keys($this->_distinct[$prop] ?: array());

        // Foreach entry within preloaded rowset, foreach prop that we need to collect distinct values for
        foreach ($this->_rs as $r) foreach ($prop as $propI)

            // If prop is not empty - collect.
            // Here we use ar($r->$propI) as some prop may have comma-separated values
            if ($r->$propI) foreach (ar($r->$propI) as $v) $this->_distinct[$propI][$v] = true;
    }
}
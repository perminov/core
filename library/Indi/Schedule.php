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
     * @return Indi_Schedule
     */
    public function load($table, $where = array()) {

        // Get model
        $model = Indi::model($table);

        // Normalize $where arg
        if (is_array($where)) $where = un($where, array(null, ''));
        else if (is_string($where) && strlen($where)) $where = array($where);
        else $where = array();

        // Convert `_since` and `_until` timestamps into datetime format
        $since = date('Y-m-d H:i:s', $this->_since);
        $until = date('Y-m-d H:i:s', $this->_until);

        // Get schedule-related field names
        $sinceField = $model->sinceField();
        $untilField = $model->untilField();
        $frameField = $model->frameField();

        // Append WHERE clause part, responsible for fetching entries
        // that are within schedule bounds (fully or partially)
        $where[] = '(' . im(array(
            '(`' . $sinceField . '` <= "' . $since . '" AND `' . $untilField . '` >  "' . $since . '")',
            '(`' . $sinceField . '` <  "' . $until . '" AND `' . $untilField . '` >= "' . $until . '")',
            '(`' . $sinceField . '` >= "' . $since . '" AND `' . $untilField . '` <= "' . $until . '")'), ' OR ') . ')';

        // Get schedule's busy spaces
        $rs = $model->fetchAll($where);

        // Load existing busy spaces into schedule
        foreach ($rs as $r)
            if ($this->busy($r->$sinceField, $r->$frameField))
                jflush(false, 'Не удалось загрузить ' . Indi::model($table)->title() . ' ' . $rs->id . ' в раcписание');

        // Return schedule itself
        return $this;
    }
}
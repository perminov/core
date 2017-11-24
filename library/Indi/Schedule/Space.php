<?php
class Indi_Schedule_Space {

    /**
     * Space's left bound (e.g. beginning)
     *
     * @var int
     */
    public $since;

    /**
     * Space's right bound (e.g. ending)
     *
     * @var int
     */
    public $until;

    /**
     * Flag, indicating space is free or busy
     *
     * @var
     */
    public $avail;

    /**
     * Instance of Indi_Db_Table_Row, that was used to create the space
     *
     * @var Indi_Db_Table_Row
     */
    public $entry;

    /**
     * Constructor
     *
     * @param $since
     * @param $until
     * @param $avail
     * @param $entry Indi_Db_Table_Row
     */
    public function __construct($since, $until, $avail, $entry = null) {

        // Set space's left bound (e.g. beginning)
        $this->since = $since;

        // Set space's right bound (e.g. ending)
        $this->until = $until;

        // Set space's availability
        $this->avail = $avail;

        // Set entry
        if ($entry) $this->entry = $entry;
    }

    /**
     * Return string human-readable representation of a space
     *
     * @return string
     */
    public function __toString() {
        return date('Y-m-d H:i:s', $this->since) . ', ' . $this->avail . ' for ' .  ago($this->since, $this->until, 'ago', true);
    }
}

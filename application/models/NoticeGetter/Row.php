<?php
class NoticeGetter_Row extends Indi_Db_Table_Row_Noeval {

    /**
     * Entry, that triggered the notice.
     * Used for building the criteria, that recipients should match
     *
     * @var Indi_Db_Table_Row
     */
    public $row = null;

    /**
     * Notify recipients
     *
     * @param Indi_Db_Table_Row $row
     * @param $diff
     */
    public function notify(Indi_Db_Table_Row $row, $diff) {

        // If $diff arg is not 0, it means that `notice` entry (that current `noticeGetter` entry belongs to)
        // has `matchMode` == 'shared', and this, in it's turn, means that change-direction-of-counter,
        // linked to the above mentioned `notice` entry - is already determined, and this direction
        // ('up' or 'down' / '+1' or '-1') - will be sole for all getter's recipients, e.g. direction won't
        // differ for different recipients having same role (specified within getter's settings).
        // But, if current getter's settings has 'separate' as the value of `criteriaMode`
        // field - there should be no notifications sent
        if ($diff != 0 && $this->criteriaMode == 'separate') return;

        // Else if $diff is 0 (e.g if `notice` entry's `matchMode` prop's value is 'separate'):
        // 1. Assign `row` prop, that will be visible in compiling context
        $this->row = $row;

        // 2.1 If current getter's `criteriaMode` is 'shared' - use $diff arg as is
        if ($this->criteriaMode == 'shared') $this->_notify($diff);

        // 2.2 Else separately notify two groups of recipients: ones for 'up' and others for 'down'
        else foreach (array(-1, 1) as $diff) $this->_notify($diff);
    }

    /**
     * Internal fn, responsible for:
     * 1. Preparing the message's header and body, according to $diff arg
     * 2. Fetching the recipients lists, that also depends on $diff arg
     * 3. Sending prepared message to fetched recipients
     *
     * @param $diff
     */
    protected function _notify($diff) {

        // Setup possible directions
        $dirs = array(-1 => 'down', 0 => 'diff', 1 => 'up');

        // Get direction, for being used as a part of field names
        $dir = ucfirst($dirs[$diff]);

        // Get header and body
        $header = $this->foreign('noticeId')->{'tpl' . $dir . 'Header'};
        $this->foreign('noticeId')->compiled('tpl' . $dir . 'Body', null);
        $body = $this->foreign('noticeId')->compiled('tpl' . $dir . 'Body');

        // Get websocket-recipients
        $ws[$this->profileId] = is_bool($_ = $this->users('criteria' . $dir)) ? $_ : array_column($_, 'id');

        // Do it using websockets
        Indi::ws($msg = array(
            'type' => 'notice',
            'mode' => 'menu-qty',
            'noticeId' => $this->noticeId,
            'diff' => $diff,
            'row' => $this->row->id,
            'to' => $ws,
            'msg' => array(
                'header' => $header,
                'body' => $body
            )
        ));
    }

    /**
     * Get array of recipients ids
     */
    public function users($criteriaProp) {

        // If no criteria specified this means that all users of current getter's role should receive notifications
        if (!$this->$criteriaProp) return true;

        // Start building WHERE clauses array
        $where = array('`toggle` = "y"');

        // Find the name of database table, where recipients should be found within
        foreach (Indi_Db::role() as $profileIds => $entityId)
            if (in($this->profileId, $profileIds))
                if ($table = Indi::model($entityId)->table())
                    break;

        // Prevent recipients duplication
        if ($table == 'admin') $where[] = '`profileId` = "' . $this->profileId . '"';

        // If criteria specified
        if (strlen($this->$criteriaProp)) {

            // Unset previously compiled criteria
            unset($this->_compiled[$criteriaProp]);

            // Append compiled criteria to WHERE clauses array
            if (strlen($criteria = $this->compiled($criteriaProp))) $where[] = '(' . $criteria . ')';
        }

        // Return array of found recipients ids
        return Indi::db()->query('SELECT `id` FROM `' . $table . '` WHERE ' . im($where, ' AND '))->fetchAll();
    }
}
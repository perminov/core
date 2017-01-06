<?php
class Indi_Db_Table_Schedule extends Indi_Db_Table {

    /**
     * @var string
     */
    protected $_sinceField = 'spaceSince';

    /**
     * @var string
     */
    protected $_untilField = 'spaceUntil';

    /**
     * @var string
     */
    protected $_frameField = 'spaceFrame';

    /**
     * @return string
     */
    public function sinceField() {
        return $this->_sinceField;
    }

    /**
     * @return string
     */
    public function untilField() {
        return $this->_untilField;
    }

    /**
     * @return string
     */
    public function frameField() {
        return $this->_frameField;
    }
}
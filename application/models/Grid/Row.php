<?php
class Grid_Row extends Indi_Db_Table_Row {

    /**
     * @return int
     */
    public function save(){

        // If no field chosen as a grid column basis - setup title same as `alterTitle`
        // if (!$this->fieldId || $this->alterTitle) $this->title = $this->alterTitle;

        // If there is no access limitation, empty `profileIds` prop
        if ($this->access == 'all') $this->profileIds = '';

        // Standard save
        return parent::save();
    }
}
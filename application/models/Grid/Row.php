<?php
class Grid_Row extends Indi_Db_Table_Row {

    /**
     * @return int
     */
    public function save(){

        // If no field chosen as a grid column basis - setup title same as `alterTitle`
        if (!$this->fieldId) $this->title = $this->alterTitle;

        // Standard save
        return parent::save();
    }
}
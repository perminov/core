<?php
class Search_Row extends Indi_Db_Table_Row {

    /**
     * @return int
     */
    public function save(){


        // Standard save
        return parent::save();
    }

    /**
     * Detect whether or not combo-filter show have the ability to deal with multiple values
     *
     * @return bool
     */
    public function any() {
        return $this->any || $this->foreign('fieldId')->storeRelationAbility == 'many';
    }
}
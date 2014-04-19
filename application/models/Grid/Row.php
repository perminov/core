<?php
class Grid_Row extends Indi_Db_Table_Row {

    /**
     * Function is redeclared for `title` property autosetup
     *
     * @return int
     */
    public function save() {

        // Setup `title` property
        $this->title = $this->foreign('fieldId')->title;

        // Standard save
        parent::save();
    }
}
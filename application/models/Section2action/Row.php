<?php
class Section2action_Row extends Indi_Db_Table_Row {

    /**
     * Function is redeclared for `title` property autosetup
     *
     * @return int
     */
    public function save() {

        // Setup `title` property
        $this->title = $this->foreign('actionId')->title;

        // Standard save
        parent::save();
    }
}
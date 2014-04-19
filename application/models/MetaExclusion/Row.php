<?php
class MetaExclusion_Row extends Indi_Db_Table_Row {

    /**
     * Function is redeclared for `title` property autosetup
     *
     * @return int|void
     */
    public function save(){

        // Setup `title` property
        $this->title = $this->foreign('entityId')->title . ' - ' . $this->foreign('identifier')->title;

        // Standard save
        parent::save();
    }
}

<?php
class Consider_Row extends Indi_Db_Table_Row {

    /**
     * Set `entityId`
     */
    public function onBeforeSave() {
        $this->entityId = $this->foreign('fieldId')->entityId;
    }
}
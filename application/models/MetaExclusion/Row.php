<?php
class MetaExclusion_Row extends Indi_Db_Table_Row {
    public function save(){
        $this->title = $this->foreign('entityId')->title . ' - ' . $this->foreign('identifier')->title;
        parent::save();
    }
}

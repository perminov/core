<?php
class Grid_Row extends Indi_Db_Table_Row{
    public function save(){
        $this->title = $this->foreign('fieldId')->title;
        parent::save();
    }
}
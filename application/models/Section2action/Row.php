<?php
class Section2action_Row extends Indi_Db_Table_Row
{
    public function save(){
        $this->title = $this->foreign('actionId')->title;
        parent::save();
    }

}
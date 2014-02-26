<?php
class MetaExclusion_Row extends Indi_Db_Table_Row
{
    public function getTitle(){
        return $this->foreign('entityId')->title . ' - ' . $this->foreign('identifier')->title;
    }
}

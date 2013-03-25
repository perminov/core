<?php
class MetaExclusion_Row extends Indi_Db_Table_Row
{
    public function getTitle(){
        return $this->getForeignRowByForeignKey('entityId')->title . ' - ' . $this->getForeignRowByForeignKey('identifier')->title;
    }
}

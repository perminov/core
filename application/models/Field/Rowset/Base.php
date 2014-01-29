<?php
class Field_Rowset_Base extends Indi_Db_Table_Rowset{
    /**
     * Set existing (original and redefined) params for each field in the current rowset
     *
     * @return Field_Rowset_Base
     */
    public function setParams() {
        foreach ($this as $r) $r->params = $r->getParams(); return $this;
    }
}
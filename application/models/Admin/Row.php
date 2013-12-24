<?php
class Admin_Row extends Indi_Db_Table_Row{
    public function save(){
        if ($this->_modified['password']) {
            $this->_modified['password'] = $this->getTable()->getAdapter()->query('
                SELECT PASSWORD("' . $this->_modified['password'] . '")
            ')->fetchColumn(0);
        }
        parent::save();
    }
}
<?php
class Admin_Row extends Indi_Db_Table_Row {

    /**
     * Function is redeclared for passwords encryption
     *
     * @return int
     */
    public function save(){

        // If password was changed
        if ($this->_modified['password']) {

            // Encrypt the password
            $this->_modified['password'] = Indi::db()->query('
                SELECT PASSWORD("' . $this->_modified['password'] . '")
            ')->fetchColumn(0);
        }

        // Standard save
        parent::save();
    }
}
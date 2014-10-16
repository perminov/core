<?php
class ColumnType_Row extends Indi_Db_Table_Row_Noeval {

    /**
     * Get zero value for a current column type
     *
     * @return mixed
     */
    public function zeroValue() {
        return $this->model()->zeroValue($this->type);
    }
}
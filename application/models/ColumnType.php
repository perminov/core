<?php
class ColumnType extends Indi_Db_Table {

    /**
     * Contains empty/zero values for column types
     *
     * @var array
     */
    protected $_zeroValue = array(
        'VARCHAR(255)' => '',
        'INT(11)' => '0',
        'TEXT' => '',
        'DOUBLE(7,2)' => '0.00',
        'DATE' => '0000-00-00',
        'YEAR' => '0000',
        'TIME' => '00:00:00',
        'DATETIME' => '0000-00-00 00:00:00',
        'ENUM' => '0',
        'SET' => '0',
        'BOOLEAN' => '0',
        'VARCHAR(10)' => '000#000000'
    );

    /**
     * Classname for row
     *
     * @var string
     */
    public $_rowClass = 'ColumnType_Row';

    /**
     * Get zero value for a given column type
     *
     * @param $type
     * @return string
     */
    public function zeroValue($type) {
        return $this->_zeroValue[$type];
    }
}
<?php
class Param extends Indi_Db_Table {

    /**
     * Array of fields, which contents will be evaluated with php's eval() function
     * @var array
     */
    protected $_evalFields = array('value');

    /**
     * Classname for row
     *
     * @var string
     */
    public $_rowClass = 'Indi_Db_Table_Row_Noeval';
}

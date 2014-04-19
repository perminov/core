<?php
class DisabledField extends Indi_Db_Table {

    /**
     * Array of fields, which contents will be evaluated with php's eval() function
     * @var array
     */
    protected $_evalFields = array('defaultValue');
}

<?php
class Field_Base extends Indi_Db_Table{
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Field_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Field_Rowset';
}
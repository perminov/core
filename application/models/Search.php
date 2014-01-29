<?php
class Search extends Indi_Db_Table{
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Search_Row';

    protected $_evalFields = array('defaultValue');
}
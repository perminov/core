<?php
class Lang extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    public $_rowClass = 'Lang_Row';

    /**
     * Array of json-templates for each l10n-fraction
     *
     * @var array
     */
    public static $_jtpl = [];
}
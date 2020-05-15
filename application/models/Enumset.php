<?php
class Enumset extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Enumset_Row';

    /**
     * Info for l10n-fraction detection
     *
     * @var array
     */
    protected $_fraction = ['field' => 'fieldId'];
}
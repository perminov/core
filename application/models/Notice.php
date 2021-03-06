<?php
class Notice extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    public $_rowClass = 'Notice_Row';

    /**
     * Array of fields, which contents will be evaluated with php's eval() function
     * @var array
     */
    protected $_evalFields = array('event', 'qtySql', 'tplIncBody', 'tplDecBody', 'tplEvtBody');

    /**
     * Info for l10n-fraction detection
     *
     * @var array
     */
    protected $_fraction = array(
        'field' => 'type',
        'value' => array(
            's' => 'adminSystemUi',
            'p' => 'adminCustomUi'
        )
    );
}
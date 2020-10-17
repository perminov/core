<?php
class Entity extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Entity_Row';

    /**
     * Info for l10n-fraction detection
     *
     * @var array
     */
    protected $_fraction = array(
        'field' => 'system',
        'value' => array(
            'y' => 'adminSystemUi',
            'n' => 'adminCustomUi',
            'o' => 'adminPublicUi'
        )
    );
}
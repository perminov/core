<?php
class Grid extends Indi_Db_Table {

    /**
     * Classname for row
     *
     * @var string
     */
    public $_rowClass = 'Grid_Row';

    /**
     * Info for l10n-fraction detection
     *
     * @var array
     */
    protected $_fraction = ['field' => 'sectionId'];
}
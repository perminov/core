<?php
class Notice_Row extends Indi_Db_Table_Row {

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array()) {

        // Explicitly set table name
        $config['table'] = 'notice';

        // Call parent
        parent::__construct($config);
    }

    public function counter($dir, $row) {
        i($dir . ' ' . $row->id, 'a');
    }
}
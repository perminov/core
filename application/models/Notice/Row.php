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

    /**
     * Increase counter
     *
     * @param $dir
     * @param $row
     */
    public function counter($dir, $row) {

        // Do it using websockets
        Indi::ws($msg = array(
            'type' => 'notice',
            'mode' => 'menu-qty',
            'noticeId' => $this->id,
            'diff' => $dir == 'up' ? 1 : -1,
            'row' => $row->id
        ));
    }
}
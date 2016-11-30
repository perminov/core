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

    public function save() {
        
        // Call parent
        $return = parent::save();

        // Sync keys, mentioned as comma-sepaarted values in `profileId` prop, with entries, nested in `noticeGetter` table
        $this->keys2nested('profileId', 'noticeGetter');
        
        if ($this->affected('profileId')) {
        
            // Build criteria-by-profileId array
            foreach ($this->nested('noticeGetter') as $noticeGetterR) $criteria[$noticeGetterR->profileId] = $noticeGetterR->criteria;
                
            // Assign criteria
            $this->criteria = json_encode($criteria);
            
            // Save
            parent::save();
        }

        // Return
        return $return;
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
<?php
class Admin_CmdController extends Indi_Controller {

    public function preDispatch() {

        // Stop
        if (!CMD) jflush(false); else session_write_close();

        // Call parent
        $this->callParent();
    }

    public function updateUsagesAction($id, array $considerIdA) {
        
        // Turn off limitations
        ignore_user_abort(1); set_time_limit(0);
        
        // Turn on logging for mflush events
        Indi::logging('mflush', true);

        // Turn on logging for mflush events
        Indi::logging('jflush', true);
    
        // Fetch usage map entries
        $considerRs = Indi::model('Consider')->fetchAll('`id` IN (' . im($considerIdA) . ')');

        // If no usage map entries - exit
        if (!$considerRs->count()) exit;

        // Group usages map entries by `entityId`
        foreach ($considerRs as $considerR) $considerRA_byEntityId[$considerR->entityId][] = $considerR;

        // For each group
        foreach ($considerRA_byEntityId as $entityId => $considerRA) {

            // Build WHERE clause
            $where = array();
            foreach ($considerRA as $considerR) {
                $field = $considerR->foreign('consider')->alias;
                $where[] = '`' . $field . '` = "' . $id . '"';
            }
            $where = im($where, ' OR ');

            // Get total qty of entries to be processed
            $qty = Indi::db()->query('SELECT COUNT(*) FROM `' . Indi::model($entityId)->table() . '` WHERE ' . $where)->fetchColumn();
            
            // Set limit per once
            $limit = 500;

            // Fetch usages by 500 at a time
            for ($p = 1; $p <= ceil($qty/$limit); $p++) {

                // Fetch usages
                $rs = Indi::model($entityId)->fetchAll($where, null, $limit, $p);
                
                // Update usages
                foreach ($rs as $i => $r) {
                    $r->noValidate = true;
                    $r->save();
                }
            }
        }
    }
}
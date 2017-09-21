<?php
class Admin_CmdController extends Indi_Controller {

    public function preDispatch() {

        // Stop
        if (!CMD) jflush(false); else session_write_close();

        // Call parent
        $this->callParent();
    }

    public function updateUsagesAction($id, array $considerIdA) {
        
        // Turn on logging for mflush events
        Indi::logging('mflush', true);
    
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

            // Fetch usages
            $rs = Indi::model($entityId)->fetchAll($where);

            // Update usages
            foreach ($rs as $r) {
                $r->noValidate = true;
                $r->save();
            }
        }
    }
}
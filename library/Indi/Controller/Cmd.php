<?php
class Indi_Controller_Cmd extends Indi_Controller {

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

    /**
     * Start running queue
     *
     * @param $queueTaskId
     */
    public function queueAction($queueTaskId) {
        Indi::model('QueueTask')->fetchRow($queueTaskId)->start();
    }


    public function fieldToggleL10nAction($toggle, $field, $table, $where) {

        // If there are no languages - return
        if (!$jtpl = Indi::db()->query('SELECT `alias`, "" AS `holder` FROM `lang`')->fetchAll(PDO::FETCH_KEY_PAIR)) return;

        // Setup flag, indicating whether or not field has localized dependency
        $hasLD = Indi::model($table)->fields($field)->hasLocalizedDependency();

        // Get total qty of entries to be processed
        $total = Indi::db()->query('SELECT COUNT(*) FROM `' . $table . '` WHERE ' . im($where, ' OR '))->fetchColumn();

        // Set limit per once
        $limit = 500;

        // Fetch entries by 500 at a time
        for ($p = 1; $p <= ceil($total/$limit); $p++) {

            // Fetch usages
            $rs = Indi::model($table)->fetchAll($where, null, $limit, $p);

            // Update usages
            foreach ($rs as $r) {

                // If $toggle arg is `true`
                if ($toggle) {

                    // Apply current value as a translation for ALL languages (initially)
                    $json = $jtpl; foreach ($json as $lang => &$holder) {

                        // If field has no localized dependencies - use transliteration
                        if (!$hasLD) $holder = Indi::l10n($r->$field, $lang); else {

                            // Backup current language
                            $_lang = Indi::ini('lang')->admin;

                            // Set current language to $lang
                            Indi::ini('lang')->admin = $lang;

                            // Get value according to required language
                            $r->{'set' . ucfirst($field)}(); $holder =  $r->$field;

                            // Restore current language back
                            Indi::ini('lang')->admin = $_lang;
                        }
                    }

                    // Get value
                    $value = json_encode($json);

                    // Else if we need to convert data back to non-localized format
                } else {

                    // Pick current translation from JSON
                    $value = json_decode($r->$field)->{Indi::ini('lang')->admin};
                }

                // Prepend WHERE clause with `id`-part
                array_unshift($where, '`id` = "' . $r->id . '"');

                // Build sql-query
                $sql = Indi::db()->sql('
                    UPDATE `' . $table . '`
                    SET `' . $field . '` = :s
                    WHERE ' . im($where, ' AND ')
                    , $value);

                // Run sql-query
                Indi::db()->query($sql);

                // Remove id-clause
                array_shift($where);
            }
        }
    }
}
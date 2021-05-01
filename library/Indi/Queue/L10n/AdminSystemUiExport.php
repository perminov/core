<?php
class Indi_Queue_L10n_AdminSystemUiExport extends Indi_Queue_L10n_AdminSystemUi {

    /**
     * Process queue items
     *
     * @param $queueTaskId
     */
    public function queue($queueTaskId) {

        // Get `queueTask` entry
        $queueTaskR = Indi::model('QueueTask')->fetchRow($queueTaskId);

        // If `queueState` is 'noneed' - do nothing
        if ($queueTaskR->queueState == 'noneed') return;

        // Update `stage` and `state`
        $queueTaskR->stage = 'queue';
        $queueTaskR->state = 'progress';
        $queueTaskR->queueState = 'progress';
        $queueTaskR->basicUpdate(false, false);

        // Get source and target languages
        $source = json_decode($queueTaskR->params)->source;
        $target = json_decode($queueTaskR->params)->target;

        // Foreach `queueChunk` entries, nested under `queueTask` entry
        foreach ($queueTaskR->nested('queueChunk', [
            'where' => '`queueState` != "finished"',
            'order' => '`queueState` = "progress" DESC, `move`'
        ]) as $queueChunkR) {

            // Remember that we're going to count
            $queueChunkR->assign(array('queueState' => 'progress'))->basicUpdate();

            // Build WHERE clause for batch() call
            $where = '`queueChunkId` = "' . $queueChunkR->id . '" AND `stage` = "items"';

            // Split `location` on $table and $field
            list ($table, $field) = explode(':', $queueChunkR->location);

            // Check whether we should use setter method call instead of google translate api call
            if (!$exportable = method_exists(m($table)->createRow(), 'export'))
                $queueChunkR->assign(array('queueState' => 'noneed'))->basicUpdate();

            // Get queue items by 50 entries at a time
            Indi::model('QueueItem')->batch(function (&$rs, &$deduct) use (&$queueTaskR, &$queueChunkR, &$gapi, $source, $target, $table, $field, $exportable) {

                // Foreach fetched `queueItem` entry
                foreach ($rs as $idx => $r) {

                    //
                    if ($exportable) {

                        // Backup current language
                        $_lang = Indi::ini('lang')->admin;

                        // Spoof current language
                        Indi::ini('lang')->admin = $source;

                        // Get target entry
                        $te = m($table)->fetchRow($r->target);

                        // Do export
                        $result = $te->export($field);

                        // Restore current language
                        Indi::ini('lang')->admin = $_lang;

                    //
                    } else $result = '';

                    // Write translation result
                    $r->assign(array('result' => $result, 'stage' => 'queue'))->basicUpdate();

                    // Increment `queueSize` prop on `queueChunk` entry and save it
                    $queueChunkR->queueSize++;
                    $queueChunkR->basicUpdate();

                    // Increment `queueSize` prop on `queueTask` entry and save it
                    $queueTaskR->queueSize++;
                    $queueTaskR->basicUpdate(false, false);

                    // Increment $deduct
                    $deduct++;
                }

            //
            }, $where, '`id` ASC', 50, true);

            // Remember that our try to count was successful
            $queueChunkR->assign(array('queueState' => 'finished'))->basicUpdate();
        }

        // Mark stage as 'Finished' and save `queueTask` entry
        $queueTaskR->assign(array('state' => 'finished', 'queueState' => 'finished'))->save();
    }

    /**
     * Apply results
     *
     * @param $queueTaskId
     */
    public function apply($queueTaskId) {

        // Get `queueTask` entry
        $queueTaskR = Indi::model('QueueTask')->fetchRow($queueTaskId);

        // Update `stage` and `state`
        $queueTaskR->stage = 'apply';
        $queueTaskR->state = 'progress';
        $queueTaskR->applyState = 'progress';
        $queueTaskR->basicUpdate(false, false);

        // Get params
        $params = json_decode($queueTaskR->params, true);

        // Build filename of a php-file, containing l10n constants for source language
        $l10n_target_abs = DOC . STD . '/core/application/lang/ui/' . $params['source'] . '.php';

        // Put opening php tag
        file_put_contents($l10n_target_abs, '<?php' . "\n");

        // Setup special migration state so that 'basicUpdate' method would be used instead of 'save' method
        // in field(...), section(...), enumset(...) and other shorthand-functions
        file_put_contents($l10n_target_abs, 'ini()->lang->migration = true;' . "\n", FILE_APPEND);

        // Foreach `queueChunk` entries, nested under `queueTask` entry
        foreach ($queueTaskR->nested('queueChunk', [
            'where' => '`applyState` != "finished"',
            'order' => '`applyState` = "progress" DESC, `move`'
        ]) as $queueChunkR) {

            // Remember that we're going to count
            $queueChunkR->assign(array('applyState' => 'progress'))->basicUpdate();

            // Build WHERE clause for batch() call
            $where = '`queueChunkId` = "' . $queueChunkR->id . '" AND `stage` = "' . ($params['toggle'] == 'n' ? 'items' : 'queue') . '"';

            // Get queue items
            Indi::model('QueueItem')->batch(function (&$r, &$deduct) use (&$queueTaskR, &$queueChunkR, $params, &$l10n_target_raw, $l10n_target_abs) {

                // Update target file
                if ($r->result) file_put_contents($l10n_target_abs, $r->result . "\n", FILE_APPEND);

                // Write translation result
                $r->assign(array('stage' => 'apply'))->basicUpdate();

                // Reset batch offset
                $deduct++;

                // Increment `applySize` prop on `queueChunk` entry and save it
                $queueChunkR->applySize++;
                $queueChunkR->basicUpdate();

                // Increment `applySize` prop on `queueTask` entry and save it
                $queueTaskR->applySize++;
                $queueTaskR->basicUpdate(false, false);

            }, $where, '`id` ASC');

            // Remember that our try to count was successful
            $queueChunkR->assign(array('applyState' => 'finished'))->basicUpdate();
        }

        // Mark stage as 'Finished' and save `queueTask` entry
        $queueTaskR->assign(array('state' => 'finished', 'applyState' => 'finished'))->save();
    }

    /**
     * Used with $queueChunkR->itemsSize
     *
     * @return int
     */
    public function itemsBytesMultiplier($params, $setter = false) {
        return 0;
    }
}

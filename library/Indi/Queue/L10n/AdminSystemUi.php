<?php
class Indi_Queue_L10n_AdminSystemUi extends Indi_Queue_L10n {

    /**
     * Create queue chunks
     *
     * @param array $params
     */
    public function chunk(array $params) {

        // Create `queueTask` entry
        $queueTaskR = Indi::model('QueueTask')->createRow(array(
            'title' => array_pop(explode('_', __CLASS__)),
            'params' => json_encode($params),
            'queueState' => $params['toggle'] == 'n' ? 'noneed' : 'waiting'
        ), true);

        // Save `queueTask` entries
        $queueTaskR->save();

        // Dict of entities having purpose-distinction
        $master = array(
            'entity' => array('field' => 'system', 'value' => 'y'),
            'section' => array('field' => 'type', 'value' => 's'),
            'action' => array('field' => 'type', 'value' => 's'),
        );

        // Additional info for detecting entries
        foreach ($master as $table => &$info) $info += array(
            'connector' => Indi::model($table)->id(),
            'instances' => Indi::model($table)->fetchAll('`' . $info['field'] . '` = "' . $info['value'] . '"')->column('id', true)
        );

        // Collect id of enties
        $masterIds = array_column($master, 'connector');

        // Foreach `entity` entry, having `system` = "n" (e.g. project's custom entities)
        foreach (Indi::model('Entity')->fetchAll('`system` = "y"') as $entityR) {

            // WHERE clause
            $where = array();

            // If current entity is a multi-purpore entity
            if ($master[$entityR->table])
                $where []= '`' . $master[$entityR->table]['field'] . '` = "' . $master[$entityR->table]['value'] . '"';

            // Else if entries of current entity are nested under at least one of multi-purpose entities entries
            else foreach (Indi::model($entityR->id)->fields()->select($masterIds, 'relation') as $fieldR)
                $where []= '`' . $fieldR->alias . '` IN (' . $master[Indi::model($fieldR->relation)->table()]['instances'] . ')';

            // Foreach `field` entry, having `l10n` = "y"
            foreach (Indi::model($entityR->id)->fields()->select('y', 'l10n') as $fieldR_having_l10nY)
                $this->appendChunk($queueTaskR, $entityR, $fieldR_having_l10nY, $where);
        }
    }

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

        // Require and instantiate Google Cloud Translation PHP API and
        require_once('google-cloud-php-translate-1.6.0/vendor/autoload.php');
        $gapi = new Google\Cloud\Translate\V2\TranslateClient(array('key' => Indi::ini('lang')->gapi->key));

        // Update `stage` and `state`
        $queueTaskR->stage = 'queue';
        $queueTaskR->state = 'progress';
        $queueTaskR->queueState = 'progress';
        $queueTaskR->basicUpdate();

        // Get source and target languages
        $source = json_decode($queueTaskR->params)->source;
        $target = json_decode($queueTaskR->params)->target;

        // Foreach `queueChunk` entries, nested under `queueTask` entry
        foreach ($queueTaskR->nested('queueChunk', [
            'where' => '`queueState` != "finished"',
            'order' => '`queueState` = "progress" DESC, `id`'
        ]) as $queueChunkR) {

            // Remember that we're going to count
            $queueChunkR->assign(array('queueState' => 'progress'))->basicUpdate();

            // Build WHERE clause for batch() call
            $where = '`queueChunkId` = "' . $queueChunkR->id . '" AND `stage` = "items"';

            // Get queue items by 50 entries at a time
            Indi::model('QueueItem')->batch(function(&$rs, &$deduct) use (&$queueTaskR, &$queueChunkR, &$gapi, $source, $target) {

                // Get translations from Google Cloud Translate API
                $result = array_column($gapi->translateBatch($rs->column('value'), [
                    'source' => $source,
                    'target' => $target,
                ]), 'text');

                // Foreach fetched `queueItem` entry
                foreach ($rs as $idx => $r) {

                    // Write translation result
                    $r->assign(array('result' => $result[$idx], 'stage' => 'queue'))->basicUpdate();

                    // Increment `queueSize` prop on `queueChunk` entry and save it
                    $queueChunkR->queueSize ++; $queueChunkR->basicUpdate();

                    // Increment `queueSize` prop on `queueTask` entry and save it
                    $queueTaskR->queueSize ++; $queueTaskR->basicUpdate();

                    // Increment $deduct
                    $deduct ++;
                }

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
        $queueTaskR->basicUpdate();

        // Get params
        $params = json_decode($queueTaskR->params, true);

        // Foreach `queueChunk` entries, nested under `queueTask` entry
        foreach ($queueTaskR->nested('queueChunk', [
            'where' => '`applyState` != "finished"',
            'order' => '`applyState` = "progress" DESC, `id`'
        ]) as $queueChunkR) {

            // Remember that we're going to count
            $queueChunkR->assign(array('applyState' => 'progress'))->basicUpdate();

            // Build WHERE clause for batch() call
            $where = '`queueChunkId` = "' . $queueChunkR->id . '" AND `stage` = "' . ($params['toggle'] == 'n' ? 'items' : 'queue') . '"';

            // Split `location` on $table and $field
            list ($table, $field) = explode(':', $queueChunkR->location);

            // Get queue items
            Indi::model('QueueItem')->batch(function(&$r, &$deduct) use (&$queueTaskR, &$queueChunkR, $params, $table, $field) {

                // Get cell's current value
                $json = Indi::db()->query('SELECT `:p` FROM `:p` WHERE `id` = :p', $field, $table, $r->target)->fetchColumn();

                // If cell value is not an empty string, but is not a json - force it to be json
                if ($json && !preg_match('~^{"~', $json)) $json = json_encode([$params['source'] => $json], JSON_UNESCAPED_UNICODE);

                // Decode translations
                $data = json_decode($json ?: '{}');

                // If 'toggle'-param is 'n' - unset translation, else append it
                if ($params['toggle'] == 'n') unset($data->{$params['source']}); else $data->{$params['target']} = $r->result;

                // Encode back
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);

                // Update cell value
                Indi::db()->query('UPDATE `:p` SET `:p` = :s WHERE `id` = :i', $table, $field, $json, $r->target);

                // Write translation result
                $r->assign(array('stage' => 'apply'))->basicUpdate();

                // Reset batch offset
                $deduct ++;

                // Increment `applySize` prop on `queueChunk` entry and save it
                $queueChunkR->applySize ++; $queueChunkR->basicUpdate();

                // Increment `applySize` prop on `queueTask` entry and save it
                $queueTaskR->applySize ++; $queueTaskR->basicUpdate();

            }, $where, '`id` ASC');

            // Remember that our try to count was successful
            $queueChunkR->assign(array('applyState' => 'finished'))->basicUpdate();
        }

        // Mark stage as 'Finished' and save `queueTask` entry
        $queueTaskR->assign(array('state' => 'finished', 'applyState' => 'finished'))->save();

        // Update target `lang` entry's state for current fraction
        $langR_target = Indi::model('Lang')->fetchRow('`alias` = "' . $params[$params['toggle'] == 'n' ? 'source' : 'target'] . '"');
        $langR_target->{lcfirst(preg_replace('~^Indi_Queue_L10n_~', '', __CLASS__))} = $params['toggle'] ?: 'y';
        $langR_target->save();
    }
}
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
            'params' => json_encode($params)
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
            foreach (Indi::model($entityR->id)->fields()->select('y', 'l10n') as $fieldR_having_l10nY) {

                // Create `queueChunk` entry and setup basic props
                $queueChunkR = Indi::model('QueueChunk')->createRow(array(
                    'queueTaskId' => $queueTaskR->id,
                    'entityId' => $entityR->id,
                    'fieldId' => $fieldR_having_l10nY->id
                ), true);

                // If it's an enumset-field
                if ($fieldR_having_l10nY->relation == 6) {

                    // Table and field names
                    $table = 'enumset'; $field = 'title';

                    // Build WHERE clause
                    $queueChunkR->where = sprintf('`fieldId` = "%s"', $fieldR_having_l10nY->id);

                // Else
                } else {

                    // Table and field names
                    $table = $entityR->table; $field = $fieldR_having_l10nY->alias;

                    // Build WHERE clause
                    if ($where) $queueChunkR->where = im($where, ' AND ');
                }

                // Setup `location`
                $queueChunkR->location = $table . ':' . $field;

                // Save `queueChunk` entry
                $queueChunkR->save();

                // Increment `countChunk`
                $queueTaskR->chunk ++;
                $queueTaskR->basicUpdate();
            }
        }
    }

    /**
     * Count queue items
     *
     * @param $queueTaskId
     * @throws Exception
     */
    public function count($queueTaskId) {

        // Fetch `queueTask` entry
        $queueTaskR = Indi::model('QueueTask')->fetchRow($queueTaskId);

        // Foreach `queueChunk` entries, nested under `queueTask` entry
        foreach ($queueTaskR->nested('queueChunk', [
            'where' => '`countState` != "finished"',
            'order' => '`countState` = "progress" DESC, `id`'
        ]) as $queueChunkR) {

            // Remember that we're going to count
            $queueChunkR->assign(array('countState' => 'progress'))->basicUpdate();

            // Get table
            list ($table, $field) = explode(':', $queueChunkR->location);

            // Count items
            $queueChunkR->countSize = Indi::db()->query('
                SELECT COUNT(`id`) FROM `' . $table . '`' . rif($queueChunkR->where, ' WHERE $1')
            )->fetchColumn();

            // Remember that our try to count was successful
            $queueChunkR->assign(array('countState' => 'finished'))->basicUpdate();

            // Update `queueTask` entry's `countSize` prop
            $queueTaskR->countSize += $queueChunkR->countSize;
            $queueTaskR->basicUpdate();
        }

        // Mark first stage as 'Finished' and save `queueTask` entry
        $queueTaskR->assign(array('state' => 'finished', 'countState' => 'finished'))->save();
    }

    /**
     * Create queue items, accodring to already created chunks
     *
     * @param $queueTaskId
     * @throws Exception
     */
    public function items($queueTaskId) {

        // Get `queueTask` entry
        $queueTaskR = Indi::model('QueueTask')->fetchRow($queueTaskId);

        // Update `stage` and `state`
        $queueTaskR->stage = 'items';
        $queueTaskR->state = 'progress';
        $queueTaskR->itemsState = 'progress';
        $queueTaskR->basicUpdate();

        // Get source language
        $source = json_decode($queueTaskR->params)->source;

        // Foreach `queueChunk` entries, nested under `queueTask` entry
        foreach ($queueTaskR->nested('queueChunk', [
            'where' => '`itemsState` != "finished"',
            'order' => '`itemsState` = "progress" DESC, `id`'
        ]) as $queueChunkR) {

            // Remember that we're going to count
            $queueChunkR->assign(array('itemsState' => 'progress'))->basicUpdate();

            // Split `location` on $table and $field
            list ($table, $field) = explode(':', $queueChunkR->location);

            // Get last target
            $last = Indi::model('QueueItem')->fetchRow('`queueChunkId` = "' . $queueChunkR->id . '"', '`id` DESC')->target;

            // Build WHERE clause
            $where = array();
            if ($queueChunkR->where) $where []= $queueChunkR->where;
            if ($last) $where []= '`id` > ' . $last;
            $where = $where ? im($where, ' AND ') : null;

            // Foreach entry matching chunk's definition
            Indi::model($table)->batch(function(&$r) use (&$queueTaskR, &$queueChunkR, $field, $source) {

                // Get value
                $value = preg_match('~^{"~', $r->$field) ? json_decode($value)->$source : $r->$field;

                // Create `queueItem` entry
                $queueItemR = Indi::model('QueueItem')->createRow(array(
                    'queueTaskId' => $queueTaskR->id,
                    'queueChunkId' => $queueChunkR->id,
                    'target' => $r->id,
                    'value' => $value
                ), true);

                // Save `queueItem` entry
                $queueItemR->save();

                // Increment `queued` prop on `queueChunk` entry and save it
                $queueChunkR->itemsSize ++;
                $queueChunkR->basicUpdate();

                // Increment `itemsSize` prop on `queueTask` entry and save it
                $queueTaskR->itemsSize ++;
                $queueTaskR->basicUpdate();

            // Fetch entries according to chunk's WHERE clause, and order by `id` ASC
            }, $where, '`id` ASC');

            // Remember that our try to count was successful
            $queueChunkR->assign(array('itemsState' => 'finished'))->basicUpdate();
        }

        // Mark first stage as 'Finished' and save `queueTask` entry
        $queueTaskR->assign(array('state' => 'finished', 'itemsState' => 'finished'))->save();
    }

    /**
     * Process queue items
     *
     * @param $queueTaskId
     */
    public function queue($queueTaskId) {

        // Require and instantiate Google Cloud Translation PHP API and
        require_once('google-cloud-php-translate-1.6.0/vendor/autoload.php');
        $gapi = new Google\Cloud\Translate\V2\TranslateClient(array('key' => Indi::ini('lang')->gapi->key));

        // Get `queueTask` entry
        $queueTaskR = Indi::model('QueueTask')->fetchRow($queueTaskId);

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

        // Get source and target languages
        $source = json_decode($queueTaskR->params)->source;
        $target = json_decode($queueTaskR->params)->target;

        // Foreach `queueChunk` entries, nested under `queueTask` entry
        foreach ($queueTaskR->nested('queueChunk', [
            'where' => '`applyState` != "finished"',
            'order' => '`applyState` = "progress" DESC, `id`'
        ]) as $queueChunkR) {

            // Remember that we're going to count
            $queueChunkR->assign(array('applyState' => 'progress'))->basicUpdate();

            // Build WHERE clause for batch() call
            $where = '`queueChunkId` = "' . $queueChunkR->id . '" AND `stage` = "queue"';

            // Split `location` on $table and $field
            list ($table, $field) = explode(':', $queueChunkR->location);

            // Get queue items
            Indi::model('QueueItem')->batch(function(&$r, &$deduct) use (&$queueTaskR, &$queueChunkR, $source, $target, $table, $field) {

                // Get cell's current value
                $json = Indi::db()->query('SELECT `:p` FROM `:p` WHERE `id` = :p', $field, $table, $r->target)->fetchColumn();

                // If cell value is not an empty string, but is not a json - force it to be json
                if ($json && !preg_match('~^{"~', $json)) $json = json_encode([$source => $json], JSON_UNESCAPED_UNICODE);

                // Append translation to cell value
                $data = json_decode($json ?: '{}'); $data->$target = $r->result; $json = json_encode($data, JSON_UNESCAPED_UNICODE);

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
        $langR_target = Indi::model('Lang')->fetchRow('`alias` = "' . $target . '"');
        $langR_target->{lcfirst(preg_replace('~^Indi_Queue_L10n_~', '', __CLASS__))} = 'y';
        $langR_target->save();
    }
}
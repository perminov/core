<?php
class Indi_Queue_L10n extends Indi_Queue {

    public function chunk(array $params) {
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

        // Get params
        $params = json_decode($queueTaskR->params, true);

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
            Indi::model($table)->batch(function(&$r) use (&$queueTaskR, &$queueChunkR, $field, $params) {

                // Get value
                $value = $params['toggle'] == 'n'
                    ? $r->language($field, $params['source'])
                    : (preg_match('~^{"~', $r->$field)
                        ? json_decode($value)->{$params['source']}
                        : $r->language($field, $params['source']));

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
                $queueTaskR->itemsBytes += mb_strlen($value, 'utf-8') * $this->itemsBytesMultiplier($params);
                $queueTaskR->basicUpdate();

                // Fetch entries according to chunk's WHERE clause, and order by `id` ASC
            }, $where, '`id` ASC');

            // Remember that our try to count was successful
            $queueChunkR->assign(array('itemsState' => 'finished'))->basicUpdate();
        }

        // Mark first stage as 'Finished' and save `queueTask` entry
        $queueTaskR->assign(array('state' => 'finished', 'itemsState' => 'finished'))->save();
    }

    public function queue($queueTaskId) {
    }
    public function apply($queueTaskId) {
    }

    /**
     * @param $queueTaskR
     * @param $entityR
     * @param $fieldR_having_l10nY
     * @param $where
     */
    public function appendChunk(&$queueTaskR, $entityR, $fieldR_having_l10nY, $where = array()) {

        // Create `queueChunk` entry and setup basic props
        $queueChunkR = Indi::model('QueueChunk')->createRow(array(
            'queueTaskId' => $queueTaskR->id,
            'entityId' => $entityR->id,
            'fieldId' => $fieldR_having_l10nY->id,
            'queueState' => $queueTaskR->queueState
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

    /**
     * Used with $queueChunkR->itemsSize
     *
     * @return int
     */
    public function itemsBytesMultiplier($params) {
        return $params['toggle'] != 'n';
    }
}
<?php
class Indi_Queue_L10n extends Indi_Queue {

    public function chunk(array $params) {
    }
    public function count($queueTaskId) {
    }
    public function items($queueTaskId) {
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
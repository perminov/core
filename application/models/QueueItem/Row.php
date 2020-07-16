<?php
class QueueItem_Row extends Indi_Db_Table_Row {

    /**
     * Reapply result, if need
     */
    public function onSave() {

        // If systen `reapply` flag is set
        if ($this->_system['reapply']) {

            // Get `queueTask` entry
            $queueTaskR = $this->foreign('queueTaskId');

            // Prepare `queueChunk` and `queueItems` entries for reapply
            $this->foreign('queueChunkId')->assign(['applyState' => 'waiting'])->save();
            $this->foreign('queueChunkId')->nested('queueItem')->assign(['stage' => 'queue'])->basicUpdate();

            // Reapply
            $queueClassName = 'Indi_Queue_' . ucfirst($queueTaskR->title);
            $queue = new $queueClassName();
            $queue->apply($queueTaskR->id);
        }
    }
}
<?php
class Admin_QueueChunkController extends Indi_Controller_Admin {

    /**
     * Turn Off grouping for non-FieldToggleL10n `queueTask` entries
     */
    public function adjustTrail() {
        if (t(1)->row->title != 'FieldToggleL10n') t()->section->zero('groupBy', true);
    }

    /**
     * Unset 'rootRowsOnly' part from summary WHERE clause
     * @param $where
     * @return mixed
     */
    public function adjustRowsetSummaryWHERE($where) {

        // Unset rootRowsOnly-part
        unset($where['rootRowsOnly']);

        // Return WHERE clause
        return $where;
    }
}
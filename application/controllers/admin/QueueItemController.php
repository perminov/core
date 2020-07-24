<?php
class Admin_QueueItemController extends Indi_Controller_Admin {

    /**
     * Set up `reapply` flag, if need
     *
     * @param $cell
     * @param $value
     */
    public function onBeforeCellSave($cell, $value) {

        // If cell is not 'result' do nothing
        if ($cell != 'result') return;

        // Set up `reapply` flag to `true`
        t()->row->system('reapply', true);
    }
}
<?php
class Indi_Controller_Admin_Myprofile extends Indi_Controller_Admin {

	/**
	 * Default action
	 */
    public $action = 'form';

    /**
     * Replace view type for 'index' action from 'grid' to 'changeLog'
     */
    public function adjustActionCfg() {
        $this->actionCfg['view']['index'] = 'myProfile';
    }

    /**
     * Force to perform formAction instead of indexAction
     */
    public function preDispatch() {

        // Force redirect to certain row
        if (Indi::uri()->action == 'index' || Indi::uri()->id != Indi::admin()->id) {

            // Set format to 'json', so scope things will be set up
            Indi::uri()->format = 'json';

            // Call parent
            parent::preDispatch();

            // Delete rowset-context `realtime` entry
            if (Indi::ini('ws')->realtime && $_ = m('realtime')->fetchRow([
                '`type` = "context"',
                '`token` = "' . t()->bid() . '"',
                '`realtimeId` = "' . m('realtime')->fetchRow('`token` = "' . CID . '"')->id . '"'
            ])) $_->delete();

            // Spoof uri params to force row-action
            Indi::uri()->action = $this->action;
            Indi::uri()->id = Indi::admin()->id;
            Indi::uri()->ph = Indi::trail()->scope->hash;
            Indi::uri()->aix = Indi::trail()->scope->aix;
        }

        // Call parent
        parent::preDispatch();
    }

    /**
     * Hardcode WHERE clause, to prevent user from accessing someone else's details
     *
     * @param $where
     * @return array|mixed
     */
    public function adjustPrimaryWHERE($where) {

        // Prevent user from accessing someone else's details
        $where['static'] = '`id` = "' . Indi::admin()->id . '"';

        // Return
        return $where;
    }
}
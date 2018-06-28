<?php
class Indi_Controller_Admin_Myprofile extends Indi_Controller_Admin {

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

        if (Indi::uri()->action == 'index' || Indi::uri()->id != Indi::admin()->id) {

            Indi::uri()->format = 'json';
            parent::preDispatch();

            Indi::uri()->action = 'form';
            Indi::uri()->id = Indi::admin()->id;
            Indi::uri()->ph = Indi::trail()->scope->hash;
            Indi::uri()->aix = Indi::trail()->scope->aix;
        }

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
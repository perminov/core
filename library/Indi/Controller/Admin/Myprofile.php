<?php
class Indi_Controller_Admin_Myprofile extends Indi_Controller_Admin {

    /**
     * Force to perform formAction instead of indexAction
     */
    public function preDispatch() {

        if (Indi::uri()->action == 'index' || Indi::uri()->id != Indi::admin()->id) {

            parent::preDispatch();

            Indi::uri()->action = 'form';
            Indi::uri()->id = Indi::admin()->id;
            Indi::uri()->ph = Indi::trail()->scope->hash;
            Indi::uri()->aix = Indi::trail()->scope->aix;

        }

        parent::preDispatch();
    }
}
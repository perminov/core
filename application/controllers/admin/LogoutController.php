<?php
class Admin_LogoutController extends Indi_Controller_Admin {
    /**
     * Action to preform Admin System logout
     * and redirect to Admin System login page
     *
     */
    public function preDispatch(){

        //
        $this->logout();
    }
}
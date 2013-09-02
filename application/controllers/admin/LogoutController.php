<?php
class Admin_LogoutController extends Indi_Controller
{
    /**
     * Action to preform Admin System logout
     * and redirect to Admin System login page
     *
     */
    public function preDispatch(){
        if ($_SESSION['admin']['id']) {
            unset($_SESSION['admin']);
        }
        die('<script>window.location.replace("' . $_SERVER['STD'] . ($GLOBALS['cmsOnlyMode'] ? '' : '/admin') . '/")</script>');
    }
}
<?php
class Admin_LogoutController extends Indi_Controller {
    /**
     * Action to preform Admin System logout
     * and redirect to Admin System login page
     *
     */
    public function preDispatch(){

        // Allow CORS
        header('Access-Control-Allow-Headers: x-requested-with, indi-auth');
        header('Access-Control-Allow-Origin: *');

        // Unset session
        if ($_SESSION['admin']['id'])  unset($_SESSION['admin'], $_SESSION['indi']['admin']);

        // Redirect
        iexit('<script>window.location.replace("' . PRE . '/")</script>');
    }
}
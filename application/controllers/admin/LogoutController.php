<?php
class Admin_LogoutController extends Indi_Controller_Admin {
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

        // Flush basic info
        if (APP) jflush(true, array(
            'std' => STD,
            'com' => COM ? '' : '/admin',
            'pre' => PRE,
            'uri' => Indi::uri()->toArray(),
            'title' => Indi::ini('general')->title ?: 'Indi Engine',
            'throwOutMsg' => Indi::view()->throwOutMsg,
            'lang' => $this->lang()
        ));

        // Else redirect
        else iexit('<script>window.location.replace("' . PRE . '/")</script>');
    }
}
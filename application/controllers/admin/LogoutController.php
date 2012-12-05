<?php
class Admin_LogoutController extends Indi_Controller_Admin
{
    /**
     * Action to preform Admin System logout
     * and redirect to Admin System login page
     *
     */
    public function indexAction()
    {
        if ($_SESSION['admin']['id']) {
            unset($_SESSION['admin']);
        }
        $this->_redirect('/' . ($GLOBALS['cmsOnlyMode'] ? '' : $this->module));
    }
}
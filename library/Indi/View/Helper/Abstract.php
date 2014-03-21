<?php
abstract class Indi_View_Helper_Abstract
{
    /**
     * View object
     *
     * @var Indi_View_Interface
     */
    public $view = null;

    /**
     * Set the View object
     *
     * @param  Indi_View $view
     * @return Indi_View_Helper_Abstract
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }
}

<?php
class Indi_View_Helper_Admin_RenderContent extends Indi_View_Helper_Abstract{

    /**
     * Render central cms page content
     *
     */
	public function renderContent()
	{
		if ($this->view->trail->getItem()) {
            $view = $this->view->trail->getItem()->section->alias . '/'. $this->view->trail->getItem()->action->alias . '.php';
            if ($this->view->exists($view)) {
                echo $this->view->render($this->view->trail->getItem()->section->alias . '/'. $this->view->trail->getItem()->action->alias . '.php');
            } else if ($this->view->getHelper('render' . ucfirst($this->view->trail->getItem()->action->alias), false)) {
                echo $this->view->{'render' . ucfirst($this->view->trail->getItem()->action->alias)}();
            }
		}
	}

}
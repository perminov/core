<?php
class Indi_View_Helper_Admin_RenderContent extends Indi_View_Helper_Abstract{

    /**
     * Render central cms page content
     *
     */
    public function renderContent()
    {
        // If we are in a section
        if (Indi::trail()) {

            // Construct filename of the template, which should be rendered by default
            $script = Indi::trail()->section->alias . '/'. Indi::trail()->action->alias . '.php';

            // Construct filename of the helper, which should be rendered if template file is not exist
            $helper = 'render' . ucfirst(Indi::trail()->action->alias);

            // If template with such filename exists, render the template
            if ($this->view->exists($script))
                return $this->view->render($script);

            // Else if helper for current action exists
            else if ($this->view->getHelper($helper, false))
                return $this->view->$helper();
        }
    }

}
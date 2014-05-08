<?php
class Indi_View_Helper_Admin_RenderContent {

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
            if (Indi::view()->exists($script))
                return Indi::view()->render($script);

            // Else if helper for current action exists
            else if (Indi::view()->getHelper($helper, false))
                return Indi::view()->$helper();
        }
    }

}
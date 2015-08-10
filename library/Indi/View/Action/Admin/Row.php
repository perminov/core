<?php
class Indi_View_Action_Admin_Row extends Indi_View_Action_Admin {
    public function render() {

        // Start output buffering
        ob_start();

        // Setup sibling combo
        Indi::view()->siblingCombo();

        // Prepare and assign raw response for rendering tab contents, if need
        $this->renderTab();

        // Return buffered contents with parent's return-value
        return ob_get_clean() . parent::render();
    }

    /**
     * Prepare and assign raw response for rendering tab contents, if need
     *
     * @return string JSON-response, got by separate call for an uri
     */
    public function renderTab() {

        // If no subsections - return
        if (!Indi::trail()->sections->count()) return;

        // Get last active tab
        $nested = Indi::trail()->scope->actionrow['south']['activeTab'];

        // If last active tab was minimized - return
        if (Indi::trail()->scope->actionrow['south']['height'] == 25) return;

        // If no last active tab, use first section alias instead
        if (!$nested) $nested = Indi::trail()->sections->at(0)->alias;

        // Build url
        $url = '/' . $nested . '/index/id/' . Indi::trail()->row->id
            . '/ph/' . Indi::uri('ph') . '/aix/' . Indi::uri('aix') . '/';

        // Get the response
        $out = Indi::lwget($url);

        // Assign response text
        foreach (Indi::trail()->sections as $sectionR) if ($sectionR->alias == $nested) $sectionR->responseText = $out;
    }
}
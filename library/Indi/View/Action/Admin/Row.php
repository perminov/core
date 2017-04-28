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

        // If `southSeparate` flag is `true` - return
        if (Indi::trail()->section->southSeparate) return;

        // Get last active tab
        $nested = Indi::trail()->scope->actionrow['south']['activeTab'];

        // If last active tab was minimized - return
        if (Indi::trail()->scope->actionrow['south']['height'] == 25) return;

        // If no last active tab, use first section alias instead
        if (!$nested) $nested = Indi::trail()->sections->at(0)->alias;

        // If current row does not have id - return
        if (!Indi::trail()->row->id) return;

        // Build url
        $url = '/' . $nested . '/index/id/' . Indi::trail()->row->id
            . '/ph/' . Indi::uri('ph') . '/aix/' . Indi::uri('aix') . '/';

        // Get the response
        $raw = Indi::lwget($url);

        // Split raw contents by errors and others
        list ($error, $out) = explode('</error>', $raw);

        // If errors detected
        if ($error) {

            // Send HTTP 500 code
            header('HTTP/1.1 500 Internal Server Error');

            // Echo errors
            echo $error . '</error>';
        }

        // Assign response text
        foreach (Indi::trail()->sections as $sectionR) if ($sectionR->alias == $nested) $sectionR->responseText = $out;
    }
}
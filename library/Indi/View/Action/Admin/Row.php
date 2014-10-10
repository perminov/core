<?php
class Indi_View_Action_Admin_Row extends Indi_View_Action_Admin {
    public function render() {

        // Start output buffering
        ob_start();

        // Setup sibling combo
        Indi::view()->siblingCombo();

        // Return buffered contents with parent's return-value
        return ob_get_clean() . parent::render();
    }
}
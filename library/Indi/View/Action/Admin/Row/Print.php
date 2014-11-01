<?php
class Indi_View_Action_Admin_Row_Print extends Indi_View_Action_Admin_Row {
    public function render() {

        // Start output buffering
        ob_start();

        // Return buffered output with parent's return-value
        return ob_get_clean() . parent::render();
    }
}
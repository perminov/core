<?php
class Indi_View_Action_Admin_Rowset extends Indi_View_Action_Admin {
    public function render() {

        // Start output buffering
        ob_start();

        // Setup filters
        foreach (Indi::trail()->filters ?: array() as $filter)
            if ($filter->foreign('fieldId')->relation || $filter->foreign('fieldId')->columnTypeId == 12)
                Indi::view()->filterCombo($filter, 'extjs');

        // Return buffered contents with parent's return-value
        return ob_get_clean() . parent::render();
    }
}
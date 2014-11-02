<?php
class Indi_View_Action_Admin_Row_Print extends Indi_View_Action_Admin_Row {
    public function render() {

        // Start output buffering
        ob_start();

        // Push rendered printable contents into special storage, accessible for javascript
        Indi::trail()->row->view('#print', $this->plain);

        // Return buffered output with parent's return-value
        return ob_get_clean() . parent::render();
    }
}
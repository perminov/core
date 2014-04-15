<?php
class Indi_View_Helper_Admin_RenderForm extends Indi_View_Helper_Abstract {
    public function renderForm() {

        // Start output buffering
        ob_start();

        // Echo form's header (<form>, <table>, misc)
        echo $this->view->formHeader();

        // Declare arrays that will store info about what fields should be excluded
        // from form, and what included, but disabled in form
        $disabled = array(); $excluded = array();
        foreach (Indi::trail()->disabledFields as $disabledField) {
            if ($disabledField->displayInForm) $disabled[$disabledField->fieldId] = true;
            else $excluded[$disabledField->fieldId] = true;
        }

        // Assign an Element_Row objects for each field's `elementId` property
        Indi::trail()->fields->foreign('elementId');

        // Echo a <tr> for each form's field, but only if field's control element's 'hidden' checkbox is not checked
        foreach (Indi::trail()->fields as $fieldR)
            if (!$excluded[$fieldR->id] && $fieldR->foreign['elementId']->hidden != 1)
                echo $this->view->formField($fieldR, $disabled[$fieldR->id]);

        // Echo form's footer
        echo $this->view->formFooter();

        // Return buffered output
        return ob_get_clean();
    }
}
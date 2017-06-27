<?php
class Indi_View_Action_Admin_Row_Form extends Indi_View_Action_Admin_Row {
    public function render() {

        // Start output buffering
        ob_start();

        // Declare arrays that will store info about what fields should be excluded
        // from form, and what included, but disabled in form
        $disabled = array(); $excluded = array();
        foreach (Indi::trail()->disabledFields as $disabledField) {
            if ($disabledField->displayInForm) $disabled[$disabledField->fieldId] = true;
            else $excluded[$disabledField->fieldId] = true;
        }

        // Echo a <tr> for each form's field, but only if field's control element's 'hidden' checkbox is not checked
        foreach (Indi::trail()->fields as $fieldR)
            if (!$excluded[$fieldR->id] && $fieldR->foreign('elementId')->hidden != 1) {
                if ($fieldR->foreign('elementId')->alias == 'upload') {
                    echo Indi::view()->formUpload($fieldR->alias, null, 'extjs');
                } else if (preg_match('/combo|radio|multicheck/', $fieldR->foreign('elementId')->alias))
                    Indi::view()->formCombo($fieldR->alias);
            }

        // Return buffered output with parent's return-value
        return ob_get_clean() . parent::render();
    }
}
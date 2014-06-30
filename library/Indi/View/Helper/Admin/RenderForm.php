<?php
class Indi_View_Helper_Admin_RenderForm {
    public function renderForm() {

        // Start output buffering
        ob_start();

        // Echo form's header (<form>, <table>, misc)
        //echo Indi::view()->formHeader();

        // Declare arrays that will store info about what fields should be excluded
        // from form, and what included, but disabled in form
        $disabled = array(); $excluded = array();
        foreach (Indi::trail()->disabledFields as $disabledField) {
            if ($disabledField->displayInForm) $disabled[$disabledField->fieldId] = true;
            else $excluded[$disabledField->fieldId] = true;
        }

        // Echo a <tr> for each form's field, but only if field's control element's 'hidden' checkbox is not checked
        foreach (Indi::trail()->fields as $fieldR)
            if (!$excluded[$fieldR->id] && $fieldR->foreign('elementId')->hidden != 1)
                if (preg_match('/combo|html/', $fieldR->foreign('elementId')->alias))
                    echo Indi::view()->{'form' . ucfirst($fieldR->foreign('elementId')->alias)}($fieldR->alias, null, 'extjs');

        ?><script>Indi.trail(true).apply(<?=json_encode(Indi::trail(true)->toArray())?>);</script><?

        // Echo form's footer
        //echo Indi::view()->formFooter();*/

        // Return buffered output
        return ob_get_clean();
    }
}
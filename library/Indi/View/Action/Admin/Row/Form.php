<?php
class Indi_View_Action_Admin_Row_Form extends Indi_View_Action_Admin_Row {
    public function render() {

        // Declare arrays that will store info about what fields will be excluded from form
        $excluded = array();
        foreach (Indi::trail()->disabledFields as $disabledField)
            if (!$disabledField->displayInForm)
                $excluded[$disabledField->fieldId] = true;

        // Echo a <tr> for each form's field, but only if field's control element's 'hidden' checkbox is not checked
        foreach (Indi::trail()->fields as $fieldR)
            if (!$excluded[$fieldR->id] && $fieldR->foreign('elementId')->hidden != 1)
                if (preg_match('/combo|radio|multicheck/', $fieldR->foreign('elementId')->alias))
                    Indi::view()->formCombo($fieldR->alias);
                else if ($fieldR->foreign('elementId')->alias == 'upload' && t()->row->abs($fieldR->alias))
                    t()->row->view($fieldR->alias, t()->row->file($fieldR->alias));

        // Return parent's return-value
        return parent::render();
    }
}
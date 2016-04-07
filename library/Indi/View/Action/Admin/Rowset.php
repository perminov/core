<?php
class Indi_View_Action_Admin_Rowset extends Indi_View_Action_Admin {
    public function render() {

        // Start output buffering
        ob_start();

        // Setup filters
        foreach (Indi::trail()->filters ?: array() as $filter)
            if ($filter->foreign('fieldId')->relation || $filter->foreign('fieldId')->columnTypeId == 12)
                Indi::view()->filterCombo($filter, 'extjs');

        // Setup combo-data for cell editors
        Indi::trail()->row = Indi::trail()->model->createRow();
        foreach (Indi::trail()->gridFields->select('one,many', 'storeRelationAbility') as $comboField)
            if ($comboField->relation != 6 || $comboField->storeRelationAbility == 'many')
                Indi::view()->formCombo($comboField->alias, null, 'extjs');

        // Return buffered contents with parent's return-value
        return ob_get_clean() . parent::render();
    }
}
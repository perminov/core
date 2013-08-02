<?php
class Indi_View_Helper_Admin_FormCombo extends Indi_View_Helper_Abstract{
    public function formCombo($name){
        // Get field
        $field = $this->view->trail->getItem()->getFieldByAlias($name);

        if ($field->storeRelationAbility == 'one') {

            // Declare $options array
            $options = array();

            // Get initial set of combo options
            $comboDataRs = $this->view->row->getComboData($name, null, $this->view->row->$name);

            // Get satellite
            if ($field->satellite) $satellite = $field->getForeignRowByForeignKey('satellite');

            // If 'optgroup' param is used
            if ($comboDataRs->optgroup) {
                $by = $comboDataRs->optgroup['by'];
            }

            // Detect key property for options
            $keyProperty = $comboDataRs->enumset ? 'alias' : 'id';

            // Setup primary data for options
            foreach ($comboDataRs as $comboDataR) {
                $system = $comboDataR->system();
                if ($by) $system = array_merge($system, array('group' => $comboDataR->$by));
                if($comboDataRs->enumset && $comboDataR->javascript)
                    $system = array_merge($system, array('js' => $comboDataR->javascript));
                $options[$comboDataR->$keyProperty] = array('title' => Misc::usubstr($comboDataR->title, 50), 'system' => $system);
            }

            // If current field column type is ENUM or SET, and current row have no selected value, we use first
            // option to get default info about what title should be displayed in input keyword field and what value
            // should have hidden field
            if ($comboDataRs->enumset) {
                $key = $this->view->row->$name ? $this->view->row->$name : key($options);
                $selected = array(
                    'title' => Misc::usubstr($options[$key]['title'], 50),
                    'value' => $key
                );
            } else {
                $selected = array(
                    'title' => Misc::usubstr($this->view->row->getForeignRowByForeignKey($name)->title, 50),
                    'value' => $this->view->row->$name ? $this->view->row->$name : $field->defaultValue
                );

            }

            // Prepare options data
            $options = array(
                'ids' => array_keys($options),
                'data' => array_values($options),
                'found' => $comboDataRs->foundRows,
                'page' => $comboDataRs->page,
                'enumset' => $comboDataRs->enumset,
                'js' => $field->javascript
            );

            // Setup tree flag in entity has a tree structure
            if ($comboDataRs->treeColumn) $options['tree'] = true;

            // Setup groups for options
            if ($comboDataRs->optgroup) $options['optgroup'] = $comboDataRs->optgroup;

            // Encode that set in json format
            $options = json_encode($options);

            // If combo-field of current row currently does not have a value, we should disable paging up
            $pageUpDisabled = $this->view->row->$name ? 'false' : 'true';
        }
        ob_start();?>
<div class="combo-div">
    <img class="combo-trigger" id="<?=$name?>-trigger" src="/i/admin/trigger-system.png"/>
    <input class="combo-keyword" type="text" id="<?=$name?>-keyword" lookup="<?=$name?>" value="<?=$selected['title']?>"/>
    <input type="hidden" id="<?=$name?>" value="<?=$selected['value']?>" name="<?=$name?>"/>
    <span class="combo-info" id="<?=$name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$pageUpDisabled?>" page-btm-reached="false" satellite="<?=$satellite->alias?>" changed="false">
            <span class="combo-count" id="<?=$name?>-count"></span>
            <span class="combo-of"><?=COMBO_OF?></span>
            <span class="combo-found" id="<?=$name?>-found"></span>
    </span>
</div>
    <?if (!$attribs['optionsOnly']) {?>
    <script>
    var comboOptions = comboOptions || {};
    comboOptions["<?=$name?>"] = (<?=$options?>);
    </script>
    <?} else {?>
        <?=$options?>
    <?}
    return ob_get_clean();
    }
}
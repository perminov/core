<?php
class Indi_View_Helper_Admin_FormCombo extends Indi_View_Helper_Abstract{
    public function formCombo($name){
        // Get field
        $field = $this->view->trail->getItem()->getFieldByAlias($name);

        if ($field->storeRelationAbility == 'one') {

            // Get initial set of combo options
            $comboDataRs = $this->view->row->getComboData($name, null, $this->view->row->$name);
            foreach ($comboDataRs as $comboDataR) {
                $options[$comboDataR->id] = array('title' => $comboDataR->title, 'system' => $comboDataR->system());
            }

            // Encode that set in json format
            $options = json_encode(array(
                'ids' => array_keys($options),
                'data' => array_values($options),
                'found' => $comboDataRs->foundRows,
                'page' => $comboDataRs->page
            ));

            // If combo-field of current row currently does not have a value, we should disable paging up
            $pageUpDisabled = $this->view->row->$name ? 'false' : 'true';
        }
        ob_start();?>
<div class="combo-div">
    <img class="combo-trigger" id="<?=$name?>-trigger" src="/i/admin/trigger-system.png"/>
    <input class="combo-keyword" type="text" id="<?=$name?>-keyword" lookup="<?=$name?>" value="<?=$this->view->row->getForeignRowByForeignKey($name)->title?>"/>
    <input type="hidden" id="<?=$name?>" value="<?=$this->view->row->$name?>" name="<?=$name?>"/>
    <span class="combo-info" id="<?=$name?>-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="<?=$pageUpDisabled?>" page-btm-reached="false">
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
<?php
class Indi_View_Helper_Admin_FormCombo extends Indi_View_Helper_Abstract{
    public function formCombo($name){
        // Get field
        $field = $this->view->trail->getItem()->getFieldByAlias($name);

        //
        if ($field->storeRelationAbility == 'one') {
            $comboDataRs = $this->view->row->getComboData($name);
            foreach ($comboDataRs as $comboDataR) {
                $options[$comboDataR->id] = array('title' => $comboDataR->title, 'system' => $comboDataR->system());
            }
            $options = json_encode(array('ids' => array_keys($options), 'data' => array_values($options), 'found' => $comboDataRs->foundRows));
        }
        ob_start();?>
<div class="combo-div">
    <img id="<?=$name?>-trigger" src="/i/admin/trigger-system.png" style="position: absolute; margin: 1px 0 0 <?=556-9?>px" class="combo-trigger"/>
    <input type="text" id="<?=$name?>-keyword" lookup="<?=$name?>" style="width: 100%;" value="" class="combo-keyword"/>
    <input type="hidden" id="<?=$name?>" value=""/>
	<span class="combo-info" id="<?=$name?>-info">
		<span class="combo-count" id="<?=$name?>-count"></span>
		<span class="combo-of"><?=COMBO_OF?></span>
		<span class="combo-found" id="<?=$name?>-found"></span>
	</span>
</div>
    <?if (!$attribs['optionsOnly']) {?>
    <script>
    var dselectOptions = dselectOptions || {};
    dselectOptions["<?=$name?>"] = (<?=$options?>);
    </script>
    <?} else {?>
        <?=$options?>
    <?}
    return ob_get_clean();
    }
}
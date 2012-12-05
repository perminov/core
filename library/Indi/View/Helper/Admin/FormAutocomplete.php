<?php
class Indi_View_Helper_Admin_FormAutocomplete extends Indi_View_Helper_Abstract
{
    public function formAutocomplete($name = '') {
		$field = $this->view->trail->getItem()->getFieldByAlias($name);
		$params = $field->getParams();
		ob_start();?>
		<input type="text" name="<?=$name?>" style="width: 100%;" class="autocomplete" identifier="<?=$field->id?>">
		<?if (trim($params['js'])) {?>
		<script>function <?=$name?>AutocompleteSelectHandler(obj){<?=$params['js']?>}</script>
		<?}?>
		<?$xhtml = ob_get_clean();
        return $xhtml; 
    }
}

<?php
class Indi_View_Helper_Admin_FormCheck extends Indi_View_Helper_Abstract
{
    public function formCheck($name = null, $texts = null, $values = null, $value = null, $attribs = null)
    {
		$field = $this->view->trail->getItem()->getFieldByAlias($name);

		$value = $this->view->row->$name;
		if (!$this->view->row->id) {
			$value = $field->defaultValue;
		}

		$xhtml = '<field>';
		$xhtml .= '<input type="hidden" name="'.$name.'" value="' . $value . '" id="' . $name . '">';
		$xhtml .= '<span class="checkbox' . ($value ? ' checked' : '') . '" id="span-checkbox-' . $name . '">&nbsp;</span>';
		$xhtml .= '<script>$("span.checkbox[id=span-checkbox-'.$name.']").click(function(){
			if ($(this).parents("field").find("input[type=hidden]").val() == "1") {
				$(this).parents("field").find("input[type=hidden]").val("0");
				$(this).removeClass("checked");
			} else {
				$(this).parents("field").find("input[type=hidden]").val("1");
				$(this).addClass("checked");
			};
 			' . $field->javascript . ';
		})</script>';

		$xhtml .= '<script>$(document).ready(function (){$("#span-checkbox-' . $name . '").click()})</script>';
		$xhtml .= '<script>$(document).ready(function (){$("#span-checkbox-' . $name . '").click()})</script>';
		$xhtml .= '</field>';
		return $xhtml;
    }
}
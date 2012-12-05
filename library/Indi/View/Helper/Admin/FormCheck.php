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
		
        $xhtml .= '<input type="hidden" name="' . $name . '[-1]"' .' value="">';
		$xhtml .= '<input type="checkbox" name="' . $name . '[]" value="1"' . ($value == '1' ? ' checked="checked"' : '') . ' style="width: 13px; height: 13px;" onclick="javascript: ' . (trim($field->javascript) ? str_replace('"', '\'', $field->javascript) : 'void(0)') . '" id="' . $name . '1">';
		$xhtml .= '<script>$(document).ready(function (){$("#' . $name . '1").click()})</script>';
		$xhtml .= '<script>$(document).ready(function (){$("#' . $name . '1").click()})</script>';
//		$xhtml .= '<script>$(document).ready(function (){$("#' . $name . $value . '").click()})</script>';
//		$xhtml .= '<script>$(document).ready(function (){$("#' . $name . $value . '").click()})</script>';
		return $xhtml;
    }
}
<?php
class Indi_View_Helper_FormSelect extends Indi_View_Helper_FormElement{
	public function formSelect($name = '', $options = array(), $value = 0, $attribs = array()){

		if (isset($attribs['zeroLabel'])){
			$zeroLabel = $attribs['zeroLabel'];
			$zero = '%';
			unset($attribs['zeroLabel']);
		}
		$html = '<select name="' . $name . '" id="' . $name . '" ' . $this->_htmlAttribs($attribs) . '>';
		if ($zero) {
			$html .= '<option value="' . $zero . '">' . $zeroLabel . '</option>';
		}
		if (!is_array($options)) {
			$field = Misc::loadModel('Field')->fetchRow('`entityId` = "' . $this->view->section->entityId . '" AND `alias` = "' . $name . '"');
			$columnType = $field->getForeignRowByForeignKey('columnTypeId')->type;
			if (strpos($columnType, 'ENUM') !== false || strpos($columnType, 'SET') !== false) {
				$options = Misc::loadModel('Enumset')->getOptions(null, $field->id);
			}
			if ($value === null) {
				$value = $this->view->post[$name];
				if (!$value) $value = $this->view->row->$name;
			}
		}
		foreach($options as $optionValue => $optionLabel) $html .= '<option value="' . $optionValue . '"' . ($optionValue == $value ? ' selected="selected"' : '') . '>' . $optionLabel . '</option>';
		$html .= '</select>';
		return $html;
	}
}
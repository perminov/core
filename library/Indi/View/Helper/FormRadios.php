<?php
class Indi_View_Helper_FormRadios extends Indi_View_Helper_Abstract
{
    public function formRadios($name = null, $texts = null, $values = null, $value = null, $attrib = '', $add = null)
    {
		if (!$texts) {
			$field = Misc::loadModel('Field')->fetchRow('`entityId` = "' . $this->view->section->entityId . '" AND `alias` = "' . $name . '"');
			$columnType = $field->getForeignRowByForeignKey('columnTypeId')->type;
			if (strpos($columnType, 'ENUM') !== false || strpos($columnType, 'SET') !== false) {
				$options = Misc::loadModel('Enumset')->getOptions(null, $field->id);
			} else {
				$options = $this->view->row->getDropdownData($name, $this->view->trail);
				$options = $options ? $options : array();
			}
			if (count($options)) {
				$xhtml = array();
				foreach ($options as $alias => $title) {
					$id = $name . ucwords($alias) . 'Label';
					$radio = '<span class="' . ($alias==$this->view->row->$name?'':'un') . 'checked radio-item">';
					$radio .= '<input type="radio" name="' . $name . '" value="' . $alias . '"  id="' . $id . '">';
					$radio .= '<label for="' . $id . '"> ' . $title . '</label>';
					$radio .= '</span>';
					$xhtml[] = $radio;
				}
				$xhtml = implode("\n", $xhtml);
			}
		} else {
			// if name not set name will be set to 'toggle'
			$name = $name ? $name : 'toggle';
			
			// texts passed to helper should be comma separated
			// if texts are not set default values are 'y,n'
			$texts = $texts ? (is_array($texts) ? $texts : explode(',', $texts)) : array ('Yes','No');

			// values passed to helper by parameter should be comma separated
			$values = $values ? (is_array($values) ? $values : explode(',', $values)) : null;

			// else default values are 'y','n'
			$values = $values ? $values : array('y', 'n');

			$xhtml = '';

			for ($i = 0; $i < count($values); $i++) {
				if ($values[$i] == $value) {
					$checked = ' checked="checked"';
					$checkedIndex = $i;
				} else {
					$checked = '';
					$uncheckedIndex = $i;
				}

				$id = ' id="' . $name . ucwords($values[$i]) .'"';
				$label = '<label for="' . $name . ucwords($values[$i]) .'" id="' . $name . ucwords($values[$i]) . 'Label">' . $texts[$i] . '</label>&nbsp;';
				$xhtml .= '<input style= "width:13px; height: 12px;" type="radio" name="' . $name . '" value="' . $values[$i] . '" ' . $checked . $id . (is_array($attrib) ? $attrib[$i] : $attrib) .  ' ' . $add . '>' . $label . '<br>';
			}
			$xhtml .= '<script>$(function(){ $("#' . $name . ucwords($values[$checkedIndex]) . '").click()})</script>';
		}
        return $xhtml;
    }
}

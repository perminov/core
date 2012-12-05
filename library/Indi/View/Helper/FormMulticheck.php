<?php
class Indi_View_Helper_FormMulticheck extends Indi_View_Helper_FormElement
{
    public function formMulticheck($name, $cols = 1, $attribs = array())
    {
		$field = Misc::loadModel('Field')->fetchRow('`entityId` = "' . $this->view->section->entityId . '" AND `alias` = "' . $name . '"');
		$columnType = $field->getForeignRowByForeignKey('columnTypeId')->type;
		if (strpos($columnType, 'ENUM') !== false || strpos($columnType, 'SET') !== false) {
			$multi = Misc::loadModel('Enumset')->getOptions(null, $field->id);
		} else {
			$multi = $this->view->row->getDropdownData($name, $this->view->trail);
			$multi = $multi ? $multi : array();
		}
                
        $data = array();
		$value = $this->view->post[$name] ? $this->view->post[$name] : $this->view->row->$name;
		$checked = is_array($value) ? $value : explode(',', $value);
        while(list($value, $text) = each($multi)) {
            $item = new stdClass();
            $item->value = $value;
            $item->text = $text;
            if (is_array($checked) && in_array($value, $checked)) $item->checked = true;
            $data[] = $item;
        }

        $rowsHeightLimit = 10;
        $count = count($data);
        $rows  = ceil($count / $cols);
        
        $checkboxIndex = 0;
        if ($count) {
			if ($rows > $rowsHeightLimit || true) $xhtml = '<div style="overflow-y: scroll; height: ' . (19 * $rowsHeightLimit) . 'px;" id="edit-char-checkboxes-wrapper" class="form-item">';
			$xhtml = '<div ' . $this->_htmlAttribs($attribs) . '>';
//			$xhtml .= '<table cellpadding="0" cellspacing="0" border="0" id="' . $name . '">';
            $checkbox = current($data);
            $aName = ' name="' . $name . '[]"';
            $type = ' type="checkbox"';
            $xhtml .= '<input' . $type . ' name="' . $name . '[-1]"' .' value="0" checked="checked" style="display: none;">';
			if ($attribs['optionsOnly']) {
				$open = $xhtml;
				$xhtml = '';
			}
            for ($i = 0; $i < $rows; $i++) {
//		                     $xhtml .= '<tr class="info">';
                for ($j = 0; $j < $cols; $j++) {
                    if ($checkbox) {
                        $checked = $checkbox->checked ? ' checked="checked"' : '';
                        $value   = ' value="' . $checkbox->value . '"';
//                        				$xhtml  .= '<td style="vertical-align:middle;">';
						$xhtml  .= '<span class="custom-checkbox ' . ($checkbox->checked ? 'checked':'unchecked') . '"><input' . $type . $aName . $value . $checked . ' style="width:13px; height: 13px;" id="' . $name . 'checkbox' . $checkboxIndex . '"><label for="' . $name . 'checkbox' . $checkboxIndex . '"> ' . $checkbox->text . '</label></span>';
//						$xhtml  .= '</td>';
                        $checkbox = next($data);
                        $checkboxIndex++;
                    }
                }
//                			$xhtml .= '</tr>';
            }
			if ($attribs['optionsOnly']) {
				$data = $xhtml;
				$xhtml = '';
			}

//            		$xhtml .= '</table>';
			if ($rows > $rowsHeightLimit || true) $xhtml .= '</div>';

/*			if ($attribs['optionsOnly']) {
				$close = $xhtml;
				$xhtml = '';
			}

			if (!$attribs['optionsOnly']) {
				if ($field->satellite) {
					$satelliteRow = $field->getForeignRowByForeignKey('satellite');
					$satellite = $satelliteRow->alias;
					$xhtml .= "<script>\$('#". $satellite ."').change(function(){\$.post('./json/1/', { field: '" . $name . "', satellite: \$('#". $satellite ."').attr('value') },   function(data) {     \$('#". $name ."').html(data);" . str_replace(array('"', "\n", "\r"), array('\"',"",""), $satelliteRow->javascript) . "},'html');}); \$('#". $satellite ."').change();</script>";
				}
				if (!$field->isSatellite() && $field->javascript){
					$xhtml .= "<script>\$('#". $field->alias ."').change(function(){". str_replace(array('"', "\n", "\r"), array('\"',"",""), $field->javascript) . "}); \$('#". $field->alias ."').change();</script>";
				}
			} else $xhtml = $data;
			*/
        }
        return $xhtml;
    }
}
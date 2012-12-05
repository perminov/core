<?php
class Indi_View_Helper_Admin_FormMulticheck extends Indi_View_Helper_Abstract
{
    public function formMulticheck($name, $cols = 1, $attribs = array())
    {
        $multi = $this->view->row->getDropdownData($name, $this->view->trail);
        $multi = $multi ? $multi : array();
                
        $data = array();
		$field = $this->view->trail->getItem()->getFieldByAlias($name);
		$params = $field->getParams();
		if ($params['cols']) $cols = $params['cols'];
		if(!$this->view->row->id) {
			$value = $field->defaultValue;
		} else {
			$value = $this->view->row->$name;
		}
		$checked = explode(',', $value);
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
			if ($rows > $rowsHeightLimit) $xhtml = '<div style="overflow-y: scroll; height: ' . (19 * $rowsHeightLimit) . 'px;">';
			$xhtml .= '<table cellpadding="0" cellspacing="0" border="0" id="' . $name . '" width="100%">';
            $checkbox = current($data);
            $aName = ' name="' . $name . '[]"';
            $type = ' type="checkbox"';
			$xhtml .= '<input type="hidden" name="' . $name . '[-1]"' .' value="">';
			if ($attribs['optionsOnly']) {
				$open = $xhtml;
				$xhtml = '';
			}
            for ($i = 0; $i < $rows; $i++) {
                $xhtml .= '<tr class="info">';
                for ($j = 0; $j < $cols; $j++) {
                    if ($checkbox) {
                        $checked = $checkbox->checked ? ' checked="checked"' : '';
                        $value   = ' value="' . $checkbox->value . '"';
                        $xhtml  .= '<td style="vertical-align:middle; padding-right: 10px;" width="' . floor(100/$cols) . '%"><input' . $type . $aName . $value . $checked . ' style="width:13px; height: 13px;" id="' . $name . 'checkbox' . $checkboxIndex . '"><label for="' . $name . 'checkbox' . $checkboxIndex . '" style="line-height:10px;">' . $checkbox->text . '</label></td>';
                        $checkbox = next($data);
                        $checkboxIndex++;
                    }
                }
                $xhtml .= '</tr>';
            }
			if ($attribs['optionsOnly']) {
				$data = $xhtml;
				$xhtml = '';
			}

            $xhtml .= '</table>';
			if ($rows > $rowsHeightLimit) $xhtml .= '</div>';

			if ($attribs['optionsOnly']) {
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
			
        }
        return $xhtml;
    }
}
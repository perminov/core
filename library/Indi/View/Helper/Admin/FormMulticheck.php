<?php
class Indi_View_Helper_Admin_FormMulticheck extends Indi_View_Helper_Abstract
{
    public function formMulticheck($name, $cols = 1, $attribs = array())
    {
        $multi = $this->view->row->getComboData($name);
        $multi = $multi ? $multi : array();
                
        $data = array();
		$field = Indi::trail()->model->fields($name);
		$params = $field->getParams();
		if ($params['cols']) $cols = $params['cols'];
		if(!$this->view->row->id) {
			$value = $field->defaultValue;
		} else {
			$value = $this->view->row->$name;
		}
		$checked = explode(',', $value);
        foreach ($multi as $multiI) {
            $item = new stdClass();
            $item->value = $multiI->id;
            $item->text = $multiI->title;
            if (is_array($checked) && in_array($multiI->id, $checked)) $item->checked = true;
            $data[] = $item;
        }
        $rowsHeightLimit = 10;
        $count = count($data);
        $rows  = ceil($count / $cols);
        
        $checkboxIndex = 0;
        if ($count) {
			if ($rows > $rowsHeightLimit) $xhtml = '<div style="overflow-y: scroll; height: ' . (19 * $rowsHeightLimit) . 'px;">';
			$xhtml .= '<table cellpadding="0" cellspacing="0" border="0" id="' . $name . '-table" width="' . $params['width'] . '" class="multicheckbox">';
            $checkbox = current($data);
            $aName = ' name="' . $name . '[]"';
            $type = ' type="hidden"';
			//$xhtml .= '<input type="hidden" name="' . $name . '[-1]"' .' value="">';
			if ($attribs['optionsOnly']) {
				$open = $xhtml;
				$xhtml = '';
			}
            for ($i = 0; $i < $rows; $i++) {
                $xhtml .= '<tr class="info" style="border: none;">';
                for ($j = 0; $j < $cols; $j++) {
                    if ($checkbox) {
                        $disabled = $checkbox->checked ? '' : ' disabled="disabled"';
                        $checked = $checkbox->checked ? ' checked' : '';
                        $value   = ' value="' . $checkbox->value . '"';
                        $xhtml  .= '<td style="vertical-align:middle; padding-right: 10px;" width="' . floor(100/$cols) . '%">';
						$xhtml  .=  '<input' . $type . $aName . $value . ' id="input-checkbox-' . $name . ucfirst($checkbox->value) . '"' . $disabled . '/>';
						$xhtml  .=  '<span class="checkbox' . $checked . '" id="span-checkbox-' . $name . ucfirst($checkbox->value) . '">';
						//$xhtml  .= 	'<input' . $type . $aName . $value . $checked . ' style="width:13px; height: 13px;" id="' . $name . 'checkbox' . $checkboxIndex . '">';
						$xhtml  .= 	'<label for="span-checkbox-' . $name . ucfirst($checkbox->value) . '" style="line-height:10px;">' . $checkbox->text . '</label>';
						$xhtml .= '</span>';
						$xhtml  .= '</td>';
                        $checkbox = next($data);
                        $checkboxIndex++;
                    }
                }
                $xhtml .= '</tr>';
            }
			$xhtml .= '<script>$("span.checkbox[id^=span-checkbox-'.$name.']").click(function(){
                if (!$(this).hasClass("disabled") && !$(this).parents("table.multicheckbox").hasClass("disabled")) {
                    if ($(this).parent().find("input[type=hidden]").attr("disabled")) {
                        $(this).parent().find("input[type=hidden]").removeAttr("disabled");
                        $(this).addClass("checked");
                    } else {
                        $(this).parent().find("input[type=hidden]").attr("disabled","disabled");
                        $(this).removeClass("checked");
                    };
                    ' . $field->javascript . ';

                }
			})</script>';
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
					$satelliteRow = $field->foreign('satellite');
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
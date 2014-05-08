<?php
class Indi_View_Helper_Admin_FormMulticheck {
    /**
     * Setup row object for combo
     *
     * @return Indi_Db_Table_Row
     */
    public function getRow(){
        return Indi::view()->row;
    }

    /**
     * Default value determining is extracted from raw code to seperate function for inherited classes
     * being able to setup a different logic for dealing with default values
     *
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->field->compiled('defaultValue');
    }

    public function formMulticheck($name, $cols = 1, $attribs = array())
    {
        $this->field = $field = Indi::trail()->model->fields($name);

        // If current row does not exist, multicheck will use field's default value as selected value
        if ($this->getRow()->id) {
            $selected = $this->getRow()->$name;
        } else {
            $selected = $this->getDefaultValue();
        }

        $key = $this->field->foreign('relation')->table == 'enumset' ? 'alias' : 'id';
        $multi = Indi::view()->row->getComboData($name, null, $selected);
        $checked = $multi->selected->column($key);
        $multi = $multi ? $multi : array();
        $data = array();
		$params = $this->field->params;
		if ($params['cols']) $cols = $params['cols'];
        foreach ($multi as $multiI) {
            $item = new stdClass();
            $item->value = $multiI->$key;
            $item->text = $multiI->title;
            if (is_array($checked) && in_array($multiI->$key, $checked)) $item->checked = true;
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
				if (!Indi::model('Field')->fetchRow('`satellite` = "' . $field->id . '"') && $field->javascript){
					$xhtml .= "<script>\$('#". $field->alias ."').change(function(){". str_replace(array('"', "\n", "\r"), array('\"',"",""), $field->javascript) . "}); \$('#". $field->alias ."').change();</script>";
				}
			} else $xhtml = $data;
			
        }
        return $xhtml;
    }
}
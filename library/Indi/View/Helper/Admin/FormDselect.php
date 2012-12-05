<?php
class Indi_View_Helper_Admin_FormDselect extends Indi_View_Helper_FormElement
{
    public function formDselect($name, $value = null, $attribs = null, $options = null)
    {
        $this->name = $name;
        $value = $value ? $value : $this->view->row->$name;
		$field = $this->view->trail->getItem()->getFieldByAlias($name);
		if (!$this->view->row->id) {
			$value = $field->defaultValue;
		}
		if ($this->view->row->$name) $value = $this->view->row->$name;
		
		if (!isset($attribs['noempty'])) $attribs['noempty'] = @eregi('ENUM', $field->getForeignRowByForeignKey('columnTypeId')->type) == 1 ? true : false; 

		$gddOptions = array(
			'find' => $attribs['find'],
			'more' => $attribs['more'],
		);
		if ($attribs['value']) $this->view->row->$name = $attribs['value'];
		ini_set('error_reporting', E_ALL ^ E_NOTICE);

		$text = $this->view->trail->getItem()->row->getForeignRowByForeignKey($name)->title;
		
		$gddOptions['value'] = $text;

		$gddOptions['element'] = 'dselect';
		
		if (isset($attribs['page'])) $gddOptions['page'] = $attribs['page'];
		if (isset($attribs['up'])) $gddOptions['up'] = $attribs['up'];
		
		$options = $options ? $options : $this->view->row->getDropdownData($name, $this->view->trail, $gddOptions);
		
		
        if (!is_array($options)) $options = array();
        $options = array_reverse($options, true);
        if ($attribs['noempty'] !== true && $attribs['noempty'] !== 'true') {
            if (!$attribs['defaultvalue']) $attribs['defaultvalue'] = 0;
            $options[$attribs['defaultvalue']] = $attribs['default'] ? $attribs['default'] : 'Выберите' ;
        }

		if (!$attribs['optionsOnly']) {
			if ($field->satellite) {
				$satelliteRow = $field->getForeignRowByForeignKey('satellite');
				$satellite = $satelliteRow->alias;
			}
			if (!$field->isSatellite() && $field->javascript){
//				$xhtml .= "\$('#". $field->alias ."').change(function(){". str_replace(array('"', "\n", "\r"), array('\"',"",""), $field->javascript) . "}); \$('#". $field->alias ."').change();";
			}
		}
		$xhtml = '
<div class="dselect-div">
	<input class="dselect-lookup" prev="'.$text.'" lookup="'.$name.'" id="'.$name.'-lookup" value="'.$text.'">
	<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.$value.'" satellite="'.$satellite.'">
	<span class="dselect-info" id="'.$name.'-info">
		<span class="dselect-current" id="'.$name.'-current"></span>
		<span class="dselect-count" id="' . $name .'-count"></span>
	</span>
</div>  
<select class="dselect-button" id="'.$name.'-button" style="width: 100%;"></select>
		';
		
        $options = array_reverse($options, true);
		
		$options = json_encode(array('keys' => array_keys($options), 'values' => array_values($options)));
		if (!$attribs['optionsOnly']) {
			$xhtml .= '<script>';
			$xhtml .= 'var dselectOptions = dselectOptions || {};';
			$xhtml .='dselectOptions["'.$name.'"] = (' . $options . ');';
			$xhtml .= '</script>';
		} else {
			$xhtml = $options;
		}
		
		return $xhtml;
	}
}
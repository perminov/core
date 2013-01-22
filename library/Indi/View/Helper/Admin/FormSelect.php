<?php
class Indi_View_Helper_Admin_FormSelect extends Indi_View_Helper_FormElement
{
    public $name = '';
    public function formSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n")
    {
        $this->name = $name;
        $value = $value ? $value : $this->view->row->$name;
		$field = $this->view->trail->getItem()->getFieldByAlias($name);
		if (!$this->view->row->id) {
			$value = $field->defaultValue;
		}
		if ($this->view->row->$name) $value = $this->view->row->$name;
		$attribs['noempty'] = @eregi('ENUM', $field->getForeignRowByForeignKey('columnTypeId')->type) == 1 ? true : false; 

		$options = $options ? $options : $this->view->row->getDropdownData($name, $this->view->trail, array('value' => $attribs['value']));
		if($field->columnTypeId == 10) {
			$jss = Misc::loadModel('Enumset')->fetchAll('`fieldId` = "' . $field->id . '" AND FIND_IN_SET(`alias`, "' . implode(',', array_keys($options)) . '")');

			ob_start();?>var <?=$this->name?>Handler = function (){
				switch(this.value) {<?foreach($jss as $option){?>
					case '<?=$option->alias?>':
						<?=$option->javascript?>;
						break;
					<?}?>
				}
			}<?$enumsetJs = ob_get_clean();
		}
		if (!is_array($options)) $options = array();
        $options = array_reverse($options, true);
        if ($attribs['noempty'] !== true) {
            if (!$attribs['defaultvalue']) $attribs['defaultvalue'] = 0;
            $options[$attribs['defaultvalue']] = $attribs['default'] ? $attribs['default'] : 'Выберите' ;
        }
        $options = array_reverse($options, true);

        if (!isset($attribs['style'])) {
            $attribs['style'] = "width: 100%;";
        }
        
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, id, value, attribs, options, listsep, disable

        // force $value to array so we can compare multiple values
        // to multiple options.
        settype($value, 'array');
        
        // check for multiple attrib and change name if needed
        if (isset($attribs['multiple']) &&
            $attribs['multiple'] == 'multiple' &&
            substr($name, -2) != '[]') {
            $name .= '[]';
        }
        
        // check for multiple implied by the name and set attrib if
        // needed
        if (substr($name, -2) == '[]') {
            $attribs['multiple'] = 'multiple';
        }
                
        // now start building the XHTML.
        if ($disable) {
        
            // disabled.
            // generate a plain list of selected options.
            // show the label, not the value, of the option.
            $list = array();
            foreach ($options as $opt_value => $opt_label) {
                if (in_array($opt_value, $value)) {
                    // add the hidden value
                    $opt = $this->_hidden($name, $opt_value);
                    // add the display label
                    $opt .= $this->view->escape($opt_label);
                    // add to the list
                    $list[] = $opt;
                }
            }
            $xhtml = implode($listsep, $list);
            
        } else {
            // enabled.
            // the surrounding select element first.
            $xhtml = '<span class="select"><select'
                   . ' name="' . $this->view->escape($name) . '"'
                   . ' id="' . $this->view->escape($id) . '"';
            if ($attribs['change'] === true) {
                if ($attribs['ajax'] === true) {
                    $xhtml .= ' onChange="javascript: var div = new Array();';
                    foreach ($attribs['div'] as $key => $val) {
                        $xhtml.='div['.$key.'] = \''.$val. '\';';
                    }
                    $xhtml.='selectChangeAjax(\'' . $this->view->escape($id) . '\',div);"';
                } else {
                    $xhtml .= ' onChange="javascript:';
                    $xhtml.='selectChange(\'' . $this->view->escape($id) . '\');"';                    
                }
                                
            }
            $xhtml .= $this->_htmlAttribs($attribs)
                   . ">\n\t";
                           
            $selectOpenTag = $xhtml; $xhtml = '';

            // build the list of options
            $list = array();
            foreach ($options as $opt_value => $opt_label) {

                if (is_array($opt_label)) {
                    $list[] = '<optgroup '
                            . 'label="' . $this->view->escape($opt_value) .'">';                           
                    foreach ($opt_label as $val => $lab) {
                        $list[] = $this->_build($val, $lab, $value);
                    }
                    $list[] = '</optgroup>';
                } else {
                    $list[] = $this->_build($opt_value, $opt_label, $value);
                }
            }

            
            // add the options to the xhtml and close the select
            $xhtml .= implode("\n\t", $list) . "\n";

			$selectCloseTag = "</select></span>";
			if (!$attribs['optionsOnly']) {
				$xhtml = $selectOpenTag . $xhtml . $selectCloseTag;
				if ($field->satellite) {
					$satelliteRow = $field->getForeignRowByForeignKey('satellite');
					$satellite = $satelliteRow->alias;
					$xhtml .= "<script>\$('#". $satellite ."').change(function(){\$.post('./json/1/', { field: '" . $name . "', satellite: \$('#". $satellite ."').attr('value') },   function(data) {     \$('#". $name ."').html(data);" . str_replace(array('"', "\n", "\r"), array('\"',"",""), $satelliteRow->javascript) . "; additionalCallback('" . $satellite . "')},'html');}); \$('#". $satellite ."').change();</script>";
				}
				if (!$field->isSatellite() && $field->javascript){
					$xhtml .= "<script>\$('#". $field->alias ."').change(function(){". str_replace(array('"', "\n", "\r"), array('\"',"",""), $field->javascript) . "}); \$('#". $field->alias ."').change();</script>";
				}
				if (isset($enumsetJs)) {
					$xhtml .= '<script>' . $enumsetJs . ";$(document).ready(function(){\$('#". $this->name ."').change(" .$this->name . "Handler);\$('#". $this->name ."').change();});</script>";
				}
			}
        }
        return $xhtml; 
    }

    /**
     * Builds the actual <option> tag
     * 
     * @param string $value Options Value
     * @param string $label Options Label
     * @param array  $selected The option value(s) to mark as 'selected'
     * @return string Option Tag XHTML
     */
    protected function _build($value, $label, $selected)
    {
        $opt = '<option'
//             . ' value="' . $this->view->escape($value) . '"'
             . ' value="' . $value . '"'
//             . ' label="' . $this->view->escape($label) . '"';
             . ' label="' . $label . '"';
             
        // selected?
        if (in_array($value, $selected)) {
            $opt .= ' selected="selected"';
        }

        $opt .= ' id="' . $this->name . '_' . $value . '"';
        
        $opt .= '>' . $label . "</option>";
//        $opt .= '>' . $this->view->escape($label) . "</option>";
        
        return $opt;
    }

}

<?php
class Indi_View_Helper_Admin_FormRadios extends Indi_View_Helper_Abstract
{
    public function formRadios($name = null, $texts = null, $values = null, $value = null, $meta = true, $attrib = '', $add = null)
    {
        // if name not set name will be set to 'toggle'
        $name = $name ? $name : 'toggle';
        
        // current value by default is got from current row
        $value = $value ? $value : $this->view->row->$name;
        // texts passed to helper should be comma separated
        // if texts are not set default values are 'y,n'
        $texts = $texts ? explode(',', $texts) : array ('Yes','No');

        // get meta information about column
        if ($meta) {
            $meta = $this->view->row->getTable()->getMetadata($name);
        }
        
        // values passed to helper by parameter should be comma separated
        $values = $values ? explode(',', $values) : null;

        // if values are not set by parameter, and column type is 'enum' then
        // default values are got from metadata of this column
        
        if (($meta)&&(preg_match('/enum\(\'(.*)\'\)/i', $meta['DATA_TYPE'], $matches))) {
            $values = $values ? $values : explode('\',\'', $matches[1]);
        } else {
            // else default values are 'y','n'
            $values = $values ? $values : array('y', 'n');
        }

        // if current row is null, for ex when creating new row
        // and if we can get metadata  of the column, default value
        // will be set to corresponding column default value, else 
        // to first value in final values array
        if ($meta) {
            $default = $meta['DEFAULT'] ? $meta['DEFAULT'] : trim($values[0]);
            // else value is set to first in valies list
            $value = $value ? $value : $default;
        }
        $xhtml = '';
        for ($i = 0; $i < count($values); $i++) {
			if ($values[$i] == $value) {
				$checked = ' checked="checked"';
				$checkedIndex = $i;
			} else {
				$checked = '';
				$uncheckedIndex = $i;
			}
//			$checked = $values[$i] == $value ? ' checked="checked"' : '';
//          $id = ' id="' . $name . ucwords($values[$i]) . $add .'"';

			$id = ' id="' . $name . ucwords($values[$i]) .'"';
//            $label = '<label for="' . $name . ucwords($values[$i]) . $add .'" id="' . $name . ucwords($values[$i]) . $add . 'Label">' . $texts[$i] . '</label>&nbsp;';
            $label = '<label for="' . $name . ucwords($values[$i]) .'" id="' . $name . ucwords($values[$i]) . 'Label">' . $texts[$i] . '</label>&nbsp;';
            $xhtml .= '<input style= "width:13px; height: 12px;" type="radio" name="' . $name . '" value="' . $values[$i] . '" ' . $checked . $id . (is_array($attrib) ? $attrib[$i] : $attrib) .  ' ' . $add . '>' . $label . '<br>';
			if ($checked) {
//				$xhtml .= '<script>$(document).ready(function(){ $("#' . $name . ucwords($values[$i]) . '").click()})</script>';
			}
		}
//		$xhtml .= '<script>$(function(){ $("#' . $name . ucwords($values[$uncheckedIndex]) . '").click()})</script>';
		$xhtml .= '<script>$(function(){ $("#' . $name . ucwords($values[$checkedIndex]) . '").click()})</script>';
        return $xhtml;
    }
}

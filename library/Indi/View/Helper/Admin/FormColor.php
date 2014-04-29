<?php
class Indi_View_Helper_Admin_FormColor extends Indi_View_Helper_Abstract
{
    public function formColor($name = 'color', $value = null)
    {
		if (Indi::view()->row->id) {
	        $value = $value ? $value : Indi::view()->row->$name;
		} else {
			$value = Indi::trail()->model->fields($name)->defaultValue;
		}

        $value = substr($value, 3);
        
        $xhtml = '<input id="' . $name . '" type="text" name="' . $name . '" onclick=\'colorPicker(event)\' style="width: 50px; text-transform: lowercase;" value="' . $value . '"/>';
        return $xhtml;
    }
}
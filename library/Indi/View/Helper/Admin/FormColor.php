<?php
class Indi_View_Helper_Admin_FormColor extends Indi_View_Helper_Abstract
{
    public function formColor($name = 'color', $value = null)
    {
		$p = '/i/admin/';

		static $zIndex;
        
        $zIndex++;
        
        // default value is got from row
		if ($this->view->row->id) {
	        $value = $value ? $value : $this->view->row->$name;
		} else {
			$value = $this->view->trail->getItem()->getFieldByAlias($name)->defaultValue;
		}
        
        $xhtml  = '<div style="position: relative; z-index: ' . (200 - $zIndex) . '">';
        $xhtml .= '<input type="text" name="' . $name . '" value="' . substr($value, 3) . '" style="width: 52px; top: 1px; position: relative;" maxlength="7" id="' . $name . 'Input">';
        $xhtml .= '<iframe id="' . $name . 'Colorpicker" name="' . $name . 'Colorpicker" src="/admin/auxillary/colorpicker/name/' . $name . '/" frameborder="0" scrolling="no" style="display: inline; width: 218px; height: 190px; position: absolute; top: 1px; left: 73px; z-index: 1; border:1px solid #999999;"></iframe>';
        $xhtml .= ' <a href="javascript:void(0);" onclick="javascript:$(\'#' . $name . 'Colorpicker\').toggle();"><img src="' . $p . 'ico_color.gif" alt="Show color picker" width="16" height="18" border="0" style="vertical-align: top; margin-top: 1px; position: relative; z-index: 100;"></a>';
        $xhtml .= '<script>$("#' . $name . 'Colorpicker").css("display","none");</script>';
        $xhtml .= '</div>';
        return $xhtml;
    }
}
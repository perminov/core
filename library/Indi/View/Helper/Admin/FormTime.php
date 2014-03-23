<?php
class Indi_View_Helper_Admin_FormTime extends Indi_View_Helper_FormElement
{
    public function formTime($name, $value = null, $attribs = null)
    {
        if ($value === null) {
			if(!$this->view->row->id) {
				$value = Indi::trail()->model->fields($name)->defaultValue;
			} else {
	            $value = $this->view->row->$name;
				if (!$value) $value = '00:00:00';
			}
		}
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        
        // build the element
        if ($disable) {
            // disabled
            $xhtml = $this->_hidden($name, $value)
                   . $this->view->escape($value);
        } else {
            // enabled
			$time = explode(':', $value);
			for ($i = 0; $i <= 2; $i++)	$time[$i] = strlen($time[$i]) == 1 ? $time[$i] . '0' : (strlen($time[$i]) == 0 ? '00' : $time[$i]);
			$xhtml = '<input type="text"'
                   . ' name="' . $this->view->escape($name) . '[hours]"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . ' value="' . $this->view->escape($time[0]) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=number(this.value)" /> часов ';

			$xhtml .= '<input type="text"'
                   . ' name="' . $this->view->escape($name) . '[minutes]"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . ' value="' . $this->view->escape($time[1]) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=decimal(number(this.value));"/> минут ';

		   $xhtml .= '<span style="display: none;"><input type="text"'
                   . ' name="' . $this->view->escape($name) . '[seconds]"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . ' value="' . $this->view->escape($time[2]) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=decimal(number(this.value));"/> секунд</span>';
        }
        
        return $xhtml;
    }    
}
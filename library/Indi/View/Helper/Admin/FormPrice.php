<?php
class Indi_View_Helper_Admin_FormPrice extends Indi_View_Helper_FormElement
{
    public function formPrice($name, $value = null, $attribs = null)
    {
        if ($value === null) {
			if(!$this->view->row->id) {
				$value = $this->view->trail->getItem()->getFieldByAlias($name)->defaultValue;
			} else {
	            $value = $this->view->row->$name;
				if (!$value) $value = '0.00';
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
			$price = explode('.', $value);
			$price[1] = strlen($price[1]) == 1 ? $price[1] . '0' : (strlen($price[1]) == 0 ? '00' : $price[1]);
			$xhtml = '<input type="text"'
                   . ' name="' . $this->view->escape($name) . '[integer]"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . ' value="' . $this->view->escape($price[0]) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: 50px; text-align: right;" maxlength="5" onchange="this.value=number(this.value)" /> руб. ';

			$xhtml .= '<input type="text"'
                   . ' name="' . $this->view->escape($name) . '[decimal]"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . ' value="' . $this->view->escape($price[1]) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=decimal(number(this.value));"/> коп.';
        }
        
        return $xhtml;
    }    
}
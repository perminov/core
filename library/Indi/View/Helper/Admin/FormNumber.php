<?php
class Indi_View_Helper_Admin_FormNumber extends Indi_View_Helper_FormElement
{
    public function formNumber($name, $value = null, $attribs = null)
    {
		$field = $this->view->trail->getItem()->getFieldByAlias($name);
		$params = $field->getParams();
        if ($value === null) {
			if(!$this->view->row->id) {
				$value = $field->defaultValue;
			} else {
	            $value = $this->view->row->$name;
				if (!$value) $value = '0';
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
			$xhtml = '<input type="text"'
                   . ' name="' . $this->view->escape($name) . '"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . ' value="' . $this->view->escape($value) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: ' . ($params['maxlength']*10) . 'px; text-align: right;" maxlength="' . $params['maxlength'] . '" ' . ($params['readonly'] == 'true' ? ' readonly' : ' oninput="this.value=number(this.value);' . $field->javascript . '"  onkeydown="if(event.keyCode==38||event.keyCode==40){if(event.keyCode==38)this.value=parseInt(this.value)+1;else if(event.keyCode==40)this.value=parseInt(this.value)-1;' . $field->javascript . '}"') . ' autocomplete="off"/> ' . $params['measure'];
        }
        
        return $xhtml;
    }    
}
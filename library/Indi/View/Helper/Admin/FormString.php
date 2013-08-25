<?php
class Indi_View_Helper_Admin_FormString extends Indi_View_Helper_FormElement
{
    public function formString($name, $value = null, $attribs = null)
    {
        $field = $this->view->trail->getItem()->getFieldByAlias($name);
        $params = $field->getParams();

        if ($value === null) {
            $value = $this->view->row->$name;
			if(empty($this->view->row->id)) {
				$value = $field->defaultValue;
			}        }
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
                   . ($attribs['oninput'] ? '' : ' oninput="' . $this->view->trail->getItem()->getFieldByAlias($name)->javascript . '"')
                   . ($params['readonly'] == 'true' ? ' readonly="readonly"':'')
                   . ($params['maxlength'] ? 'style="width: ' . ($params['maxlength']*10) . 'px;" maxlength="' . $params['maxlength'] . '"' : '')
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: 100%;"/>';
        }
        
        return $xhtml;
    }    
}
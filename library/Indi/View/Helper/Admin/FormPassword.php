<?php
class Indi_View_Helper_FormPassword extends Indi_View_Helper_FormElement
{
    /**
     * Generates a 'password' element.
     * 
     * @access public
     * 
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * 
     * @param mixed $value The element value.
     * 
     * @param array $attribs Attributes for the element tag.
     * 
     * @return string The element XHTML.
     */
    public function formPassword($name = 'password', $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        
        if (!$value) {
            $value = $this->view->row->$name;
        }
        // build the element
        if ($disable) {
            // disabled
            $xhtml = $this->_hidden($name, $value) . 'xxxxxxxx';
        } else {
            // enabled
            $xhtml = '<input type="password"'
                   . ' name="' . $this->view->escape($name) . '"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . ' value="' . $this->view->escape($value) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' style="width: 100%;"/>';
        }
        
        return $xhtml;
    }
    
}

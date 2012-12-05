<?php
class Indi_View_Helper_Admin_FormTextarea extends Indi_View_Helper_FormElement
{
    /**
     * Generates a 'textarea' element.
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
    public function formTextarea($name, $value = null, $attribs = null)
    {
        if ($value === null) {
            $value = $this->view->row->$name;
        }        
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        
        // build the element
        if ($disable) {
        
            // disabled.
            $xhtml = $this->_hidden($name, $value)
                   . nl2br($this->view->escape($value));
            
        } else {
        
            // enabled.
            
            // first, make sure that there are 'rows' and 'cols' values
            // as required by the spec.  noted by Orjan Persson.
            if (empty($attribs['rows'])) {
                $attribs['rows'] = (int) $this->rows;
            }
            
            if (empty($attribs['cols'])) {
                $attribs['cols'] = (int) $this->cols;
            }
            
            // now build the element.
            $xhtml = '<textarea name="' . $this->view->escape($name) . '"'
                   . ' id="' . $this->view->escape($id) . '"'
                   . $this->_htmlAttribs($attribs) . ' style="width: 100%; height: 60px;">'
                   . $this->view->escape($value) . '</textarea>';
            
        }
        
        return $xhtml;
    }
}

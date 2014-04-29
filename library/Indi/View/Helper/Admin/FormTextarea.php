<?php
class Indi_View_Helper_Admin_FormTextarea
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
            $value = Indi::view()->row->$name;
			if ($name == 'title' && $value == 'No title') $value = '';
        }        

        // first, make sure that there are 'rows' and 'cols' values
        // as required by the spec.  noted by Orjan Persson.
        if (empty($attribs['rows'])) {
            $attribs['rows'] = (int) $this->rows;
        }

        if (empty($attribs['cols'])) {
            $attribs['cols'] = (int) $this->cols;
        }

        // now build the element.
        $xhtml = '<textarea name="' . Indi::view()->escape($name) . '"'
               . ' id="' . Indi::view()->escape($name) . '"'
               . ' style="width: 100%; height: 60px;">'
               . Indi::view()->escape($value) . '</textarea>';

        return $xhtml;
    }
}

<?php
class Indi_View_Helper_Admin_FormString {

    public function formString($name, $value = null, $attribs = null) {

        $field = Indi::trail()->model->fields($name);

        if ($value === null) {
            $value = Indi::view()->row->$name;
			if(empty(Indi::view()->row->id)) {
				$value = $field->defaultValue;
			}
        }
            // enabled
            $xhtml = '<input type="text"'
                   . ' name="' . $name . '"'
                   . ' id="' . $name . '"'
                   . ' value="' . Indi::view()->escape($value) . '"'
                   . ($attribs['oninput'] ? '' : ' oninput="' . Indi::trail()->model->fields($name)->javascript . '"')
                   . ($field->params['readonly'] == 'true' ? ' readonly="readonly"':'')
                   . ($field->params['maxlength'] ? 'style="width: ' . ($field->params['maxlength']*10) . 'px;" maxlength="' . $field->params['maxlength'] . '"' : '')
                   . ' style="width: 100%;"/>';
        
        return $xhtml;
    }    
}
<?php
class Indi_View_Helper_Admin_FormNumber
{
    public function formNumber($name, $value = null, $attribs = null)
    {
		$field = Indi::trail()->model->fields($name);

        if ($value === null) {
			if(!Indi::view()->row->id) {
				$value = $field->defaultValue;
			} else {
	            $value = Indi::view()->row->$name;
				if (!$value) $value = '0';
			}
		}

        // enabled
        $xhtml = '<input type="text"'
               . ' name="' . Indi::view()->escape($name) . '"'
               . ' id="' . Indi::view()->escape($name) . '"'
               . ' value="' . Indi::view()->escape($value) . '"'
               . ' style="width: ' . ($field->params['maxlength']*10) . 'px; text-align: right;" maxlength="' . $field->params['maxlength'] . '" ' . ($field->params['readonly'] == 'true' ? ' readonly' : ' oninput="this.value=number(this.value);' . $field->javascript . '"  onkeydown="if(event.keyCode==38||event.keyCode==40){if(event.keyCode==38)this.value=parseInt(this.value)+1;else if(event.keyCode==40)this.value=parseInt(this.value)-1;' . $field->javascript . '}"') . ' autocomplete="off"/> ' . $field->params['measure'];

        return $xhtml;
    }    
}
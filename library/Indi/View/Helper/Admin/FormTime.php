<?php
class Indi_View_Helper_Admin_FormTime
{
    public function formTime($name, $value = null, $attribs = null)
    {
        if ($value === null) {
			if(!Indi::view()->row->id) {
				$value = Indi::trail()->model->fields($name)->defaultValue;
			} else {
	            $value = Indi::view()->row->$name;
				if (!$value) $value = '00:00:00';
			}
		}
        
            // enabled
			$time = explode(':', $value);
			for ($i = 0; $i <= 2; $i++)	$time[$i] = strlen($time[$i]) == 1 ? $time[$i] . '0' : (strlen($time[$i]) == 0 ? '00' : $time[$i]);
			$xhtml = '<input type="text"'
                   . ' name="' . Indi::view()->escape($name) . '[hours]"'
                   . ' id="' . Indi::view()->escape($id) . '"'
                   . ' value="' . Indi::view()->escape($time[0]) . '"'
                   . ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=number(this.value)" /> часов ';

			$xhtml .= '<input type="text"'
                   . ' name="' . Indi::view()->escape($name) . '[minutes]"'
                   . ' id="' . Indi::view()->escape($id) . '"'
                   . ' value="' . Indi::view()->escape($time[1]) . '"'
                   . ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=decimal(number(this.value));"/> минут ';

		   $xhtml .= '<span style="display: none;"><input type="text"'
                   . ' name="' . Indi::view()->escape($name) . '[seconds]"'
                   . ' id="' . Indi::view()->escape($id) . '"'
                   . ' value="' . Indi::view()->escape($time[2]) . '"'
                   . ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=decimal(number(this.value));"/> секунд</span>';
        
        return $xhtml;
    }    
}
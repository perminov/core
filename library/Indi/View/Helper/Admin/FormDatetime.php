<?php
class Indi_View_Helper_Admin_FormDatetime extends Indi_View_Helper_FormElement
{
    public function formDatetime($name = 'datetime', $minimal = null, $value = null, $attribs = '')
    {
		$p = '/i/admin/';

        $value = $value ? $value : '0000-00-00 00:00:00';

		static $zIndex;
        
        $zIndex++;
        
        //by default, value is got from row object's value of $name field
		if($this->view->row->id) {
			$value = $value != '0000-00-00 00:00:00' ? $value : $this->view->row->$name;
		} else {
			$value = $this->view->trail->getItem()->getFieldByAlias($name)->defaultValue;
		}
        $value = $value ? $value : date('Y-m-d H:i:s');

        //minimal date available to select in calendar, 2006-01-01 by default
        $minimal = $minimal ? $minimal : '1930-01-01 12:00:00';
        
        // if current value earlier than minimal date, minimal date is to be set
        // equal to value
        $minimal = $minimal > $value ? $value : $minimal;
        $xhtml  = '<div style="position: relative; z-index: ' . (100 - $zIndex) . '">';
		$parts = explode(' ', $value);
		$minimal = explode(' ', $minimal);
        $xhtml .= '<input type="text" name="' . $name . '[date]" value="' . $parts[0] . '" style="width: 61px;" id="' . $name . 'Input"> ';
        $xhtml .= '<iframe id="' . $name . 'Calendar" name="' . $name . 'Calendar" src="/admin/auxillary/calendar/" frameborder="0" scrolling="no" style="display: none; width: 168px; height: 173px; position: absolute; z-index: 500;"></iframe>';
        $xhtml .= '<a href="javascript:void(0);" onclick="showCalendar(\'' . $name . '\', \'' . $minimal[0] . '\');" id="' . $name . 'CalendarIcon"><img src="' . $p . 'b_calendar.png" alt="Show calendar" width="16" height="19" border="0" style="vertical-align: top; margin-top: 1px; "></a>';

		$time = explode(':', $parts[1]);
		for ($i = 0; $i <= 2; $i++)	$time[$i] = strlen($time[$i]) == 1 ? $time[$i] . '0' : (strlen($time[$i]) == 0 ? '00' : $time[$i]);
		$xhtml .= '&nbsp; &nbsp;<input type="text"'
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

		$xhtml .= '</div>';
 	    return $xhtml;
    }
}
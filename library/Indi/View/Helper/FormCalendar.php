<?php
class Indi_View_Helper_FormCalendar extends Indi_View_Helper_Abstract
{
    public function formCalendar($name = 'date', $minimal = null, $value = null, $attribs = '')
    {
		$p = '/i/admin/';

		static $zIndex;
        
        $zIndex++;
        
        //by default, value is a current date
        $value = $value ? $value : date('Y-m-d');

        //minimal date available to select in calendar, 2006-01-01 by default
        $minimal = $minimal ? $minimal : '1930-01-01';
        
        // if current value earlier than minimal date, minimal date is to be set
        // equal to value
        $minimal = $minimal > $value ? $value : $minimal;
        $xhtml  = '<div style="position: relative; z-index: ' . (50 - $zIndex) . '; display: inline;">';
        $xhtml .= '<input type="text" name="' . $name . '" value="' . $value . '" style="width: 65px;" id="' . $name . 'Input" ' . $attribs . ' readonly="true"> ';
        $xhtml .= '<iframe id="' . $name . 'Calendar" name="' . $name . 'Calendar" src="/admin/auxillary/calendar/" frameborder="0" scrolling="no" style="display: none; width: 168px; height: 173px; position: absolute; z-index: 500;"></iframe>';
        $xhtml .= '<a href="javascript:void(0);" onclick="showCalendar(\'' . $name . '\', \'' . $minimal . '\');" id="' . $name . 'CalendarIcon"><img src="' . $p . 'b_calendar.png" alt="Show calendar" width="16" height="19" border="0" style="vertical-align: top; margin-top: 3px; "></a>';
        $xhtml .= '</div>';
        return $xhtml;
    }
}
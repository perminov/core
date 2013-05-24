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
			if ($value == '0000-00-00 00:00:00') $value = date('Y-m-d H:i:s');
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
        $xhtml .= '<input type="text" name="' . $name . '[date]" value="' . $parts[0] . '" style="width: 62px; margin-top: 1px;" id="' . $name . 'Input"> ';
		$xhtml .= '<a href="javascript:void(0);" onclick="$(\'#' . $name . 'CalendarRender\').toggle();" id="' . $name . 'CalendarIcon"><img src="' . $p . 'b_calendar.png" alt="Show calendar" width="14" height="18" border="0" style="vertical-align: top; margin-top: 1px; margin-left: -2px;"></a>';
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
		ob_start();?>
		<div id="<?=$name?>CalendarRender" style="position: absolute; display: none; margin-top: 1px;">
			<script>
				Ext.onReady(function() {
					Ext.Date.monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
					Ext.create('Ext.picker.Date', {
						dayNames: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
						monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
						renderTo: '<?=$name?>CalendarRender',
						width: 185,
						todayText: 'Сегодня',
						ariaTitle: 'Выбрать месяц и год',
						ariaTitleDateFormat: 'Y-m-d',
						longDayFormat: 'Y-m-d',
						nextText: 'Следующий месяц',
						prevText: 'Предыдущий месяц',
						todayTip: 'Выбрать сегодняшнюю дату',
						startDay: 1,
						handler: function(picker, date) {
							var y = date.getFullYear();
							var m = date.getMonth() + 1; if (m.toString().length < 2) m = '0' + m;
							var d = date.getDate(); if (d.toString().length < 2) d = '0' + d;
							var selectedDate = y + '-' + m + '-' + d;
							$('#<?=$name?>Input').val(selectedDate);
							$('#<?=$name?>CalendarRender').toggle();
						}
					});
				});
			</script>
		</div>
		<?$xhtml .= ob_get_clean();

			$xhtml .= '</div>';
		$xhtml .= '</div>';
 	    return $xhtml;
    }
}
<?php
class Indi_View_Helper_Admin_FormCalendar extends Indi_View_Helper_Abstract
{
    public function formCalendar($name = 'date', $minimal = null, $value = null, $attribs = '')
    {
		$p = '/i/admin/';

        $value = $value ? $value : '0000-00-00';

		static $zIndex;

        $zIndex++;

        //by default, value is got from row object's value of $name field
		if($this->view->row->id) {
			$value = $value != '0000-00-00' ? $value : $this->view->row->$name;
		} else {
			$value = $this->view->trail->getItem()->getFieldByAlias($name)->defaultValue;
		}
        $value = $value ? $value : date('Y-m-d');

        //minimal date available to select in calendar, 2006-01-01 by default
        $minimal = $minimal ? $minimal : '1930-01-01';

        // if current value earlier than minimal date, minimal date is to be set
        // equal to value
        $minimal = $minimal > $value ? $value : $minimal;
        $xhtml  = '<div style="position: relative; z-index: ' . (100 - $zIndex) . '" id="calendar' . $name . 'Div">';
        $xhtml .= '<input type="text" name="' . $name . '" value="' . $value . '" style="width: 62px; margin-top: 1px;" id="' . $name . '"> ';
		$xhtml .= '<a href="javascript:void(0);" onclick="$(\'#' . $name . 'CalendarRender\').toggle();" id="' . $name . 'CalendarIcon"><img src="' . $p . 'b_calendar.png" alt="Show calendar" width="14" height="18" border="0" style="vertical-align: top; margin-top: 1px; margin-left: -2px;"></a>';
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
							$('#<?=$name?>').val(selectedDate);
							$('#<?=$name?>CalendarRender').toggle();
						}
					});
				});
			</script>
		</div>
		<?$xhtml .= ob_get_clean();

        $xhtml .= '</div>';
		return $xhtml;
    }
}
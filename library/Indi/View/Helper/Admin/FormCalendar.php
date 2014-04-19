<?php
class Indi_View_Helper_Admin_FormCalendar extends Indi_View_Helper_Abstract
{
    public function formCalendar($name = 'date', $minimal = null, $value = null, $attribs = '')
    {
        $p = '/i/admin/';

        $value = $value ? $value : '0000-00-00';

		static $zIndex;

        $zIndex++;

        $field = Indi::trail()->model->fields($name);
        //by default, value is got from row object's value of $name field
		if ($this->view->row->$name != '0000-00-00') {
			$value = $value != '0000-00-00' ? $value : $this->view->row->$name;
		} else {
			$value = $field->defaultValue;
            Indi::$cmpTpl = $value; eval(Indi::$cmpRun); $value = Indi::$cmpOut;
            //if ($value == '0000-00-00') $value = date('Y-m-d');
		}
        $value = $value ? $value : date('Y-m-d');

        //minimal date available to select in calendar, 2006-01-01 by default
        $minimal = $minimal ? $minimal : '1930-01-01';

        // if current value earlier than minimal date, minimal date is to be set
        // equal to value
        $minimal = $minimal > $value ? $value : $minimal;
        if ($field->params['displayFormat']) {
            if ($value == '0000-00-00' && $field->params['displayFormat'] == 'd.m.Y') {
                $value = '00.00.0000';
            } else if ($value != '0000-00-00'){
                $value = date($field->params['displayFormat'], strtotime($value));
                if ($value == '30.11.-0001') $value = '00.00.0000';
            }
        }
        $xhtml  = '<div style="position: relative; ' . $shift .  '" id="calendar' . $name . 'Div" class="calendar-div i-element-calendar-wrapper">';
        $xhtml .= '<input type="text" name="' . $name . '" value="' . $value . '" style="width: 62px; margin-top: 1px;" id="' . $name . '" class="calendar-input"> ';
		$xhtml .= '<a href="javascript:void(0);" onclick="$(\'#' . $name . 'CalendarRender\').toggle();" id="' . $name . 'CalendarIcon" class="calendar-trigger"><img src="' . $p . 'b_calendar.png" alt="Show calendar" width="14" height="18" border="0" style="vertical-align: top; margin-top: 1px; margin-left: -2px;"></a>';
		ob_start();?>
		<div id="<?=$name?>CalendarRender" style="position: absolute; display: none; margin-top: 1px; z-index: <?=(100 - $zIndex)?>;">
			<script>
                $('#<?=$name?>').change(function(){
                    <?=$field->javascript?>
                });
				Ext.onReady(function() {
					Ext.create('Ext.picker.Date', {
						renderTo: '<?=$name?>CalendarRender',
                        id: '<?=$name?>Calendar',
						width: 185,
                        ariaTitleDateFormat: '<?=$field->params['displayFormat']?>',
                        longDayFormat: '<?=$field->params['displayFormat']?>',
                        format: '<?=$field->params['displayFormat']?>',
                        value: Ext.Date.parse('<?=$value?>', '<?=$field->params['displayFormat']?>'),
						handler: function(picker, date) {
                            var selectedDate = Ext.Date.format(date, '<?=$field->params['displayFormat']?>');
                            $('#<?=$name?>').val(selectedDate);
							$('#<?=$name?>CalendarRender').toggle();
                            $('#<?=$name?>').change();
                        },
                        listeners: {
                            render: function(cal) {
                                $('body').bind('click', function(e) {
                                    if($(e.target).closest('#'+cal.id).length == 0 &&
                                        !($(e.srcElement || e.target).hasClass('calendar-trigger') || $(e.srcElement || e.target).parent().hasClass('calendar-trigger')) &&
                                        $('#'+cal.id+'Render').css('display') != 'none') {
                                        $('#'+cal.id+'Render').hide();
                                    }
                                });
                            }
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
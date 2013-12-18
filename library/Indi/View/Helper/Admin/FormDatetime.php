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
        $field = $this->view->trail->getItem()->getFieldByAlias($name);
		if($this->view->row->id) {
			$value = $value != '0000-00-00 00:00:00' ? $value : $this->view->row->$name;
		} else {
			$value = $field->defaultValue;
            Indi::$cmpTpl = $value; eval(Indi::$cmpRun); $value = Indi::$cmpOut;
            //if ($value == '0000-00-00 00:00:00') $value = date('Y-m-d H:i:s');
		}
        $value = $value ? $value : date('Y-m-d H:i:s');

        //minimal date available to select in calendar, 2006-01-01 by default
        //$minimal = $minimal ? $minimal : '1930-01-01 12:00:00';
        // if current value earlier than minimal date, minimal date is to be set
        // equal to value
        //$minimal = $minimal > $value ? $value : $minimal;
        if (preg_match('/Firefox/', $_SERVER['HTTP_USER_AGENT'])) $shift = 'top: -1px;';
        $xhtml  = '<div style="position: relative; ' . $shift .  '">';
		$parts = explode(' ', $value);

        $params = $field->getParams();
        if ($params['displayDateFormat']) {
            if ($parts[0] == '0000-00-00') {
                if ($params['displayDateFormat'] == 'd.m.Y') {
                    $parts[0] = '00.00.0000';
                } else if (!$params['displayDateFormat'] || $params['displayDateFormat'] == 'Y-m-d'){
                    $parts[0] = '0000-00-00';
                }
            } else {
                $parts[0] = date($params['displayDateFormat'], strtotime($parts[0]));
            }
        }

        $xhtml .= '<input type="text" name="' . $name . '[date]" value="' . $parts[0] . '" style="width: 62px; margin-top: 1px;" id="' . $name . 'Input"> ';
		$xhtml .= '<a href="javascript:void(0);" onclick="$(\'#' . $name . 'CalendarRender\').toggle();" id="' . $name . 'CalendarIcon" class="calendar-trigger"><img src="' . $p . 'b_calendar.png" alt="Show calendar" width="14" height="18" border="0" style="vertical-align: top; margin-top: 1px; margin-left: -2px;"></a>';
		$time = explode(':', $parts[1]);
		for ($i = 0; $i <= 2; $i++)	$time[$i] = strlen($time[$i]) == 1 ? $time[$i] . '0' : (strlen($time[$i]) == 0 ? '00' : $time[$i]);
		$xhtml .= '&nbsp; &nbsp;<input type="text"'
				. ' name="' . $this->view->escape($name) . '[hours]"'
				. ' id="' . $this->view->escape($id) . '"'
				. ' value="' . $this->view->escape($time[0]) . '"'
				. $this->_htmlAttribs($attribs)
				. ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=number(this.value)" /> ' . FORM_DATETIME_HOURS . ' ';

		$xhtml .= '<input type="text"'
				. ' name="' . $this->view->escape($name) . '[minutes]"'
				. ' id="' . $this->view->escape($id) . '"'
				. ' value="' . $this->view->escape($time[1]) . '"'
				. $this->_htmlAttribs($attribs)
				. ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=decimal(number(this.value));"/> ' . FORM_DATETIME_MINUTES . ' ';

		$xhtml .= '<span style="display: none;"><input type="text"'
				. ' name="' . $this->view->escape($name) . '[seconds]"'
				. ' id="' . $this->view->escape($id) . '"'
				. ' value="' . $this->view->escape($time[2]) . '"'
				. $this->_htmlAttribs($attribs)
				. ' style="width: 18px; text-align: right;" maxlength="2" onchange="this.value=decimal(number(this.value));"/> ' . FORM_DATETIME_SECONDS . '</span>';
		ob_start();?>
		<div id="<?=$name?>CalendarRender" style="position: absolute; display: none; margin-top: 1px; z-index: <?=(100 - $zIndex)?>;">
			<script>
				Ext.onReady(function() {
					Ext.create('Ext.picker.Date', {
						renderTo: '<?=$name?>CalendarRender',
                        id: '<?=$name?>Calendar',
                        width: 185,
						ariaTitleDateFormat: '<?=$params['displayDateFormat']?>',
						longDayFormat: '<?=$params['displayDateFormat']?>',
                        format: '<?=$params['displayDateFormat']?>',
                        value: Ext.Date.parse('<?=$parts[0]?>', '<?=$params['displayDateFormat']?>'),
						handler: function(picker, date) {
                            var selectedDate = Ext.Date.format(date, '<?=$params['displayDateFormat']?>');
                            $('#<?=$name?>Input').val(selectedDate);
							$('#<?=$name?>CalendarRender').toggle();
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
		$xhtml .= '</div>';
 	    return $xhtml;
    }
}
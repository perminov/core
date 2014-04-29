<?php
class Indi_View_Helper_Admin_FormRadios extends Indi_View_Helper_Abstract
{
    public function formRadios($name = null, $texts = null, $values = null, $value = null, $meta = true, $attrib = '', $add = null)
    {
        // if name not set name will be set to 'toggle'
        $name = $name ? $name : 'toggle';
        
        // current value by default is got from current row
        $value = $value ? $value : Indi::view()->row->$name;
        // texts passed to helper should be comma separated
        // if texts are not set default values are 'y,n'
        $texts = $texts ? explode(',', $texts) : array ('Yes','No');

        // values passed to helper by parameter should be comma separated
        $values = $values ? explode(',', $values) : null;

        // if values are not set by parameter, and column type is 'enum' then
        // default values are got from metadata of this column
        
        // else default values are 'y','n'
        $values = $values ? $values : array('y', 'n');

        $xhtml = '<field><input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value .'">';
        for ($i = 0; $i < count($values); $i++) {
			if ($values[$i] == $value) {
				$checked = ' checked="checked"';
				$checkedIndex = $i;
			} else {
				$checked = '';
			}

			$id = ' id="' . $name . ucwords(str_replace('.', '', $values[$i])) .'"';
            if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $values[$i], $matches)) {
                $color = '<span class="i-color-box" style="background: #' . $matches[1] . ';"></span> ';
            } else {
                $color = '';
            }

            $label = '<label id="' . $name . ucwords(str_replace('.', '', $values[$i])) . 'Label">' . $color . $texts[$i] . '</label>&nbsp;';
            $xhtml .= '<span class="radio' . ($checked?' checked':'') . '" val="' . $values[$i] . '" type="radio" ' . $checked . $id . (is_array($attrib) ? $attrib[$i] : $attrib) .  ' ' . $add . '>' . $label . '</span>';
		}
		$xhtml .= '<script>
		$("span.radio[id^='.$name.']").click(function(){
		    if ($(this).hasClass("disabled") == false) {
                $(this).parent().find("input").val($(this).attr("val"));
                $(this).parent().find("span.radio").removeAttr("checked").removeClass("checked");
                $(this).attr("checked", "checked").addClass("checked");
		    }
		});
		</script>';
		$xhtml .= '<script>$(function(){ $("#' . $name . ucwords(str_replace('.','',$values[$checkedIndex])) . '").click()})</script>';
		$xhtml .= '</field>';
        return $xhtml;
    }
}

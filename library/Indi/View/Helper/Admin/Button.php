<?php
class Indi_View_Helper_Admin_Button extends Indi_View_Helper_Abstract
{
    public function button($title = 'Back', $action = 'window.history.go(-1)')
    {
        $xhtml = '
<!-- button -->
<table cellspacing="0" cellpadding="0" border="0" height="22" style="cursor: hand;" id="' . $title . '"><tr>
<td width="20" background="/i/admin/but_bg.gif"><img src="/i/admin/but_left.gif" width="9" height="22" border="0"></td>
<td background="/i/admin/but_bg.gif" class="but_text" onclick="' . str_replace('"', '\'', $action) . '">' . $title . '</td>
<td width="20" background="/i/admin/but_bg.gif" align="right"><img src="/i/admin/but_right.gif" width="9" height="22" border="0"></td>
</tr></table>
<!-- /button -->
        ';
        return $xhtml;
    }
}

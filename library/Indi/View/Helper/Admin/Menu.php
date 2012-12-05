<?php
class Indi_View_Helper_Admin_Menu extends Indi_View_Helper_Abstract
{
    public function menu()
    {
        if ($this->view->menu->count()) {
            $xhtml = '
<table class="menu" cellspacing="0" cellpadding="0" width="161" border="0">
<!-- menu ico + dir-->
<tbody>';
//, $this->view->reset
            foreach ($this->view->menu as $section) {
                if ($section->sectionId) {
					if ($this->view->reset) {
                        $this->view->cycle(array('#e6e6e6','#cccccc'), 'color')->rewind();
                        $this->view->cycle(array('1','2'), 'section')->rewind();
					}
                    $color = $this->view->cycle(array('#e6e6e6','#cccccc'), 'color')->next()->toString();
                    $index = $this->view->cycle(array('1','2'), 'section')->next()->toString();
                            $xhtml .= '
            <tr height="19">
                <td></td>
                <td bgcolor="' . $color . '"><img height="19" src="/i/admin/menu_lf' . $index . '.gif" width="8"></td>
                <td bgcolor="' . $color . '"><a href="/' . $this->view->module . '/' . $section->alias . '/">' . $section->title . '</a></td>
            </tr>';
                    $this->view->assign('reset', false);
                } else {
                    $xhtml .= '                    
        <tr height="30">
            <td class="menu_ico" colspan="2"><img height="21" alt="' . 
                    $this->view->escape($section->title) .  
                    '" src="/i/admin/menu_item' . $this->view->cycle(array('1','2','3','4'), 'group')->next()->toString() . '.gif" width="21" border="0"></td>
            <td class="menu_dir">' . $this->view->escape($section->title) . '</td>
        </tr>';
                    $this->view->assign('reset', true);
                }
            }
            $xhtml .= '
    <tr height="20">
        <td width="15"><img height="0" src="/i/admin/spacer.gif" width="15"></td>
        <td width="26"><img height="0" src="/i/admin/spacer.gif" width="26"></td>
        <td width="120"><img height="0" src="/i/admin/spacer.gif" width="120"></td>
    </tr>
</tbody>
</table>';
        }
        return $xhtml;        
    }
}
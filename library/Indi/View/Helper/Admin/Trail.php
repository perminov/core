<?php
class Indi_View_Helper_Admin_Trail extends Indi_View_Helper_Abstract
{
    public function trail($asItems = false) {
	$xhtml = '
<table width="100%" style="margin-top: -17px;" id="trail">
    <tr>
        <td colspan="3">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" height="19" class="menu">
                <tr>
                    <td width="20" bgcolor="#ffcc99" valign="bottom"><img src="/i/admin/cont_lf1.gif" width="8" height="19" valign="bottom" style="vertical-align:bottom;"></td>
                	<td bgcolor="#ffcc99">
	    		<b>You are here :</b>&nbsp;&nbsp;';
        $items = $this->view->trail->items;
        $count = $this->view->trail->count();
        $trail[] = $count ? '<a href="/' . $this->view->module . '/">Menu</a>' : '<b>Menu</b>';
        foreach ($items as $i=>$item) {
            $href1 = '/' . $this->view->module . '/';
            if ($item->section->sectionId) {
                if ($i == $count - 1) {
                    if ($item->action->alias != 'index') {
                        $href2 = $item->section->alias . '/';
                        if ($items[$i-1]->row->id) {
				$href2 .= 'index/id/' . $items[$i-1]->row->id . '/';
                        }
                        $trail[] = '<a href="' . $href1 . $href2 . '">' . $item->section->title . '</a>';
                        if ($item->row->id) {
                            $trail[] = '<i style="cursor: default;">' . iconv('WINDOWS-1251', 'UTF-8', substr(iconv('UTF-8', 'WINDOWS-1251', stripcslashes($item->row->getTitle())),0, 50)) . '</i>';
                            $trail[] = '<b>' . $item->action->title .'</b>';
                        } else if ($item->action->alias == 'form') {
                            $trail[] = '<b>Add</b>';
                        } else if ($item->action->rowRequired == 'n') {
                            $trail[] = '<b>' . $item->action->title .'</b>';
						}
                    } else {
		        $trail[] = '<b>' . $item->section->title . '</b>';
                    }
                } else {
                    $href2 = $item->section->alias . '/';
                    if ($items[$i-1]->row->id) {
                        $href2 .= 'index/id/' . $items[$i-1]->row->id . '/';
                    }
                    $trail[] = '<a href="' . $href1 . $href2 . '">' . $item->section->title . '</a>';
                    if ($item->row->id) {
                        $trail[] = '<i style="cursor: default;">' . stripcslashes($item->row->getTitle()) . '</i>';
		    }
                }
            } else {
                $trail[] = '<a style="cursor: default">' . $item->section->title . '</a>';
            }
       }
    $xhtml .= count($trail) ? implode(' > ', $trail) : '';
    $xhtml .= ' 	</td>
	                <td width="20" bgcolor="#ffcc99"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>';
		if ($asItems) unset($trail[0]);
        return $asItems ? $trail : $xhtml;
    }
}
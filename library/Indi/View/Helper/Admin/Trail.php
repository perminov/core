<?php
class Indi_View_Helper_Admin_Trail extends Indi_View_Helper_Abstract
{
    public function trail($asItems = false) {
        $items = $this->view->trail->items;
        $count = $this->view->trail->count();
        foreach ($items as $i=>$item) {
            $href1 = '/' . $this->view->module . '/';
            if ($item->section->sectionId) {
                if ($i == $count - 1) {
                    if ($item->action->alias != 'index') {
                        $href2 = $item->section->alias . '/';
                        if ($items[$i-1]->row->id) {
							$href2 .= 'index/id/' . $items[$i-1]->row->id . '/';
                        }
                        $trail[] = '<a href="#" onclick="loadContent(\'' . $href1 . $href2 . '\');return false;">' . $item->section->title . '</a>';
                        if ($item->row->id) {
                            $trail[] = '<i style="cursor: default;">' . mb_substr($item->row->getTitle(),0, 50, 'utf-8') . '</i>';
                            $trail[] = '' . $item->action->title .'';
                        } else if ($item->action->alias == 'form') {
                            $trail[] = 'Создать';
                        } else if ($item->action->rowRequired == 'n') {
                            $trail[] = '' . $item->action->title .'';
						}
                    } else {
		        $trail[] = '' . $item->section->title . '';
                    }
                } else {
                    $href2 = $item->section->alias . '/';
                    if ($items[$i-1]->row->id) {
                        $href2 .= 'index/id/' . $items[$i-1]->row->id . '/';
                    }
                    $trail[] = '<a href="#" onclick="loadContent(\'' . $href1 . $href2 . '\');return false;">' . $item->section->title . '</a>';
                    if ($item->row->id) {
                        $trail[] = '<i style="cursor: default;">' . mb_substr($item->row->getTitle(), 0, 50, 'utf-8') . '</i>';
		    }
                }
            } else {
                $trail[] = '<span style="cursor: default">' . $item->section->title . '</span>';
            }
       }
    $xhtml .= count($trail) ? implode(' &raquo; ', $trail) : '';
		if ($asItems) unset($trail[0]);
        return $asItems ? $trail : $xhtml;
    }
}
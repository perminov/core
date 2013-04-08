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
                        $trail[] = '<a href="#" onclick="loadContent(\'' . $href1 . $href2 . '\');return false;">' . str_replace('<br>', ' ', $item->section->title) . '</a>';
                        if ($item->row->id) {
                            $trail[] = '<i style="cursor: default;">' . str_replace('<br>', ' ',mb_substr($item->row->getTitle(),0, 50, 'utf-8')) . '</i>';
                            $trail[] = '' . $item->action->title .'';
                        } else if ($item->action->alias == 'form') {
                            $trail[] = 'Создать';
                        } else if ($item->action->rowRequired == 'n') {
                            $trail[] = '' . $item->action->title . '';
                        }
                    } else {
                        $trail[] = '' . $item->section->title . '';
                    }
                } else {
                    $href2 = $item->section->alias . '/';
                    if ($items[$i-1]->row->id) {
                        $href2 .= 'index/id/' . $items[$i-1]->row->id . '/';
                    }
                    $trail[] = '<a href="#" onclick="loadContent(\'' . $href1 . $href2 . '\');return false;">' . str_replace('<br>', ' ', $item->section->title) . '</a>';
                    if ($item->row->id) {
                        $title = str_replace('<br>', ' ', mb_substr($item->row->getTitle(), 0, 50, 'utf-8'));
                        $formAllowed = false; foreach ($item->actions as $allowed) {
                            if ($allowed->alias == 'form') {
                                $formAllowed = true;
                                break;
                            }
                        }

                        if ($formAllowed) {
                            $trail[] = '<a href="#" style="font-style: italic;" onclick="loadContent(\'' . $href1 . $item->section->alias . '/form/id/' . $item->row->id . '/\')">' . $title . '</a>';
                        } else {
                            $trail[] = '<i style="cursor: default;">' . $title . '</i>';
                        }

                    }
                }
            } else {
                $trail[] = '<span style="cursor: default">' . str_replace('<br>', ' ', $item->section->title) . '</span>';
            }
        }
        $xhtml = '';
        for ($i = 0; $i < count($trail); $i++) {
            $xhtml .= $trail[$i];
            if ($i < count($trail) - 1) {
                if ($i == 0) {
                    $xhtml .=  ' &raquo; ';
                } else {
                    $xhtml .=  ' <span id="trail-item-' . $i . '">&raquo;</span> ';
                }
            }
        }
        //$xhtml = count($trail) ? implode(' &raquo; ', $trail) : '';
        if ($asItems) unset($trail[0]);
        return $asItems ? $trail : $xhtml;
    }
}
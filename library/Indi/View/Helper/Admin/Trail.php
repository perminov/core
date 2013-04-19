<?php
class Indi_View_Helper_Admin_Trail extends Indi_View_Helper_Abstract
{
    public function trail($asItems = false) {
        $items = $this->view->trail->items;
        $count = $this->view->trail->count();
        foreach ($items as $i=>$item) {
            $href1 = $_SERVER['STD'] . ($GLOBALS['cmsOnlyMode'] ? '': '/' . $this->view->module) . '/';
            if ($item->section->sectionId) {
                if ($i - 1) {
                    $s = '<div style="display: none;" class="trail-siblings" id="trail-item-' . $i . '-sections">';
                    $s.= '<ul>';
                    foreach ($items[$i-1]->sections as $sibling) if ($sibling->id != $item->section->id){
                        $s.= '<a href="#" onclick="loadContent(\'' . $href1 . $sibling->alias . '/index/id/' . $items[$i-1]->row->id . '/\')"><li>&raquo; ' . $sibling->title . '</li></a>';
                    }
                    $s.= '</ul>';
                    $s.= '</div>';
                    $itemIndex = $i;
                } else {
                    $s = '';
                    $itemIndex = 0;
                }
                if ($i == $count - 1) {
                    if ($item->action->alias != 'index') {
                        $href2 = $item->section->alias . '/';
                        if ($items[$i-1]->row->id) {
                            $href2 .= 'index/id/' . $items[$i-1]->row->id . '/';
                        }
                        $trail[] = $s. '<a href="#" class="trail-item-section"' . ($itemIndex?' item-index="' . $itemIndex . '"':'') . ' onclick="loadContent(\'' . $href1 . $href2 . '\');return false;">' . str_replace('<br>', ' ', $item->section->title) . '</a>';
                        if ($item->row->id) {
                            $title = str_replace('<br>', ' ',mb_substr(preg_replace('/[\n\r]/', '',$item->row->getTitle()),0, 50, 'utf-8'));
                            $trail[] = '<i style="cursor: default;">' . $title . '</i>';
                            $trail[] = '' . $item->action->title .'';
                        } else if ($item->action->alias == 'form') {
                            $trail[] = 'Создать';
                        } else if ($item->action->rowRequired == 'n') {
                            $trail[] = '' . $item->action->title . '';
                        }
                    } else {
                        $trail[] = $s. '<span class="trail-item-section"' . ($itemIndex?' item-index="' . $itemIndex . '"':'') . '>' . $item->section->title . '</span>';
                    }
                } else {
                    $href2 = $item->section->alias . '/';
                    if ($items[$i-1]->row->id) {
                        $href2 .= 'index/id/' . $items[$i-1]->row->id . '/';
                    }
                    $trail[] = $s . '<a class="trail-item-section"' . ($itemIndex?' item-index="' . $itemIndex . '"':'') . ' href="#" onclick="loadContent(\'' . $href1 . $href2 . '\');return false;">' . str_replace('<br>', ' ', $item->section->title) . '</a>';
                    if ($item->row->id) {
                        $title = str_replace('<br>', ' ',mb_substr(preg_replace('/[\n\r]/', '',$item->row->getTitle()),0, 50, 'utf-8'));
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
                $trail[] = '<span style="cursor: default" id="trail-item-' . $i . '-section">' . str_replace('<br>', ' ', $item->section->title) . '</span>';
            }
        }
        $xhtml = '';
        for ($i = 0; $i < count($trail); $i++) {
            $xhtml .= $trail[$i];
            if ($i < count($trail) - 1) {
                if ($i == 0) {
                    $xhtml .=  ' &raquo; ';
                } else {
                    $xhtml .=  ' <span>&raquo;</span> ';
                }
            }
        }
        //$xhtml = count($trail) ? implode(' &raquo; ', $trail) : '';
        if ($asItems) unset($trail[0]);
        return $asItems ? $trail : $xhtml;
    }
}
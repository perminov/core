<?php
class Indi_View_Helper_Admin_GridFilters extends Indi_View_Helper_Abstract{
    public function gridFilters(){
        if ($this->view->trail->getItem()->filters->count()) {
        ob_start();?>
    {
        xtype: 'toolbar',
        dock: 'top',
        padding: '1 3 5 5',
        id: 'search-toolbar',
        items: [{
        xtype:'fieldset',
        title: 'Фильтры',
        width: '100%',
        columnWidth: '100%',
        padding: '0 4 1 5',
        layout: 'column',
        defaults: {
            padding: '0 5 4 0',
            margin: '-1 0 0 0'
        },
        items: [<?foreach($this->view->trail->getItem()->filters as $filter){?>
            <?if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'check' || $filter->foreign['fieldId']->relation) {?>
                <?$combo = $filter->combo();?>
                {
                    cls: 'filter-field',
                    margin: 0,
                    items:[{
                        html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$filter->alt ? $filter->alt : $filter->foreign['fieldId']->title?></label>',
                        cls: 'filter-field-label x-form-item',
                        margin: '0 4 0 0'
                    },{
                        xtype: 'combobox',
                        valueField: 'id',
                        displayField: 'title',
                        value: '%',
//                      multiSelect: true,
                        cls: 'subsection-select',
                        typeAhead: false,
                        editable: false,
                        width: <?=$combo['width']?>,
                        id: 'filter-<?=$filter->foreign['fieldId']->alias?>',
                        margin: 0,
                        store: {
                            fields: ['id', 'title'],
                            data: <?=$combo['store']?>
                        },
                        listeners: {
                            change: filterChange
                        }
                    }]
                },
            <?} else if (in_array($filter->foreign['fieldId']->foreign['elementId']['alias'], array('string', 'textarea'))) {?>
                {
                    cls: 'filter-field',
                    margin: 0,
                    items:[{
                        html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$filter->alt ? $filter->alt : $filter->foreign['fieldId']->title?></label>',
                        cls: 'filter-field-label x-form-item',
                        margin: '0 4 0 0'
                    },{
                        xtype: 'textfield',
                        id: 'filter-<?=$filter->foreign['fieldId']->alias?>',
                        height: 19,
                        width: 80,
                        margin: 0,
                        cls: 'fast-search-keyword',
                        listeners: {
                            change: filterChange
                        }
                    }]
                },
            <?} else if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'number') {?>
                {
                cls: 'filter-field',
                margin: 0,
                items:[                {
                    html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$filter->alt ? $filter->alt : $filter->foreign['fieldId']->title?> от</label>',
                    cls: 'filter-field-label x-form-item',
                    margin: '0 4 0 0'
                },{
                    xtype: 'numberfield',
                    id: 'filter-<?=$filter->foreign['fieldId']->alias?>-gte',
                    height: 19,
                    width: 60,
                    margin: '0 0 0 0',
                    cls: 'fast-search-keyword',
                    minValue: 0,
                    listeners: {
                        change: filterChange
                    }
                },                {
                    html: '<label style="position: relative; top: -1px;">до</label>',
                    cls: 'filter-field-label x-form-item',
                    margin: '0 4 0 4'
                },{
                    xtype: 'numberfield',
                    id: 'filter-<?=$filter->foreign['fieldId']->alias?>-lte',
                    height: 19,
                    width: 60,
                    margin: '0 4 0 0',
                    cls: 'fast-search-keyword',
                    minValue: 0,
                    listeners: {
                        change: filterChange
                    }
                }]},

                    <?}?>
        <?}?>]
    }]
    },<?
            return ob_get_clean();
        } else {
            return '';
        }
    }
}
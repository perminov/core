<?php
class Indi_View_Helper_Admin_GridFilters extends Indi_View_Helper_Abstract{
    public function gridFilters(){
        if ($this->view->trail->getItem()->filters->count()) {
            $fieldsetMarginBottom = preg_match('/Opera/', $_SERVER['HTTP_USER_AGENT']) ? 2 : 1;
            ob_start();
            ?>
    {
        xtype: 'toolbar',
        dock: 'top',
        padding: '1 3 5 5',
        id: 'search-toolbar',
        items: [{
            padding: '0 4 1 5',
            margin: '0 2 <?=$fieldsetMarginBottom?> 0',
            xtype:'fieldset',
            title: '<?=GRID_FILTER?>',
            width: '100%',
            columnWidth: '100%',
            layout: 'column',
            defaults: {
                padding: '0 5 4 0',
                margin: '-1 0 0 0'
            },
            listeners: {
                afterrender: function(obj, width, height, eOpts){
                    <?foreach($this->view->trail->getItem()->filters as $filter){?>
                        <?if ($filter->defaultValue) {
                            $filter->defaultValue = Indi::cmp($filter->defaultValue);
                            if (in_array($filter->foreign['fieldId']->getForeignRowByForeignKey('elementId')->alias, array('number', 'calendar', 'datetime'))) {?>
                                <?$filter->defaultValue = json_decode(str_replace('\'','"',$filter->defaultValue), true)?>
                                <?if($filter->defaultValue['gte']){?>
                                    Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>-gte').noReload = true;
                                    Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>-gte').setValue(<?=json_encode($filter->defaultValue['gte'])?>);
                                    Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>-gte').noReload = false;
                                <?}?>
                                <?if($filter->defaultValue['lte']){?>
                                    Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>-lte').noReload = true;
                                    Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>-lte').setValue(<?=json_encode($filter->defaultValue['lte'])?>);
                                    Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>-lte').noReload = false;
                                <?}?>
                            <?} else {?>
                                Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>').noReload = true;
                                <?if ($filter->foreign['fieldId']->storeRelationAbility == 'many') {
                                    $value = explode(',', $filter->defaultValue);
                                } else {
                                    $value = $filter->defaultValue;
                                }?>
                                Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>').setValue(<?=json_encode($value)?>);
                                Ext.getCmp('filter-<?=$filter->foreign['fieldId']->alias?>').noReload = false;
                            <?}?>
                        <?}?>
                    <?}?>
                }
            },
            items: [<?foreach($this->view->trail->getItem()->filters as $filter){?>
                <?if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'check') {?>
                    <?$combo = $filter->combo();?>
                    <?$label = $filter->alt ? $filter->alt : $filter->foreign['fieldId']->title;?>
                    <?$labelWidth = mb_strlen($label, 'utf-8') * 7 + 10?>
                    <?$totalWidth = $this->view->section->alias == 'entities' ? 350 : $labelWidth + $combo['width'];?>
                    <?$padding = preg_match('/Firefox/', $_SERVER['HTTP_USER_AGENT']) ? 1 : 2?>
                    {
                        cls: 'filter-field',
                        margin: 0,
                        width: <?=$totalWidth?>,
                        items:[{
                            html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$label?></label>',
                            cls: 'filter-field-label x-form-item',
                            margin: '0 4 0 0'
                        },{
                            xtype: 'combobox',
                            valueField: 'id',
                            displayField: 'title',
                            value: '%',
                            cls: 'subsection-select',
                            typeAhead: true,
                            editable: true,
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
                <?} else if ($filter->foreign['fieldId']->relation) {?>
                    <?$label = $filter->alt ? $filter->alt : $filter->foreign['fieldId']->title;?>
                    {
                        cls: 'filter-field',
                        margin: 0,
                        id: 'filter-<?=$filter->foreign['fieldId']->alias?>-item',
                        items:[{
                            id: 'filter-<?=$filter->foreign['fieldId']->alias?>-label',
                            html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$label?></label>',
                            cls: 'filter-field-label x-form-item',
                            margin: '0 4 0 0'
                        },{
                            id: 'filter-<?=$filter->foreign['fieldId']->alias?>',
                            contentEl: 'filter-<?=$filter->foreign['fieldId']->alias?>-combo',
                            getValue: function(){
                                var me = this;
                                var hidden = $(me.el.dom).find('#'+me.id.split('-')[1]);
                                if (hidden.parent().hasClass('i-combo-single')) {
                                    return hidden.val() == '0' ? '' : hidden.val();
                                } else if (hidden.parent().hasClass('i-combo-multiple')) {
                                    return hidden.val().split(',');
                                }
                            },
                            setValue: function(value){
                            },
                            listeners: {
                                render: function(){
                                    var me = this, name = me.id.split('-')[1];
                                    var width = parseInt($('#filter-'+name+'-combo').css('width'));
                                    var diff = width - me.getWidth();
                                    me.setWidth(width);
                                    Ext.getCmp('filter-'+name+'-item').setWidth(Ext.getCmp('filter-'+name+'-item').getWidth() + diff);
                                }
                            }
                        }]
                    },
                <?} else if (in_array($filter->foreign['fieldId']->foreign['elementId']['alias'], array('string', 'textarea, html'))) {?>
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
                        html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$filter->alt ? $filter->alt : $filter->foreign['fieldId']->title?> <?=GRID_FILTER_NUMBER_FROM?></label>',
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
                    },{
                        html: '<label style="position: relative; top: -1px;"><?=GRID_FILTER_NUMBER_TO?></label>',
                        cls: 'filter-field-label x-form-item',
                        margin: '0 4 0 4'
                    },{
                        xtype: 'numberfield',
                        id: 'filter-<?=$filter->foreign['fieldId']->alias?>-lte',
                        height: 19,
                        width: 60,
                        margin: '0 0 0 0',
                        cls: 'fast-search-keyword',
                        minValue: 0,
                        listeners: {
                            change: filterChange
                        }
                    }]},
                <?} else if (in_array($filter->foreign['fieldId']->foreign['elementId']['alias'], array('calendar','datetime'))) {?>
                    <?$params = $filter->foreign['fieldId']->getParams()?>
                    {
                    cls: 'filter-field',
                    margin: 0,
                    items:[                {
                        html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$filter->alt ? $filter->alt : $filter->foreign['fieldId']->title?> <?=GRID_FILTER_DATE_FROM?></label>',
                        cls: 'filter-field-label x-form-item',
                        margin: '0 4 0 0'
                    },{
                        xtype: 'datefield',
                        id: 'filter-<?=$filter->foreign['fieldId']->alias?>-gte',
                        height: 19,
                        width: 80,
                        startDay: 1,
                        margin: '0 0 0 0',
                        <?if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'calendar') {?>
                            <?if ($params['displayFormat']){?>
                                format: '<?=$params['displayFormat']?>',
                                ariaTitleDateFormat: '<?=$params['displayFormat']?>',
                                longDayFormat: '<?=$params['displayFormat']?>',
                            <?}?>
                        <?} else if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'datetime') {?>
                            <?if ($params['displayDateFormat']){?>
                                format: '<?=$params['displayDateFormat']?>',
                                ariaTitleDateFormat: '<?=$params['displayDateFormat']?>',
                                longDayFormat: '<?=$params['displayDateFormat']?>',
                            <?}?>
                        <?}?>
                        cls: 'fast-search-keyword calendar',
                        validateOnChange: false,
                        listeners: {
                            change: filterChange
                        }
                    },{
                        html: '<label style="position: relative; top: -1px;"><?=GRID_FILTER_DATE_UNTIL?></label>',
                        cls: 'filter-field-label x-form-item',
                        margin: '0 4 0 4'
                    },{
                        xtype: 'datefield',
                        id: 'filter-<?=$filter->foreign['fieldId']->alias?>-lte',
                        height: 19,
                        width: 80,
                        startDay: 1,
                        validateOnChange: false,
                        margin: '0 0 0 0',
                        <?if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'calendar') {?>
                            <?if ($params['displayFormat']){?>
                                format: '<?=$params['displayFormat']?>',
                                ariaTitleDateFormat: '<?=$params['displayFormat']?>',
                                longDayFormat: '<?=$params['displayFormat']?>',
                            <?}?>
                        <?} else if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'datetime') {?>
                            <?if ($params['displayDateFormat']){?>
                                format: '<?=$params['displayDateFormat']?>',
                                ariaTitleDateFormat: '<?=$params['displayDateFormat']?>',
                                longDayFormat: '<?=$params['displayDateFormat']?>',
                            <?}?>
                        <?}?>
                cls: 'fast-search-keyword calendar',
                        listeners: {
                            change: filterChange
                        }
                    }]},
                <?} else if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'color') {?>
                    {
                    cls: 'filter-field',
                    margin: 0,
                    items:[                {
                        html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$filter->alt ? $filter->alt : $filter->foreign['fieldId']->title?></label>',
                        cls: 'filter-field-label x-form-item',
                        margin: '0 4 0 0'
                    },{
                        xtype: 'multislider',
                        values: [0, 360],
                        increment: 1,
                        minValue: 0,
                        maxValue: 360,
                        constrainThumbs: false,
                        id: 'filter-<?=$filter->foreign['fieldId']->alias?>',
                        width: 197,
                        margin: '1 0 0 0',
                        cls: 'color',
                        listeners: {
                            changecomplete: filterChange
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
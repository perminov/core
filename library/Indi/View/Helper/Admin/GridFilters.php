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
                        <?if($filter->foreign['fieldId']->storeRelationAbility == 'many'){?>
                        multiSelect: true,
                        <?}else{?>
                        value: '%',
                        <?}?>
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
                        <?if($combo['color']){?>
                        fieldSubTpl: [
                            '<div class="{hiddenDataCls}" role="presentation"></div>',
                            '<div id="{id}-div" style="position: absolute; width: <?=$combo['width']?$combo['width']-17:81?>px; padding-top: 2px; cursor: default;"  class="{fieldCls} {typeCls}">Неважно</div>',
                            '<input id="{id}" type="{type}" class="{fieldCls} {typeCls}" autocomplete="off"',
                            '<tpl if="size">size="{size}" </tpl>',
                            '<tpl if="tabIdx">tabIndex="{tabIdx}" </tpl>',
                            '/>',
                            '<div id="{cmpId}-triggerWrap" class="{triggerWrapCls}" role="presentation">',
                            '{triggerEl}',
                            '<div class="{clearCls}" role="presentation"></div>',
                            '</div>',
                            {
                                compiled: true,
                                disableFormats: true
                            }
                        ],
                        setRawValue: function(value) {
                            var me = this;
                            value = Ext.value(value, '');
                            me.rawValue = value;
                            if (me.el) {
                                $(me.el.dom).find('#'+me.inputId+'-div').html(value);
                                if (me.inputEl) {
                                    me.inputEl.dom.value = value;
                                }
                            }
                            return value;
                        },
                        listeners: {
                            change: filterChange,
                            focus: function(obj){
                                if (obj.el) {
                                    $(obj.el.dom).find('#'+obj.inputId+'-div').addClass('x-form-focus');
                                }
                            },
                            blur: function(obj){
                                if (obj.el) {
                                    $(obj.el.dom).find('#'+obj.inputId+'-div').removeClass('x-form-focus');
                                }
                            },
                            afterrender: function(obj) {
                                if (obj.el) {
                                    $(obj.el.dom).find('#'+obj.inputId+'-div').click(function(){
                                        if(obj.isExpanded) obj.collapse(); else obj.expand();
                                    });
                                }
                            }
                        }
                        <?} else {?>
                        listeners: {
                            change: filterChange
                        }
                    <?}?>
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
                },{
                    html: '<label style="position: relative; top: -1px;">до</label>',
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
            <?} else if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'calendar') {?>
                {
                cls: 'filter-field',
                margin: 0,
                items:[                {
                    html: '<label for="filter-<?=$filter->foreign['fieldId']->alias?>"><?=$filter->alt ? $filter->alt : $filter->foreign['fieldId']->title?> c</label>',
                    cls: 'filter-field-label x-form-item',
                    margin: '0 4 0 0'
                },{
                    xtype: 'datefield',
                    id: 'filter-<?=$filter->foreign['fieldId']->alias?>-gte',
                    height: 19,
                    width: 80,
                    startDay: 1,
                    margin: '0 0 0 0',
                    cls: 'fast-search-keyword calendar',
                    validateOnChange: false,
                    listeners: {
                        change: filterChange
                    }
                },{
                    html: '<label style="position: relative; top: -1px;">по</label>',
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
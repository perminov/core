Ext.define('Ext.Component', {
    extend: 'Ext.Component',
    // Merge Config Object-Properties With Superclass Ones
    mcopwso: [],
    mergeParent: function(config) {
        var initialMcopwso = this.mcopwso.join(',').split(',');
        var obj = this;
        while (obj.superclass) {
            if (obj.superclass.mcopwso && obj.superclass.mcopwso.length)
                for (var i = 0; i < obj.superclass.mcopwso.length; i++)
                    if (this.mcopwso.indexOf(obj.superclass.mcopwso[i]) == -1)
                        this.mcopwso.push(obj.superclass.mcopwso[i]);
            obj = obj.superclass;
        }
        obj = this;
        if (this.mcopwso.length)
            while (obj.superclass) {
                for (var i = 0; i < this.mcopwso.length; i++)
                    if (this[this.mcopwso[i]] && obj.superclass && obj.superclass[this.mcopwso[i]])
                        this[this.mcopwso[i]]
                            = Ext.merge(obj.superclass[this.mcopwso[i]], this[this.mcopwso[i]]);

                obj = obj.superclass;
            }

        for (var i = 0; i < initialMcopwso.length; i++) {
            if (typeof config == 'object' && typeof config[initialMcopwso[i]] == 'object') {
                this[initialMcopwso[i]] = Ext.merge(this[initialMcopwso[i]], config[initialMcopwso[i]]);
                delete config[initialMcopwso[i]];
            }
        }
    },
    constructor: function(config) {
        this.mergeParent(config);
        this.callParent(arguments);
    }
});
Ext.define('Indi.Controller.Action', {
    extend: 'Ext.Component',
    statics: {
        defaultModes: {
            index: 'rowset',
            form: 'row'
        },
        defaultViews: {
            index: 'grid',
            form: 'form'
        },

        launch: function(config) {
            if (!Indi.trail.item().action.mode) Indi.trail.item().action.mode
                = this.defaultModes[Indi.trail.item().action.alias];
            if (!Indi.trail.item().action.view) Indi.trail.item().action.view
                = this.defaultViews[Indi.trail.item().action.alias];
            return Ext.create('Indi.Controller.Action.'
                + Indi.ucfirst(Indi.trail.item().action.mode) + '.'
                + Indi.ucfirst(Indi.trail.item().action.view), config);
        }
    },
    shit: {
        hello2: 'asd'
    },
    shit1: {
        hello2: 'asd'
    },
    mcopwso: ['wrapperPanel'],
    wrapperPanel: {
        id: 'i-center-center-wrapper',
        renderTo: 'i-center-center-body',
        border: 0,
        height: '100%',
        closable: true,
        layout: 'fit'
    },

    initComponent: function() {

        // Create the main panel
        Ext.create('Ext.Panel', Ext.merge(this.wrapperPanel, {
            title: this.wrapperPanel.title || Indi.trail.item().section.title,
            //items: [instance['build'+type.charAt(0).toUpperCase() + type.slice(1)]()],
            listeners: {
                afterrender: function(){
                    //instance.store.load();
                }
            }
        }));
    }
});
Ext.define('Indi.Db.Table.Row', {
    extend: 'Ext.Component',
    foreign: function(key) {
        if (key == 'fieldId') {

        }
    }
})

Ext.define('Indi.Controller.Action.Rowset', {
    extend: 'Indi.Controller.Action',
    constructor: function() {
        this.wrapperPanel.tools = this.wrapperPanelTools();
        this.wrapperPanel.dockedItems = this.wrapperPanelDockedItems(),
        this.callParent();
    },

    wrapperPanelFilterToolbarItems: function() {

        console.log(Indi.trail.item().filters);
        return [];
        // Items array
        var items = [];

        // For each filter
        for (var i = 0; i < Indi.trail.item().filters.length; i++) {

            // Define a shortcut for filter field alias
            var alias = Indi.trail.item().filters[i].foreign('fieldId').alias;

            // Prepare the id for current filter component
            var filterCmpId = 'i-section-' + Indi.trail.item().section.alias + '-action-index-filter-' + alias;

            // If current filter is defined for 'string', 'textarea' or 'html' field
            if (['string', 'textarea', 'html']
                .indexOf(Indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias) != -1) {

                // Get the label
                var fieldLabel = Indi.trail.item().filters[i].alt ?
                    Indi.trail.item().filters[i].alt :
                    Indi.trail.item().filters[i].foreign('fieldId').title;

                // Append the extjs textfield component data object to filters stack
                items.push({
                    xtype: 'textfield',
                    id: filterCmpId,
                    fieldLabel: fieldLabel,
                    labelWidth: Indi.metrics.getWidth(fieldLabel),
                    labelSeparator: '',
                    hiddenName: alias,
                    width: 80 + Indi.metrics.getWidth(fieldLabel),
                    margin: 0,
                    listeners: {
                        change: function(cmp){
                            if (!cmp.noReload) instance.filterChange(cmp);
                        }
                    }
                });

                // Else if current filter is defined for 'number' field
            } else if (Indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias == 'number') {

                // Get the label
                var fieldLabel = (Indi.trail.item().filters[i].alt ?
                    Indi.trail.item().filters[i].alt :
                    Indi.trail.item().filters[i].foreign('fieldId').title) + ' ' +
                    Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM;

                // Append the extjs numberfield component data object to filters stack, for minimum value
                items.push({
                    xtype: 'numberfield',
                    id: filterCmpId + '-gte',
                    fieldLabel: fieldLabel,
                    labelWidth: Indi.metrics.getWidth(fieldLabel),
                    labelSeparator: '',
                    width: 50 + Indi.metrics.getWidth(fieldLabel),
                    margin: 0,
                    minValue: 0,
                    listeners: {
                        change: function(cmp){
                            if (!cmp.noReload) instance.filterChange(cmp);
                        }
                    }
                });

                // Append the extjs numberfield component data object to filters stack, for maximum value
                items.push({
                    xtype: 'numberfield',
                    id: filterCmpId + '-lte',
                    fieldLabel: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO,
                    labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
                    labelSeparator: '',
                    width: 50 + Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
                    margin: 0,
                    minValue: 0,
                    listeners: {
                        change: function(cmp){
                            if (!cmp.noReload) instance.filterChange(cmp);
                        }
                    }
                });

                // Else if current filter is defined for 'calendar' or 'datetime' field
            } else if (['calendar', 'datetime']
                .indexOf(Indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias) != -1) {

                // Get the format
                var dateFormat = Indi.trail.item().filters[i].foreign('fieldId').params['display' +
                    (Indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias == 'datetime' ?
                        'Date': '') + 'Format'] || 'Y-m-d';

                // Get the label for filter minimal value component
                var fieldLabel = (Indi.trail.item().filters[i].alt ?
                    Indi.trail.item().filters[i].alt :
                    Indi.trail.item().filters[i].foreign('fieldId').title) + ' ' +
                    Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM;

                // Prepare the data for extjs datefield component, for use as control for filter minimal value
                var datefieldFrom = {
                    xtype: 'datefield',
                    id: filterCmpId + '-gte',
                    fieldLabel: fieldLabel,
                    labelWidth: Indi.metrics.getWidth(fieldLabel),
                    labelSeparator: '',
                    width: 85 + Indi.metrics.getWidth(fieldLabel),
                    startDay: 1,
                    margin: 0,
                    validateOnChange: false,
                    listeners: {
                        change: function(cmp){
                            if (!cmp.noReload) instance.filterChange(cmp);
                        }
                    }
                };

                // Prepare the data for extjs datefield component, for use as control for filter maximal value
                var datefieldUntil = {
                    xtype: 'datefield',
                    id: filterCmpId + '-lte',
                    fieldLabel: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO,
                    labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
                    labelSeparator: '',
                    width: 85 + Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
                    startDay: 1,
                    margin: 0,
                    validateOnChange: false,
                    listeners: {
                        change: function(cmp){
                            if (!cmp.noReload) instance.filterChange(cmp);
                        }
                    }
                };

                // Append a number of format-related properties to the data objects
                datefieldFrom = $.extend(datefieldFrom, {
                    format: dateFormat,
                    ariaTitleDateFormat: dateFormat,
                    longDayFormat: dateFormat
                });
                datefieldUntil = $.extend(datefieldUntil, {
                    format: dateFormat,
                    ariaTitleDateFormat: dateFormat,
                    longDayFormat: dateFormat
                });

                // Append the extjs datefield components to filters stack, for minimum and maximum
                items.push(datefieldFrom, datefieldUntil);

                // Else if current filter is defined for 'color' field
            } else if (Indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias == 'color') {

                // Get the label
                var fieldLabel = (Indi.trail.item().filters[i].alt ?
                    Indi.trail.item().filters[i].alt :
                    Indi.trail.item().filters[i].foreign('fieldId').title);

                // Append the extjs multislider component data object to filters stack, as multislider will
                // be the approriate way to represent color hue range (0 to 360)
                items.push({
                    xtype: 'multislider',
                    id: filterCmpId,
                    fieldLabel: fieldLabel,
                    labelWidth: Indi.metrics.getWidth(fieldLabel),
                    labelSeparator: '',
                    labelClsExtra: 'i-multislider-color-label',
                    values: [0, 360],
                    increment: 1,
                    minValue: 0,
                    maxValue: 360,
                    constrainThumbs: false,
                    // Hue bg width + label width + labelPad + thumb-overlap * number-of-thumbs
                    width: 183 + Indi.metrics.getWidth(fieldLabel) + 5 + 7 * 2,
                    margin: '1 0 0 0',
                    cls: 'i-multislider-color',
                    listeners: {
                        changecomplete: function(cmp){
                            if (!cmp.noReload) instance.filterChange(cmp);
                        }
                    }
                });

                // Else if current filter is defined for 'combo' or 'check' field
            } else if (parseInt(Indi.trail.item().filters[i].foreign('fieldId').relation) || ['check', 'combo']
                .indexOf(Indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias) != -1) {

                // Get the label
                var fieldLabel = (Indi.trail.item().filters[i].alt ?
                    Indi.trail.item().filters[i].alt :
                    Indi.trail.item().filters[i].foreign('fieldId').title);

                // Push the special extjs component data object to represent needed filter. Component consists of
                // two hboxed components. First is extjs label component, and second - is setup to pick up
                // a custom DOM node as it's contentEl property. This DOM node is already prepared by non-extjs
                // solution, implemented in IndiEngine
                items.push({
                    padding: 0,
                    id: filterCmpId + '-item',
                    layout: 'hbox',
                    margin: '0 5 4 0',
                    cls: 'i-filter-combo',
                    border: 0,
                    items: [{
                        xtype: 'label',
                        id: filterCmpId + '-label',
                        text: fieldLabel,
                        forId: Indi.trail.item().filters[i].foreign('fieldId').alias + '-keyword',
                        margin: '0 5 0 0',
                        padding: '1 0 1 0',
                        cls: 'i-filter-combo-label',
                        border: 0
                    }, {
                        id: filterCmpId,
                        contentEl: filterCmpId + '-combo',
                        border: 0,
                        multiple: Indi.trail.item().filters[i].foreign('fieldId').storeRelationAbility == 'many',
                        boolean: Indi.trail.item().filters[i].foreign('fieldId').storeRelationAbility == 'none',
                        cls: 'i-filter-combo-component',
                        getValue: function(){
                            // Me
                            var me = this;

                            // If at this monent combo DOM node is not yet picked by 'me', we set 'hidden'
                            // variable directly as a DOM node outside 'me'
                            if (!me.el) var hidden = $('#' + me.contentEl + ' input[type="hidden"]');

                            // Else we set 'hidden' variable as a node within 'me'
                            else var hidden = $(me.el.dom).find('input[type="hidden"]');

                            // If combo is single-value
                            if (hidden.parents('i-combo-single')) {

                                // If combo value is 0, and combo is not used to represent BOOLEAN database
                                // column - the '' (empty string) will be returned, or actial combo value otherwise
                                return hidden.val() == '0' && hidden.attr('boolean') != 'true' ? '' : hidden.val();

                                // Else if combo is mltiple-value, an array of values (got by splitting combo value,
                                // by ',') will be returned
                            } else if (hidden.parents('i-combo-multiple')) {
                                return hidden.val().split(',');
                            }
                        },

                        // Here we define a setValue method, because it will be called at certain stage
                        // within instance.setFilterValues() execution, and if if won't define - error will
                        // occur. Also we need it to be working at 'clear-all-filters' function, represented by
                        // a special panel header tool
                        setValue: function(value){
                            if (value == '') {
                                Indi.combo.filter.clearCombo(
                                    $('#'+this.contentEl).find('input[type="hidden"]').attr('name')
                                );
                            }
                        },

                        // Provide the event handlers
                        listeners: {

                            // Provide a handler for 'render' event
                            render: function(){

                                // Here we provide an ability for width autoadjusting, in case if current combo has
                                // a satellite, and satelitte value was changed, so maximum option width may change,
                                // because options list was refreshed
                                var me = this;
                                var width = parseInt($('#'+me.contentEl).css('width'));
                                var diff = width + Ext.getCmp(me.id+'-label').getWidth() - Ext.getCmp(me.id + '-item').getWidth()
                                me.setWidth(width);
                                Ext.getCmp(me.id + '-item').setWidth(
                                    Ext.getCmp(me.id + '-item').getWidth() + diff + 5
                                );
                            }
                        }
                    }]
                });
            }
        }

        // Return filter toolbar items
        return items;
    },

    wrapperPanelTools: function() {

        // Declare tools array
        var tools = [];

        // We add the filter-reset tool only if there is at least one filter defined for current section
        if (Indi.trail.item().filters.length) {

            // Append tool data object to the 'tools' array
            tools.push({
                type: 'search',
                cls: 'i-tool-search-reset',
                handler: function(event, target, owner, tool){

                    // Prepare a prefix for filter component ids
                    var filterCmpIdPrefix = 'i-section-' + Indi.trail.item().section.alias + '-action-index-filter-';

                    // Setup a flag, what will
                    var atLeastOneFilterIsUsed = false;

                    // We define an array of functions, first within which will check if at least one filter is used
                    // and if so, second will do a store reload
                    var loopA = [function(cmp, control){
                        if (control == 'color') {
                            if (cmp.getValue().join() != '0,360') atLeastOneFilterIsUsed = true;
                        } else {
                            if ([null, ''].indexOf(cmp.getValue()) == -1) {
                                if (JSON.stringify(cmp.getValue()) != '[""]') {
                                    atLeastOneFilterIsUsed = true;
                                }
                            }
                        }
                    }, function(cmp, control){
                        if (control == 'color') {
                            cmp.setValue(0, 0, false);
                            cmp.setValue(1, 360, false);
                        } else {
                            cmp.setValue('');
                        }
                    }];

                    // We iterate throgh filter twice - for each function within loopA array
                    for (var l = 0; l < loopA.length; l++) {

                        // We prevent unsetting filters values if they are already empty
                        if (l == 1 && atLeastOneFilterIsUsed == false) break;

                        // For each filter
                        for (var i = 0; i < Indi.trail.item().filters.length; i++) {

                            // Define a shortcut for filter field alias
                            var alias =  Indi.trail.item().filters[i].foreign('fieldId').alias;

                            // Shortcut for control element, assigned to filter field
                            var control = Indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias;

                            // If current filter is a range-filter, we reset values for two filter components, that
                            // are representing min and max values
                            if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                                // Range filters limits's postfixes
                                var limits = ['gte', 'lte'];

                                // Setup empty values for range filters
                                for (var j = 0; j < limits.length; j++) {
                                    Ext.getCmp(filterCmpIdPrefix + alias + '-' + limits[j]).noReload = true;
                                    loopA[l](Ext.getCmp(filterCmpIdPrefix + alias + '-' + limits[j]));
                                    Ext.getCmp(filterCmpIdPrefix + alias + '-' + limits[j]).noReload = false;
                                }

                                // Else we reset one filter component
                            } else if (control == 'color') {

                                // Resetted values for color multislider filter
                                var v = [0, 360];

                                // Set a value for each multislider thumb
                                for (var j = 0; j < v.length; j++) {
                                    Ext.getCmp(filterCmpIdPrefix + alias).noReload = true;
                                    loopA[l](Ext.getCmp(filterCmpIdPrefix + alias), control);
                                    Ext.getCmp(filterCmpIdPrefix + alias).noReload = false;
                                }

                                // Else set by original way
                            } else {
                                Ext.getCmp(filterCmpIdPrefix + alias).noReload = true;
                                loopA[l](Ext.getCmp(filterCmpIdPrefix + alias));
                                Ext.getCmp(filterCmpIdPrefix + alias).noReload = false;
                            }
                        }
                    }

                    // Reload store for empty filter values to be picked up.
                    // We do reload only in case if at least one filter was emptied by reset filter tool
                    if (atLeastOneFilterIsUsed)
                        instance.filterChange({});

                    // Otherwise we display a message box saying that filters cannot be emptied because
                    // they are already empty
                    else Ext.MessageBox.show({
                        title: Indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_TITLE,
                        msg: Indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_MSG,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.INFO
                    });
                }
            });
        }

        // Return array of tools
        return tools;
    },

    wrapperPanelDockedItems: function() {

        // Toolbars array
        var toolbars = [];

        // If there is at least one filter was setup for current section
        if (Indi.trail.item().filters.length) {

            // Prepare Opera fieldset margin bottom fix
            var fieldsetMarginBottom = (navigator.userAgent.match(/Opera/)) ? 2 : 1;

            // Append filters toolbar to the toolbars stack
            toolbars.push({
                xtype: 'toolbar',
                dock: 'top',
                padding: '1 3 5 5',
                id: 'i-section-' + Indi.trail.item().section.alias + '-action-' + Indi.trail.item().action.alias + '-toolbar-filter',
                items: [{
                    xtype:'fieldset',
                    padding: '0 4 1 5',
                    margin: '0 2 ' + fieldsetMarginBottom + ' 0',
                    title: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_TITLE,
                    width: '100%',
                    columnWidth: '100%',
                    layout: 'column',
                    defaults: {
                        padding: '0 5 4 0',
                        margin: '-1 0 0 0'
                    },
                    items: this.wrapperPanelFilterToolbarItems(),
                    listeners: {
                        //afterrender: instance.setFilterValues
                    }
                }]
            });
        }

        // Append keyword toolbar to the toolbars stack
        toolbars.push({
            xtype: 'toolbar',
            id: 'i-section-' + Indi.trail.item().section.alias + '-action-index-toolbar-keyword',
            dock: 'top',
            height: 27,
            padding: '0 3 0 2',
            items: []//instance.keywordToolbarItems()
        });

        // Return toolbars array
        return toolbars;
    }
});
Ext.define('Indi.Controller.Action.Rowset.Grid', {
    extend: 'Indi.Controller.Action.Rowset'
});

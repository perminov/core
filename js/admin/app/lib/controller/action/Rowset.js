Ext.define('Indi.lib.controller.action.Rowset', {
    alternateClassName: 'Indi.Controller.Action.Rowset',
    extend: 'Indi.Controller.Action',
    mcopwso: ['store', 'rowset'],
    panel: {

        toolbarMasterItemActionIconA: ['form', 'delete', 'save', 'toggle', 'up', 'down'],

        /**
         * Provide store autoloading once panel panel is rendered
         */
        listeners: {
            afterrender: function(me){
                setTimeout(function(){
                    me.ctx().getStore().load();
                });
                Indi.trail(true).breadCrumbs();
            }
        }
    },

    /**
     * Extjs's Store object for current section
     *
     * @type {*}
     */
    store: {
        method: 'POST',
        remoteSort: true,
        proxy:  new Ext.data.HttpProxy({
            method: 'POST',
            reader: {
                type: 'json',
                root: 'blocks',
                totalProperty: 'totalCount',
                idProperty: 'id'
            }
        }),
        listeners: {
            beforeload: function(){
                this.ctx().filterChange({noReload: true});
            },
            load: function(){
                this.ctx().storeLoadCallbackDefault();
                this.ctx().storeLoadCallback();
            }
        }
    },

    getStore: function() {
        return Ext.getStore(this.bid() + '-store');
    },

    /**
     * Handler for any filter change
     *
     * @param cmp Component, that fired filterChange
     */
    filterChange: function(cmp){

        // Declare an array with filters component ids
        var filterCmpIdA = [];

        // Prepare a prefix for filter component ids
        var filterCmpIdPrefix = this.bid() + '-filter-';

        // For each filter
        for (var i = 0; i < this.trail().filters.length; i++) {

            // Define a shortcut for filter field alias
            var alias =  this.trail().filters[i].foreign('fieldId').alias;

            var control = this.trail().filters[i].foreign('fieldId').foreign('elementId').alias;

            // If current filter is a range-filter, we push two filter component ids - for min and max values
            if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {
                filterCmpIdA.push(filterCmpIdPrefix + alias + '-gte');
                filterCmpIdA.push(filterCmpIdPrefix + alias + '-lte');

                // Else we push one filter component id
            } else {
                filterCmpIdA.push(filterCmpIdPrefix + alias);
            }
        }

        // Declare and fulfil an array with properties, available for each row in the rowset
        var columnA = [];
        for (i = 0; i < this.trail().gridFields.length; i++) {
            columnA.push(this.trail().gridFields[i].alias);
        }

        // Declare an array for params, which will be fulfiled with filters's values
        var paramA = [];

        // Declare an array for filter fields (that are currently use for search), that are presented in a list
        // of properties, available for each row within a rowset, retrived by this.getStore(). We will need that
        // array bit later, to be able to determine if corresponding filters are used for all available
        // properties, and if so - keyword component from keyword toolbar should be disabled, because search
        // mechanism, that keyword component is involved in - is searching value, inputted in keyword field,
        // only within available properties. For example, if come row have a details field (as a HTML-editor)
        // which is not in the list of available properties (because list of available properties - is the same
        // almost the same as available grid columns, if Ext.panel.Grid is used to represent a rowset) - the
        // value, inputted in keyword search field - will not be searched in that details field.
        var usedFilterAliasesThatHasGridColumnRepresentedByA = [];

        // Foreach filter component id in filterCmpIdA array
        for (i = 0; i < filterCmpIdA.length; i++) {

            // Define a shortcut for filter filed alias
            var alias = filterCmpIdA[i].replace(filterCmpIdPrefix, '');

            // Get current filter value
            var value = Ext.getCmp(filterCmpIdA[i]).getValue();

            // If current filter is filter for color-field, and it's value is [0, 360], we set 'value' variable
            // as '' (empty string) because such value for color field filter mean that filter is not used
            if (Ext.getCmp(filterCmpIdA[i]).xtype == 'multislider' &&
                JSON.stringify(Ext.getCmp(filterCmpIdA[i]).getValue()) == '[0,360]')
                value = '';

            // If value is not empty
            if (value + '' != '' && value !== null) {

                // Prepare param object for storing current filter value. We will be using separate objects for
                // each used filter, e.g [{property1: "value1"}, {property2: "value2"}], instead of single object
                // {property1: "value1", property2: "value2"}, because it's the way of how extjs use it, for
                // passing sorting params within store request, so here we just do by the same way
                var paramO = {};

                // If current filter is a ext's datefield components
                if (Ext.getCmp(filterCmpIdA[i]).xtype == 'datefield') {

                    // If format of date, used in ext's datafield component - differs from 'Y-m-d'
                    if (Ext.getCmp(filterCmpIdA[i]).format != 'Y-m-d') {

                        // We get the raw value in that format, convert it back to 'Y-m-d' format
                        // and assign to paramO's object certain property as a current filter value
                        paramO[alias] = Ext.Date.format(
                            Ext.Date.parse(
                                Ext.getCmp(filterCmpIdA[i]).getRawValue(),
                                Ext.getCmp(filterCmpIdA[i]).format)
                            , 'Y-m-d');

                        // Else we just assign the value to param's object certain property as a current filter value
                    } else {
                        paramO[alias] = Ext.getCmp(filterCmpIdA[i]).getRawValue();
                    }

                    // Else if current filter is not a ext's datetime component
                } else {

                    // We just assign the value to param's object certain property as a current filter value, too
                    paramO[alias] = Ext.getCmp(filterCmpIdA[i]).getValue();
                }

                // Push the paramO object to the param stack
                paramA.push(paramO);

                // If current filter field alias is within an array of available properties (columns)
                for (var j =0; j < columnA.length; j++)
                    if (columnA[j] == alias.replace(/-(g|l)te$/, '') &&
                        usedFilterAliasesThatHasGridColumnRepresentedByA.indexOf(
                            alias.replace(/-(g|l)te$/, '')) == -1)

                    // We remember that, by pushing curren filter field alias to the
                    // usedFilterAliasesThatHasGridColumnRepresentedByA array
                        usedFilterAliasesThatHasGridColumnRepresentedByA.push(alias.replace(/-(g|l)te$/, ''));

            }
        }

        // Apply collected used filter alises and their values as a this.getStore().proxy.extraParams property
        //console.log(JSON.stringify(paramA));
        this.getStore().getProxy().extraParams = {search: JSON.stringify(paramA)};

        // Get id of the keyword component
        var keywordCmpId = this.bid() + '-keyword';

        // Get the value of keyword component, if component is not disabled
        var keyword = Ext.getCmp(keywordCmpId) && Ext.getCmp(keywordCmpId).disabled == false &&
            Ext.getCmp(keywordCmpId).getValue() ? Ext.getCmp(keywordCmpId).getValue() : '';

        if (keyword) this.getStore().getProxy().extraParams.keyword = keyword;

        var fs = Ext.getCmp(this.bid() + '-toolbar-filter-fieldset');
        if (fs) for (var i = 0; i < fs.items.keys.length; i++) {
            var key = fs.items.keys[i].replace(/-item$/,''), acmp = null;
            if (filterCmpIdA.indexOf(key) == -1 && (acmp = Ext.getCmp(key))) {
                if ((typeof acmp.getValue == 'function') && acmp.name) {
                    var value = acmp.getValue();
                    if (value + '' != '' && value !== null) {
                        if (acmp.xtype == 'datefield') value = Ext.Date.format(value, 'Y-m-d');
                        this.getStore().getProxy().extraParams[acmp.name] = value;
                    }
                }
            }
        }

        // Adjust an 'url' property of  this.getStore().proxy object, to apply keyword search usage
        this.getStore().getProxy().url = Indi.pre + '/' + this.trail().section.alias + '/index/' +
            (this.trail(1).row ? 'id/' + this.trail(1).row.id + '/' : '') + 'json/1/';

        // Disable keyword component, if all available properties are already involved in search by
        // corresponding filters usage
        if (Ext.getCmp(keywordCmpId)) Ext.getCmp(keywordCmpId).setDisabled(usedFilterAliasesThatHasGridColumnRepresentedByA.length == columnA.length);

        // If there is no noReload flag turned on
        if (!cmp.noReload) {

            // We reset the page's number, that should be retrieved by search, to 1, because if it currently
            // is not 1, there is a possiblity of no results displayed, as there is no guarantee, that there
            // will be enough results found matched new filters params, to display the same page. Example:
            // we were in a Countries section, where were ~300 countries, and we were at page 6, which mean
            // that countries from (if countries-on-page = 25) 126 to 150 were displayed. So if we use filters
            // or keyword search, but page number will remain the same (6) - there should be at least 126 results
            // matched our keyword/filters search, to at least 1 row to be displayed, but there is no guarantee
            // that there will be such number of results, that match our search criteria
            this.getStore().currentPage = 1;
            this.getStore().lastOptions.page = 1;
            this.getStore().lastOptions.start = 0;

            // If used filter is a combobox or multislider, we reload store data immideatly
            if (['combobox', 'combo.filter', 'multislider'].indexOf(cmp.xtype) != -1) {
                this.getStore().reload();

                // Else if used filter is not a datefield, or is, but it's value matches proper date format or
                // value is empty, we reload store data with a 500ms delay, because direct typing is allowed in that
                // datefield, so it's better to reload after user has finished typing.
            } else if (cmp.xtype != 'datefield' || (/^([0-9]{4}-[0-9]{2}-[0-9]{2}|[0-9]{2}\.[0-9]{2}\.[0-9]{4})$/
                .test(cmp.getRawValue()) || !cmp.getRawValue().length)) {
                clearTimeout(this.getStore().timeout);
                this.getStore().timeout = setTimeout(function(me){
                    me.getStore().reload();
                }, 500, this);
            }
        }
    },

    constructor: function(config) {
        if (config.trailLevel) this.trailLevel = config.trailLevel;
        this.mergeParent(config);
        this.store = Ext.merge({
            id: this.bid() + '-store',
            fields: this.storeFieldA(),
            sorters: this.storeSorters(),
            pageSize: parseInt(Indi.trail().section.rowsOnPage),
            currentPage: this.storeCurrentPage(),
            ctx: Ext.Component.prototype.ctx
        }, this.store);
        Ext.create('Ext.data.Store', this.store);
        this.callParent(arguments);
    },

    initComponent: function() {
        this.panel = Ext.merge({
            tools: this.panelToolA(),
            dockedItems: this.panelToolbarA()
        }, this.panel);
        this.callParent();
    },

    panelToolA: function() {

        // Declare tools array
        var tools = [], resetTool = this.panelToolReset();

        if (resetTool) tools.push(resetTool);

        // Return array of tools
        return tools;
    },

    panelToolReset: function() {

        // We add the filter-reset tool only if there is at least one filter defined for current section
        if (Indi.trail().filters.length)

        // Append tool data object to the 'tools' array
            return {
                type: 'search',
                cls: 'i-tool-search-reset',
                handler: function(event, target, owner, tool){

                    // Prepare a prefix for filter component ids
                    var filterCmpIdPrefix = this.ctx().bid() + '-filter-';

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
                        for (var i = 0; i < this.ctx().trail().filters.length; i++) {

                            // Define a shortcut for filter field alias
                            var alias =  this.ctx().trail().filters[i].foreign('fieldId').alias;

                            // Shortcut for control element, assigned to filter field
                            var control = this.ctx().trail().filters[i].foreign('fieldId').foreign('elementId').alias;

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
                        this.filterChange({});

                    // Otherwise we display a message box saying that filters cannot be emptied because
                    // they are already empty
                    else Ext.MessageBox.show({
                        title: Indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_TITLE,
                        msg: Indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_MSG,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.INFO
                    });
                }
            }
    },

    panelToolbarA: function() {

        // Toolbars array
        var toolbarA = [], toolbarFilter = this.panelToolbarFilter(), toolbarMaster = this.panelToolbarMaster();

        if (toolbarFilter) toolbarA.push(toolbarFilter);
        if (toolbarMaster) toolbarA.push(toolbarMaster);

        // Return toolbars array
        return toolbarA;
    },

    panelToolbarFilter: function() {
        var me = this;

        // If there is at least one filter was setup for current section
        if (this.trail().filters.length) {

            // Prepare Opera fieldset margin bottom fix
            var fieldsetMarginBottom = (navigator.userAgent.match(/Opera/)) ? 2 : 1;

            // Append filters toolbar to the toolbars stack
            return {
                xtype: 'toolbar',
                dock: 'top',
                padding: '1 5 5 5',
                id: this.bid()+'-toolbar-filter',
                layout: 'auto',
                items: [{
                    xtype:'fieldset',
                    id: this.bid()+'-toolbar-filter-fieldset',
                    padding: '0 0 1 3',
                    title: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_TITLE,
                    width: '100%',
                    layout: 'column',
                    defaults: {
                        margin: '0 5 4 2',
                        labelSeparator: '',
                        labelPad: 6,
                        labelStyle: 'padding-left: 0'
                    },
                    items: this.panelToolbarFilterItemA(),
                    listeners: {
                        afterrender: function(){
                            me.setFilterValues();
                        }
                    }
                }]
            }
        }
    },

    panelToolbarMaster: function() {

        // Append keyword toolbar to the toolbars stack
        return {
            xtype: 'toolbar',
            id: this.bid() + '-toolbar-master',
            dock: 'top',
            height: 27,
            padding: '0 3 0 2',
            items: this.panelToolbarMasterItemA()
        }
    },

    panelToolbarFilterItemA: function() {

        // Items array
        var itemA = [], itemI, itemICustom;

        for (var i = 0; i < this.trail().filters.length; i++) {
            itemI = this.panelToolbarFilterItemIDefault(this.trail().filters[i]);
            itemICustom = 'panelToolbarFilterItemI$' + Indi.ucfirst(this.trail().filters[i].foreign('fieldId').alias);
            if (typeof this[itemICustom] == 'function') itemI = this[itemICustom](itemI);
            if (itemI) itemA = itemA.concat(itemI.length ? itemI: [itemI]);
        }

        // Return filter toolbar items
        return itemA;
    },

    panelToolbarFilterItemI_Combo: function(filter) {

        // Get the field
        var field = filter.foreign('fieldId');

        // Define a shortcut for filter field alias
        var alias = field.alias;

        // Prepare the id for current filter component
        var filterCmpId = this.bid() + '-filter-' + alias;

        // Get the label
        var fieldLabel = filter.alt || field.title;

        // Setup a shortcut for filters shared row
        var row = this.trail().filtersSharedRow;

        // Push the special extjs component data object to represent needed filter. Component consists of
        // two hboxed components. First is extjs label component, and second - is setup to pick up
        // a custom DOM node as it's contentEl property. This DOM node is already prepared by non-extjs
        // solution, implemented in IndiEngine
        return {
            id: filterCmpId,
            xtype: 'combo.filter',
            fieldLabel : fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            field: field,
            name: alias,
            value: Ext.isNumeric(row[field.alias]) ? parseInt(row[field.alias]) : row[field.alias],
            subTplData: row.view(field.alias).subTplData,
            store: row.view(field.alias).store
        }
    },

    panelToolbarFilterItemIColor: function(filter) {

        // Define a shortcut for filter field alias
        var alias = filter.foreign('fieldId').alias;

        // Prepare the id for current filter component
        var filterCmpId = this.bid() + '-filter-' + alias;

        // Get the label
        var fieldLabel = filter.alt || filter.foreign('fieldId').title;

        // Append the extjs multislider component data object to filters stack, as multislider will
        // be the approriate way to represent color hue range (0 to 360)
        return {
            xtype: 'multislider',
            id: filterCmpId,
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            //labelSeparator: '',
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
                    if (!cmp.noReload) this.ctx().filterChange(cmp);
                }
            }
        }
    },

    panelToolbarFilterItemI_Keyword: function(filter) {

        // Define a shortcut for filter field alias
        var alias = filter.foreign('fieldId').alias;

        // Get the label
        var fieldLabel = filter.alt || filter.foreign('fieldId').title;

        // Append the extjs textfield component data object to filters stack
        return {
            xtype: 'textfield',
            id: this.bid() + '-filter-' + alias,
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            //labelSeparator: '',
            hiddenName: alias,
            width: 80 + Indi.metrics.getWidth(fieldLabel),
            margin: 0,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) this.ctx().filterChange(cmp);
                }
            }
        }
    },

    panelToolbarFilterItemIString: function(filter) {
        return this.panelToolbarFilterItemI_Keyword(filter);
    },

    panelToolbarFilterItemITextarea: function(filter) {
        return this.panelToolbarFilterItemI_Keyword(filter);
    },

    panelToolbarFilterItemIHtml: function(filter) {
        return this.panelToolbarFilterItemI_Keyword(filter);
    },

    panelToolbarFilterItemINumber: function(filter) {

        // Define a shortcut for filter field alias
        var alias = filter.foreign('fieldId').alias;

        // Prepare the id for current filter component
        var filterCmpId = this.bid() + '-filter-' + alias;

        // Get the label
        var fieldLabel = (filter.alt || filter.foreign('fieldId').title) + ' ' +
            Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM;

        // Append the extjs numberfield component data object to filters stack, for minimum value
        var gte = {
            xtype: 'numberfield',
            id: filterCmpId + '-gte',
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            //labelSeparator: '',
            width: 50 + Indi.metrics.getWidth(fieldLabel),
            margin: 0,
            minValue: 0,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) this.ctx().filterChange(cmp);
                }
            }
        };

        // Append the extjs numberfield component data object to filters stack, for maximum value
        var lte = {
            xtype: 'numberfield',
            id: filterCmpId + '-lte',
            fieldLabel: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO,
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
            //labelSeparator: '',
            width: 50 + Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
            margin: 0,
            minValue: 0,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) this.ctx().filterChange(cmp);
                }
            }
        };

        return [gte, lte];
    },

    panelToolbarFilterItemI_Calendar: function(filter) {

        // Define a shortcut for filter field alias
        var alias = filter.foreign('fieldId').alias;

        // Prepare the id for current filter component
        var filterCmpId = this.bid() + '-filter-' + alias;

        // Get the format
        var dateFormat = filter.foreign('fieldId').params['display' +
            (filter.foreign('fieldId').foreign('elementId').alias == 'datetime' ?
                'Date': '') + 'Format'] || 'Y-m-d';

        // Get the label for filter minimal value component
        var fieldLabel = (filter.alt ?
            filter.alt :
            filter.foreign('fieldId').title) + ' ' +
            Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM;

        // Prepare the data for extjs datefield component, for use as control for filter minimal value
        var datefieldFrom = {
            xtype: 'datefield',
            id: filterCmpId + '-gte',
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            width: 85 + Indi.metrics.getWidth(fieldLabel),
            startDay: 1,
            validateOnChange: false,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) this.ctx().filterChange(cmp);
                }
            }
        };

        // Prepare the data for extjs datefield component, for use as control for filter maximal value
        var datefieldUntil = {
            xtype: 'datefield',
            id: filterCmpId + '-lte',
            fieldLabel: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO,
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
            width: 85 + Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
            startDay: 1,
            validateOnChange: false,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) this.ctx().filterChange(cmp);
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
        return [datefieldFrom, datefieldUntil];
    },

    panelToolbarFilterItemICalendar: function(filter) {
        return this.panelToolbarFilterItemI_Calendar(filter);
    },

    panelToolbarFilterItemIDatetime: function(filter) {
        return this.panelToolbarFilterItemI_Calendar(filter);
    },

    panelToolbarFilterItemICombo: function(filter) {
        return this.panelToolbarFilterItemI_Combo(filter);
    },

    panelToolbarFilterItemIRadio: function(filter) {
        return this.panelToolbarFilterItemI_Combo(filter);
    },

    panelToolbarFilterItemICheck: function(filter) {
        return this.panelToolbarFilterItemI_Combo(filter);
    },

    panelToolbarFilterItemIMulticheck: function(filter) {
        return this.panelToolbarFilterItemI_Combo(filter);
    },

    panelToolbarFilterItemIDefault: function(filter) {

        var itemI, itemIDefault, itemICustom;

        // Define a shortcut for filter field element alias
        var control = filter.foreign('fieldId').foreign('elementId').alias;

        itemIDefault = 'panelToolbarFilterItemI' + Indi.ucfirst(control);
        if (typeof this[itemIDefault] == 'function') itemI = this[itemIDefault](filter);

        return itemI;
    },

    panelToolbarMasterItemAction$Create: function(){

        // Check if 'save' and 'form' actions are allowed
        var canSave = false, canForm = false, canAdd = this.trail().section.disableAdd == '0';
        for (var i = 0; i < this.trail().actions.length; i++) {
            if (this.trail().actions[i].alias == 'save') canSave = true;
            if (this.trail().actions[i].alias == 'form') canForm = true;
        }

        // 'Create' button will be added only if it was not switched off
        // in section config and if 'save' and 'form' actions are allowed
        if (canForm && canSave && canAdd) {

            return {
                id: this.bid() + '-button-add',
                tooltip: Indi.lang.I_CREATE,
                iconCls: 'i-btn-icon-create',
                actionAlias: 'form',
                handler: function(){
                    Indi.load(
                        this.ctx().trail().section.href +
                            this.actionAlias + '/ph/' + this.ctx().trail().section.primaryHash + '/'
                    );
                }
            }
        }
    },

    panelToolbarMasterItemActionDefault: function(action) {

        if (action.display == 1) {

            // Basic action object
            var actionItem = {
                id: this.bid() + '-button-' + action.alias,
                text: action.title,
                action: action,
                actionAlias: action.alias,
                rowRequired: action.rowRequired,
                javascript: action.javascript,
                handler: function(){

                    // Get selection
                    var selection = Ext.getCmp('i-center-center-wrapper')
                        .getComponent(0)
                        .getSelectionModel()
                        .getSelection();

                    // Get first selected row and it's index, if selected
                    if (selection.length) {
                        var row = selection[0].data;
                        var aix = selection[0].index + 1;
                    }

                    // If there is no rows selected, but at elast one should
                    if (this.rowRequired == 'y' && !selection.length) {

                        // Display a message box with approriate warning
                        Ext.MessageBox.show({
                            title: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                            msg: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.WARNING
                        });

                        // Run the handler
                    } else {
                        if (typeof this.javascript == 'function') this.javascript(); else eval(this.javascript);
                    }
                }
            }

            // Setup iconCls property, if need
            if (this.panel.toolbarMasterItemActionIconA.indexOf(action.alias) != -1) {
                actionItem.iconCls = 'i-btn-icon-' + action.alias;
                actionItem.text = '';
                actionItem.tooltip = action.title;
            }

            // Put to the actions stack
            return actionItem;
        }
    },

    panelToolbarMasterItemActionA: function() {

        var actionItemA = [], actionItem, actionItemCustom, actionItemCreate = this.panelToolbarMasterItemAction$Create();
        if (actionItemCreate) actionItemA.push(actionItemCreate);
        for (var i = 0; i < this.trail().actions.length; i++) {
            actionItem = this.panelToolbarMasterItemActionDefault(this.trail().actions[i]);
            actionItemCustom = 'panelToolbarMasterItemAction$'+Indi.ucfirst(this.trail().actions[i].alias);
            if (typeof this[actionItemCustom] == 'function') actionItem = this[actionItemCustom](actionItem);
            if (actionItem) actionItemA.push(actionItem);
        }
        return actionItemA;
    },

    panelToolbarMasterItemA: function() {

        // Items array
        var itemA = [], actionItemA = this.panelToolbarMasterItemActionA(), keywordItem = this.panelToolbarMasterItemKeyword();

        // Append separator, if at least one action button item is alredy exist within 'items' array
        if (actionItemA.length) (itemA = itemA.concat(actionItemA)).push('-');

        // Append subsections list
        if (Indi.trail().sections.length) itemA.push({
            xtype: 'subsectionlist',
            toolbarId: 'i-section-' + Indi.trail().section.alias + '-action-' + Indi.trail().action.alias + '-toolbar-master',
            id: 'i-section-' + Indi.trail().section.alias + '-action-' + Indi.trail().action.alias + '-subsections',
            tooltip: {
                html: Indi.lang.I_NAVTO_NESTED,
                hideDelay: 0,
                showDelay: 1000,
                dismissDelay: 2000,
                staticOffset: [0, 1]
            },
            itemClick: function(item){

                // Get selection
                var selection = Ext.getCmp('i-center-center-wrapper').getComponent(0).getSelectionModel().getSelection();

                // If no selection - show a message box
                if (selection.length == 0) {
                    Ext.MessageBox.show({
                        title: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                        msg: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.WARNING

                        // Else load the subsection
                    });
                } else if (item.getAttribute('alias'))
                    Indi.load(Indi.pre + '/' + item.getAttribute('alias') + '/index/id/' + selection[0].data.id + '/' +
                        'ph/'+Indi.trail().scope.hash+'/aix/' + (selection[0].index + 1)+'/');
            }
        });

        // We figure that other items should be right-aligned at the keyword toolbar
        if (keywordItem) itemA.push('->', keywordItem);

        return itemA;
    },

    panelToolbarMasterItemKeyword: function() {

        // Append fast search keyword field component to the items stack
        return {
            xtype: 'textfield',
            fieldLabel: Indi.lang.I_ACTION_INDEX_KEYWORD_LABEL,
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_KEYWORD_LABEL),
            labelClsExtra: 'i-action-index-keyword-toolbar-keyword-label',
            //labelSeparator: '',
            value: this.trail().scope.keyword ? Indi.urldecode(this.trail().scope.keyword) : '',
            width: 100 + Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_KEYWORD_LABEL),
            height: 19,
            cls: 'i-form-text',
            margin: '0 0 0 5',
            id: this.bid() + '-keyword',
            listeners: {
                change: function(){
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(function(me){
                        me.ctx().filterChange({});
                    }, 500, this);
                }
            }
        }
    },

    /**
     * Build and return an array, containing definition of data, that will be got by this.store's request
     *
     * @return {Array}
     */
    storeFieldA: function (){
        // Id field
        var fieldA = [], fieldI$Id = this.storeFieldI$Id(), fieldI, fieldICustom;

        if (fieldI$Id) fieldA.push(fieldI$Id);

        // Other fields
        for (var i = 0; i < this.trail().gridFields.length; i++) {
            fieldI = this.storeFieldIDefault(this.trail().gridFields[i]);
            fieldICustom = 'storeFieldI$' + Indi.ucfirst(this.trail().gridFields[i].alias);
            if (typeof this[fieldICustom] == 'function') fieldI = this[fieldICustom](fieldI);
            if (fieldI) fieldA.push(fieldI);
        }

        // Return array
        return fieldA;
    },

    storeFieldIDefault: function(field) {
        return {
            name: field.alias,
            type: !parseInt(field.entityId) && [3,5].indexOf(field.columnTypeId) != -1 ? 'int' : 'string'
        }
    },

    storeFieldI$Id: function() {
        return {name: 'id', type: 'int'}
    },

    /**
     * Set up and return store full request string (but without paging params)
     */
    storeLastRequest: function(){

        // Get the initial uri
        var url = this.getStore().getProxy().url;

        // Declare an array for $_GET params
        var get = [];

        // If filters were used during last store request, we retrieve info about, encode and append it to 'get'
        if (this.getStore().getProxy().extraParams.search)
            get.push('search=' + encodeURIComponent(this.getStore().getProxy().extraParams.search));

        // If keyword was used during last store request, we retrieve info about, encode and append it to 'get'
        if (this.getStore().getProxy().extraParams.keyword)
            get.push('keyword=' + encodeURIComponent(this.getStore().getProxy().extraParams.keyword));

        // If sorters were used during last store request, we retrieve info about, encode and append it to 'get'
        if (this.getStore().getSorters().length)
            get.push('sort=' + encodeURIComponent(JSON.stringify(this.getStore().getSorters())));

        // Return the full url string
        return url + (get.length ? '?' + get.join('&') : '');
    },

    /**
     * Prepare and return a sorters for this.store
     *
     * @return {Array}
     */
    storeSorters: function(){

        // If we have sorting params, stored in scope - we use them
        if (this.trail().scope.order && eval(this.trail().scope.order).length)
            return eval(this.trail().scope.order);

        // Else we use current section's default sorting params, if specified
        else if (this.trail().section.defaultSortField)
            return [{
                property : this.trail().section.defaultSortFieldAlias,
                direction: this.trail().section.defaultSortDirection
            }];

        // Else no sorting at all
        return [];
    },

    storeLoadCallbackDefault: function() {
        this.trail().scope = this.getStore().proxy.reader.jsonData.scope;
    },

    storeLoadCallback: function() {

    },

    /**
     * Determines this.store's current page. At first it will try to get it from this.trail().scope, at it it fails
     *  - return 1
     *
     * @return {*}
     */
    storeCurrentPage: function(){
        return this.trail().scope.page ? parseInt(this.trail().scope.page): 1;
    },

    /**
     * Gets a value, stored in scope for filter, by given filter alias
     *
     * @param alias
     * @return {*}
     */
    getScopeFilter: function(alias){

        // If there is no filters used at all - return
        if (this.trail().scope.filters == null) return;

        var value = undefined;

        // Filter values are stored in Indi.trail().scope as a stringified json array, so we need to convert it back,
        // to be able to find something there
        var values = eval(this.trail().scope.filters);

        // Find a filter value
        for (var i = 0; i < values.length; i++)
            if (values[i].hasOwnProperty(alias))
                value = values[i][alias];

        // Return value
        return value;
    },

    /**
     * Assign values to filters, before store load, for store to be loaded with respect to filter params.
     * These values will be got from Indi.trail().scope.filters, and if there is no value for some filter there - then
     * we'll try to get that in Indi.trail().filters[i].defaultValue. If there will no value too - then
     * filter will be empty.
     */
    setFilterValues: function(){

        // Foreach filter
        for (var i = 0; i < this.trail().filters.length; i++) {

            // Create a shortcut for filter field alias
            var name = this.trail().filters[i].foreign('fieldId').alias;

            // Create a shortcut for filter field control element alias
            var control = this.trail().filters[i].foreign('fieldId').foreign('elementId').alias;

            // At first, we check if current scope contain the value for the current filter, and if so - we use
            // that value instead of filter's own default value, whether it was defined or not. Also, we
            // implement a bit different behaviour for range-filters (number, calendar, datetime) and for other
            // types of filters
            if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                // Object for default values
                var def = {};

                // Assign the 'gte' and 'lte' properties to the object of default values
                if (['undefined', ''].indexOf(this.getScopeFilter(name + '-gte') + '') == -1)
                    def.gte = this.getScopeFilter(name + '-gte');
                if (['undefined', ''].indexOf(this.getScopeFilter(name + '-lte') + '') == -1)
                    def.lte = this.getScopeFilter(name + '-lte');

                // If at least 'gte' or 'lte' properies was set, we assing 'def' object as filter default value
                if (Object.getOwnPropertyNames(def).length) this.trail().filters[i].defaultValue = def;

                // Else current filter is not a range-filter
            } else if (this.getScopeFilter(name)) {

                // Just assign the value, got from scope as filter default value
                this.trail().filters[i].defaultValue = this.getScopeFilter(name);
            }

            // Finally, if filter has a non-null default value
            if (this.trail().filters[i].defaultValue) {

                // Define a shortcut for filter's default value
                var d = this.trail().filters[i].defaultValue;

                // Prepare the id for current filter component
                var filterCmpId = this.bid() + '-filter-' + this.trail().filters[i].foreign('fieldId').alias;

                // If current filter is a range filter - set up min and/or max separately
                if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                    // If default value is a stringified javascript array of javascript object, we convert it back
                    if ((typeof d == 'string') && (d.match(/^\[.*\]$/) || d.match(/^\{.*\}$/))) d = eval('('+ d + ')');

                    // If default value is an object
                    if (typeof d == 'object')

                    // Foreach property in default value object (which nameds can be 'gte' and 'lte' only)
                        for (var j in d) {

                            // Toggle 'noReload' property to 'true' to prevend store reload, because we do not need it
                            // to be reloaded at this time. We will need that later, after all values will be assigned
                            // to filters
                            Ext.getCmp(filterCmpId + '-' + j).noReload = true;

                            // Set filter value
                            Ext.getCmp(filterCmpId + '-' + j).setValue(d[j]);

                            // Revert back 'noReload' property to 'false'
                            Ext.getCmp(filterCmpId + '-' + j).noReload = false;
                        }

                    // Else current filter is not a renge-filter
                } else {

                    // Toggle 'noReload' property to 'true' to prevent store reload
                    Ext.getCmp(filterCmpId).noReload = true;

                    // If filter is for multiple combo - set value as array, joined by comma
                    if (this.trail().filters[i].foreign('fieldId').storeRelationAbility == 'many')
                        Ext.getCmp(filterCmpId).setValue(typeof d == 'string' ? d : d.join(','));

                    // Else if filter is for color field, that is represented by two-thumb multislider
                    // we set values separately for each thumb. According to Ext docs, there is a way to set
                    // value at once, but for some reason that way gives an error, so we need to use the same
                    // method (setValue) but with an alternative set of agruments
                    else if (control == 'color') {

                        // If color multislider default value is a stringified array e.g "[123, 234]", we should
                        // convert it back
                        if (typeof d == 'string') d = eval(d);

                        // Set a value for each multislider thumb
                        for (var j = 0; j < d.length; j++)
                            Ext.getCmp(filterCmpId).setValue(j, d[j], false);


                        // Else set by original way
                    } else
                        Ext.getCmp(filterCmpId).setValue(d);

                    // Revert back 'noReload' property to 'false'
                    Ext.getCmp(filterCmpId).noReload = false;
                }
            }
        }
    }
});

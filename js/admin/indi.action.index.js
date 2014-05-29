var Indi = (function (indi) {
    "use strict";
    var process = function () {
        /**
         * Setup empty indi.proto.action object
         *
         * @type {Object}
         */
        indi.proto.action = {};

        /**
         * Setup `filter` property of indi.proto.combo object
         */
        indi.proto.action.index = function(){

            /**
             * This is for context stabilization
             *
             * @type {*}
             */
            var instance = this;

            /**
             * This will be used at the stage of request uri constructing while within remoteFetch()
             * and also, is used to get a proper stack of callbacks that should be called in run()
             *
             * @type {String}
             */
            this.componentName = 'action.index';

            /**
             * Configuration
             *
             * @type {Object}
             */
            this.options = {
                grid: {
                    multiSelect: false,
                    firstColumnWidthFraction: 0.4,
                    storeLoadCallback: function(){
                    }
                }
            }

            /**
             * Get the panel, that wraps grid, tile or calendar, and that is rendered to center region of center region
             * of viewport
             *
             * @return {*}
             */
            this.getPanel = function() {
                return top.window.Ext.getCmp('i-center-center-wrapper');
            }

            /**
             * Handler for any filter change
             *
             * @param cmp Component, that fired filterChange
             */
            this.filterChange = function(cmp){

                // Declare an array with filters component ids
                var filterCmpIdA = [];

                // Prepare a prefix for filter component ids
                var filterCmpIdPrefix = 'i-section-' + indi.trail.item().section.alias + '-action-index-filter-';

                // For each filter
                for (var i = 0; i < indi.trail.item().filters.length; i++) {

                    // Define a shortcut for filter field alias
                    var alias =  indi.trail.item().filters[i].foreign('fieldId').alias;

                    // If current filter is a range-filter, we push two filter component ids - for min and max values
                    if (['number', 'calendar', 'datetime']
                        .indexOf(indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias) != -1) {
                        filterCmpIdA.push(filterCmpIdPrefix + alias + '-gte');
                        filterCmpIdA.push(filterCmpIdPrefix + alias + '-lte');

                    // Else we push one filter component id
                    } else {
                        filterCmpIdA.push(filterCmpIdPrefix + alias);
                    }
                }

                // Declare and fulfil an array with properties, available for each row in the rowset
                var columnA = [];
                for (var i = 0; i < indi.trail.item().gridFields.length; i++) {
                    columnA.push(indi.trail.item().gridFields[i].alias);
                }

                // Declare an array for params, which will be fulfiled with filters's values
                var paramA = [];

                // Declare an array for filter fields (that are currently use for search), that are presented in a list
                // of properties, available for each row within a rowset, retrived by instance.store. We will need that
                // array bit later, to be able to determine if corresponding filters are used for all available
                // properties, and if so - keyword component from keyword toolbar should be disabled, because search
                // mechanism, that keyword component is involved in - is searching value, inputted in keyword field,
                // only within available properties. For example, if come row have a details field (as a HTML-editor)
                // which is not in the list of available properties (because list of available properties - is the same
                // almost the same as available grid columns, if Ext.panel.Grid is used to represent a rowset) - the
                // value, inputted in keyword search field - will not be searched in that details field.
                var usedFilterAliasesThatHasGridColumnRepresentedByA = [];

                // Foreach filter component id in filterCmpIdA array
                for (var i = 0; i < filterCmpIdA.length; i++) {

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

                // Apply collected used filter alises and their values as a instance.store.proxy.extraParams property
                //console.log(JSON.stringify(paramA));
                instance.store.getProxy().extraParams = {search: JSON.stringify(paramA)};

                // Get id of the keyword component
                var keywordCmpId = 'i-section-' + indi.trail.item().section.alias + '-action-index-keyword';

                // Get the value of keyword component, if component is not disabled
                var keyword = Ext.getCmp(keywordCmpId).disabled == false && Ext.getCmp(keywordCmpId).getValue() ?
                    Ext.getCmp(keywordCmpId).getValue() : '';

                if (keyword) instance.store.getProxy().extraParams.keyword = keyword;

                // Adjust an 'url' property of  instance.store.proxy object, to apply keyword search usage
                instance.store.getProxy().url = indi.pre + '/' + indi.trail.item().section.alias + '/index/' +
                    (indi.trail.item(1).row ? 'id/' + indi.trail.item(1).row.id + '/' : '') + 'json/1/'/* +
                    (keyword ? 'keyword/' + keyword + '/' : '')*/;

                // Disable keyword component, if all available properties are already involved in search by
                // corresponding filters usage
                Ext.getCmp(keywordCmpId).setDisabled(usedFilterAliasesThatHasGridColumnRepresentedByA.length == columnA.length);

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
                    instance.store.currentPage = 1;
                    instance.store.lastOptions.page = 1;
                    instance.store.lastOptions.start = 0;

                    // If used filter is a combobox or multislider, we reload store data immideatly
                    if (['combobox', 'multislider'].indexOf(cmp.xtype) != -1) {
                        instance.store.reload();

                    // Else if used filter is not a datefield, or is, but it's value matches proper date format or
                    // value is empty, we reload store data with a 500ms delay, because direct typing is allowed in that
                    // datefield, so it's better to reload after user has finished typing.
                    } else if (cmp.xtype != 'datefield' || (/^([0-9]{4}-[0-9]{2}-[0-9]{2}|[0-9]{2}\.[0-9]{2}\.[0-9]{4})$/
                        .test(cmp.getRawValue()) || !cmp.getRawValue().length)) {
                        clearTimeout(instance.timeout);
                        instance.timeout = setTimeout(function(){
                            instance.store.reload();
                        }, 500);
                    }
                }
            }

            /**
             * Build and return an array, containing definition of data, that will be got by instance.store's request
             *
             * @return {Array}
             */
            this.storeFields = function (){
                // Id field
                var fieldA = [{name: 'id', type: 'int'}];

                // Other fields
                for (var i = 0; i < indi.trail.item().gridFields.length; i++)
                    fieldA.push({
                        name: indi.trail.item().gridFields[i].alias,
                        type: !parseInt(indi.trail.item().gridFields[i].entityId) &&
                            [3,5].indexOf(indi.trail.item().gridFields[i].columnTypeId) != -1 ? 'int' : 'string'
                    });

                // Return array
                return fieldA;
            }

            /**
             * Build and return an array, containing column definitions for grid panel
             *
             * @return {Array}
             */
            this.gridColumns = function (){

                // Id column
                var columnA = [{header: 'id', dataIndex: 'id', width: 30, sortable: true, align: 'right', hidden: true}];

                // Other columns
                for (var i = 0; i < indi.trail.item().gridFields.length; i++) {
                    columnA.push({
                        id: 'i-section-' + indi.trail.item().section.alias + '-action-index-grid-column-' + indi.trail.item().gridFields[i].alias,
                        header: indi.trail.item().gridFields[i].title,
                        dataIndex: indi.trail.item().gridFields[i].alias,
                        cls: 'i-grid-column-filtered',
                        sortable: true,
                        align: function(){
                            return (indi.trail.item().gridFields[i].storeRelationAbility == 'none' &&
                                [3,5].indexOf(parseInt(indi.trail.item().gridFields[i].columnTypeId)) != -1) ? 'right' : 'left';
                        }(),
                        hidden: indi.trail.item().gridFields[i].alias == 'move' ? true : false
                    });
                }

                // Setup flex for first non-hidden column
                columnA[1].flex = 1;

                // Return array
                return columnA;
            }

            /**
             * Gets a value, stored in scope for filter, by given filter alias
             *
             * @param alias
             * @return {*}
             */
            this.getScopeFilter = function(alias){

                // If there is no filters used at all - return
                if (indi.trail.item().scope.filters == null) return;

                var value = undefined;

                // Filter values are stored in indi.trail.item().scope as a stringified json array, so we need to convert it back,
                // to be able to find something there
                var values = eval(indi.trail.item().scope.filters);

                // Find a filter value
                for (var i = 0; i < values.length; i++)
                    if (values[i].hasOwnProperty(alias))
                        value = values[i][alias];

                // Return value
                return value;
            }


            /**
             * Assign values to filters, before store load, for store to be loaded with respect to filter params.
             * These values will be got from indi.trail.item().scope.filters, and if there is no value for some filter there - then
             * we'll try to get that in indi.trail.item().filters[i].defaultValue. If there will no value too - then
             * filter will be empty.
             */
            this.setFilterValues = function(){

                // Foreach filter
                for (var i = 0; i < indi.trail.item().filters.length; i++) {

                    // Create a shortcut for filter field alias
                    var name = indi.trail.item().filters[i].foreign('fieldId').alias;

                    // Create a shortcut for filter field control element alias
                    var control = indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias;

                    // At first, we check if current scope contain the value for the current filter, and if so - we use
                    // that value instead of filter's own default value, whether it was defined or not. Also, we
                    // implement a bit different behaviour for range-filters (number, calendar, datetime) and for other
                    // types of filters
                    if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                        // Object for default values
                        var def = {};

                        // Assign the 'gte' and 'lte' properties to the object of default values
                        if (['undefined', ''].indexOf(instance.getScopeFilter(name + '-gte') + '') == -1)
                            def.gte = instance.getScopeFilter(name + '-gte');
                        if (['undefined', ''].indexOf(instance.getScopeFilter(name + '-lte') + '') == -1)
                            def.lte = instance.getScopeFilter(name + '-lte');

                        // If at least 'gte' or 'lte' properies was set, we assing 'def' object as filter default value
                        if (Object.getOwnPropertyNames(def).length) indi.trail.item().filters[i].defaultValue = def;

                    // Else current filter is not a range-filter
                    } else if (instance.getScopeFilter(name)) {

                        // Just assign the value, got from scope as filter default value
                        indi.trail.item().filters[i].defaultValue = instance.getScopeFilter(name);
                    }

                    // Finally, if filter has a non-null default value
                    if (indi.trail.item().filters[i].defaultValue) {

                        // Define a shortcut for filter's default value
                        var d = indi.trail.item().filters[i].defaultValue;

                        // Prepare the id for current filter component
                        var filterCmpId = 'i-section-' + indi.trail.item().section.alias + '-action-index-filter-' +
                            indi.trail.item().filters[i].foreign('fieldId').alias;

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
                            if (indi.trail.item().filters[i].foreign('fieldId').storeRelationAbility == 'many')
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

            /**
             * Create and append toolbar panels main content wrapper panel
             *
             * @return {Array}
             */
            this.toolbars = function () {

                // Toolbars array
                var toolbars = [];

                // If there is at least one filter was setup for current section
                if (indi.trail.item().filters.length) {

                    // Prepare Opera fieldset margin bottom fix
                    var fieldsetMarginBottom = (navigator.userAgent.match(/Opera/)) ? 2 : 1;

                    // Append filters toolbar to the toolbars stack
                    toolbars.push({
                        xtype: 'toolbar',
                        dock: 'top',
                        padding: '1 3 5 5',
                        id: 'i-section-' + indi.trail.item().section.alias + '-action-index-toolbar-filter',
                        items: [{
                            xtype:'fieldset',
                            padding: '0 4 1 5',
                            margin: '0 2 ' + fieldsetMarginBottom + ' 0',
                            title: indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_TITLE,
                            width: '100%',
                            columnWidth: '100%',
                            layout: 'column',
                            defaults: {
                                padding: '0 5 4 0',
                                margin: '-1 0 0 0'
                            },
                            items: instance.filterToolbarItems(),
                            listeners: {
                                afterrender: instance.setFilterValues
                            }
                        }]
                    });
                }

                // Append keyword toolbar to the toolbars stack
                toolbars.push({
                    xtype: 'toolbar',
                    id: 'i-section-' + indi.trail.item().section.alias + '-action-index-toolbar-keyword',
                    dock: 'top',
                    height: 27,
                    padding: '0 3 0 2',
                    items: instance.keywordToolbarItems()
                });

                // Return toolbars array
                return toolbars;
            };

            // Timeout variable (an identifier for javascript setTimeout function) for delay between last keyword
            // letter was typed and store reload fired
            this.timeout;

            /**
             * Prepare and return an array containing components for each filter, defined for a section
             *
             * @return {Array}
             */
            this.filterToolbarItems = function() {

                // Items array
                var items = [];

                // For each filter
                for (var i = 0; i < indi.trail.item().filters.length; i++) {

                    // Define a shortcut for filter field alias
                    var alias = indi.trail.item().filters[i].foreign('fieldId').alias;

                    // Prepare the id for current filter component
                    var filterCmpId = 'i-section-' + indi.trail.item().section.alias + '-action-index-filter-' + alias;

                    // If current filter is defined for 'string', 'textarea' or 'html' field
                    if (['string', 'textarea', 'html']
                        .indexOf(indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias) != -1) {

                        // Get the label
                        var fieldLabel = indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign('fieldId').title;

                        // Append the extjs textfield component data object to filters stack
                        items.push({
                            xtype: 'textfield',
                            id: filterCmpId,
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            hiddenName: alias,
                            width: 80 + indi.metrics.getWidth(fieldLabel),
                            margin: 0,
                            listeners: {
                                change: function(cmp){
                                    if (!cmp.noReload) instance.filterChange(cmp);
                                }
                            }
                        });

                    // Else if current filter is defined for 'number' field
                    } else if (indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias == 'number') {

                        // Get the label
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign('fieldId').title) + ' ' +
                            indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM;

                        // Append the extjs numberfield component data object to filters stack, for minimum value
                        items.push({
                            xtype: 'numberfield',
                            id: filterCmpId + '-gte',
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            width: 50 + indi.metrics.getWidth(fieldLabel),
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
                            fieldLabel: indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO,
                            labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
                            labelSeparator: '',
                            width: 50 + indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
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
                        .indexOf(indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias) != -1) {

                        // Get the format
                        var dateFormat = indi.trail.item().filters[i].foreign('fieldId').params['display' +
                            (indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias == 'datetime' ?
                                'Date': '') + 'Format'] || 'Y-m-d';

                        // Get the label for filter minimal value component
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign('fieldId').title) + ' ' +
                            indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM;

                        // Prepare the data for extjs datefield component, for use as control for filter minimal value
                        var datefieldFrom = {
                            xtype: 'datefield',
                            id: filterCmpId + '-gte',
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            width: 85 + indi.metrics.getWidth(fieldLabel),
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
                            fieldLabel: indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO,
                            labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
                            labelSeparator: '',
                            width: 85 + indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
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
                    } else if (indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias == 'color') {

                        // Get the label
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign('fieldId').title);

                        // Append the extjs multislider component data object to filters stack, as multislider will
                        // be the approriate way to represent color hue range (0 to 360)
                        items.push({
                            xtype: 'multislider',
                            id: filterCmpId,
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            labelClsExtra: 'i-multislider-color-label',
                            values: [0, 360],
                            increment: 1,
                            minValue: 0,
                            maxValue: 360,
                            constrainThumbs: false,
                            // Hue bg width + label width + labelPad + thumb-overlap * number-of-thumbs
                            width: 183 + indi.metrics.getWidth(fieldLabel) + 5 + 7 * 2,
                            margin: '1 0 0 0',
                            cls: 'i-multislider-color',
                            listeners: {
                                changecomplete: function(cmp){
                                    if (!cmp.noReload) instance.filterChange(cmp);
                                }
                            }
                        });

                    // Else if current filter is defined for 'combo' or 'check' field
                    } else if (parseInt(indi.trail.item().filters[i].foreign('fieldId').relation) || ['check', 'combo']
                        .indexOf(indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias) != -1) {

                        // Get the label
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign('fieldId').title);

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
                                forId: indi.trail.item().filters[i].foreign('fieldId').alias + '-keyword',
                                margin: '0 5 0 0',
                                padding: '1 0 1 0',
                                cls: 'i-filter-combo-label',
                                border: 0
                            }, {
                                id: filterCmpId,
                                contentEl: filterCmpId + '-combo',
                                border: 0,
                                multiple: indi.trail.item().filters[i].foreign('fieldId').storeRelationAbility == 'many',
                                boolean: indi.trail.item().filters[i].foreign('fieldId').storeRelationAbility == 'none',
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
                                        indi.combo.filter.clearCombo(
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
            }

            /**
             * Adjust grid columns widths, for widths to match column contents
             */
            this.adjustGridColumnsWidths = function() {
                var grid = Ext.getCmp('i-center-center-wrapper').getComponent(0);
                var columnWidths = {};
                var totalColumnsWidth = 0;
                for(var i in grid.columns) {
                    if (grid.columns[i].hidden == false) {
                        columnWidths[i] = indi.metrics.getWidth(grid.columns[i].text) + 12;
                        if (grid.columns[i].dataIndex == indi.trail.item().section.defaultSortFieldAlias) {
                           columnWidths[i] += 12;
                        }
                        for (var j = 0; j < grid.store.data.items.length; j++) {
                            var cellWidth = indi.metrics.getWidth(grid.store.data.items[j].data[grid.columns[i].dataIndex]) + 12;
                            if (cellWidth > columnWidths[i]) columnWidths[i] = cellWidth;
                        }
                        totalColumnsWidth += columnWidths[i];
                    }
                }
                var totalGridWidth = grid.getWidth();
                if (totalColumnsWidth < totalGridWidth) {
                    var first = true;
                    for(i in columnWidths) {
                        if (first) {
                            first = false;
                        } else {
                            grid.columns[i].width = columnWidths[i];
                        }
                    }
                } else {
                    var smallColumnsWidth = 0;
                    var first = true;
                    for(var i in columnWidths) {
                        if (first) {
                            first = false;
                        } else if (columnWidths[i] <= 100) {
                            smallColumnsWidth += columnWidths[i];
                        }
                    }
                    var firstColumnWidth = Math.ceil(totalGridWidth*instance.options.grid.firstColumnWidthFraction);
                    var percent = (totalGridWidth-firstColumnWidth-smallColumnsWidth)/(totalColumnsWidth-columnWidths[1]-smallColumnsWidth);
                    var first = true;
                    for(i in columnWidths) {
                        if (first) {
                            grid.columns[i].width = firstColumnWidth;
                            first = false;
                        } else if (columnWidths[i] > 100) {
                            grid.columns[i].width = columnWidths[i] * percent;
                        } else {
                            grid.columns[i].width = columnWidths[i];
                        }
                    }
                }
            }

            /**
             * Set up and return store full request string (but without paging params)
             */
            this.lastRequest = function(){

                // Get the initial uri
                var url = instance.store.getProxy().url;

                // Declare an array for $_GET params
                var get = [];

                // If filters were used during last store request, we retrieve info about, encode and append it to 'get'
                if (instance.store.getProxy().extraParams.search)
                    get.push('search=' + encodeURIComponent(instance.store.getProxy().extraParams.search));

                // If keyword was used during last store request, we retrieve info about, encode and append it to 'get'
                if (instance.store.getProxy().extraParams.keyword)
                    get.push('keyword=' + encodeURIComponent(instance.store.getProxy().extraParams.keyword));

                // If sorters were used during last store request, we retrieve info about, encode and append it to 'get'
                if (instance.store.getSorters().length)
                    get.push('sort=' + encodeURIComponent(JSON.stringify(instance.store.getSorters())));

                // Return the full url string
                return url + (get.length ? '?' + get.join('&') : '');
            }

            /**
             * Prepare the array, containing data objects for keyword toolbar components
             *
             * @return {Array}
             */
            this.keywordToolbarItems = function(){

                // Items array
                var items = [];

                // Check if 'save' and 'form' actions are allowed
                var canSave = false, canForm = false, canAdd = indi.trail.item().section.disableAdd == '0';
                for (var i = 0; i < indi.trail.item().actions.length; i++) {
                    if (indi.trail.item().actions[i].alias == 'save') canSave = true;
                    if (indi.trail.item().actions[i].alias == 'form') canForm = true;
                }

                // 'Create' button will be added only if it was not switched off
                // in section config and if 'save' and 'form' actions are allowed
                if (canForm && canSave && canAdd) {

                    items.push({
                        id: 'i-section-' +indi.trail.item().section.alias + '-action-index-button-add',
                        tooltip: indi.lang.I_CREATE,
                        iconCls: 'i-btn-icon-create',
                        actionAlias: 'form',
                        handler: function(){
                            indi.load(
                                indi.trail.item().section.href +
                                    this.actionAlias + '/ph/' + Indi.trail.item().section.primaryHash + '/'
                            );
                        }
                    });
                }

                // Fulfil items array with other actions
                var iconA = ['form', 'delete', 'save', 'toggle', 'up', 'down'];

                for (var i = 0; i < indi.trail.item().actions.length; i++) {
                    if (indi.trail.item().actions[i].display == 1) {

                        // Basic action object
                        var item = {
                            id: 'i-section-' +indi.trail.item().section.alias + '-action-index-button-' +
                                indi.trail.item().actions[i].alias,
                            text: indi.trail.item().actions[i].title,
                            action: indi.trail.item().actions[i],
                            actionAlias: indi.trail.item().actions[i].alias,
                            rowRequired: indi.trail.item().actions[i].rowRequired,
                            javascript: indi.trail.item().actions[i].javascript,
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
                                        title: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                                        msg: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.WARNING
                                    });

                                // Run the handler
                                } else {
                                    eval(this.javascript);
                                }
                            }
                        };

                        // Setup iconCls property, if need
                        if(iconA.indexOf(indi.trail.item().actions[i].alias) != -1) {
                            item.iconCls = 'i-btn-icon-' + indi.trail.item().actions[i].alias;
                            item.text = '';
                            item.tooltip = indi.trail.item().actions[i].title;
                        }

                        // Put to the actions stack
                        items.push(item);
                    }
                }

                // Append separator
                items.push('-');

                // Append subsections list
                if (indi.trail.item().sections.length) items.push(new indi.layout.ux.Subsections({
                    toolbarId: 'i-section-' + indi.trail.item().section.alias + '-action-' + indi.trail.item().action.alias + '-toolbar-keyword',
                    id: 'i-section-' + indi.trail.item().section.alias + '-action-' + indi.trail.item().action.alias + '-subsections',
                    tooltip: {
                        html: indi.lang.I_NAVTO_NESTED,
                        hideDelay: 0,
                        showDelay: 1000,
                        dismissDelay: 2000
                    },
                    itemClick: function(item){

                        // Get selection
                        var selection = Ext.getCmp('i-center-center-wrapper').getComponent(0).getSelectionModel().getSelection();

                        // If no selection - show a message box
                        if (selection.length == 0) {
                            console.log('ss');
                            Ext.MessageBox.show({
                                title: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                                msg: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING

                                // Else load the subsection
                            });
                        } else if (item.getAttribute('alias'))
                            indi.load(indi.pre + '/' + item.getAttribute('alias') + '/index/id/' + selection[0].data.id + '/' +
                                'ph/'+Indi.trail.item().scope.hash+'/aix/' + (selection[0].index + 1)+'/');
                    }
                }));

                // We figure that other items should be right-aligned at the keyword toolbar
                items.push('->');

                // Append fast search keyword field component to the items stack
                items.push({
                    xtype: 'textfield',
                    fieldLabel: indi.lang.I_ACTION_INDEX_KEYWORD_LABEL,
                    //hideEmptyLabel: true,
                    labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_KEYWORD_LABEL),
                    labelClsExtra: 'i-action-index-keyword-toolbar-keyword-label',
                    labelSeparator: '',
                    value: indi.trail.item().scope.keyword ? indi.urldecode(indi.trail.item().scope.keyword) : '',
                    width: 100 + indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_KEYWORD_LABEL),
                    //minWidth: 50,// + indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_KEYWORD_LABEL),
                    height: 19,
                    cls: 'i-form-text',
                    margin: '0 0 0 5',
                    id: 'i-section-' + indi.trail.item().section.alias + '-action-index-keyword',
                    listeners: {
                        change: function(){
                            clearTimeout(instance.timeout);
                            instance.timeout = setTimeout(function(){
                                instance.filterChange({});
                            }, 500);
                        }
                    }
                });

                return items;
            };


            /**
             * Prepare and return an array with panel header tools
             *
             * @return {Array}
             */
            this.tools = function(){

                // Declare tools array
                var tools = [];

                // We add the filter-reset tool only if there is at least one filter defined for current section
                if (indi.trail.item().filters.length) {

                    // Append tool data object to the 'tools' array
                    tools.push({
                        type: 'search',
                        cls: 'i-tool-search-reset',
                        handler: function(event, target, owner, tool){

                            // Prepare a prefix for filter component ids
                            var filterCmpIdPrefix = 'i-section-' + indi.trail.item().section.alias + '-action-index-filter-';

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
                                for (var i = 0; i < indi.trail.item().filters.length; i++) {

                                    // Define a shortcut for filter field alias
                                    var alias =  indi.trail.item().filters[i].foreign('fieldId').alias;

                                    // Shortcut for control element, assigned to filter field
                                    var control = indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias;

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
                                title: indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_TITLE,
                                msg: indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_MSG,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.INFO
                            });
                        }
                    });
                }

                // Return array of tools
                return tools;
            }

            /**
             * Prepare and return a sorters for instance.store
             *
             * @return {Array}
             */
            this.storeSorters = function(){

                // If we have sorting params, stored in scope - we use them
                if (indi.trail.item().scope.order && eval(indi.trail.item().scope.order).length)
                    return eval(indi.trail.item().scope.order);

                // Else we use current section's default sorting params, if specified
                else if (indi.trail.item().section.defaultSortField)
                    return [{
                        property : indi.trail.item().section.defaultSortFieldAlias,
                        direction: indi.trail.item().section.defaultSortDirection
                    }];

                // Else no sorting at all
                return [];
            }

            /**
             * Determines instance.store's current page. At first it will try to get it from indi.trail.item().scope, at it it fails
             *  - return 1
             *
             * @return {*}
             */
            this.storeCurrentPage = function(){
                if (indi.trail.item().scope.page)
                    return parseInt(indi.trail.item().scope.page);

                return 1;
            }

            this.highlightGridFilteredColumns = function(){

                /*var filteredColumnA = ['title'];
                var columnIdPrefix = 'i-section-' + indi.trail.item().section.alias + '-action-index-grid-column-';
                var cellClassPrefix = '.x-grid-cell-';


                for (var i = 0; i < filteredColumnA.length; i++) {
                    var columnId = columnIdPrefix + filteredColumnA[i];
                    if (!$(cellClassPrefix + columnId).hasClass('i-grid-filtered-column-cell'))
                        $(cellClassPrefix + columnId).addClass('i-grid-filtered-column-cell');
                }*/
            }

            /**
             * Callback for store load, will be fired if current section type = 'grid'
             */
            this.storeLoadCallbackGrid = function() {

                // Get the grid panel object
                var grid = Ext.getCmp('i-section-' + indi.trail.item().section.alias +
                    '-action-' + indi.trail.item().action.alias + '-' + indi.trail.item().section.type);

                // Set the focus on grid, to automatically provide an ability to use keyboard
                // cursor to navigate through rows
                grid.getView().focus();

                // Setup last row autoselection, if need
                if (Indi.trail.item().scope.aix) {

                    // Calculate row index value, relative to current page
                    var index = parseInt(indi.trail.item().scope.aix) - 1 - (parseInt(indi.trail.item().scope.page) - 1) *
                        parseInt(indi.trail.item().section.rowsOnPage);

                    // If such row (row at that index) exists in grid - selectit
                    if (grid.store.getAt(index)) grid.selModel.select(index, true);
                }

                // Add keyboard event handelers
                grid.body.addKeyMap({
                    eventName: "keyup",
                    binding: [{
                        key: Ext.EventObject.ENTER,
                        fn:  function(){
                            if (Ext.getCmp('i-section-' + indi.trail.item().section.alias + '-action-index-button-form'))
                                Ext.getCmp('i-section-' + indi.trail.item().section.alias + '-action-index-button-form')
                                    .handler();
                        }
                    }]
                });

                // Run custom callback function
                instance.options.grid.storeLoadCallback();

                // Adjust grid column widths
                instance.adjustGridColumnsWidths();
            }

            /**
             * Extjs's Store object for current section
             *
             * @type {*}
             */
            this.store = Ext.create('Ext.data.Store', {
                fields: instance.storeFields(),
                sorters: instance.storeSorters(),
                method: 'POST',
                pageSize: indi.trail.item().section.rowsOnPage,
                remoteSort: true,
                currentPage: instance.storeCurrentPage(),
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
                        instance.filterChange({noReload: true});
                    },
                    load: function(store){
                        var type = indi.trail.item().section.type;
                        instance['storeLoadCallback'+type.charAt(0).toUpperCase() + type.slice(1)]();
                        indi.trail.item().scope = store.proxy.reader.rawData.scope;
                    }
                }
            });

            /**
             * Build the grid
             */
            this.buildGrid = function() {

                // Prepare object with grid properties
                var gridO = {
                    id: 'i-section-' + indi.trail.item().section.alias + '-action-index-grid',
                    border: 0,
                    store: instance.store,
                    columns: instance.gridColumns(),
                    viewConfig: {
                        getRowClass: function (row) {
                            if (row.raw._system && row.raw._system.disabled)
                                return 'i-grid-row-disabled';
                        },
                        loadingText: Ext.LoadMask.prototype.msg
                    },
                    listeners: {
                        beforeselect: function (selectionModel, row) {
                            if (row.raw._system && row.raw._system.disabled)
                                return false;
                        },
                        selectionchange: function (selectionModel, selectedRows) {
                            if (selectedRows.length > 0)
                                Ext.Array.each(selectedRows, function (row) {
                                    if (row.raw._system && row.raw._system.disabled)
                                        selectionModel.deselect(row, true);
                                });
                        },
                        itemdblclick: function() {
                            if (Ext.getCmp('i-section-' + indi.trail.item().section.alias + '-action-index-button-form'))
                                Ext.getCmp('i-section-' + indi.trail.item().section.alias + '-action-index-button-form')
                                    .handler();
                        }
                    },

                    // Padging toolbar
                    bbar: new Ext.PagingToolbar({
                        store: instance.store,
                        displayInfo: true,
                        items:instance.gridBbarItems()
                    })
                };

                // Create and return ExtJs grid panel
                return Ext.create('Ext.grid.Panel', $.extend(gridO, instance.options.grid));
            }

            /**
             * Prepare and return array of items, that are to be placed at grid paging bar
             *
             * @return {Array}
             */
            instance.gridBbarItems = function() {

                // Init items array with a separator as first item
                var items = ['-'];

                // Push the excel export button to item array
                items.push({
                    text: '',
                    iconCls: 'i-btn-icon-xls',
                    tooltip: indi.lang.I_EXPORT_EXCEL,
                    handler: function(){

                        // Start preparing request string
                        var request = instance.lastRequest().replace('json/1/', 'excel/1/');

                        // Get grid component id
                        var gridCmpId = 'i-section-' + indi.trail.item().section.alias + '-action-index-grid';

                        // Get grid columns
                        var gridColumnA = Ext.getCmp(gridCmpId).columns;

                        // Define and array for storing column info, required for excel columns building
                        var excelColumnA = [];

                        // Setup a multiplier, for proper column width calculation
                        var multiplier = screen.availWidth/Ext.getCmp(gridCmpId).getWidth();

                        // Collect needed data about columns
                        for (var i = 0; i < gridColumnA.length; i++) {
                            if (gridColumnA[i].hidden == false) {

                                // Prepare the data object for excel column
                                var excelColumnI = {
                                    title: gridColumnA[i].text,
                                    dataIndex: gridColumnA[i].dataIndex,
                                    align: gridColumnA[i].align,
                                    width: Math.ceil(gridColumnA[i].getWidth() * multiplier)
                                };

                                // If current grid column - is column, currently used for sorting,
                                // we pick sorting direction, and column title width
                                if (gridColumnA[i].sortState) {
                                    excelColumnI = $.extend(excelColumnI, {
                                        sortState: gridColumnA[i].sortState.toLowerCase(),
                                        titleWidth: indi.metrics.getWidth(gridColumnA[i].text)
                                    })
                                }

                                // Push the data object to array
                                excelColumnA.push(excelColumnI);
                            }
                        }

                        // Set column info as a request variable
                        var columns = 'columns=' + encodeURIComponent(JSON.stringify(excelColumnA));

                        // Check if there is color-filters within used filters, and if so, we append a _xlsLabelWidth
                        // property for each object, that is representing a color-filter in request
                        for (var i = 0; i < indi.trail.item().filters.length; i++) {
                            if (indi.trail.item().filters[i].foreign('fieldId')._foreign.elementId.alias == 'color') {
                                var reg = new RegExp('(%7B%22' + indi.trail.item().filters[i].foreign('fieldId').alias + '%22%3A%5B[0-9]{1,3}%2C[0-9]{1,3}%5D)');
                                request = request.replace(reg, '$1' + encodeURIComponent(',"_xlsLabelWidth":"' + indi.metrics.getWidth(indi.trail.item().filters[i].foreign('fieldId').title + '&nbsp;-&raquo;&nbsp;') + '"'));
                            }
                        }

                        // Do the request
                        window.location = request + '&' + columns;
                    }
                });

                // Return items
                return items;
            }

            /**
             * Build the wrapper panel with the listing pagel inside it, depending on indi.trail.item().type
             */
            this.build = function(){

                // Type of the section
                var type = indi.trail.item().section.type;

                // Create the main panel
                Ext.create('Ext.Panel', {
                    id: 'i-center-center-wrapper',
                    renderTo: 'i-center-center-body',
                    border: 0,
                    height: '100%',
                    closable: true,
                    layout: 'fit',
                    title: indi.trail.item().section.title,
                    items: [instance['build'+type.charAt(0).toUpperCase() + type.slice(1)]()],
                    tools: instance.tools(),
                    dockedItems: instance.toolbars(),
                    listeners: {
                        afterrender: function(){
                            instance.store.load();
                        }
                    }
                });
            }

            /**
             * The enter point.
             */
            this.run = function() {

                // Provide an ability for javascript to be executed, if specified
                if (indi.trail.item().section.javascript) eval(indi.trail.item().section.javascript);

                // Call the callbacks
                if (indi.callbacks && indi.callbacks[instance.componentName] && indi.callbacks[instance.componentName].length) {
                    for (var i = 0; i < indi.callbacks[instance.componentName].length; i++) {
                        indi.callbacks[instance.componentName][i]();
                    }
                }

                // View type. Later also 'tile' and 'calendar' will be available
                indi.trail.item().section.type = 'grid';

                // Build
                instance.build();
            }
        }
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof indi.proto !== 'undefined' &&
                typeof indi.proto.combo !== 'undefined' &&
                typeof top.window.Indi.proto.combo.sibling !== 'undefined') {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));
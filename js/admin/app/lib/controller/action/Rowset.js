/**
 * Base class for all controller actions instances, that operate with rowsets
 */
Ext.define('Indi.lib.controller.action.Rowset', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset',

    // @inheritdoc
    extend: 'Indi.lib.controller.action.Action',

    // @inheritdoc
    mcopwso: ['store', 'rowset', 'south', 'panel', 'rowsetPlugin$Cellediting'],

    // @inheritdoc
    panel: {

        // @inheritdoc
        xtype: 'actionrowset',

        /**
         * Tools special config
         */
        tools: [{alias: 'fundock'}, {alias: 'reset'}],

        /**
         * Docked items special config
         */
        docked: {
            default: {minHeight: 26},
            items: [{alias: 'filter'}, {alias: 'master'}],
            inner: {
                master: [{alias: 'actions'}, {alias: 'nested'}, '->', {alias: 'keyword'}]
            }
        },

        /**
         * Instance of {xtype: window}, created for filters toolbar to be injected in
         */
        filterWin: null,

        // @inheritdoc
        listeners: {
            resize: function(c, w, h, ow, oh) {
                var me = Ext.getCmp(c.id.replace('-wrapper', '')),
                    f = Ext.getCmp(c.id.replace('-wrapper', '-toolbar$filter')),
                    win = c.getWindow();

                // If filters toolbar has no items (e.g. there is no filters) - return
                if (f.empty || !win || !win.down('tool[alias="fundock"]') || !f.lastBox) return;

                // If filters toolbar's height wastes more than 20% of total height, available for wrapper-panel
                if (f.lastBox.height / c.getHeight() > 0.2) {
                    if (f.up('[isWrapper]')) f.hide(); else if (!c.filterWin.hidden) {
                        if (c.filterWin) c.filterWin.maxWidth = c.getWidth() - 30;
                        c.filterWin.setWidth(Indi.viewport.getWidth() - 30);
                        c.filterWin.setHeight(f.getHeight() + 1);
                        c.filterWin.center();
                    }
                    win.down('tool[alias="fundock"]').show();
                } else {
                    win.down('tool[alias="fundock"]').hide();
                    if (f.up('[hasCtx]')) {
                        c.insertDocked(0, f);
                        if (c.filterWin) c.filterWin.close();
                    } else if (f.hidden) {
                        f.show();
                    }
                }
            }
        }
    },

    /**
     * Extjs's Store config object for current section
     *
     * @type {*}
     */
    store: {
        method: 'POST',
        remoteSort: true,
        listeners: {
            beforeload: function(){
                this.$ctx.filterChange({noReload: true});
            },
            load: function(){
                var ctx = this.ctx() || this.$ctx;
                ctx.storeLoadCallbackDefault.apply(ctx, arguments);
                ctx.storeLoadCallback.apply(ctx, arguments);
            }
        },
        ctx: function() {
            return Ext.getCmp(this.storeId.replace('-store', ''));
        }
    },

    /**
     * This function provide batch-adjustment ability for each row within the store
     */
    storeLoadCallbackDataRowAdjust: Ext.emptyFn,

    /**
     * Get store, that current action is dealing with
     *
     * @return {*}
     */
    getStore: function() {
        return Ext.getStore(this.bid() + '-store');
    },

    /**
     * Handler for any filter change
     *
     * @param cmp Component, that fired filterChange
     */
    filterChange: function(cmp){
        var me = this, extraParams = {};

        // Get all filter components
        var filterCmpA = Ext.getCmp(me.panel.id).query('[isFilter][name]');

        // If filters toolbar was undocked from main panel into a window - try search within that window
        if (!filterCmpA.length && Ext.getCmp(me.panel.id).filterWin)
            filterCmpA = Ext.getCmp(me.panel.id).filterWin.query('[isFilter][name]');

        me.onFilterChange(cmp, filterCmpA);

        // Declare and fulfil an array with properties, available for each row in the rowset
        var columnA = []; if (me.ti().gridFields) for (i = 0; i < me.ti().gridFields.length; i++) columnA.push(me.ti().gridFields[i].alias);

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
        for (var i = 0; i < filterCmpA.length; i++) {

            // We do not involve values of hidden or disabled filter components in request query building
            if ((filterCmpA[i].hidden && !filterCmpA[i].isImportantDespiteHidden) || (filterCmpA[i].disabled && filterCmpA[i].field && filterCmpA[i].field.satellite)) continue;

            // Define a shortcut for filter filed alias
            var alias = filterCmpA[i].name;

            // Get current filter value
            var value = filterCmpA[i].getValue();

            // If current filter is filter for color-field, and it's value is [0, 360], we set 'value' variable
            // as '' (empty string) because such value for color field filter mean that filter is not used
            if (filterCmpA[i].xtype == 'multislider' && JSON.stringify(filterCmpA[i].getValue()) == '[0,360]')
                value = '';

            // If value is not empty
            if (value + '' != '' && value !== null) {

                // Prepare param object for storing current filter value. We will be using separate objects for
                // each used filter, e.g [{property1: "value1"}, {property2: "value2"}], instead of single object
                // {property1: "value1", property2: "value2"}, because it's the way of how extjs use it, for
                // passing sorting params within store request, so here we just do by the same way
                var paramO = {};

                // If current filter is a ext's datefield components
                if (filterCmpA[i].xtype == 'datefield') {

                    // If format of date, used in ext's datafield component - differs from 'Y-m-d'
                    if (filterCmpA[i].format != 'Y-m-d') {

                        // We get the raw value in that format, convert it back to 'Y-m-d' format
                        // and assign to paramO's object certain property as a current filter value
                        paramO[alias] = Ext.Date.format(
                            Ext.Date.parse(filterCmpA[i].getRawValue(), filterCmpA[i].format),
                            'Y-m-d'
                        );

                    // Else we just assign the value to param's object certain property as a current filter value
                    } else paramO[alias] = filterCmpA[i].getRawValue();

                // Else if current filter is not a ext's datetime component
                } else {

                    // We just assign the value to param's object certain property as a current filter value, too
                    paramO[alias] = filterCmpA[i].getValue();
                }

                // Push the paramO object to the param stack
                paramA.push(paramO);

                // If current filter field alias is within an array of available properties (columns)
                for (var j =0; j < columnA.length; j++)
                    if (columnA[j] == alias.replace(/-(g|l)te$/, '') &&
                        usedFilterAliasesThatHasGridColumnRepresentedByA.indexOf(
                            alias.replace(/-(g|l)te$/, '')) == -1)

                        // We remember that, by pushing curren filter field alias to the
                        usedFilterAliasesThatHasGridColumnRepresentedByA.push(alias.replace(/-(g|l)te$/, ''));

            }
        }

        // Apply collected used filter alises and their values as a this.getStore().proxy.extraParams property
        extraParams.search = JSON.stringify(paramA);

        // Get id of the keyword component
        var keywordCmpId = me.bid() + '-toolbar-master-keyword';

        // Get the value of keyword component, if component is not disabled
        var keyword = Ext.getCmp(keywordCmpId) && Ext.getCmp(keywordCmpId).disabled == false &&
            Ext.getCmp(keywordCmpId).getValue() ? Ext.getCmp(keywordCmpId).getValue() : '';

        // Append `keyword` property to the request extra params
        if (keyword) extraParams.keyword = keyword;

        // If me.rowsetSummary is a function
        if (Ext.isFunction(me.rowsetSummary)) {

            // Get summary definitions
            var summary = me.rowsetSummary();

            // Append them to extraParams
            if (Ext.isObject(summary) && Ext.Object.getSize(summary))
                extraParams.summary = JSON.stringify(summary);
        }

        // Set extra params for store's proxy
        me.getStore().getProxy().extraParams = extraParams;

        // Adjust an 'url' property of  this.getStore().proxy object, to apply keyword search usage
        me.getStore().getProxy().url = Indi.pre + '/' + me.ti().section.alias + '/' + me.ti().action.alias + '/' +
            (me.ti(1).row ? 'id/' + me.ti(1).row.id + '/' : '') + 'format/json/';

        // Disable keyword component, if all available properties are already involved in search by
        // corresponding filters usage
        if (Ext.getCmp(keywordCmpId))
            Ext.getCmp(keywordCmpId).setDisabled(usedFilterAliasesThatHasGridColumnRepresentedByA.length == columnA.length);

        // Ensure page size to be fully dependent on me.ti().section.rowsOnPage
        me.getStore().pageSize = me.getStore().getProxy().extraParams.limit = me.ti().section.rowsOnPage || me.ti().section.defaultLimit;
        if (me.getStore().lastOptions) me.getStore().lastOptions.limit = me.getStore().pageSize;

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
            me.getStore().currentPage = 1;
            if (me.getStore().lastOptions) {
                me.getStore().lastOptions.page = 1;
                me.getStore().lastOptions.start = 0;
            }

            // If used filter is a combobox or multislider, we reload store data immideatly
            if (['combobox', 'combo.filter', 'multislider'].indexOf(cmp.xtype) != -1) {
                me.preventViewFocus = true;
                me.getStore().reload();

            // Else if used filter is not a datefield, or is, but it's value matches proper date format or
            // value is empty, we reload store data with a 500ms delay, because direct typing is allowed in that
            // datefield, so it's better to reload after user has finished typing.
            } else if (cmp.xtype != 'datefield' || (/^([0-9]{4}-[0-9]{2}-[0-9]{2}|[0-9]{2}\.[0-9]{2}\.[0-9]{4})$/
                .test(cmp.getRawValue()) || !cmp.getRawValue().length)) {
                clearTimeout(me.getStore().timeout);
                me.getStore().timeout = setTimeout(function(){
                    me.preventViewFocus = true;
                    me.getStore().reload();
                }, 500);
            }
        }
    },

    /**
     * Empty function
     */
    onFilterChange: Ext.emptyFn,

    /**
     * Function is to return an object, containing summaries definitions. Example:
     *
     * rowsetSummary: function() {
     *     return {
     *         sum: ['field1', 'field2'],
     *         min: ['field3'],
     *         max: ['field4', 'field5', 'field6']
     *     }
     * }
     *
     * Here, 'sum', 'min' and 'max' are aggregation types, and 'field1', 'field2' etc are fields, that aggregations
     * should be performed and calculated on. This (- 'rowsetSummary') function is a part of a implementation for a
     * server-side summaries/aggregations calculations abilities. This function is for overriding in child classes.
     */
    rowsetSummary: Ext.emptyFn,

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Setup `route` property
        if (config.route) me.route = config.route;

        // Setup main panel title
        me.panel.title = me.ti().action.alias == 'index' ? me.ti().section.title : me.ti().action.title;

        // Merge configs
        me.mergeParent(config);

        // Setup store config
        me.store = Ext.merge({
            id: me.bid() + '-store',
            fields: me.storeFieldA(),
            table: me.ti().model.tableName,
            sorters: me.storeSorters(),
            pageSize: me.ti().section.rowsOnPage,
            currentPage: me.storeCurrentPage(),
            $ctx: me,
            proxy: new Ext.data.HttpProxy({
                method: 'POST',
                reader: {
                    type: 'json',
                    root: 'blocks',
                    totalProperty: 'totalCount',
                    idProperty: 'id'
                }
            })
        }, me.store);

        // Set group field
        if (me.ti().section.groupBy && me.ti().fields.r(me.ti().section.groupBy))
            me.store.groupField = me.ti().fields.r(me.ti().section.groupBy).alias;

        // Create store
        Ext.create('Ext.data.Store', me.store);

        // Call parent
        me.callParent(arguments);
    },

    /**
     * Build and return array of panel tools
     *
     * @return {Array}
     */
    panelToolA: function() {
        return this.push(this.panel.tools, 'panelTool', false, function(itemI){
            return itemI.type ? itemI: null;
        });
    },

    /**
     * Check whether filter/keyword search is currently used.
     *
     * @return {Boolean}
     */
    atLeastOneFilterIsUsed: function() {
        var me = this, filterCmpIdPrefix, j, l, i, v, alias, control, atLeastOneFilterIsUsed = false,
            loopA, limits, keywordC;

        // Prepare a prefix for filter component ids
        filterCmpIdPrefix = me.bid() + '-toolbar$filter$';

        // We define an array of functions, first within which will check if at least one filter is used
        // and if so, second will do a store reload
        loopA = [function(cmp, control){
            //if (cmp.isImportantDespiteHidden) return;
            if (control == 'color') {
                if (cmp.getValue().join() != '0,360') atLeastOneFilterIsUsed = true;
            } else {
                if ([null, ''].indexOf(cmp.getValue()) == -1) {
                    if (JSON.stringify(cmp.getValue()) != '[""]') atLeastOneFilterIsUsed = true;
                }
            }
        }];

        // We iterate throgh filter twice - for each function within loopA array
        for (l = 0; l < loopA.length; l++) {

            // We prevent unsetting filters values if they are already empty
            if (l == 1 && atLeastOneFilterIsUsed == false) break;

            // For each filter
            for (i = 0; i < me.ti().filters.length; i++) {

                // Define a shortcut for filter field alias
                alias =  me.ti().filters[i].foreign('fieldId').alias;

                // Shortcut for control element, assigned to filter field
                control = me.ti().filters[i].foreign('fieldId').foreign('elementId').alias;

                // If current filter is a range-filter, we reset values for two filter components, that
                // are representing min and max values
                if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                    // Range filters limits's postfixes
                    limits = ['gte', 'lte'];

                    // Setup empty values for range filters
                    for (j = 0; j < limits.length; j++) loopA[l](Ext.getCmp(filterCmpIdPrefix + alias + '-' + limits[j]));

                    // Else we reset one filter component
                } else if (control == 'color') {

                    // Resetted values for color multislider filter
                    v = [0, 360];

                    // Set a value for each multislider thumb
                    for (j = 0; j < v.length; j++) loopA[l](Ext.getCmp(filterCmpIdPrefix + alias), control);

                    // Else set by original way
                } else loopA[l](Ext.getCmp(filterCmpIdPrefix + alias));
            }
        }

        // Here we handle case, then we have keyword-search field injected into
        // filters docked panel, rather than in master docked panel
        var mtb; if (mtb = Ext.getCmp(me.bid() + '-toolbar-master')) {
            keywordC = mtb.down('[isKeyword]');
            if (keywordC && keywordC.getValue()) atLeastOneFilterIsUsed = true;
        }

        // Return
        return atLeastOneFilterIsUsed;
    },

    /**
     * Reset all rowset filters and keyword
     */
    filterReset: function(noReload) {
        var me = this, resetFn, keywordC, filterCmpIdPrefix, v, limits, control, alias, i, j;

        // If filter/keyword search is not currently used
        if (!me.atLeastOneFilterIsUsed()) {

            // Show message box
            if (!noReload) Ext.MessageBox.show({
                title: Indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_TITLE,
                msg: Indi.lang.I_ACTION_INDEX_FILTERS_ARE_ALREADY_EMPTY_MSG,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.INFO
            });

            // Return
            return;
        }

        // Prepare a prefix for filter component ids
        filterCmpIdPrefix = me.bid() + '-toolbar$filter$';

        // We define an array of functions, first within which will check if at least one filter is used
        // and if so, second will do a store reload
        resetFn = function(cmp, control){
            if (control == 'color') {
                cmp.setValue(0, 0, false);
                cmp.setValue(1, 360, false);
            } else {
                cmp.setValue('');
            }
        };

        // For each filter
        for (i = 0; i < me.ti().filters.length; i++) {

            // Define a shortcut for filter field alias
            alias =  me.ti().filters[i].foreign('fieldId').alias;

            // Shortcut for control element, assigned to filter field
            control = me.ti().filters[i].foreign('fieldId').foreign('elementId').alias;

            // If current filter is a range-filter, we reset values for two filter components, that
            // are representing min and max values
            if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                // Range filters limits's postfixes
                limits = ['gte', 'lte'];

                // Setup empty values for range filters
                for (j = 0; j < limits.length; j++) {
                    Ext.getCmp(filterCmpIdPrefix + alias + '-' + limits[j]).noReload = true;
                    resetFn(Ext.getCmp(filterCmpIdPrefix + alias + '-' + limits[j]));
                    Ext.getCmp(filterCmpIdPrefix + alias + '-' + limits[j]).noReload = false;
                }

                // Else we reset one filter component
            } else if (control == 'color') {

                // Resetted values for color multislider filter
                v = [0, 360];

                // Set a value for each multislider thumb
                for (j = 0; j < v.length; j++) {
                    Ext.getCmp(filterCmpIdPrefix + alias).noReload = true;
                    resetFn(Ext.getCmp(filterCmpIdPrefix + alias), control);
                    Ext.getCmp(filterCmpIdPrefix + alias).noReload = false;
                }

            // Else set by original way
            } else {
                if (Ext.getCmp(filterCmpIdPrefix + alias).allowClear !== false) {
                    Ext.getCmp(filterCmpIdPrefix + alias).noReload = true;
                    resetFn(Ext.getCmp(filterCmpIdPrefix + alias));
                    Ext.getCmp(filterCmpIdPrefix + alias).noReload = false;
                }
            }
        }

        // Here we handle case, then we have keyword-search field injected into
        // filters docked panel, rather than in master docked panel
        var mtb; if (mtb = Ext.getCmp(me.bid() + '-toolbar-master')) {
            keywordC = mtb.down('[isKeyword]');
            if (keywordC && keywordC.getValue()) keywordC.setValue('');
        }

        // Reload store for empty filter values to be picked up.
        if (!noReload) me.filterChange({});
    },

    /**
     * Filters reset tool config builder
     *
     * @return {Object}
     */
    panelTool$Reset: function() {
        var me = this;

        // We add the filter-reset tool only if there is at least one filter defined for current section
        if (!me.ti().filters.length) return null;

        // Append tool data object to the 'tools' array
        return {
            type: 'search',
            cls: 'i-tool-search-reset',
            handler: function() {
                me.filterReset();
            }
        }
    },

    /**
     * Get filter window. If if not yet exists - preliminary create it
     *
     * @return {*}
     */
    getFilterWindow: function() {
        var me = this;

        // If filter window was not yet created - create it
        if (!Ext.getCmp(me.panel.id).filterWin) Ext.getCmp(me.panel.id).filterWin = Ext.widget({
            xtype: 'window',
            frame: false,
            $ctx: me,
            hasCtx: true,
            frameHeader: false,
            layout: 'fit',
            style: 'background: transparent; border: 0;',
            bodyStyle: 'background: transparent; border-bottom-width: 0;',
            modal: true,
            width: Ext.getCmp(me.panel.id).getWidth() - 30,
            border: '1 1 2 1',
            padding: 0,
            closable: false,
            closeAction: 'hide',
            resizable: {
                handles: 'e w'
            },
            header: false,
            listeners: {
                afterrender: function(с) {
                    с.mon(Ext.getBody(), 'click', function(el, e){
                        с.close(с.closeAction);
                    }, с, {delegate: '.x-mask'});
                },
                resize: function(c, w, h, ow, oh) {
                    if (Ext.EventObject.getTarget() && Ext.EventObject.getTarget('.x-resizable-proxy')) {
                        if (w != ow) c.setHeight(c.down('[name="toolbar$filter"]').getHeight() + 1);
                    }
                }
            }
        });

        // Return
        return Ext.getCmp(me.panel.id).filterWin;
    },

    /**
     * Filters reset tool config builder
     *
     * @return {Object}
     */
    panelTool$Fundock: function() {
        var me = this;

        // We add the filter-reset tool only if there is at least one filter defined for current section
        if (!me.ti().filters.length) return null;

        // Append tool data object to the 'tools' array
        return {
            type: 'search',
            hidden: true,
            tooltip: 'Показать фильтры',
            handler: function() {
                var f = Ext.getCmp(me.bid() + '-toolbar$filter');
                Ext.getCmp(me.panel.id).removeDocked(f, false);
                me.getFilterWindow().show().center().addDocked(f.show(), 'top');
                me.getFilterWindow().center();
            }
        }
    },

    /**
     * Panel filter toolbar builder
     *
     * @return {Object}
     */
    panelDocked$Filter: function() {
        var me = this, hidden = !(me.ti().filters.length - me.ti().filters.select('master', 'toolbar').length) &&
            (!me.panel.docked.inner || !me.panel.docked.inner.filter || !me.panel.docked.inner.filter.length);

        // 'Filter' toolbar config
        return {
            xtype: 'filtertoolbar',
            ctx: me,
            hidden: hidden,
            id: me.bid() + '-toolbar$filter',
            cls: 'x-poppable',
            items: [{
                xtype:'fieldset',
                id: me.bid()+'-toolbar$filter-fieldset',
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
                items: me.panelDocked$FilterItemA(),
                listeners: {
                    afterrender: function(){
                        me.setFilterValues();
                    }
                }
            }]
        }
    },

    /**
     * Build and return array of filter toolbar items configs
     *
     * @return {Array}
     */
    panelDocked$FilterItemA: function(filters) {

        // Declare toolbar filter panel items array, and some additional variables
        var me = this, itemA = [], itemI, item$, moreItemA = [], eItem$,
            filters = filters || me.ti().filters.select(undefined, 'toolbar');

        // Fulfil items array
        for (var i = 0; i < filters.length; i++) {

            // Get default filter config
            itemI = me.panelDocked$Filter_Default(filters[i]);

            // Own element/prop, related to current filter
            eItem$ = 'panelDocked$Filter$' + Indi.ucfirst(filters[i].foreign('fieldId').alias);

            // Apply filter custom config
            if (Ext.isFunction(me[eItem$]) || Ext.isObject(me[eItem$])) {
                item$ = Ext.isFunction(me[eItem$]) ? me[eItem$](itemI) : me[eItem$];
                itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
            } else if (me[eItem$] === false) itemI = me[eItem$];

            // If item is non-empty/null/false/undefined
            if (itemI) {

                // If it has no `name` prop yet - setit up
                if (!itemI.name) itemI.name = filters[i].foreign('fieldId').alias;

                // Refresh label width
                if (itemI.fieldLabel) itemI.labelWidth = Indi.metrics.getWidth(itemI.fieldLabel);

                // Push into itemA array
                itemA = itemA.concat(itemI.length ? itemI: [itemI]);
            }
        }

        // Setup non-regular filters
        if (me.panel.docked && me.panel.docked.inner && me.panel.docked.inner.filter && me.panel.docked.inner.filter.length)
        moreItemA = me.push(me.panel.docked.inner.filter, 'panelDocked$Filter', false, function(itemI){
            return Ext.merge({
                listeners: {
                    change: function(cmp) {
                        me.filterChange(cmp);
                    }
                }
            }, itemI);
        });

        // Append them
        itemA = itemA.concat(moreItemA);

        // Return filter toolbar items
        return itemA;
    },

    /**
     * Builds and returns default/initial config for all filter panel items
     *
     * @return {Object}
     */
    panelDocked$Filter_Default: function(filter) {

        // Setup auxilliary variables
        var me = this, itemI, itemIDefault, control = filter.foreign('fieldId').foreign('elementId').alias;

        // Setup default filter config, builded upon filter field's xtype
        itemIDefault = 'panelDocked$FilterX' + Indi.ucfirst(control);
        if (typeof me[itemIDefault] == 'function') itemI = me[itemIDefault](filter);

        // Setup special `isFilter` property indicating that current component will be used as one of rowset filters
        if (Ext.isObject(itemI)) itemI = Ext.merge({isFilter: true}, itemI);

        // Return default config
        return itemI;
    },

    /**
     * Combo-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXCombo: function(filter) {

        // Setup auxilliary variables/shortcuts
        var me = this, field = filter.foreign('fieldId'), alias = field.alias, filterCmpId = me.bid() + '-toolbar$filter$'
            + alias, fieldLabel = filter.alt || field.title, row = me.ti().filtersSharedRow;

        // Push the special extjs component data object to represent needed filter. Component consists of
        // two hboxed components. First is extjs label component, and second - is setup to pick up
        // a custom DOM node as it's contentEl property. This DOM node is already prepared by non-extjs
        // solution, implemented in IndiEngine
        return {
            id: filterCmpId,
            xtype: 'combo.filter',
            fieldLabel : fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            name: alias,
            field: field,
            value: Ext.isNumeric(row[field.alias]) ? parseInt(row[field.alias]) : row[field.alias],
            subTplData: row.view(field.alias).subTplData,
            store: row.view(field.alias).store,
            multiSelect: parseInt(filter.any) || filter.foreign('fieldId').storeRelationAbility == 'many' ? true : false
        }
    },

    /**
     * Radio-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXRadio: function(filter) {
        return this.panelDocked$FilterXCombo(filter);
    },

    /**
     * Check-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXCheck: function(filter) {
        return this.panelDocked$FilterXCombo(filter);
    },

    /**
     * Multicheck-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXMulticheck: function(filter) {
        return this.panelDocked$FilterXCombo(filter);
    },

    /**
     * Keyword-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXString: function(filter) {

        // Setup auxilliary variables/shortcuts
        var me = this, alias = filter.foreign('fieldId').alias, fieldLabel = filter.alt || filter.foreign('fieldId').title;

        // 'String' item config
        return {
            xtype: 'textfield',
            id: me.bid() + '-toolbar$filter$' + alias,
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            hiddenName: alias,
            name: alias,
            width: 80 + Indi.metrics.getWidth(fieldLabel),
            margin: '0 5 4 2',
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) me.filterChange(cmp);
                }
            }
        }
    },

    /**
     * Textarea-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXTextarea: function(filter) {
        return this.panelDocked$FilterXString(filter);
    },

    /**
     * Html-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXHtml: function(filter) {
        return this.panelDocked$FilterXString(filter);
    },

    /**
     * Calendar-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXCalendar: function(filter) {

        // Setup auxilliary variables/shortcuts
        var me = this, alias = filter.foreign('fieldId').alias, filterCmpId = this.bid() + '-toolbar$filter$' + alias,
            dateFormat, fieldLabel, datefieldFrom, datefieldUntil;

        // Get date format
        dateFormat = filter.foreign('fieldId').params['display' +
            (filter.foreign('fieldId').foreign('elementId').alias == 'datetime' ? 'Date': '') + 'Format'] || 'Y-m-d';

        // Get the label for filter minimal value component
        fieldLabel = (filter.alt ?
            filter.alt :
            filter.foreign('fieldId').title) + ' ' +
            Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM;

        // Prepare the data for extjs datefield component, for use as control for filter minimal value
        datefieldFrom = {
            xtype: 'datefield',
            id: filterCmpId + '-gte',
            name: alias + '-gte',
            isFilter: true,
            isFrom: true,
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel) + 5,
            width: 90 + Indi.metrics.getWidth(fieldLabel),
            startDay: 1,
            validateOnChange: false,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) me.filterChange(cmp);
                }
            }
        };

        // Prepare the data for extjs datefield component, for use as control for filter maximal value
        datefieldUntil = {
            xtype: 'datefield',
            id: filterCmpId + '-lte',
            name: alias + '-lte',
            isFilter: true,
            isTill: true,
            fieldLabel: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO,
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
            width: 85 + Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
            startDay: 1,
            validateOnChange: false,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) me.filterChange(cmp);
                }
            }
        };

        // Append a number of format-related properties to the data objects
        Ext.merge(datefieldFrom, {format: dateFormat, ariaTitleDateFormat: dateFormat, longDayFormat: dateFormat});
        Ext.merge(datefieldUntil, {format: dateFormat, ariaTitleDateFormat: dateFormat, longDayFormat: dateFormat});

        // Append the extjs datefield components to filters stack, for minimum and maximum
        return [datefieldFrom, datefieldUntil];
    },

    /**
     * Datetime-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXDatetime: function(filter) {
        return this.panelDocked$FilterXCalendar(filter);
    },

    /**
     * Price-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXPrice: function(filter) {
        return this.panelDocked$FilterXNumber(filter);
    },

    /**
     * Number-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXNumber: function(filter) {

        // Setup auxilliary variables/shortcuts
        var me = this, alias = filter.foreign('fieldId').alias, filterCmpId = me.bid() + '-toolbar$filter$' + alias,
            fieldLabel, gte, lte;

        // Get the label
        fieldLabel = (filter.alt || filter.foreign('fieldId').title) + ' ' +
            Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM;

        // Append the extjs numberfield component data object to filters stack, for minimum value
        gte = {
            xtype: 'numberfield',
            id: filterCmpId + '-gte',
            name: alias + '-gte',
            isFilter: true,
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
            width: 50 + Indi.metrics.getWidth(fieldLabel),
            margin: '0 5 0 0',
            minValue: 0,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) me.filterChange(cmp);
                }
            }
        };

        // Append the extjs numberfield component data object to filters stack, for maximum value
        lte = {
            xtype: 'numberfield',
            id: filterCmpId + '-lte',
            name: alias + '-lte',
            isFilter: true,
            fieldLabel: Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO,
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
            width: 50 + Indi.metrics.getWidth(Indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
            margin: '0 4 0 0',
            minValue: 0,
            listeners: {
                change: function(cmp){
                    if (!cmp.noReload) me.filterChange(cmp);
                }
            }
        };

        // Return pair of items
        return [gte, lte];
    },

    /**
     * Color-filters configurator function
     *
     * @param filter
     * @return {Object}
     */
    panelDocked$FilterXColor: function(filter) {

        // Setup auxilliary variables/shortcuts
        var me = this, alias = filter.foreign('fieldId').alias, filterCmpId = me.bid() + '-toolbar$filter$' + alias,
            fieldLabel = filter.alt || filter.foreign('fieldId').title;

        // Append the extjs multislider component data object to filters stack, as multislider will
        // be the approriate way to represent color hue range (0 to 360)
        return {
            xtype: 'multislider',
            id: filterCmpId,
            fieldLabel: fieldLabel,
            labelWidth: Indi.metrics.getWidth(fieldLabel),
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
                    if (!cmp.noReload) me.filterChange(cmp);
                }
            }
        }
    },

    /**
     * Panel master toolbar builder
     *
     * @return {Object}
     */
    panelDocked$Master: function() {

        // Master toolbar cfg
        return {
            id: this.bid() + '-toolbar-master',
            items: this.panelDocked$MasterItemA(),
            border: 0
        }
    },

    /**
     * Builds and returns config for master toolbar 'Create' action-button item
     *
     * @return {Object}
     */
    panelDockedInner$Actions$Create: function(){
        var me = this, section = me.ti().section, canSave = false, canForm = false, canAdd = parseInt(me.ti().section.disableAdd) != 1;

        // Check if 'save' and 'form' actions are allowed
        for (var i = 0; i < me.ti().actions.length; i++) {
            if (me.ti().actions[i].alias == 'save') canSave = true;
            if (me.ti().actions[i].alias == 'form') canForm = true;
        }

        // 'Create' button will be added only if it was not switched off
        // in section config and if 'save' and 'form' actions are allowed
        if (canForm && canSave && canAdd) {

            // Return cfg
            return {
                id: me.bid() + '-docked-inner$create',
                tooltip: Indi.lang.I_CREATE,
                iconCls: 'i-btn-icon-create',
                actionAlias: 'form',
                handler: function(){
                    var south, already;

                    // If Ctrl-key is pressed
                    if (Ext.EventObject.ctrlKey) {

                        // Get south region panel
                        south = Ext.getCmp(me.panel.id).down('[isSouth]');

                        // If tab, that we want to add - is already exists within south region panel - set it active
                        if (already = south.down('[isSouthItem][name="0"]')) south.setActiveTab(already);

                        // Else add new tab within south panel
                        else south.add(me.southItemIDefault({
                            id: 0,
                            title: Indi.lang.I_CREATE
                        }));

                    // Else proceed standard behaviour
                    } else Indi.load('/' + section.alias + '/' + this.actionAlias + '/ph/' + section.primaryHash + '/');
                }
            }
        }
    },

    /**
     *
     * @param action
     * @return {*}
     */
    panelDockedInner$Actions_Default: function(action) {
        var me = this, cfg = me.callParent(arguments), sel, rs; if (!cfg) return;

        // Set handler
        cfg.handler = function(btn) {

            // Get rowset panel
            rs = Ext.getCmp(me.rowset.id);

            // If there is no rows selected, but at least one should - display a message box with appropriate warning
            if (!(sel = rs.getSelectionModel().getSelection()).length && btn.rowRequired == 'y')
                return Ext.MessageBox.show({
                    title: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                    msg: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.WARNING,
                    fn: function() {
                        Ext.defer(function(){
                            rs.getView().focus ? rs.getView().focus() : rs.getView().normalView.focus();
                        }, 100);
                    }
                });

            // Run the inner handler
            me.panelDockedInner$Actions_InnerHandler(btn.action, sel.length ? sel[0] : 0, sel.length ? sel[0].index + 1 : 0, btn);
        }

        // Return
        return cfg;
    },

    panelDockedInner$Actions_DefaultInnerHandler: function(action, row, aix, btn) {
        var me = this, rs = Ext.getCmp(me.rowset.id), srs = rs.getSelectionModel().selected.collect('id', 'data');
        me.panelDockedInner$Actions_DefaultInnerHandlerReload.call(this, action, row, aix, btn, {
            params: {
                'others[]': srs.length ? Ext.Array.remove(srs, row.get('id')) : []
            }
        });
    },

    /**
     * This action-button inner handler is the same as me.panelDockedInner$Actions_DefaultInnerHandlerLoad,
     * but it does not reload the whole panel - it just reload store only instead
     *
     * @param action
     * @param row
     * @param aix
     * @param btn
     */
    panelDockedInner$Actions_DefaultInnerHandlerReload: function(action, row, aix, btn, ajaxCfg) {
        var me = this, ajaxCfg = ajaxCfg || {}; me.panelDockedInner$Actions_DefaultInnerHandlerLoad(action, row, aix, btn, Ext.merge({
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText, true);
                if (json.page) me.getStore().loadPage(json.page);
                else if (json.affected) me.affectRecord(row, json);
                else me.getStore().load();
            }
        }, ajaxCfg));
    },

    /**
     * Inner handler function for 'Delete' action button
     *
     * @param action
     * @param row
     * @param aix
     */
    panelDockedInner$Actions$Delete_InnerHandler: function(action, row, aix, btn) {
        var me = this, rs = Ext.getCmp(me.rowset.id), srs = rs.getSelectionModel().selected.collect('id', 'data'),
            v = rs.getView();

        // Show the deletion confirmation message box
        Ext.MessageBox.show({
            title: Indi.lang.I_ACTION_DELETE_CONFIRM_TITLE,
            msg: Indi.lang.I_ACTION_DELETE_CONFIRM_MSG + ' "' + row.raw._system.title + '"?',
            buttons: Ext.MessageBox.YESNO,
            icon: Ext.MessageBox.QUESTION,
            fn: function(answer) {
                if (answer == 'yes') me.panelDockedInner$Actions_DefaultInnerHandlerReload.call(me, action, row, aix, btn, {
                    params: {
                        'others[]': Ext.Array.remove(srs, row.get('id'))
                    }
                })
                else (v.lockedView || v).focus();
            }
        });
    },

    /**
     * Inner handler function choicer. Detects whether special inner handler function exists certainly
     * for current action, and if yes - call it, or call default otherwise
     *
     * @param action
     * @param row
     * @param aix
     * @param btn
     */
    panelDockedInner$Actions_InnerHandler: function(action, row, aix, btn) {
        var me = this, fn;
        fn = 'panelDockedInner$Actions$' + Indi.ucfirst(action.alias) + '_InnerHandler';
        if (typeof me[fn] == 'function') me[fn](action, row, aix, btn);
        else me.panelDockedInner$Actions_DefaultInnerHandler(action, row, aix, btn);
    },

    /**
     * Inner handler function for form-action button
     *
     * @param action
     * @param row
     * @param aix
     */
    panelDockedInner$Actions$Form_InnerHandler: function(action, row, aix, btn) {
        var me = this, south, already;

        // If Ctrl-key is pressed
        if (Ext.EventObject.ctrlKey) {

            // Get south region panel
            south = Ext.getCmp(me.panel.id).down('[isSouth]');

            // If tab, that we want to add - is already exists within south region panel - set it active
            if (already = south.down('[isSouthItem][name="' + row.get('id') + '"]')) south.setActiveTab(already);

            // Else add new tab within south panel
            else south.add(me.southItemIDefault({
                id: row.get('id'),
                title: row.raw._system.title,
                aix: aix,
                action: action.alias
            }));

        // Else proceed standard behaviour
        } else me.panelDockedInner$Actions_DefaultInnerHandlerLoad(action, row, aix, btn);
    },

    /**
     * Default inner handler for print-action - same as for form-action
     */
    panelDockedInner$Actions$Print_InnerHandler: function() {
        this.panelDockedInner$Actions$Form_InnerHandler.apply(this, arguments);
    },

    /**
     * Default inner handler for call-action - same as for form-action
     */
    panelDockedInner$Actions$Call_InnerHandler: function() {
        this.panelDockedInner$Actions$Form_InnerHandler.apply(this, arguments);
    },

    /**
     * Default inner handler function for action button
     *
     * @param action
     * @param row
     * @param aix
     */
    panelDockedInner$Actions_DefaultInnerHandlerLoad: function(action, row, aix, btn, ajaxCfg) {
        var me = this, uri, section = me.ti().section;

        // Build the uri
        uri = '/' + section.alias + '/' + action.alias;

        //
        if (action.rowRequired == 'y' || me.ti(1).row)
            uri += '/id/' + (action.rowRequired == 'y' ? row.get('id') : me.ti(1).row.id)
                + '/ph/' + (action.rowRequired == 'y' ? section.primaryHash : me.ti().scope.upperHash)
                + '/aix/' + (action.rowRequired == 'y' ? aix : me.ti().scope.upperAix)

        // Append slash
        uri += '/';

        // Load it
        Indi.load(uri, ajaxCfg);
    },

    /**
     * Master toolbar 'Nested' item, for ability to navigate to selected row's nested entries lists
     *
     * @return {Object}
     */
    panelDockedInner$Nested: function(){
        var me = this;
        return {
            id: me.bid() + '-docked-inner$nested',
            xtype: 'shrinkbar',
            hidden: !me.ti().sections.length,
            border: 1,
            defaults: {
                margin: 0,
                padding: 0,
                border: 1,
                handler: function(btn) {

                    // Get selection
                    var selection = Ext.getCmp(me.rowset.id).getSelectionModel().getSelection();

                    // If no selection - show a message box
                    if (selection.length == 0) {
                        Ext.MessageBox.show({
                            title: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                            msg: Indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.WARNING,
                            fn: function() {
                                Ext.defer(function(){Ext.getCmp(me.rowset.id).getView().focus();}, 100);
                            }
                        });

                        // Else load the nested subsection contents
                    } else if (btn.alias) Indi.load('/' + btn.alias + '/index/id/'
                        + selection[0].data.id + '/ph/' + me.ti().scope.hash + '/aix/' + (selection[0].index + 1)+'/');
                }
            },
            shrinkCfg: {
                prop: 'title'
            },
            items: Ext.clone(me.ti().sections)
        }

        // 'Nested' item config
        return {
            tooltip: {
                html: Indi.lang.I_NAVTO_NESTED,
                hideDelay: 0,
                showDelay: 1000,
                dismissDelay: 2000,
                staticOffset: [0, 1]
            }
        }
    },

    /**
     * Master toolbar 'Keyword' item, for ability to use a keyword-search
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelDockedInner$Keyword: function() {
        var me = this;

        // 'Keyword' item config
        return {
            id: me.bid() + '-toolbar-master-keyword',
            xtype: 'textfield',
            isKeyword: true,
            labelClsExtra: 'i-action-index-keyword-toolbar-keyword-label',
            labelSeparator: '',
            tooltip: Indi.lang.I_ACTION_INDEX_KEYWORD_TOOLTIP,
            emptyText: Indi.lang.I_ACTION_INDEX_KEYWORD_LABEL,
            value: me.ti().scope.keyword ? Indi.urldecode(me.ti().scope.keyword) : '',
            width: 100,
            height: 19,
            cls: 'i-form-text',
            margin: '0 0 0 5',
            listeners: {
                change: function(tf){
                    clearTimeout(tf.timeout);
                    tf.timeout = setTimeout(function(){
                        me.filterChange({});
                    }, 500);
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

        // Setup auxilliary variables/shortcuts
        var me = this, itemA = [], itemI$Id = me.storeField$Id(), itemI, eItemX, itemX, eItem$, item$, fieldR, renderer;

        // Push 'id' store field to fields configs array
        if (itemI$Id) itemA.push(itemI$Id);

        // Other fields
        for (var i = 0; i < me.ti().gridFields.length; i++) {

            // Get Indi Engine's field
            fieldR = me.ti().fields.r(me.ti().gridFields[i].id);

            // Get default field config
            itemI = me.storeField_Default(fieldR);

            // Apply specific control element config, as fields control elements/xtypes may be different
            eItemX = 'storeFieldX' + Indi.ucfirst(fieldR.foreign('elementId').alias);
            if (Ext.isFunction(me[eItemX]) || Ext.isObject(me[eItemX])) {
                itemX = Ext.isFunction(me[eItemX]) ? me[eItemX](itemI, fieldR) : me[eItemX];
                itemI = Ext.isObject(itemX) ? Ext.merge(itemI, itemX) : itemX;
            } else if (me[eItemX] === false) itemI = me[eItemX];

            // Apply custom config
            eItem$ = 'storeField$' + Indi.ucfirst(me.ti().gridFields[i].alias);
            if (Ext.isFunction(me[eItem$]) || Ext.isObject(me[eItem$])) {
                item$ = Ext.isFunction(me[eItem$]) ? me[eItem$](itemI, fieldR) : me[eItem$];
                itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
            } else if (me[eItem$] === false) itemI = me[eItem$];

            // Add
            if (itemI) itemA.push(itemI);
        }

        // Return array
        return itemA;
    },

    /**
     * Builds and returns default/initial config for all store fields (except 'id' field)
     *
     * @return {Object}
     */
    storeField_Default: function(field) {
        return {
            name: field.alias,
            type: !parseInt(field.relation) && [3,5].indexOf(parseInt(field.columnTypeId)) != -1 && !parseInt(field.satellite)
                ? (field.foreign('elementId').alias == 'price' ? 'float' : 'int') : 'string'
        }
    },

    /**
     * Builds and returns config for store 'id' field
     *
     * @return {Object}
     */
    storeField$Id: function() {
        return {name: 'id', type: 'int'}
    },

    /**
     * Default config for date-fields
     *
     * @param field
     * @param fieldR
     * @return {Object}
     */
    storeFieldXCalendar: function (field, fieldR){
        return {
            type: 'date',
            dateFormat: fieldR.params.displayFormat
        }
    },

    /**
     * Default config for datetime-fields
     *
     * @param field
     * @param fieldR
     * @return {Object}
     */
    storeFieldXDatetime: function (field, fieldR){
        return {
            type: 'date',
            dateFormat: fieldR.params.displayDateFormat + ' ' + fieldR.params.displayTimeFormat
        }
    },

    /**
     * Set up and return store full request string (but without paging params)
     */
    storeLastRequest: function(){

        // Setup auxilliary variables/shortcuts
        var me = this, url, extra, get = [];

        // If first store data was got not by separate request - make filterChange() call,
        // that will set up `url` and `extraParams` props within proxy
        if (!me.getStore().lastOptions) me.filterChange({noReload: true});

        // Get url
        url = me.getStore().getProxy().url;

        // Extra
        extra = me.getStore().getProxy().extraParams;

        // Append param-value pairs
        ['search', 'keyword', 'summary'].forEach(function(r){
            if (extra[r]) get.push(r + '=' + encodeURIComponent(extra[r]));
        });

        // If sorters were used during last store request, we retrieve info about, encode and append it to 'get'
        if (me.getStore().getSorters().length)
            get.push('sort=' + encodeURIComponent(JSON.stringify(me.getStore().getSorters())));

        // Return the full url string
        return url + (get.length ? '?' + get.join('&') : '');
    },

    /**
     * Prepare and return a sorters for this.store
     *
     * @return {Array}
     */
    storeSorters: function(){
        var me = this;

        // If we have sorting params, stored in scope - we use them
        if (me.ti().scope.order && eval(me.ti().scope.order).length)
            return eval(me.ti().scope.order);

        // Else we use current section's default sorting params, if specified
        else if (me.ti().section.defaultSortField)
            return [{
                property : me.ti().section.defaultSortFieldAlias,
                direction: me.ti().section.defaultSortDirection
            }];

        // Else no sorting at all
        return [];
    },

    /**
     * Internal callback for store load/reload
     */
    storeLoadCallbackDefault: function() {
        var me = this, fo = me.getStore().proxy.reader.jsonData.filter, f;

        // Setup scope
        Ext.merge(me.ti().scope, me.getStore().proxy.reader.jsonData.scope);

        // Update combo-filter contents, if need
        if (Ext.isObject(fo) && Ext.Object.getSize(fo)) Ext.Object.each(fo, function(name, store){
            if (f = Ext.getCmp(me.panel.id).query('[isFilter][name='+name+']')[0]) {
                f.store = store; f[f.store.ids.length ? 'enable' : 'disable']();
            }
        });

        // Set index-of-total, for each data-row
        me.getStore().each(function(r, i) {
            r.index = i + (parseInt(me.ti().scope.page) - 1) * parseInt(me.ti().section.rowsOnPage);
        });

        // Adjust each data-row within the store
        me.getStore().each(me.storeLoadCallbackDataRowAdjust);
    },

    /**
     * Callback function for store load/reload. Is for use in subclasses
     */
    storeLoadCallback: Ext.emptyFn,

    /**
     * Determines this.store's current page. At first it will try to get it from this.ti().scope, at it it fails
     *  - return 1
     *
     * @return {*}
     */
    storeCurrentPage: function(){
        return this.ti().scope.page ? parseInt(this.ti().scope.page): 1;
    },

    /**
     * Gets a value, stored in scope for filter, by given filter alias
     *
     * @param alias
     * @return {*}
     */
    getScopeFilter: function(alias){

        // If there is no filters used at all - return
        if (this.ti().scope.filters == null) return;

        // Setup initial value for `value` variable as 'undefined'
        var value = undefined;

        // Filter values are stored in this.ti().scope as a stringified json array, so we need to convert it back,
        // to be able to find something there
        var values = eval(this.ti().scope.filters);

        // Find a filter value
        for (var i = 0; i < values.length; i++)
            if (values[i].hasOwnProperty(alias))
                value = values[i][alias];

        // Return value
        return value;
    },

    /**
     * Assign values to filters, before store load, for store to be loaded with respect to filter params.
     * These values will be got from this.ti().scope.filters, and if there is no value for some filter there - then
     * we'll try to get that in this.ti().filters[i].defaultValue. If there will no value too - then
     * filter will be empty.
     */
    setFilterValues: function(){
        var me = this, name, control, def, d, filterCmpId, already = [];

        // Foreach filter
        for (var i = 0; i < me.ti().filters.length; i++) {

            // Create a shortcut for filter field alias
            name = me.ti().filters[i].foreign('fieldId').alias;

            // Append name to `already` array. This will be needed bit later,
            // in the process of assigning values for non-regular filters,
            // Non-regular filters are those that are not in me.ti().filters array
            already.push(name);

            // Create a shortcut for filter field control element alias
            control = me.ti().filters[i].foreign('fieldId').foreign('elementId').alias;

            // At first, we check if current scope contain the value for the current filter, and if so - we use
            // that value instead of filter's own default value, whether it was defined or not. Also, we
            // implement a bit different behaviour for range-filters (number, calendar, datetime) and for other
            // types of filters
            if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                // Object for default values
                def = {};

                // Assign the 'gte' and 'lte' properties to the object of default values
                if (['undefined', ''].indexOf(me.getScopeFilter(name + '-gte') + '') == -1)
                    def.gte = me.getScopeFilter(name + '-gte');
                if (['undefined', ''].indexOf(me.getScopeFilter(name + '-lte') + '') == -1)
                    def.lte = me.getScopeFilter(name + '-lte');

                // If at least 'gte' or 'lte' properies was set, we assing 'def' object as filter default value
                if (Object.getOwnPropertyNames(def).length) me.ti().filters[i].defaultValue = def;

            // Else current filter is not a range-filter
            } else if (me.getScopeFilter(name)) {

                // Just assign the value, got from scope as filter default value
                me.ti().filters[i].defaultValue = me.getScopeFilter(name);
            }

            // Finally, if filter has a non-null default value
            if (me.ti().filters[i].defaultValue || (control == 'check' && me.ti().filters[i].defaultValue === 0)) {

                // Setup a shortcut for filter's default value
                d = me.ti().filters[i].defaultValue;

                // Prepare the id for current filter component
                filterCmpId = me.bid() + '-toolbar$filter$' + me.ti().filters[i].foreign('fieldId').alias;

                // If current filter is a range filter - set up min and/or max separately
                if (['number', 'calendar', 'datetime'].indexOf(control) != -1) {

                    // If default value is a stringified javascript array of javascript object, we convert it back
                    if ((typeof d == 'string') && (d.match(/^\[.*\]$/) || d.match(/^\{.*\}$/))) d = eval('('+ d + ')');

                    // If default value is an object
                    if (typeof d == 'object')

                        // Foreach property in default value object (which nameds can be 'gte' and 'lte' only)
                        for (var j in d) {

                            // If there is actually no component for this filter - try next iteration
                            if (!Ext.getCmp(filterCmpId + '-' + j)) continue;

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

                    // If there is actually no component for this filter - try next iteration
                    if (!Ext.getCmp(filterCmpId)) continue;

                    // Toggle 'noReload' property to 'true' to prevent store reload
                    Ext.getCmp(filterCmpId).noReload = true;

                    // If filter is for multiple combo - set value as array, joined by comma
                    if (me.ti().filters[i].foreign('fieldId').storeRelationAbility == 'many')
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

        // Get all filter components
        var value, filterCmpA = Ext.getCmp(me.panel.id).query('[isFilter][name]');

        // Foreach filter component
        for (i = 0; i < filterCmpA.length; i++) {

            // Get it's name
            name = filterCmpA[i].name;

            // Ensure that value hasn't yet been assigned to component, and
            // current scope contains a value for that component, and if so - assign it
            if (already.indexOf(name) == -1 && (value = me.getScopeFilter(name))) {
                filterCmpA[i].noReload = true;
                filterCmpA[i].setValue(value);
                filterCmpA[i].noReload = false;
            }
        }
    },

    /**
     * Build and return array of rowset-panel toolbars
     *
     * @return {Array}
     */
    rowsetDockedA: function() {
        return this._docked('rowset');
    },

    /**
     * Builds and return an array of panels, that will be used to represent the major UI contents.
     * Currently is consists only from this.rowset form panel configuration
     *
     * @return {Array}
     */
    panelItemA: function() {
        var me = this, itemA = [], rowsetItem = me.rowsetPanel(), southItem = me.south;

        // Append rowset (center region) panel
        if (rowsetItem) itemA.push(rowsetItem);

        // Append tab (south region) panel only if it's consistent
        if (southItem) {

            // Init tabs within tab panel, and set height and active tab
            if ((southItem.items = me.southItemA()).length) {

                // Set up tabpanel height
                southItem.height = me.ti().scope.actionrowset.south.height;

                // Set up active tab
                southItem.activeTab = me.ti().scope.actionrowset.south.tabs.column('id').indexOf(me.ti().scope.actionrowset.south.activeTab.toString());

            // Else set up south item as hidden
            } else southItem.hidden = true;

            // Push tabpanel as south region within main panel
            itemA.push(southItem);
        }

        // Return panels array
        return itemA;
    },

    /**
     * South-panel config
     */
    south: {
        xtype: 'rowsetactionsouth',
        maxTabs: 0
    },

    /**
     * Build an return main panel's rowset panel config object
     *
     * @return {*}
     */
    rowsetPanel: function() {
        var me = this;

        // Return
        return Ext.merge({
            id: me.id + '-rowset',
            dockedItems: me.rowsetDockedA(),
            store: me.getStore()
        }, me.rowset);
    },

    /**
     * Default config for south region panel items
     *
     * @param src
     * @return {Object}
     */
    southItemIDefault: function(src) {
        var me = this, section = me.ti().section, scope = me.ti().scope,
            id = 'i-section-' + section.alias + '-action-' + (src.action || 'form') + '-row-' + src.id + '-wrapper',
            exst = Ext.getCmp(id), exstWin;

        // Close the window, containing existing wrapper having same id
        if (exst && (exstWin = exst.getWindow())) exstWin.close();

        // Config
        return {
            xtype: 'panel',
            isSouthItem: true,
            title: src.title,
            name: src.id,
            closable: true,
            border: 0,
            layout: 'fit',
            items: [{
                xtype: 'actiontabrow',
                id: id,
                load: '/' + section.alias + '/' + (src.action || 'form')
                    + (parseInt(src.id) ? '/id/' + src.id : '')
                    + '/ph/' + scope.hash + '/'
                    + (parseInt(src.id) ? 'aix/' + src.aix + '/' : '')
            }]
        }
    },

    /**
     * Build and return the array of components configs, that will be used as inner items within south region panel
     *
     * @return {Array}
     */
    southItemA: function() {
        var me = this, south, srcA, itemA = [], itemI, i;

        // If namespace 'me.ti().scope.actionrowset.south.tabs' does not exists - return
        try {
            south = me.ti().scope.actionrowset.south;
            srcA = south.tabs;
        } catch(e) {
            return itemA;
        }

        // If `srcA` is not an array - return empty array
        if (!Ext.isArray(srcA)) return itemA;

        // Foreach item within srcA
        for (i = 0; i < srcA.length; i++) {

            // Get item default config
            itemI = me.southItemIDefault(srcA[i]);

            // Setup special `isFromScope` flag, for detecting if tab was initialised
            // automatically rather than was added manually by user a moment ago, because
            // tabs added by user won't have such a flag
            itemI.isFromScope = true;

            // Add item
            if (itemI) itemA.push(itemI);
        }

        // Return
        return itemA;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this, id = me.panel.id, exst = Ext.getCmp(id), southItem;

        // If such a wrapper-panel is already exists
        if (exst && exst.$ctx) {

            // Check if it exists within a tab
            southItem = exst.up('[isSouthItem]');

            // Destroy that existing panel
            exst.destroy();

            // If destroyed wrapper-panel existed within a tab
            if (southItem) {

                // Remove garbage
                if (Ext.get(id)) Ext.get(id).remove();

                // Add wrapper-panel placeholder, so it will appear instead of destroyed wrapper-panel
                southItem.up('[isSouth]').addTabPlaceholder(southItem.id, id, 'rowset');
            }
        }

        // Call parent
        me.callParent();

        // Attach key map
        me.rowsetKeyMap();
    },

    /**
     *
     */
    rowsetKeyMap: Ext.emptyFn,

    /**
     * Build and return an array, containing plugin definitions for rowset panel
     *
     * @return {Array}
     */
    rowsetPluginA: function() {
        var me = this, itemA = [], itemI, eItem$, item$;

        // Walk through desired plugins
        me.rowset.$plugins.forEach(function(i){

            // If plugin cfg has `alias` prop
            if (i.alias) {

                // Empty obj for now
                itemI = i;

                // Get member name, responsible for extended plugin cfg
                eItem$ = 'rowsetPlugin$' + Indi.ucfirst(i.alias);

                // Deal with extended cfg, whatever it is funcion, object or `false`
                if (Ext.isFunction(me[eItem$]) || Ext.isObject(me[eItem$])) {
                    item$ = Ext.isFunction(me[eItem$]) ? me[eItem$](itemI) : me[eItem$];
                    itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
                } else if (me[eItem$] === false) itemI = me[eItem$];

            // Else if plugin cfg has no 'alias' prop, but has 'ptype' prop - append it as is
            } else if (i.ptype) itemI = i;

            // Add
            if (itemI) itemA.push(itemI);
        });

        // Return
        return itemA;
    },

    /**
     * Save dirty row
     *
     * @param record
     * @param aix
     */
    recordRemoteSave: function(record, aix, $ti, callback) {
        var me = this, ti = $ti || me.ti(), params, bool = [];

        // If no changed was made - return
        if (!record.dirty) return;

        // Get changes
        params = Ext.clone(record.getChanges());

        // Convert values before submit
        Object.keys(params).forEach(function(i){

            // Boolean values to int values
            if (Ext.isBoolean(params[i])) params[i] = params[i] ? 1 : 0;

            // Process date values
            if (Ext.isDate(params[i])) {
                if (ti.fields.r(i, 'alias').foreign('elementId').alias == 'datetime') {
                    params[i] = Ext.Date.format(params[i], 'Y-m-d H:i:s');
                } else {
                    params[i] = Ext.Date.format(params[i], 'Y-m-d');
                }
            }

            // Process foreign key values
            if (ti.fields.r(i, 'alias').storeRelationAbility != 'none') {
                params[i] = record.key(i);
            }
        });

        // Show loader
        Indi.app.loader();

        // Get part of query string, related to filters
        var search = me.storeLastRequest().split('?');
        search.shift();
        search = search.join('?').split('&')[0];
        search = search.match(/^search=/) ? '?' + search : '';

        // Try to save via Ajax-request
        Ext.Ajax.request({

            // Params
            url: Indi.pre + '/' + ti.section.alias + '/save/id/' + record.get('id')
                + '/ph/' + ti.scope.hash + '/aix/' + aix + '/' + search,
            method: 'POST',
            params: params,

            // Success handler
            success: function(response) {
                var json, value, field;

                // Parse response text
                json = Ext.JSON.decode(response.responseText, true);

                // Visually update record
                me.affectRecord(record, json);

                // Call callback
                if (Ext.isFunction(callback)) callback(json);
            },

            // Failure handler
            failure: function(response) {

                // Reject changes
                record.reject();
            }
        });
    },

    /**
     *
     * @param record
     * @param json
     */
    affectRecord: function(record, json) {

        // If response contains info about affected fields
        if (Ext.isObject(json) && Ext.isObject(json.affected)) {

            // Walk through affected fields
            Object.keys(json.affected).forEach(function(i){

                // Apply _system modifications
                if (i.match(/^_system/)) record.raw._system = json.affected[i];

                // If affected field's name starts with '_' - skip
                if (i.match(/^_/)) return;

                // If affected field's name starts with '$'
                if (i.match(/^\$/)) {

                    // If affected field's name is '$keys' - update field's key values
                    if (i == '$keys') Object.keys(json.affected[i]).forEach(function(j){
                        record.key(j, json.affected[i][j], true);
                    });

                // Update field's rendered values
                } else {

                    // Shortcuts
                    value = json.affected[i]; field = record.fields.get(i);

                    // If field's type is 'bool' (this may, for example, happen in case if 'xtype: checkcolumn' usage)
                    if (field && field.type.type == 'bool') value = !!parseInt(json.affected.$keys[i]);

                    // Set field's value
                    record.set(i, value);
                }
            });
        }

        // Commit row
        record.commit();
    },

    // @inheritdoc
    onDestroy: function() {
        var me = this;

        // Destroy filters window, if it was created
        if (Ext.getCmp(me.panel.id).filterWin) Ext.getCmp(me.panel.id).filterWin.destroy();

        // Destroy store
        if (me.getStore()) me.getStore().destroyStore();

        // Call parent
        me.callParent();
    }
});

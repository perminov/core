/**
 * Base class for all controller actions instances
 */
Ext.define('Indi.lib.controller.action.Action', {

    // @inheritdoc
    extend: 'Ext.Component',

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action',

    // @inheritdoc
    mcopwso: ['panel'],

    /**
     * Wrapper-panel config
     */
    panel: {
        xtype: 'actionpanel',
        height: '100%',
        closable: true,
        layout: 'border',
        docked: {
            default: {
                xtype: 'toolbar',
                style: {paddingRight: '3px'},
                padding: '0 3 0 2',
                items: []
            },
            items: [],
            inner: {}
        }
    },

    /**
     * Rowset-panel config
     */
    rowset: {
        region: 'center',
        layout: 'fit',
        height: '70%',
        docked: {
            default: {
                xtype: 'toolbar',
                style: {paddingRight: '3px'},
                padding: '0 3 0 2',
                height: 26
            },
            items: [],
            inner: {}
        }
    },

    /**
     * Row-panel config
     */
    row: {
        region: 'center',
        layout: 'fit',
        height: '40%',
        docked: {
            default: {
                xtype: 'toolbar',
                style: {paddingRight: '3px'},
                padding: '0 3 0 2',
                height: 26
            },
            items: [],
            inner: {}
        }
    },

    /**
     * South-panel config
     */
    south: {
        xtype: 'actionsouth'
    },

    /**
     * Get the current trail item, or upper trail item - if `up` argument is given
     *
     * @param up
     * @return {Indi.lib.Trail.Item}
     */
    ti: function(up) {
        return this.route.last(up);
    },

    /**
     * Get the base id for all components, created while controller's action execution
     * If `up` argument is given, function will return base id of upper-level controller's action
     * Actually, there is no need to call this method with `up` argument not passed or passed as zero,
     * because the return value will be exact the same as `id` property, which is available initially.
     * So this method should be used if upper-level base ids are needed to be got
     *
     * @param up
     * @return {String}
     */
    bid: function(up) {
        var me = this, s = 'i-section-' + me.ti(up).section.alias + '-action-' + me.ti(up).action.alias;

        // Normalize `up` argument
        up = isNaN(up) ? 0 : up;

        // Build the tail part of base id
        if (me.ti(up).row) {
            s += '-row-' + (me.ti(up).row.id || 0);
        } else if (me.ti(up + 1).row) {
            s += '-parentrow-' + me.ti(up + 1).row.id;
        }

        // Return
        return s;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Set up docked items
        me.panel.dockedItems = me.panelDockedA();

        // If all contents should be added to existing panel
        if (me.cfg.into) me.panel.header = false; else {

            // Append tools and toolbars to the main panel
            Ext.merge(me.panel, {
                renderTo: 'i-center-center-body',
                tools: me.panelToolA()
            });

            // Update id of the main panel (temporary)
            Indi.centerId = me.panel.id;
        }

        // Create panel instance
        var panel = Ext.widget(me.panel);

        // If created instance should be inserted as a tab - do it
        if (me.cfg.into) Ext.getCmp(me.cfg.into).add(panel);

        // If panel has `onLoad` property, and it's a function - call it
        if (Ext.isFunction(panel.onLoad)) panel.onLoad(me);

        // If special `onLoad` callback is provided within me.cfg - call it
        if (Ext.isFunction(me.cfg.onLoad)) me.cfg.onLoad.call(panel, me);

        // Call parent
        me.callParent();
    },

    /**
     * Builder for items arrays. Used for toolbars, items within each toolbar, etc
     *
     * @param itemCfgA
     * @param fnItemPrefix
     * @param allowNaO
     * @param adjust
     * @return {Array}
     */
    push: function(itemCfgA, fnItemPrefix, allowNaO, adjust) {

        // Setup auxilliary variables
        var me = this, itemA = [], itemI, fnItemI;

        // Build itemA array
        for (var i = 0; i < itemCfgA.length; i++) {

            // If only object-items are allowed for pushing, but itemCfgA[i] is not an object, try itemCfg[i+1]
            if (!allowNaO && !Ext.isObject(itemCfgA[i])) continue;

            // Reset itemI
            itemI = null;

            // Else if itemCfgA[i] is an object, and have `alias` property
            if (itemCfgA[i].hasOwnProperty('alias')) {

                // Get the item's creator function name
                fnItemI = fnItemPrefix + '$' + Indi.ucfirst(itemCfgA[i].alias);

                // If such function exists and return value of that function call
                if (typeof me[fnItemI] == 'function') itemI = me[fnItemI]();

                // If config is an object, merge itemI with it
                if (Ext.isObject(itemCfgA[i]) && (itemI || typeof me[fnItemI] != 'function'))
                    Ext.merge(itemI = itemI ? itemI : {}, itemCfgA[i]);

            // Else use as is
            } else itemI = itemCfgA[i];

            // Adjust item
            if (itemI && typeof adjust == 'function') itemI = adjust(itemI);

            // If itemI become consistent - push it to items array
            if (itemI && ((Ext.isObject(itemI) && JSON.stringify(Object.keys(itemI)) != '["alias"]') || allowNaO))
                itemA.push(itemI);
        }

        // Return items array
        return itemA;
    },

    /**
     * Panel tools array builder. This method is for use in subclasses of Indi.Controller.Action
     *
     * @return {Array}
     */
    panelToolA: function() {
        return []
    },

    /**
     * Build and return array of wrapper-panel toolbars
     *
     * @return {Array}
     */
    panelDockedA: function() {
        return this._docked('panel');
    },

    /**
     *
     * @param panel
     * @private
     */
    _docked: function(panel) {
       var me = this;

        // Setup docked items
        return me.push(me[panel].docked.items, panel+'Docked', false, function(itemI){

            // Setup default config
            if (itemI) itemI = Ext.merge({}, me[panel].docked.default, itemI);

            // Try to setup `items` property, if it's not yet set
            if (!itemI.items && itemI.alias && me[panel].docked.inner && me[panel].docked.inner[itemI.alias])
                itemI.items = me.push(me[panel].docked.inner[itemI.alias], panel+'DockedInner', true);

            // Return
            return itemI.items ? itemI : null;
        });
    },

    /**
     * Panel filter toolbar builder
     *
     * @return {Object}
     */
    panelDocked$Filter: function() {
        var me = this;

        // 'Filter' toolbar config
        return {
            xtype: 'toolbar',
            dock: 'top',
            hidden: (!me.panel.docked.inner || !me.panel.docked.inner.filter || !me.panel.docked.inner.filter.length),
            padding: '1 5 5 5',
            id: me.bid() + '-toolbar$filter',
            layout: 'auto',
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
                items: me.panelDocked$FilterItemA()
            }],
            doRequest: function() {
                Ext.Ajax.request({
                    method: 'GET',
                    url: me.uri,
                    params: {search: this.extraParams},
                    success: me.obarRequestCallback
                });
            }
        }
    },

    /**
     * Get Options/Filters toolbar
     *
     * @return {*}
     */
    getObar: function() {
        return Ext.getCmp(this.bid() + '-toolbar$filter');
    },

    /**
     * Build and return array of filter toolbar items configs
     *
     * @return {Array}
     */
    panelDocked$FilterItemA: function() {

        // Declare toolbar filter panel items array, and some additional variables
        var me = this, itemA = [];

        // Setup filters
        if (me.panel.docked && me.panel.docked.inner && me.panel.docked.inner.filter && me.panel.docked.inner.filter.length)
            itemA = me.push(me.panel.docked.inner.filter, 'panelDocked$Filter', false, function(itemI){
                return Ext.merge({
                    listeners: {
                        change: function(cmp) {
                            me.filterChange(cmp);
                        }
                    }
                }, itemI);
            });

        // Return filter toolbar items
        return itemA;
    },

    /**
     * Handler for any filter change
     *
     * @param cmp Component, that fired filterChange
     */
    filterChange: function(cmp){

        // Setup auxilliary variables/shortcuts
        var me = this;

        // Declare an array for params, which will be fulfiled with filters's values
        var paramA = [];

        // Get all filter components
        var filterCmpA = Ext.getCmp(me.panel.id).query('[isFilter][name]');

        // Foreach filter component id in filterCmpIdA array
        for (var i = 0; i < filterCmpA.length; i++) {

            // We do not involve values of hidden or disabled filter components in request query building
            if (filterCmpA[i].hidden || filterCmpA[i].disabled) continue;

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
            }
        }

        // Apply collected used filter alises and their values as a this.getStore().proxy.extraParams property
        me.getObar().extraParams = {search: JSON.stringify(paramA)};

        // Adjust an 'url' property of  this.getStore().proxy object, to apply keyword search usage
        me.getObar().url = me.uri;

        // If there is no noReload flag turned on
        if (!cmp.noReload) {

            // If used filter is a combobox or multislider, we do a request immediately
            if (['combobox', 'combo.filter', 'multislider'].indexOf(cmp.xtype) != -1) {
                me.getObar().doRequest();

            // Else if used filter is not a datefield, or is, but it's value matches proper date format or
            // value is empty, we reload store data with a 500ms delay, because direct typing is allowed in that
            // datefield, so it's better to reload after user has finished typing.
            } else if (cmp.xtype != 'datefield' || (/^([0-9]{4}-[0-9]{2}-[0-9]{2}|[0-9]{2}\.[0-9]{2}\.[0-9]{4})$/
                .test(cmp.getRawValue()) || !cmp.getRawValue().length)) {
                clearTimeout(me.getObar().timeout);
                me.getObar().timeout = setTimeout(function(){
                    me.getObar().doRequest();
                }, 500);
            }
        }
    },

    /**
     * Callback function for requests, made by Options/Filters toolbar
     *
     * @param response
     */
    obarRequestCallback: function(response) {
    },

    /**
     * Batch-attach key-map, for ability to navigate to subsections via keyboard, using Shift+1, Shift+2, etc
     *
     * @param target
     */
    setupSubsectionsAccessKeys: function(target) {
        var me = this, keys = ['ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE'], binding = [],
            nested = Ext.getCmp(me.bid() + '-docked-inner$nested');

        // If there is no 'nested' docked inner item - return
        if (!nested) return;

        // Fulfil bindings array
        for (var i = 0; i < keys.length; i++)
            binding.push({
                key: Ext.EventObject[keys[i]],
                alt: true,
                fn:  function(key){
                    nested.press(key - 49);
                },
                scope: me
            });

        // Add keyboard event handelers
        if (Ext.getCmp(target).rendered) Ext.getCmp(target).getEl().addKeyMap({
            eventName: 'keydown',
            binding: binding
        });
    },

    /**
     * Empty function
     */
    keyMap: Ext.emptyFn,

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Set up an id for wrapper panel
        me.panel.id = config.id + '-wrapper';

        // Set up and xtype for wrapper panel
        if (config.cfg.into) me.panel.xtype = 'actiontab' + config.route.last().action.mode.toLowerCase();

        // Call parent
        me.callParent(arguments);
    }
});

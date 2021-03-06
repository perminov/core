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
        },

        /**
         * Array of action-button aliases, that have special icons
         */
        toolbarMasterItemActionIconA: ['form', 'delete', 'save', 'toggle', 'up', 'down',
            'print', 'm4d', 'cancel', 'php', 'author', 'login', 'confirm', 'goto', 'call',
            'pay', 'refund', 'activate'
        ]
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
        } else if (me.ti(up + 1) && me.ti(up + 1).row) {
            s += '-parentrow-' + me.ti(up + 1).row.id;
        }

        // Return
        return s;
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
        var me = this, itemA = [], itemI, $ItemI;

        // Build itemA array
        for (var i = 0; i < itemCfgA.length; i++) {

            // If only object-items are allowed for pushing, but itemCfgA[i] is not an object, try itemCfg[i+1]
            if (!allowNaO && !Ext.isObject(itemCfgA[i])) continue;

            // Reset itemI
            itemI = null;

            // Else if itemCfgA[i] is an object, and have `alias` property
            if (itemCfgA[i].hasOwnProperty('alias')) {

                // Get the item's creator function name
                $ItemI = fnItemPrefix + '$' + Indi.ucfirst(itemCfgA[i].alias);

                // If such function exists and return value of that function call
                if (typeof me[$ItemI] == 'function') itemI = me[$ItemI]();
                else if (Ext.isObject(me[$ItemI])) itemI = me[$ItemI];

                // If config is an object, merge itemI with it
                if (Ext.isObject(itemCfgA[i]) && (itemI || typeof me[$ItemI] != 'function'))
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
     * Build and return array of master toolbar items configs
     *
     * @return {Array}
     */
    panelDocked$MasterItemA: function() {
        var me = this, merged = [],
            pushed = me.push(me.panel.docked.inner['master'], 'panelDockedInner', true),
            filter = me.ti().filters.select('master', 'toolbar');

        for (var i = 0; i < pushed.length; i++) merged = merged.concat(pushed[i]);
        merged = merged.concat(me.panelDocked$FilterItemA(filter));

        // Return
        return merged;
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

        // If filters toolbar was undocked from main panel into a window - try search within that window
        if (!filterCmpA.length && Ext.getCmp(me.panel.id).filterWin)
            filterCmpA = Ext.getCmp(me.panel.id).filterWin.query('[isFilter][name]');

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

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Set up an id for wrapper panel
        me.panel.id = config.id + '-wrapper';

        // Set up and xtype for wrapper panel
        if (config.cfg.into) me.panel.xtype = 'actiontab' + config.route.last().action.mode.toLowerCase();

        // Call parent
        me.callParent(arguments);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this, wrp;

        // Set up docked items
        me.panel.dockedItems = me.panelDockedA();

        // Remove panel header
        me.panel.header = false;

        // Set up context to be available as panel's `$ctx` prop
        me.panel.$ctx = me;

        // If all contents should be added to a window
        if (!me.cfg.into) {

            // Append tools and toolbars to the main panel
            me.panel.renderTo = me.prepareWindow().getTargetEl();

            // Update id of the main panel (temporary)
            Indi.centerId = me.panel.id;

        // Else
        } else {

            // Remove layout definition
            delete me.panel.layout;
        }

        // If we're going create a wrapper within a window
        // but wrapper with same id is already exist within a south-panel tab
        if ((wrp = Ext.getCmp(me.panel.id)) && !me.cfg.into) {

            // Backup some info (tab id and wrapper initial config),
            // that will help us to re-instantiate wrapper within tab
            // in case if user will close the window
            me.panel.tabDraft = {
                containerId: wrp.ownerCt.id,
                itemConfig: wrp.initialConfig
            }

            // Add placeholder into the tab
            wrp.up('[isSouth]').addTabPlaceholder(wrp.ownerCt.id, wrp.initialConfig.id, 'action');

            // Destroy wrapper, that currently exists within a south-panel tab
            // as we're going to create same wrapper within a separate window
            wrp.destroy();
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
     * Here we decide whether existing window should be used as place where wrapper should be rendered in,
     * or new separate window should be created for that
     *
     * @return {*}
     */
    prepareWindow: function() {
        var me = this, app = Indi.app, window, active = app.getActiveWindow(me.cfg.trail), create = false, a = {}, n = {}, i, cfg;

        // If we have no windows yet - set `create` as `true`, else
        if (!active) create = true; else {

            // Get info about active window
            a.section = active.ctx.ti().section.alias;
            a.action = active.ctx.ti().action.alias;
            a.mode = active.ctx.ti().action.mode.toLowerCase();
            a.row =  active.ctx.ti().row ? active.ctx.ti().row.id : null;

            // Get info about new-window-candidate
            n.section = me.ti().section.alias;
            n.action = me.ti().action.alias;
            n.mode = me.ti().action.mode.toLowerCase();
            n.row =  me.ti().row ? me.ti().row.id : null;

            // If we're going to create same-section panel
            if (a.section == n.section) {

                // If active window's panel is a rowset-panel
                if (a.mode == 'rowset') {

                    // If we're going to create a row-panel
                    if (n.mode == 'row') {

                        // Set up `create` flag as `true`
                        create = true;

                    // Else iа we're going to create a rowset-panel
                    } else if (n.mode == 'rowset') {

                        // Set up `create` flag as `false`
                        create = false;
                    }

                // Else if active window's panel is a row-panel
                } else if (a.mode == 'row') {

                    // If we're going to create a row-panel
                    if (n.mode == 'row') {

                        // If active window's panel is a same-action panel as the panel we're going to create
                        create = false;//a.action != n.action;

                    // Else iа we're going to create a rowset-panel
                    } else if (n.mode == 'rowset') {

                        // Set up `create` flag as `false`
                        create = false;
                    }
                }

            // Else if we're going to create panel, that have section,
            // that is a child section for active window's section
            } else if (me.isChildOf(a.section)) {

                // If active window's panel is a rowset-panel
                if (a.mode == 'rowset') {

                    // If we're going to create a row-panel
                    if (n.mode == 'row') {

                        // Set up `create` flag as `true`
                        create = true;

                    // Else iа we're going to create a rowset-panel
                    } else if (n.mode == 'rowset') {

                        // Set up `create` flag as `false`
                        create = true;
                    }

                    // Else if active window's panel is a row-panel
                } else if (a.mode == 'row') {

                    // If we're going to create a row-panel
                    if (n.mode == 'row') {

                        // Set up `create` flag as `true`
                        create = true;

                    // Else if we're going to create a rowset-panel
                    } else if (n.mode == 'rowset') {

                        // If current panel is a row-panel, related to an existing row
                        if (parseInt(a.row)) {

                            // If current action is 'form'
                            if (a.action == 'form') {

                                // Setup `create` flag as `true` only if autosave-checkbox was not checked
                                create = !Ext.getCmp(active.ctx.panelDockedInnerBid() + 'autosave').checked;

                            } else {

                                // Set up `create` flag as `true`
                                create = true;
                            }

                        // Else if current panel relates to row, not yet exisiting and the moment of current panel rendering
                        } else {

                            // Set up `create` flag as `true`
                            create = false;
                        }
                    }
                }

            // Else if we're going to create panel, that have section,
            // that is a parent section for active window's section
            } else if (active.ctx.isChildOf(n.section)) {

                // If we're going to create a row-panel
                if (n.mode == 'row') {

                    // Set up `create` flag as `false`
                    create = false;

                    // Else iа we're going to create a rowset-panel
                } else if (n.mode == 'rowset') {

                    // Set up `create` flag as `false`
                    create = false;
                }

            // Else if we're here because of some of trail-buttons was clicked
            } else if (me.cfg.trail || me.cfg.whd) {

                // Set up `create` flag as `false`
                create = false;

            // All other situations
            } else {

                // Set up `create` flag as `false`
                create = true;
            }
        }

        // Set up window partial cfg
        cfg = {
            wrapperId: me.panel.id,
            title: me.panel.title,
            ctx: me,
            replaceTitle: {
                xtype: 'toolbar',
                cls: 'i-window-header',
                padding: 0,
                style: 'background: none;',
                border: 0,
                enableOverflow: {
                    menu: {
                        cls: 'i-trail-item-menu i-trail-overflow-menu i-window-header-menu',
                        plain: true
                    }
                },
                defaults: {
                    xtype: 'trailbutton',
                    padding: 0,
                    margin: 0,
                    height: 15,
                    border: 0,
                    menuStyle: 'border-top-width: 1px',
                    menuCls: 'i-trail-item-menu i-window-header-menu',
                    menuOffset: [-5, 0],
                    loadCfg: {whd: true},
                    handler: function(btn) {
                        if (btn.load) Indi.load(btn.load, btn.loadCfg);
                    }
                },
                items: Indi.trail(true).breadCrumbA(me.route, true).pop()
            }
        }

        // If new window should be created
        if (create) {

            // Try to check if current windows collection already contains a window that we're going to create
            i = app.windows.collect('wrapperId').indexOf(me.panel.id);

            // Id not contains - create that window
            if (i == -1)
                window = Ext.widget(Ext.merge({
                    xtype: 'desktopwindow',
                    tools: me.panelToolA(),
                    maximized: true
                }, cfg));

            // Else use existing window
            else {

                // Remember target window at this time to avoid accessing it by app.windows.getAt(i)
                // because after call of active.close() target window's index within app.windows may shift
                var target = app.windows.getAt(i);

                // Close active window
                //if (target.id != active.id) active.close();

                // Error catcher. Problem, that cause this line to be added - seem to be fixed,
                // but it's still remain here to check if there are other reasons of problem
                //if (!target) console.log('create == true', i, app.windows.getCount(), me.panel.id);

                // Apply new contents to existing window
                window = target.apply(cfg).show();
            }

        // Else set up active window usage as a place for new panel
        } else {

            // Try to check if current windows collection already contains a window that we're going to create
            i = app.windows.collect('wrapperId').indexOf(me.panel.id);

            // If contains - close that existing window
            if (i != -1 && app.windows.getAt(i).id != active.id) app.windows.getAt(i).close();

            // Error catcher
            if (!active) console.log('create == false', app.windows.getCount(), me.panel.id);

            // Apply new contents to active window
            window = active.apply(cfg);

            // Update trail
            Indi.app.updateTrail();
        }

        // Return
        return window;
    },

    /**
     * Check whether or not current section is child for `parent` section
     *
     * @param parent
     * @return {Boolean}
     */
    isChildOf: function(parent) {
        var me = this, i;

        // Check all parent trail items and investigate whether or not
        // some of them has section.alias equal to `parent` argument
        for (i = me.route.length - 2; i > 0; i--)
            if (me.route[i].section.alias == parent)
                return true;

        // Return
        return false;
    },

    /**
     * Build and return array of configs of master toolbar items, that represent action-buttons
     *
     * @return {Array}
     */
    panelDockedInner$Actions: function() {

        // Setup auxillirary variables
        var me = this, itemA = [], itemI, eItem$, item$, itemICreate = me.panelDockedInner$Actions$Create();

        // Append 'Create' action button
        if (itemICreate) itemA.push(itemICreate);

        // Append other action buttons
        for (var i = 0; i < me.ti().actions.length; i++) {

            // Skip current section
            if (me.ti().actions[i].alias == me.ti().action.alias) continue;

            // Get default column config
            itemI = me.panelDockedInner$Actions_Default(me.ti().actions[i]);

            // Apply custom config
            eItem$ = 'panelDockedInner$Actions$'+Indi.ucfirst(me.ti().actions[i].alias);
            if (Ext.isFunction(me[eItem$]) || Ext.isObject(me[eItem$])) {
                item$ = Ext.isFunction(me[eItem$]) ? me[eItem$](itemI) : me[eItem$];
                itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
            } else if (me[eItem$] === false) itemI = me[eItem$];

            // Add
            if (itemI) itemA.push(itemI);
        }

        // Push a separator
        if (itemA.length) itemA.push('-');

        // Return
        return itemA;
    },

    /**
     * Panel master toolbar id constructor
     *
     * @return {String}
     */
    panelDockedInnerBid: function() {
        return this.bid() + '-docked-inner$';
    },

    /**
     * Builds and returns default/initial config for all action-button master panel items
     *
     * @return {Object}
     */
    panelDockedInner$Actions_Default: function(action) {
        var me = this, bid = me.panelDockedInnerBid(), ats;

        // If action is visible - return
        if (action.display != 1) return null;

        // If current context's action is a certain-row-action,
        // but `action` arg - is not a certain-row action - return
        if (me.ti().action.rowRequired == 'y' && action.rowRequired != 'y') return null;

        // Basic action object
        var actionItem = {
            id: bid + action.alias,
            text: action.title,
            action: action,
            actionAlias: action.alias,
            rowRequired: action.rowRequired,
            listeners: {
                boxready: function(btn) {
                    if (me.ti().action.rowRequired == 'y'
                        && !me.ti().row.id
                        && !(ats = Ext.getCmp(bid + 'autosave')))
                        btn.setDisabled(true)
                }
            }
        }

        // Setup iconCls property, if need
        if (me.panel.toolbarMasterItemActionIconA.indexOf(action.alias) != -1) {
            actionItem.iconCls = '!i-btn-icon-' + action.alias;
        }

        // Return
        return actionItem;
    }
});

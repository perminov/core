/**
 * Base class for all controller actions instances, that operate with rowsets,
 * and use Ext.panel.Grid view to display/modify those rowsets
 */
Ext.define('Indi.lib.controller.action.Calendar', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset.Calendar',

    // @inheritdoc
    extend: 'Indi.lib.controller.action.Rowset',

    /**
     * Config of panel, that will be used for representing the rowset
     */
    rowset: {
        xtype: 'calendarpanel',
        border: 0,

        /**
         * View config
         */
        viewConfig: {
            loadingText: Ext.LoadMask.prototype.msg
        },

        /**
         * Month-view config
         */
        monthViewCfg: {
            format: {
                calFirstDate: 'F j, o',
                headerWeekDay: 'l',
                headerWeekDayTitle: 'l, F j, Y',
                monthFirstDate: 'F, j',
                day: 'j',
                dayShowHeaderFalse: 'l, j',
                todayTime: 'H:i'
            },
            showWeekLinks: true,
            showHeader: true,
            showWeekNumbers: true,
            todayText: 'Сегодня',
            startDay: 1
        },

        /**
         * Week-view config
         */
        weekViewCfg: {
            format: {
                calFirstDate: 'F j, o',
                dayShowHeaderFalse: 'l, j',
                todayTime: 'H:i',
                time: 'H:i'
            },
            fromHour: 0,
            tillHour: 24,
            todayText: 'Сегодня',
            startDay: 1
        },

        /**
         * Day-view config
         */
        dayViewCfg: {
            fromHour: 0,
            tillHour: 24,
            format: {
                time: 'H:i'
            }
        },
        showNavBar: false
    },

    /**
     * Hide action-buttons
     * 
     * @param action
     * @return {*}
     */
    panelDockedInner$Actions_Default: function(action) {
        var me = this, btn = me.callParent(arguments);
        if (btn) btn.hidden = true;
        return btn;
    },

    /**
     * Expand time range
     *
     * @param store
     * @param rs
     */
    storeLoadCallback: function(store, rs) {
        var me = this, card = Ext.getCmp(me.rowset.id).getActiveView(), daily = me.ti().model.daily;

        // Add results count
        Ext.getCmp(me.panel.id).query('button[toggleGroup]').forEach(function(btn){
            btn.setText(btn.getText().split(':')[0]
                + (btn.pressed ? ': <span class="i-calendar-total">' + store.getTotalCount() + '</span>' : ''));
        });

        // If no daily hours set - return
        if (!daily) return;

        // More aux variables
        var s = daily.since, u = daily.until,
            from = s ? parseInt(s.split(':')[0]) :  0, fromHour, minHour = null, f,
            till = u ? parseInt(u.split(':')[0]) : 24, tillHour, maxHour = null, t;

        // If card's fromHour setting is 0, or is not set - return
        if (card.xtype == 'monthview') return;

        // Get minimum spaceSince's hour, and maximum spaceUntil's hour among events
        rs.forEach(function(r){

            // Get minimum spaceSince's hour
            if (minHour === null) minHour = r.get('spaceSince').getHours();
            else if (r.get('spaceSince').getHours() < minHour) minHour = r.get('spaceSince').getHours();

            // Get hour
            t = r.get('spaceUntil').getHours() + (r.get('spaceUntil').getMinutes() || r.get('spaceUntil').getSeconds() ? 1 : 0) || 24;

            // Get maximum spaceUntil's hour, incremented by 1 in case of non-zero minutes/seconds
            if (maxHour === null || t > maxHour) maxHour = t;
        });

        // Set hour, that card should start from, and hour, that card should end till
        fromHour = Math.min(minHour === null ? from : minHour, from);
        tillHour = Math.max(maxHour === null ? till : maxHour, till);

        // If card bounds should be adjusted
        if (card.fromHour != fromHour || card.tillHour != tillHour) {

            // If hour, that card start from is not equal to hour, that card SHOULD start from - fix it
            if (card.fromHour != fromHour) card.fromHour = card.body.fromHour = card.body.tpl.fromHour = fromHour;

            // If hour, that card start from is not equal to hour, that card SHOULD start from - fix it
            if (card.tillHour != tillHour) card.tillHour = card.body.tillHour = card.body.tpl.tillHour = tillHour;

            // Re-run layout
            card.body.renderTemplate();
            card.refresh();
        }
    },

    /**
     * Here we force date-filter value to be picked from calendar panel active view's bounds
     */
    filterChange: function() {
        var me = this, card = Ext.getCmp(me.rowset.id).getActiveView(), bounds = card.getViewBounds(),
            query = '[isFilter][isImportantDespiteHidden]', from = Ext.getCmp(me.panel.id).down(query + '[isFrom]'),
            till = Ext.getCmp(me.panel.id).down(query + '[isTill]');

        // Pick values from calendar's active view's bounds
        from.noReload = true; from.setValue(bounds.start); from.noReload = false;
        till.noReload = true; till.setValue(bounds.end); till.noReload = false;

        // Call parent
        me.callParent(arguments);
    },

    /**
     * Here we set up `hidden` and `isImportantDespiteHidden` props for date-bounds filters
     *
     * @param filter
     * @return {*}
     */
    panelDocked$FilterXCalendar: function(filter) {
        var me = this, fA = me.callParent(arguments);

        // If current filter is not a special filter added for calendar panel system purposes - return as is
        if (filter.foreign('fieldId').alias != 'spaceSince') return fA;

        // If gte/lte filters are special filters added for calendar panel system purposes
        // - set up both `hidden` and `isImportantDespiteHidden` props to `true`
        fA.forEach(function(r, i, a){
            r.hidden = r.isImportantDespiteHidden = true;
        });

        // Return
        return fA;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this, colorField = me.ti().section.colors ? me.ti().section.colors.field : false, kanban;

        // Setup id
        me.id = me.bid();

        // If daily hours were set
        if (me.ti().model.daily) {

            // Set from hours
            if (me.ti().model.daily.since)
                me.rowset.dayViewCfg.fromHour = me.rowset.weekViewCfg.fromHour
                    = parseInt(me.ti().model.daily.since.split(':')[0]);

            // Set till hours
            if (me.ti().model.daily.until)
                me.rowset.dayViewCfg.tillHour = me.rowset.weekViewCfg.tillHour
                    = parseInt(me.ti().model.daily.until.split(':')[0]);
        }

        // Pass kanban info into day-view cfg
        if (kanban = me.ti().section.kanban) me.rowset.dayViewCfg.kanban = kanban;

        // Set flag, indicating whether or not only month view should be available within calendar panel
        var onlyMonthView = Indi.in(me.ti().model.space.scheme, 'date,datetime,date-time,date-timeId,date-dayQty');

        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-calendar',
            store: me.getStore(),
            colors: me.ti().section.colors,
            showWeekView: !onlyMonthView,
            showDayView: !onlyMonthView,
            dayViewCfg: {store: me.getStore(), colorField: colorField, scheme: me.ti().model.space.scheme},
            weekViewCfg: {store: me.getStore(), colorField: colorField, scheme: me.ti().model.space.scheme},
            monthViewCfg: {store: me.getStore(), colorField: colorField, scheme: me.ti().model.space.scheme},
            listeners: {
                initdrag: function(view, rec){
                    if (!rec) return;

                    // Make a special request to get the inaccessible values for each field considering their current values
                    Indi.load('/' + me.ti().section.alias + '/form/id/' + rec.get('id') + '/consider/duration/', {
                        params: {
                            purpose: 'drag',
                            uixtype: view.ownerCt.xtype,
                            fromHour: view.ownerCt.fromHour
                        },
                        success: function(response) {

                            // Get info about disabled values for each field
                            var dd = response.responseText.json().disabled;

                            // Apply those disabled values, so only non-disabled will remain accessible
                            view.setDisabledValues(dd);
                        }
                    });
                },
                eventmove: function(view, rec, eOpts) {
                    me.recordRemoteSave(rec, view.store.indexOfTotal(rec) + 1);
                },
                eventresize: function(view, rec, eOpts) {
                    me.recordRemoteSave(rec, view.store.indexOfTotal(rec) + 1);
                },
                eventclick: function(view, rec, el, eOpts) {
                    var action = me.ti().actions.r('form', 'alias'), aix = view.store.indexOfTotal(rec);
                    if (action) me.panelDockedInner$Actions_DefaultInnerHandlerLoad(action, rec, aix + 1);
                },
                dayclick: function(view, date, wtf, el, kanban) {
                    me.rowset.space = {since: Ext.Date.format(date, 'U')};
                    if (kanban) me.rowset.space.kanban = kanban;
                    var create = Ext.getCmp(me.id + '-docked-inner$create');
                    if (create && !create.disabled) create.press();
                },
                rangeselect: function(view, range) {
                    if (view.xtype == 'monthview') range.spaceUntil = Ext.Date.add(range.spaceUntil, Ext.Date.DAY, 1);
                    me.rowset.space = {
                        since: Ext.Date.format(range.spaceSince, 'U'),
                        until: Ext.Date.format(range.spaceUntil, 'U')
                    }
                    if (range.kanban) me.rowset.space.kanban = range.kanban;
                    view.dropZone.clearShims();
                    var create = Ext.getCmp(me.id + '-docked-inner$create');
                    if (create && !create.disabled) create.press();
                },
                viewchange: function(p, vw, dateInfo){
                    var m = Ext.getCmp(p.id +'-tb-month');
                    if (dateInfo && m) m.setText(Ext.Date.format(dateInfo.activeDate, 'F'));
                },
                boxready: function(c) {

                    // Apply colors
                    if (c.colors) {

                        // Template styles
                        var tpl = '' +
                            '{scope}-month-details-panel-body .ext-color-{option}.ext-cal-evt.ext-cal-evr, ' +
                            '{scope} .ext-color-{option}, ' +
                            '{scope} .x-ie .ext-color-{option}-ad, ' +
                            '{scope} .x-opera .ext-color-{option}-ad { ' +
                            'color: {color};' +
                            '} ' +
                            '{scope} .ext-cal-day-col .ext-color-{option}, ' +
                            '{scope} .ext-dd-drag-proxy .ext-color-{option}, ' +
                            '{scope} .ext-color-{option}-ad, ' +
                            '{scope} .ext-color-{option}-ad .ext-cal-evm, ' +
                            '{scope} .ext-color-{option} .ext-cal-picker-icon, ' +
                            '{scope} .ext-color-{option}-x dl, ' +
                            '{scope} .ext-color-{option}-x .ext-cal-evb { ' +
                            'background: {background-color}; ' +
                            '} ' +
                            '{scope} .ext-color-{option}-x .ext-cal-evb, ' +
                            '{scope} .ext-color-{option}-ad .ext-cal-evm, ' +
                            '{scope} .ext-color-{option}-ad, ' +
                            '{scope} .ext-color-{option}-x dl, ' +
                            '{scope} .ext-color-{option}.ext-cal-evt.ext-cal-evr {' +
                            'border: 1px dotted {border-color}; ' +
                            '}' +
                            '{scope} .ext-color-{option} .ext-evt-rsz-h {' +
                            'border-color: {border-color}; ' +
                            '}';

                        var cssA = [], css = '';
                        for (var value in c.colors.colors) {
                            css = tpl.replace(/\{scope\}/g, '#' + c.id);
                            css = css.replace(/\{option\}/g, value);
                            css = css.replace(/\{color\}/g, c.colors.colors[value]['color']);
                            css = css.replace(/\{border-color\}/g, c.colors.colors[value]['border-color']);
                            css = css.replace(/\{background-color\}/g, c.colors.colors[value]['background-color']);
                            cssA.push(css);
                        }

                        // Insert style node
                        Ext.DomHelper.insertFirst(c.el, '<style>' + cssA.join("\n") + '</style>');
                    }

                    // Get master toolbar
                    var master = Ext.getCmp(me.panel.id.replace(/wrapper$/, '') + 'toolbar-master');

                    // Prepend navigation items
                    if (this.tbarItems && this.tbarItems.length) {
                        this.tbarItems.pop();
                        master.insert(0, this.tbarItems);
                        master.insert(this.tbarItems.length, '-')
                    }
                }
            }
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        me.callParent();
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
                    var south, already, qs = '';

                    // If we are
                    if (this.ctx().rowset.space) {
                        if (this.ctx().rowset.space.since) qs = 'since/' + this.ctx().rowset.space.since + '/';
                        if (this.ctx().rowset.space.until) qs += 'until/' + this.ctx().rowset.space.until + '/';
                        if (this.ctx().rowset.space.kanban) qs += 'kanban/' + this.ctx().rowset.space.kanban+ '/';
                    }

                    // If Ctrl-key is pressed
                    if (Ext.EventObject.ctrlKey) {

                        // Get south region panel
                        south = Ext.getCmp(me.panel.id).down('[isSouth]');

                        // If tab, that we want to add - is already exists within south region panel - set it active
                        if (already = south.down('[isSouthItem][name="0"]')) south.setActiveTab(already);

                        // Else add new tab within south panel
                        else south.add(me.southItemIDefault({
                            id: 0,
                            title: Indi.lang.I_CREATE,
                            qs: qs
                        }));

                        // Else proceed standard behaviour
                    } else Indi.load('/' + section.alias + '/' + this.actionAlias + '/ph/' + section.primaryHash + '/' + qs);
                }
            }
        }
    }
});

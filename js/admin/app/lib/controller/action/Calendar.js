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
        var me = this, colorField = me.ti().section.colors ? me.ti().section.colors.field : false;

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

        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-calendar',
            store: me.getStore(),
            colors: me.ti().section.colors,
            dayViewCfg: {store: me.getStore(), colorField: colorField},
            weekViewCfg: {store: me.getStore(), colorField: colorField},
            monthViewCfg: {store: me.getStore(), colorField: colorField},
            listeners: {
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
                dayclick: function(view, date) {
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
    }
});

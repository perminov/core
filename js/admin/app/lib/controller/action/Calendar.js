/**
 * Base class for all controller actions instances, that operate with rowsets,
 * and use Ext.panel.Grid view to display/modify those rowsets
 */
Ext.define('Indi.lib.controller.action.Calendar', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset.Calendar',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Rowset',

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
            fromHour: 4,
            tillHour: 20,
            todayText: 'Сегодня',
            startDay: 1
        },

        /**
         * Day-view config
         */
        dayViewCfg: {
            fromHour: 4,
            tillHour: 20,
            format: {
                time: 'H:i'
            }
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
        if (me.ti().model.dateColumn != filter.foreign('fieldId').alias) return fA;

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
        var me = this;

        // Setup id
        me.id = me.bid();

        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-calendar',
            store: me.getStore(),
            dayViewCfg: {store: me.getStore()},
            weekViewCfg: {store: me.getStore()},
            monthViewCfg: {store: me.getStore()}
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        me.callParent();
    }
});

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
        var me = this, colorField = me.ti().section.colors ? me.ti().section.colors.field : false;

        // Setup id
        me.id = me.bid();



        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-calendar',
            store: me.getStore(),
            colors: me.ti().section.colors,
            dayViewCfg: {store: me.getStore(), colorField: colorField},
            weekViewCfg: {store: me.getStore(), colorField: colorField},
            monthViewCfg: {store: me.getStore(), colorField: colorField},
            listeners: {
                eventclick: function(view, rec, el, eOpts) {
                    var action = me.ti().actions.r('form', 'alias');
                    if (action) me.panelDockedInner$Actions_DefaultInnerHandlerLoad(action, rec);
                },
                boxready: function(c) {
                    if (!c.colors) return;

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
                    '{scope} .ext-color-{option}-x dl { ' +
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
            }
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        me.callParent();
    }
});

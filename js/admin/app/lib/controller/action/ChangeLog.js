/**
 * General solution for dealing with grid, that is purposed to control a grid representing changelog entries
 */
Ext.define('Indi.lib.controller.action.ChangeLog', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset.ChangeLog',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Rowset.Grid',

    // @inheritdoc
    panel: {
        docked: {
            default: {minHeight: 27},
            items: [{alias: 'filter'}],
            inner: {
                filter: [{alias: 'keyword'}]
            }
        }
    },

    // @inheritdoc
    rowset: {
        features: [{
            ftype: 'grouping',
            groupHeaderTpl: '{name}'
        }]
    },

    panelDocked$Filter$Keyword: function() {
        return this.panelDockedInner$Keyword();
    },

    /**
     * Do not show excel-export toolbar button, as currently grouped grid is not available for excel-export at php-side
     *
     * @return {Object}
     */
    rowsetInner$Excel: function() {
        return {disabled: true};
    },

    /**
     * Grouper field
     */
    store: {
        groupField: 'datetime'
    },

    /**
     * Adjust all rows `datetime` properties, for them to contain `author` property contents additionally
     *
     * @param r {Ext.data.Model}
     */
    storeLoadCallbackDataRowAdjust: function(r) {
        r.set('datetime', r.get('datetime') + ' - ' + r.get('changerId'));
    },

    /**
     * Turn Off Author grid column, as author titles are involved within grouper contents
     *
     * @param column
     */
    gridColumn$ChangerId: function(column) {
        column = null;
    },

    /**
     * Turn Off Datetime grid column, as datetimes are involved within grouper contents
     *
     * @param column
     */
    gridColumn$Datetime: function(column) {
        column = null;
    },

    /**
     * Restrict most of column abilities for 'Field' column
     *
     * @param column
     * @return {*}
     */
    gridColumn$FieldId: function(column) {
        return Ext.merge(column, {
            groupable: false,
            sortable: false,
            menuDisabled: true,
            header: 'Что'
        });
    },

    /**
     * Restrict most of column abilities for 'Was' column
     *
     * @param column
     * @return {*}
     */
    gridColumn$Was: function(column) {
        return Ext.merge(column, {
            groupable: false,
            sortable: false,
            menuDisabled: true,
            renderer: function(value) {
                return value;
            }
        })
    },

    /**
     * Restrict most of column abilities for 'Now' column
     *
     * @param column
     * @return {*}
     */
    gridColumn$Now: function(column) {
        return Ext.merge(column, {
            groupable: false,
            sortable: false,
            menuDisabled: true,
            renderer: function(value) {
                return value;
            }
        })
    }
});

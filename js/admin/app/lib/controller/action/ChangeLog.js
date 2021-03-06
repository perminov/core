/**
 * General solution for dealing with grid, that is purposed to control a grid representing changelog entries
 */
Ext.define('Indi.lib.controller.action.ChangeLog', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset.ChangeLog',

    // @inheritdoc
    extend: 'Indi.lib.controller.action.Grid',

    // @inheritdoc
    panel: {
        docked: {
            default: {minHeight: 27},
            items: [{alias: 'filter', cls: 'x-poppable x-nobelow'}],
            inner: {
                filter: [{alias: 'keyword', margin: '0 5 4 2'}]
            }
        }
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
        groupField: 'changerId'
    },

    /**
     * Turn Off `entityId` grid column
     *
     * @param column
     */
    gridColumn$EntityId: false,

    /**
     * Turn Off `changerId` grid column, as it's mentioned within group title
     *
     * @param column
     */
    gridColumn$ChangerId: false,

    /**
     * Turn Off `profileId` grid column, as it's mentioned within group title
     *
     * @param column
     */
    gridColumn$ProfileId: false,

    /**
     * Turn Off `datetime` grid column, as datetimes are involved within grouper contents
     *
     * @param column
     */
    gridColumn$Datetime: false,

    /**
     * Turn Off `key` grid column, as entry title (e.g. "<entity-title> » <entry-title>")
     * anyway won't be displayed as a separate grid column
     *
     * @param column
     */
    gridColumn$Key: false,

    /**
     * Restrict most of column abilities for 'Field' column
     *
     * @param column
     * @return {*}
     */
    gridColumn$FieldId: {
        groupable: false,
        sortable: false,
        menuDisabled: true
    },

    /**
     * Restrict most of column abilities for 'Was' column
     *
     * @param column
     * @return {*}
     */
    gridColumn$Was: {
        groupable: false,
        sortable: false,
        menuDisabled: true,
        renderer: function(value) {
            return value;
        }
    },

    /**
     * Restrict most of column abilities for 'Now' column
     *
     * @param column
     * @return {*}
     */
    gridColumn$Now: {
        groupable: false,
        sortable: false,
        menuDisabled: true,
        renderer: function(value) {
            return value;
        }
    }
});

/**
 * Base class for all controller actions instances, that operate with rowsets,
 * and use Ext.panel.Grid view to display/modify those rowsets
 */
Ext.define('Indi.lib.controller.action.Chart', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset.Chart',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Rowset',

    /**
     * Config of panel, that will be used for representing the rowset
     */
    rowset: {
        xtype : 'highstock',
        chartConfig : {
            chart : {
            }
        }
    },

    // @inheritdoc
    storeField_Default: function(field) {
        return {
            name: field.alias,
            mapping: field.alias
        }
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-chart',
            store: me.getStore(),
            series: me.seriesA(),
            dockedItems: me.rowsetDockedA()
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        me.callParent();
    },

    /**
     * Append `_system`.`xAxis` field
     *
     * @return {*}
     */
    storeFieldA: function() {
        var me = this, fieldA = me.callParent();

        // Append `_system`.`xAxis` field
        fieldA.push({
            name: '_system.xAxis',
            mapping: '_system.xAxis'
        });

        // Return
        return fieldA;
    },

    /**
     * Builder for array of series, that should be displayed within chart
     *
     * @return {Array}
     */
    seriesA: function() {
        return []
    }
});

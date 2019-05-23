/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.SiblingCombo', {

    // @inheritdoc
    extend: 'Indi.lib.form.field.Combo',

    // @inheritdoc
    alias: 'widget.combo.sibling',

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Default after render handler
        me.callParent(arguments);

        // Fit combo width
        me.fitWidth();
    },

    // @inheritdoc
    afterFetchAdjustments: function() {
        var me = this;

        // Default adjustments
        me.callParent(arguments);

        // Fit combo width
        me.fitWidth();
    },

    /**
     * Adjust combo width, so all involved things are taken into consideration while calculating least combo width
     */
    fitWidth: function() {
        var me = this, width = me.getFitWidth();

        // Set width
        me.setWidth(width);
    }
});
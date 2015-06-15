/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.SiblingCombo', {

    // @inheritdoc
    extend: 'Indi.form.Combo',

    // @inheritdoc
    alternateClassName: 'Indi.form.SiblingCombo',

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
    }
});
/**
 * This class is used to represent the certain level within the Indi Engine interface places hierarchy
 */
Ext.define('Indi.lib.trail.Item', {

    // @inheritdoc
    extend: 'Ext.Component',

    // @inheritdoc
    alternateClassName: 'Indi.trail.Item',

    // @inheritdoc
    constructor: function(config) {
        var me = this, f;

        // Call parent
        me.callParent(arguments);

        // Setup a 'href' property for each store's item's section object, as a shortcut, which will be used
        // in configuring urls for all system interface components, that are used for navigation
        me.section.href = Indi.pre + '/' + me.section.alias + '/';

        // Convert each filter to an instance of Indi.lib.dbtable.Row object
        if (me.filters)
            for (f = 0; f < me.filters.length; f++)
                me.filters[f] = new Indi.lib.dbtable.Row.prototype(me.filters[f]);

        // Convert each field to an instance of Indi.lib.dbtable.Row object
        if (me.fields)
            for (f = 0; f < me.fields.length; f++)
                me.fields[f] = new Indi.lib.dbtable.Row.prototype(me.fields[f]);

        // Convert this.row plain object to an instance of Indi.lib.dbtable.Row object
        if (me.row) me.row = new Indi.lib.dbtable.Row.prototype(me.row);

        // Convert this.filtersShared row plain object to an instance of Indi.lib.dbtable.Row object
        if (me.filtersSharedRow) me.filtersSharedRow = new Indi.lib.dbtable.Row.prototype(me.filtersSharedRow);
    }
});
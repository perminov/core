/**
 * This class is used to represent the certain level within the Indi Engine interface places hierarchy
 */
Ext.define('Indi.lib.trail.Item', {

    // @inheritdoc
    extend: 'Ext.Component',

    // @inheritdoc
    alternateClassName: 'Indi.trail.Item',

    /**
     * Build and return the base id for current trail item, that can be used
     * as a prefix for ids of all components, created within current trail item,
     * so these ids will be unique.
     *
     * @return {String}
     */
    bid: function() {
        var s = 'i-section-' + this.section.alias + '-action-' + this.action.alias;
        if (this.row) {
            s += '-row-' + (this.row.id || 0);
        } else if (this.parent().row) {
            s += '-parentrow-' + this.parent().row.id;
        }
        return s;
    },

    /**
     * Get the parent trail item. If `up` arg is given, grandparent,
     * grandgrandparent etc. trail items can be accessed
     *
     * @param up
     * @return {Indi.lib.trail.Item}
     */
    parent: function(up) {
        return Indi.trail(true).store[this.level - 1 - (up ? up : 0)];
    },

    // @inheritdoc
    constructor: function(config) {

        // Call parent
        this.callParent(arguments);

        // Setup a 'href' property for each store's item's section object, as a shortcut, which will be used
        // in configuring urls for all system interface components, that are used for navigation
        this.section.href = Indi.pre + '/' + this.section.alias + '/';

        // Convert each filter to an instance of Indi.lib.dbtable.Row object
        if (this.filters)
            for (var f = 0; f < this.filters.length; f++)
                this.filters[f] = new Indi.lib.dbtable.Row.prototype(this.filters[f]);

        // Convert each field to an instance of Indi.lib.dbtable.Row object
        if (this.fields)
            for (var f = 0; f < this.fields.length; f++)
                this.fields[f] = new Indi.lib.dbtable.Row.prototype(this.fields[f]);

        // Convert this.row plain object to an instance of Indi.lib.dbtable.Row object
        if (this.row) this.row = new Indi.lib.dbtable.Row.prototype(this.row);

        // Convert this.filtersShared row plain object to an instance of Indi.lib.dbtable.Row object
        if (this.filtersSharedRow) this.filtersSharedRow = new Indi.lib.dbtable.Row.prototype(this.filtersSharedRow);
    }
});
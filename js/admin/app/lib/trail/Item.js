Ext.define('Indi.lib.trail.Item', {
    extend: 'Ext.Component',
    alternateClassName: 'Indi.trail.Item',
    bid: function() {
        var s = 'i-section-' + this.section.alias + '-action-' + this.action.alias;
        if (this.row) {
            s += '-row-' + (this.row.id || 0);
        } else if (this.parent().row) {
            s += '-parentrow-' + this.parent().row.id;
        }
        return s;
    },

    parent: function(up) {
        return Indi.trail(true).store[this.level - 1 - (up ? up : 0)];
    },

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
//                this.fields[f] = Ext.create('Indi.lib.dbtable.Row', this.fields[f]);
                this.fields[f] = new Indi.lib.dbtable.Row.prototype(this.fields[f]);

        // Convert this.row plain object to an instance of Indi.lib.dbtable.Row object
        //if (this.row) this.row = Ext.create('Indi.lib.dbtable.Row', this.row);
        if (this.row) this.row = new Indi.lib.dbtable.Row.prototype(this.row);

        if (this.filtersSharedRow) this.filtersSharedRow = new Indi.lib.dbtable.Row.prototype(this.filtersSharedRow);
    }
});
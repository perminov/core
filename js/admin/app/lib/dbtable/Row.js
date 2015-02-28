/**
 * Create the 'Indi.lib.dbtable.Row' namespace and prototype
 */
Ext.ns('Indi.lib.dbtable.Row');
Indi.lib.dbtable.Row.prototype = function (data) {

    /**
     * Get foreign data (row/rowset) by a given foreign key/field name/alias
     *
     * @param key
     * @return {Object/Array}
     */
    this.foreign = function(key) {

        // If key name is 'fieldId'
        if (key == 'fieldId') return Indi.field(this.fieldId);

        // Else if key name is not 'fieldId', but current row has `_foreign` property, and
        // such key - is one of existing keys within `_foreign` property - return it
        else if (this._foreign && this._foreign[key]) return this._foreign[key];
    };

    /**
     * Return the nested data, stored under `key` arguments within current row's `_nested` property
     *
     * @param key
     * @return {*}
     */
    this.nested = function(key) {
        return this._nested[key];
    };

    /**
     * Return the view data, stored under `key` arguments within current row's `_view` property
     *
     * @param key
     * @return {*}
     */
    this.view = function(key) {
        return this._view[key];
    };

    /**
     * Apply `data` argument to current row instance
     */
    Ext.merge(this, data);
};

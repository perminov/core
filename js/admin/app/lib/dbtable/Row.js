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
        if (key == 'fieldId') {

            // For each trail item we check if it has fields, and if so, we try to find the field
            // (within those fields), that has the same value as row object's `fieldId` property,
            // and if such field will be found - return it
            for (var i = 0; i < Indi.trail(true).store.length; i++)
                if (Indi.trail(i).fields)
                    for (var j = 0; j < Indi.trail(i).fields.length; j++)
                        if (parseInt(Indi.trail(i).fields[j].id) == parseInt(this.fieldId))
                            return Indi.trail(i).fields[j];

        // Else if key name is not 'fieldId', but current row has `_foreign` property, and
        // such key - is one of existing keys within `_foreign` property - return it
        } else if (this._foreign && this._foreign[key])
            return this._foreign[key];
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

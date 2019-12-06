Ext.override(Ext.data.Model, {

    /**
     * Deal with keys
     *
     * @param key
     * @param val
     * @param affected
     * @return {*}
     */
    key: function(key, val, affected) {
        var me = this;

        // If model has no `$keys` property within it's `raw` object, or it's not an object - return
        if (!Ext.isObject(me.raw.$keys)) return null;

        // If `$keys` object within model's `raw` property has no own property, named as `key` argument - return
        if (!me.raw.$keys.hasOwnProperty(key)) return null;

        if (arguments.length > 1) {

            if (!affected) {
                me.modifiedKeys = me.modifiedKeys || {};
                if (val != me.modifiedKeys[key]) me.modifiedKeys[key] = me.raw.$keys[key];
                else delete me.modifiedKeys[key];
            }

            return me.raw.$keys[key] = val;

        } else {

            // Return original value for a given key name
            return me.raw.$keys[key];
        }
    },

    /**
     * Deal with keys
     *
     * @param silent
     */
    reject : function(silent) {
        var me = this,
            modified = me.modified,
            modifiedKeys = me.modifiedKeys,
            field;

        for (field in modified) {
            if (modified.hasOwnProperty(field)) {
                if (typeof modified[field] != "function") {
                    me[me.persistenceProperty][field] = modified[field];
                }
            }
        }

        if (me.modifiedKeys) for (field in modifiedKeys) {
            if (modifiedKeys.hasOwnProperty(field)) {
                if (typeof modifiedKeys[field] != "function") {
                    me.raw.$keys[field] = modifiedKeys[field];
                }
            }
        }

        me.dirty = false;
        me.editing = false;
        me.modified = {};
        me.modifiedKeys = {};

        if (silent !== true) {
            me.afterReject();
        }
    },

    /**
     * Get value from system data
     *
     * @param key
     * @return {*}
     */
    system: function(key) {
        return this.raw._system[key];
    }
});
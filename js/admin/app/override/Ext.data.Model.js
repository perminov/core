/**
 * Here we override Ext.Component component, to provide an ability for 'tooltip' config properties to be used for
 * creating Ext.tip.ToolTip objects instead of standart Ext.tip.QuickTip objects
 */
Ext.override(Ext.data.Model, {
    key: function(key, val) {
        var me = this;

        // If model has no `$keys` property within it's `raw` object, or it's not an object - return
        if (!Ext.isObject(me.raw.$keys)) return null;

        // If `$keys` object within model's `raw` property has no own property, named as `key` argument - return
        if (!me.raw.$keys.hasOwnProperty(key)) return null;

        // Return original value for a given key name
        return arguments.length > 1 ? (me.raw.$keys[key] = val) : me.raw.$keys[key];
    }
});
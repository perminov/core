/**
 * The general purpose of this override is to provide the ability to threat this component instances values
 * as strings rather than objects, containing key-value pairs
 */
Ext.override(Ext.form.RadioGroup, {

    /**
     * Override native getValue() method for it to return the value of checked radio item,
     * instead of object, containing key-value pair
     *
     * @return {*}
     */
    getValue: function() {
        return this.callParent(arguments)[this.name];
    },

    /**
     * Override native checkChange() method for it to skip several operations that assume the return of getValue()
     * calls is object, as bit upper we overrided getValue() method for it to return non-object value
     *
     * @return {*}
     */
    checkChange: function() {
        Ext.form.field.Field.prototype.checkChange.call(this, arguments);
    },

    /**
     * Override native checkChange() method for it to be able to deal with non-object `value` argument, e.g
     * not only setValue({name: 'value'}) calls are allowable, but setValue('value') call are too
     *
     * @param {Object/String} value
     * @return {*}
     */
    setValue: function(value) {
        var arg1 = value;
        if (!Ext.isObject(value)) {
            arg1 = {};
            arg1[this.name] = value;
        }
        return this.callParent([arg1]);
    }
});
/**
 * The general purpose of this component is to provide the ability to treat this
 * component instances values as strings rather than objects containing key-value pairs
 */
Ext.define('Indi.lib.form.field.Radios', {

    // @inheritdoc
    extend: 'Ext.form.RadioGroup',

    // @inheritdoc
    alternateClassName: 'Indi.form.Radios',

    // @inheritdoc
    alias: 'widget.radios',

    // @inheritdoc
    columns: 1,

    // @inheritdoc
    vertical: true,

    // @inheritdoc
    initComponent: function() {
        var me = this;
        me.items = me.itemA();
        me.callParent();
    },

    /**
     * Build and return radio items array
     *
     * @return {Array}
     */
    itemA: function() {

        // Setup auxiliary variables
        var me = this, itemI, itemA = [], inputValue;

        // For each store data item
        me.row.view(me.name).store.data.forEach(function(enumset, index){

            // Get radio input value
            inputValue = me.row.view(me.name).store.ids[index] + '';

            // Prepare initial radio item cfg
            itemI = {
                name: me.name,
                id: me.id + '$' + inputValue,
                inputValue: inputValue,
                checked: inputValue == me.row[me.name],
                enumset: enumset,
                listeners: {
                    change: function(rb, now) {
                        if (now) {
                            try {
                                Indi.eval(rb.enumset.system.js, rb.ownerCt);
                            } catch (e) {
                                throw e;
                            }
                        }
                    }
                }
            }

            // Append item cfg to the items array
            itemA.push(itemI);
        });

        return itemA;
    },

    // @inheritdoc
    afterRender: function() {

        // Get checked radio
        var me = this, checked = me.getChecked()[0];

        // If checked radio exists - fire 'change' event for it
        if (checked) checked.fireEvent('change', checked, true);

        // Execute javascript code, assigned as an additional handler value change event
        if (me.field.javascript) Indi.eval(me.field.javascript, me);

        // Call parent
        me.callParent();
    },

    /**
     * Function that will be called after combo value change. Provide dependent-combos reloading in case
     * if current field is a satellite for one or more combos, that are siblings to current field
     */
    onChange: function() {

        // Setup auxilliary variables
        var me = this, name = me.name, dComboName, dCombo;

        // Execute javascript code, assigned as an additional handler value change event
        if (me.field.javascript) Indi.eval(me.field.javascript, me);

        // Call parent
        me.callParent(arguments);

        // If current field is a satellite for one or more sibling combos, we should refresh data in that sibling combos
        if (me.ownerCt) me.ownerCt.query('[satellite="' + me.field.id + '"]').forEach(function(d){
            if (d.xtype == 'combo.form') {
                d.setDisabled(false, true);
                if (!d.disabled) {
                    d.remoteFetch({
                        satellite: me.getValue(),
                        mode: 'refresh-children'
                    });
                }
            }
        });
    },

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
     * Override native {xtype:radiogroup}'s checkChange() method, and use {Ext.form.field.Field}'s one directly,
     * for skipping several operations that assume the return value of getValue() method is object, as bit upper
     * we overrided getValue() method for it to return non-object value
     *
     * @return {*}
     */
    checkChange: function() {
        if (!Ext.isArray(this.getValue())) Ext.form.field.Field.prototype.checkChange.call(this);
    },

    /**
     * Override native setValue() method for it to be able to deal with non-object `value` argument, e.g
     * not only setValue({name: 'value'}) calls are allowable, but setValue('value') call are too
     *
     * @param {Object/String} value
     * @return {*}
     */
    setValue: function(value) {
        return this.callParent([this.normalizeValue(value)]);
    },

    /**
     * Convert given `value` argument to a format, that this component's
     * parent component - xtype: radiogroup - is used to deal
     *
     * @param value
     * @return {Object}
     */
    normalizeValue: function(value) {
        var me = this, normalized = {};

        // If `value` argument is an object - return it as is
        if (Ext.isObject(value)) return value;

        // Build object, containing normalized value
        normalized[me.name] = value;

        // Return normalized value
        return normalized;
    }
});
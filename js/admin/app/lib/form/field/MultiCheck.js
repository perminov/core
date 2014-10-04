/**
 * The general purpose of this component is to provide the ability to treat this
 * component instances values as strings rather than objects
 */
Ext.define('Indi.lib.form.field.MultiCheck', {

    // @inheritdoc
    extend: 'Ext.form.CheckboxGroup',

    // @inheritdoc
    alternateClassName: 'Indi.form.MultiCheck',

    // @inheritdoc
    alias: 'widget.multicheck',

    /**
     * Css class for component
     */
    cls: 'i-field-multicheck',

    //minChecked: 0,
    maxChecked: 0,

    // @inheritdoc
    columns: 'auto',

    // @inheritdoc
    vertical: true,

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Normalize value
        me.value = me.normalizeValue(me.value);

        // Setup checkboxes
        me.items = me.itemA();

        // Call parent
        me.callParent();
    },

    // @inheritdoc
    beforeRender: function() {
        var me = this;
        if (me.columns == 'auto') me.columns = 1;
        me.callParent();
    },

    /**
     * Override native getValue() method for it to return the string, containing comma-imploded values
     * of each checked checkbox's inputValue property, instead of object-format representation
     *
     * @return {*}
     */
    getValue: function() {
        var me = this, value = me.callParent(arguments)[me.name];
        return Ext.isArray(value) ? value.join(',') : (value == undefined ? "" : value);
    },

    /**
     * Override native setValue() method for it to be able to deal with non-object `value` argument, e.g
     * not only setValue({name: ['value1', 'value2']}) calls are allowable, but also
     * setValue('value1,value2'), setValue(['value1', 'value2']), setValue('1,2'), setValue(['1', 2]) are too,
     * as this component assumes that names of each checkbox are the same within the single MultiCheck component
     *
     * @param {Object/String} value
     * @return {*}
     */
    setValue: function(value) {

        // Normalize value
        value = this.normalizeValue(value);

        // Call parent
        return this.callParent([value]);
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
                checked: me.row[me.name].length && me.row[me.name].split(',').indexOf(inputValue) != -1,
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
                },

                // Here we ensure that each individual checkbox's value won't be submit,
                // mean the native data submit approach, implemented for xtype:checkboxgroup
                // "the form will get the info from the individual checkboxes themselves."
                // - will not be in use. There is used different approach for this component,
                // and that approach assumes that getSubmitData method of whole xtype:multicheck
                // won't return null value
                getSubmitData: function() {
                    return null;
                }
            }

            // Append item cfg to the items array
            itemA.push(itemI);
        });

        // Return checkboxes configs array
        return itemA;
    },

    // @inheritdoc
    afterRender: function() {

        // Get checked radio
        var me = this, checked = me.getChecked();

        // If checked checkboxes exists - fire 'change' event for each
        for (var i = 0; i < checked.length; i++)
            checked[i].fireEvent('change', checked[i], true);

        // Execute javascript code, assigned as an additional handler value change event
        if (me.field.javascript) Indi.eval(me.field.javascript, me);

        // Call parent
        me.callParent();
    },

    /**
     * Here we use data submit approach, that differs from the Ext's native one:
     * 1. We totally switch off submitting values of each individual checkbox grouped under this component
     * 2. We override this method for it to return object, containing imploded values of
     *    all checked checkboxes grouped under this component, rather that returning null
     *
     * @return {Object}
     */
    getSubmitData: function() {
        var me = this, data = me.normalizeValue(me.getValue());
        data[me.name] = data[me.name].join(',');
        return data;
    },

    /**
     * Convert given `value` argument to a format, that this component's
     * parent component - xtype: checkboxgroup is used to deal
     *
     * @param value
     * @return {Object}
     */
    normalizeValue: function(value) {

        // Define auxilliary variables
        var me = this, normalized = {};

        // If `value` arg is already an object - return it
        if (Ext.isObject(value)) value = value[me.name];

        // If `value` arg is an array
        if (Ext.isArray(value)) {

            // Convert each array item as string
            for (var i = 0; i < value.length; i++) {

                // If item is null/empty/undefined - drop it from array
                if (!value[i]) value.splice(i, 1);

                // Else convert it to string
                else value[i] = value[i] + '';
            }

            // Put `value` into `normalized` object, as a value of me.name property
            normalized[me.name] = value;

            // Return normalized value
            return normalized;
        }

        // If `value` argument is a number - convert it to string
        if (Ext.isNumber(value)) value = value + '';

        // If value is a non-emtpy string - split it by comma, or setup an empy array
        normalized[me.name] = Ext.isString(value) && value.length ? value.split(','): [];

        // Return normalized value
        return normalized;
    },

    /**
     * Override native {xtype:radiogroup}'s checkChange() method, and use {Ext.form.field.Field}'s one directly,
     * for skipping several operations that assume the return value of getValue() method is object, as bit upper
     * we overrided getValue() method for it to return non-object value
     *
     * @return {*}
     */
    checkChange: function() {
        var me = this;

        // Convert last value to string
        if (Ext.isObject(me.lastValue)) me.lastValue = me.lastValue[me.name].join(',');

        // Call {Ext.form.field.Field}'s checkChange() method
        Ext.form.field.Field.prototype.checkChange.call(me);
    },

    /**
     * Function that will be called after combo value change. Provide dependent-combos reloading in case
     * if current field is a satellite for one or more combos, that are siblings to current field
     */
    onChange: function() {

        // Setup auxilliary variables
        var me = this;

        // Execute javascript code, assigned as an additional handler value change event
        if (me.field.javascript) Indi.eval(me.field.javascript, me);

        // Call parent
        me.callParent(arguments);

        if (me.maxChecked) {
            if (me.getChecked().length >= me.maxChecked) {
                me.items.filter('checked', false).each(function(box){
                    box.disable();
                });
            } else me.items.each(function(box){
                box.enable();
            })
        }

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
    }
});
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
    mixins: {fieldBase: 'Ext.form.field.Base'},

    // @inheritdoc
    alias: 'widget.radios',

    // @inheritdoc
    columns: 1,

    // @inheritdoc
    vertical: true,

    /**
     * Array or comma-separated list of values of `inputValue` prop, that should be disabled
     */
    disabledOptions: [],

    /**
     * Append `zeroValue` property initialisation
     */
    constructor: function() {
        var me = this;
        me.callParent(arguments);
        me.mixins.fieldBase._constructor.call(this, arguments);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Build items
        me.items = me.itemA();

        // Setup disabled options
        if (Ext.isString(me.disabledOptions)) me.disabledOptions = me.disabledOptions.split(',');
        else if (me.disabledOptions === undefined) me.disabledOptions = [];

        // Call parent
        me.callParent();

        // Call mixin's initComponent method
        me.mixins.fieldBase._initComponent.call(this, arguments);
    },

    /**
     * Build and return radio items array
     *
     * @return {Array}
     */
    itemA: function() {
        var me = this, itemI, itemA = [], inputValue, disabled = {};

        // If `disabledOptions` arg is a string - split it by comma
        if (Ext.isString(me.disabledOptions)) me.disabledOptions = me.disabledOptions.split(',');

        // Store disabled values as keys
        me.disabledOptions.forEach(function(value){
            disabled[value] = true;
        });

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
                disabled: inputValue in disabled,
                onBoxClick: function(e) {
                    var me = this;
                    if (!me.disabled && !me.readOnly) {
                        if (me.getValue() === true && me.ownerCt.allowBlank === true) {
                            me.setValue(false);
                        } else {
                            me.setValue(true);
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

        // Call parent
        me.callParent();

        // Fire `considerchange` event
        me.mixins.fieldBase._afterRender.call(this, arguments);
    },

    /**
     * Disable options.
     *
     * @param disabledOptions May be passed as comma-separated string, or array
     * @return {*}
     */
    setDisabledOptions: function(disabledOptions) {
        var me = this, disabled = {};

        // If `disabledOptions` arg is a string - split it by comma
        if (Ext.isString(disabledOptions)) disabledOptions = disabledOptions.split(',');

        // Store disabled values as keys
        disabledOptions.forEach(function(value){
            disabled[value] = true;
        });

        // Toggle options
        me.items.each(function(item, i, l){
            item.setDisabled(item.inputValue in disabled);
        });

        // Update `disabledOptions` prop
        me.disabledOptions = disabledOptions;

        // Return
        return me;
    },

    /**
     * Function that will be called after radios value change
     */
    onChange: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Call mixin's _onChange() method
        me.mixins.fieldBase._onChange.call(this, arguments);
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
     * This function is declared here, because it was not declared in parent class
     *
     * @return {*}
     */
    getSubmitValue: function() {
        return this.getValue();
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
    },

    /**
     * Get this field's input actual width usage
     *
     * @return {Number}
     */
    getInputWidthUsage: function() {
        var me = this, width = 0, cbLabelWidth;

        // Walk through radio buttons
        me.items.each(function(item){

            // Get item's label
            cbLabelWidth = item.getEl().down('.x-form-cb-label').getWidth(true) + 10;

            // Get width
            width = Math.max(width, item.inputEl.getWidth() + cbLabelWidth);
        });

        // Return
        return width;
    }
});
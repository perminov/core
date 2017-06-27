/**
 * Replacement for native Ext's timefield
 */
Ext.define('Ext.lib.form.field.Time', {
    extend: 'Ext.form.field.Base',
	alias: 'widget.timefield',
	alternateClassName: 'Indi.form.Time',
	inputType: 'hidden',
    hideSeconds: true,
    mcopwso: ['spinnerCfg'],

    /**
     * Configuration object, for apply to all spinners, involved in this component
     */
    spinnerCfg: {
        width: 35,
        readOnlyWidth: 18,
        cls: 'i-field-time',
        style: 'float: left;',
        zeroPad: 2
    },

    // @inheritdoc
    initComponent: function() {
        var me = this, cfg;

        // Call parent
        me.callParent();

        // Declare spinners array
        me.spinners = [];

        // Prepare spinners config
        cfg = Ext.Object.merge({}, me.spinnerCfg, {
            readOnly: me.readOnly,
            disabled: me.disabled,
            timefield: me,
            listeners: {
                change: {
                    fn: me.onSpinnerChange,
                    scope: me
                }
            }
        });

        // Extend spinners config
        me.hSpinner = Ext.create('Ext.form.Number', Ext.apply({tooltip: 'Часы'}, cfg, {minValue: 0, maxValue: 23}));
        me.mSpinner = Ext.create('Ext.form.Number', Ext.apply({tooltip: 'Минуты'}, cfg, {minValue: 0, maxValue: 59}));
        me.sSpinner = Ext.create('Ext.form.Number', Ext.apply({tooltip: 'Секунды'}, cfg, {minValue: 0, maxValue: 59, hidden: me.hideSeconds}));

        // Append created spinners to the 'spinners' array
        me.spinners.push(me.hSpinner, me.mSpinner, me.sSpinner);
    },

    /**
     * Render spinners
     */
    onRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Render spinners to bodyEl
        me.spinnersCall('render', me.bodyEl);

        // Set raw value
        me.setRawValue(me.value);
    },

    /**
     * Get an object, containing h, m, and s properties, got by splitting 'value' argument by colon sign
     *
     * @param value
     * @return {Object}
     */
    splitValue: function(value) {

        // If 'value' arguments is a JavaScript Date object - convert it to time string in 'H:i:s' format
        if (Ext.isDate(value)) value = Ext.Date.format(value, 'H:i:s');

        // Setup value as '00:00:00', if value is null/empty, or does not match time regex pattern
        if (!Ext.isString(value) || !value.match(/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/)) value = '00:00:00';

        // Split value by a colon sign
        var split = value.split(':');

        // Return object, containing splitted value
        return {
            h: parseInt(split[0]) ? parseInt(split[0]) : (parseInt(split[1]) || parseInt(split[2]) ? '0' : ''),
            m: parseInt(split[1]) ? parseInt(split[1]) : (parseInt(split[0]) || parseInt(split[2]) ? '0' : ''),
            s: parseInt(split[2]) ? parseInt(split[2]) : (parseInt(split[0]) || parseInt(split[1]) ? '0' : '')
        };
    },

    /**
     * Call the function with name specified in 'fn' argument, and pass arguments, specified in 'args' argument
     * for each spinner within this.spinners array
     *
     * @param fn {String}
     * @param args
     */
    spinnersCall: function(fn, args) {
        this.spinners.forEach(function(spinner){
            spinner[fn].call(spinner, args);
        });
    },

    /**
     * Handler for 'change' event for every spinner within this component
     */
    onSpinnerChange: function() {
        var me = this, value = me.getValue(true);

        // We encapsulate me.setValue call with me.skipSpinnerSetValue flag,
        // to prevent setValue call on each spinner (within me.setRawValue method)
        // because we'd already picked values from spinners a moment ago and there is no
        // need to set the same values to spinners
        me.skipSpinnersSetValue = true;
        me.setValue(value);
        me.skipSpinnersSetValue = false;
    },

    // @inheritdoc
    setRawValue: function(value) {
        var me = this;

        // If 'value' argument is a string, or is an object, but not containing 'h', 'm' and 's' own properties
        if (Ext.isString(value) || !value || (Ext.isObject(value)
            && !(value.hasOwnProperty('h') && value.hasOwnProperty('m') && value.hasOwnProperty('s'))))

            // Split string into object, containing 'h', 'm' and 's' properties,
            // or if object - use empty time value string for splitting
            value = me.splitValue(value);

        // Set spinners values
        if (me.rendered && !me.skipSpinnersSetValue) {
            me.hSpinner.setValue(value.h);
            me.mSpinner.setValue(value.m);
            me.sSpinner.setValue(value.s);
        }

        // If value props are empty string, we replace thme with '00', to have proper value
        // to be set for me.rawValue property within me.callParent call
        value.h = Ext.String.leftPad(Ext.valueFrom(value.h, '00'), 2, '0');
        value.m = Ext.String.leftPad(Ext.valueFrom(value.m, '00'), 2, '0');
        value.s = Ext.String.leftPad(Ext.valueFrom(value.s, '00'), 2, '0');

        // Call parent
        me.callParent([value]);
    },

    /**
     * Get raw value either from current component's 'rawValue' property, or from spinners,
     * as object - if 'src' argument is set to boolean 'true'
     *
     * @param src
     * @return {*}
     */
    getRawValue: function(src) {
        var me = this;

        // If 'src' argument is set to boolean 'true', we pick raw value directly from spinners
        if (src) return {
            h: Ext.valueFrom(me.hSpinner.getValue(), 0),
            m: Ext.valueFrom(me.mSpinner.getValue(), 0),
            s: Ext.valueFrom(me.sSpinner.getValue(), 0)
        };

        // Else we just return self 'rawValue' prperty value
        else return this.rawValue;
    },

    /**
     * Get raw value either from current component's 'rawValue' property, or from spinners,
     * as object - if 'src' argument is set to boolean 'true'
     *
     * @param src
     * @return {*}
     */
    getValue: function(src) {

        // Get raw value
        var value = this.getRawValue(src);

        // Return stringified
        return [
            Ext.String.leftPad(value.h, 2, '0'),
            Ext.String.leftPad(value.m, 2, '0'),
            Ext.String.leftPad(value.s, 2, '0')
        ].join(':');
    },

    // @inheritdoc
    setValue: function(value) {

        // If 'value' argument is a JavaScript Date object, convert it to stringygied time,
        // else use '00:00:00' if it's empty/null/undefined
        value = Ext.isDate(value) ? Ext.Date.format(value, 'H:i:s') : Ext.valueFrom(value, '00:00:00');

        // Call parent
        this.callParent([value]);
    },

    // @inheritdoc
    getSubmitValue: function() {
        return this.getValue();
    },

    // @inheritdoc
    disable: function() {
        this.callParent(arguments);
        this.spinnersCall('disable', arguments);
    },

    // @inheritdoc
    enable: function() {
        this.callParent(arguments);
        this.spinnersCall('enable', arguments);
    },

    // @inheritdoc
    setReadOnly: function(readOnly) {
        this.callParent(arguments);
        this.spinnersCall('setReadOnly', readOnly);
        this.spinnersCall('setWidth', this.spinnerCfg[readOnly ? 'readOnlyWidth': 'width']);
    },

    // @inheritdoc
    clearInvalid: function() {
        this.callParent();
        this.spinnersCall('clearInvalid');
    },

    // @inheritdoc
    isValid: function(pm) {
        return this.hSpinner.isValid(pm) && this.mSpinner.isValid(pm) && this.sSpinner.isValid(pm);
    },

    // @inheritdoc
    validate: function() {
        return this.hSpinner.validate() && this.mSpinner.validate() && this.sSpinner.validate();
    },

    /**
     * Show/Hide seconds spinner
     *
     * @param show
     */
    showSeconds: function(show) {
        this.sSpinner[show ? 'show' : 'hide']();
    },

    /**
     * Get this field's input actual width usage
     *
     * @return {Number}
     */
    getInputWidthUsage: function() {
        var me = this, width = 0;

        // Walk through spinners and sum their widths
        me.spinners.forEach(function(item){
            width += item.getWidth() + item.getEl().getMargin('rl');
        });

        // Return
        return width;
    }
});
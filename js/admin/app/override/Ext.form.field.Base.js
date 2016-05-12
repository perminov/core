/**
 * Make up that hidden fields to be treated as valid
 */
Ext.override(Ext.form.field.Base, {
    statics: {

        /**
         * Zero-values for different xtypes
         */
        zeroValue: {
            ckeditor: '',
            'combo.form': {
                multiple: '',
                single: '0'
            },
            datetimefield: '0000-00-00 00:00:00',
            filepanel: '',
            'combo.filter': {
                multiple: '',
                single: '0'
            },
            multicheck: '',
            'combo.sibling': '0',
            timefield: '00:00:00',

            textfield: '',
            datefield: '0000-00-00',
            numberfield: '0',
            pricefield: '0.00',
            textarea: '',
            colorfield: ''
        }
    },

    /**
     * The list of additonal (non-primary) satellites
     */
    considerOn: [],

    /**
     * Append `zeroValue` property initialisation
     */
    constructor: function() {
        var me = this;
        me.callParent(arguments);
        me._constructor();
    },

    /**
     * Append 'enablebysatellite' event
     */
    initComponent: function() {
        var me = this;
        me.callParent();
        me._initComponent();
    },

    /**
     * Setup a value for `zeroValue` property, depending on current component's xtype
     */
    initZeroValue: function() {
        var me = this, zvO = Ext.form.field.Base.zeroValue;

        if (zvO.hasOwnProperty(me.xtype))
            me.zeroValue = Ext.isObject(zvO[me.xtype])
                ? zvO[me.xtype][me.multiSelect ? 'multiple' : 'single']
                : zvO[me.xtype];
    },

    /**
     * Assign the zeroValue as current value of the field
     *
     * @return {*}
     */
    clearValue: function() {
        var me = this;
        if (typeof me.zeroValue != 'undefined') me.setValue(me.zeroValue);
        return me;
    },

    /**
     * If current field is hidden - assume it's valid
     *
     * @return {*}
     */
    isValid: function() {
        return this.hidden || this.callParent();
    },

    /**
     * Checks whether or not current field value is a zero-value
     *
     * @return {Boolean}
     */
    hasZeroValue: function() {
        var me = this;
        return me.getValue() === null || me.getValue() == me.zeroValue;
    },

    /**
     * Provide current field to be disabled if any of required satellited
     * have zero value as it's current value
     */
    afterRender: function() {
        var me = this;
        me.callParent();
        me._afterRender();
    },

    /**
     * Check if any of required satellites currently has a zero-value,
     * and if so - disable current field, and assign a zero-value to it
     *
     * @return {Boolean} Result of a check
     */
    disableBySatellites: function() {
        var me = this, disable = false;

        // Check if any of required satellites currently has a zero-value,
        // and therefore current field should be disabled and zero-valued
        me.considerOn.forEach(function(item){
            if (item.required) disable = disable || me.sbl(item.name).hasZeroValue();
        });

        // Disable current field, and assign a zero-value to it
        if (disable) me.disable().clearValue();

        // Return the result of a check
        return disable;
    },

    /**
     * Collect all data, that current field's state depends on
     *
     * @return {Object}
     */
    considerOnData: function() {
        var me = this, data = {}, v, s;

        // Collect satellite values
        if (Ext.isArray(me.considerOn)) {
            me.considerOn.forEach(function(stl){
                if (me.sbl(stl.name)) {

                    // Get submit value
                    v = me.sbl(stl.name).getSubmitValue();

                    // Get it's string version
                    s = v + '';

                    // If s is a string, representing an integer number - convert it into interger-type and return
                    if (s.match(/^(-?[1-9][0-9]{0,9}|0)$/)) v = parseInt(s);

                    // If s is a string, representing a floating-point number - convert it into float-type and return
                    if (s.match(/^(-?[0-9]{1,8})(\.[0-9]{1,2})?$/)) v = parseFloat(s);

                    // If s is a string, representing a floating-point number, containing
                    // up to 10 digits in integer part, optionally prepended with an '-' sign,
                    // and containing up to 3 digits in fractional part - convert it into float-type
                    if (s.match(/^(-?[0-9]{1,10})(\.[0-9]{1,3})?$/)) v = parseFloat(s);

                    data[stl.name] = v;
                }
            });
        }

        // Return data
        return data;
    },

    /**
     * Enable current field and fire 'enablebysatellite' event, passing an object containing all satellites values
     */
    enableBySatellites: function(cfg) {
        var me = this;

        // Enable field
        if (!cfg.hasOwnProperty('enable') || cfg.enable) me.enable();

        // Clear value
        if (!cfg.hasOwnProperty('clear') || cfg.clear) me.clearValue();

        // Fire 'enablebysatellite' event
        me.fireEvent('enablebysatellite', me, me.considerOnData());
    },

    /**
     * Check whether or not current field's satellites are in state, that allows to enable/disable current field
     */
    toggleBySatellites: function(cfg) {
        var me = this; if (!me.disableBySatellites(cfg)) me.enableBySatellites(cfg);
    },

    /**
     * Lookup satellites changes
     */
    onChange: function() {
        var me = this; me.callParent(arguments); me._onChange();
    },

    /**
     * Mixin constructor() function, for usage in cases if current class is a mixin for another class
     *
     * @private
     */
    _constructor: function() {
        this.initZeroValue();
    },

    /**
     * Mixin initComponent() function, for usage in cases if current class is a mixin for another class
     *
     * @private
     */
    _initComponent: function() {
        this.addEvents('enablebysatellite');
    },

    /**
     * Mixin afterRender() function, for usage in cases if current class is a mixin for another class
     *
     * @private
     */
    _afterRender: function() {
        var me = this;
        if (!me.disabled && !me.disableBySatellites()) me.fireEvent('enablebysatellite', me, me.considerOnData());
    },

    /**
     * Mixin onChange() function, for usage in cases if current class is a mixin for another class
     *
     * @private
     */
    _onChange: function() {
        var me = this;

        // Lookup current field's satellites changes, and toggle it, depending on their state
        if (me.ownerCt) me.ownerCt.query('> [satellite]').forEach(function(sbl){
            if (Ext.isArray(sbl.considerOn)) {
                sbl.considerOn.forEach(function(considerOnStlCfg){
                    if (considerOnStlCfg.name == me.name) sbl.toggleBySatellites(considerOnStlCfg);
                });
            }
        });
    },

    /**
     * By default, assume height usage is equal to actual height
     *
     * @return {*}
     */
    getHeightUsage: function() {
        return this.getHeight();
    },

    /**
     * Get this field's actual width usage, separately for label and input
     *
     * @return {Object}
     */
    getWidthUsage: function() {
        return {label: this.getLabelWidthUsage(), input: this.getInputWidthUsage()}
    },

    /**
     * Get this field's label's actual width usage
     *
     * @return {Number}
     */
    getLabelWidthUsage: function() {
        var me = this, label = me.labelEl; return label.getTextWidth() + label.getPadding('lr') + label.getMargin('lr');
    },

    /**
     * Get this field's input actual width usage
     *
     * @return {Number}
     */
    getInputWidthUsage: function() {
        var me = this, input = me.inputEl, inputValueWidth = Indi.metrics.getWidth(me.getValue()), lim = 0;

        // Prevent inputValueWidth to be too great
        if (inputValueWidth > (lim = Ext.getCmp('i-center-center').getWidth()/2 * 0.8)) inputValueWidth = lim;

        // Return
        return inputValueWidth + input.getPadding('lr') + input.getMargin('lr') + input.getBorderWidth('lr');
    }
});
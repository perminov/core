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
            textarea: ''
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
        me.initZeroValue();
    },

    /**
     * Append 'enablebysatellite' event
     */
    initComponent: function() {
        var me = this;
        me.callParent();
        me.addEvents('enablebysatellite');
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
        if (!me.disabled && !me.disableBySatellites()) me.fireEvent('enablebysatellite', me, me.considerOnData());
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
        var me = this, data = {};

        // Collect satellite values
        if (Ext.isArray(me.considerOn)) {
            me.considerOn.forEach(function(stl){
                data[stl.name] = me.sbl(stl.name).getSubmitValue();
            });
        }

        // Return data
        return data;
    },

    /**
     * Enable current field and fire 'enablebysatellite' event, passing an object containing all satellites values
     */
    enableBySatellites: function() {
        var me = this, data = {};

        // Enable field
        me.enable().clearValue();

        // Fire 'enablebysatellite' event
        me.fireEvent('enablebysatellite', me, me.considerOnData());
    },

    /**
     * Check whether or not current field's satellites are in state, that allows to enable/disable current field
     */
    toggleBySatellites: function() {
        var me = this; if (!me.disableBySatellites()) me.enableBySatellites();
    },

    /**
     * Lookup satellites changes
     */
    onChange: function() {
        var me = this; me.callParent(arguments);

        // Lookup current field's satellites changes, and toggle it, depending on their state
        me.ownerCt.query('> [satellite]').forEach(function(sbl){
            if (Ext.isArray(sbl.considerOn)) {
                sbl.considerOn.forEach(function(stl){
                    if (stl.name == me.name) sbl.toggleBySatellites();
                });
            }
        });
    }
});
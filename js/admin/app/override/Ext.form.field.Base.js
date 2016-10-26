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
        me.lbarItems = me.lbarItems || [];
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
        me.initLbar();
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
        if ((!me.disabled || me.readOnly) && !me.disableBySatellites()) me.fireEvent('enablebysatellite', me, me.considerOnData());
    },

    /**
     * Mixin onChange() function, for usage in cases if current class is a mixin for another class
     *
     * @private
     */
    _onChange: function() {
        var me = this;

        // Lookup current field's satellites changes, and toggle it, depending on their state
        if (me.ownerCt) me.ownerCt.query('> *').forEach(function(sbl){
            if (Ext.isArray(sbl.considerOn)) {
                sbl.considerOn.forEach(function(considerOnStlCfg){
                    if (considerOnStlCfg.name == me.name) sbl.toggleBySatellites(considerOnStlCfg);
                });
            }
        });
    },

    /**
     * Init left-bar. Left-bar will be created only if me.lbarItems is not an empty array
     */
    initLbar: function() {
        var me = this;

        me.lbarItems = me.lbarItems || [];

        // Build lbarItems
        me.lbarItems.forEach(function(item){
            me.addBtn(item);
        });
    },

    /**
     * Get left-side bar, and preliminary create it, if it does not exists
     *
     * @return {*}
     */
    getLbar: function() {
        var me = this;

        // If left bar not yet exists - create it
        if (!me.lbar) me.lbar = Ext.create('Ext.toolbar.Toolbar', {
            autoShow: true,
            margin: '1 1 0 0',
            padding: 0,
            height: (me.triggerWrap || me.inputEl).getHeight() - 1,
            style: {
                background: 'none',
                float: 'right'
            },
            defaults: {
                xtype: 'button',
                padding: 0,
                target: me,
                enablerEvents: 'change',
                enabler: function(c, eventName, args) {
                    return !c.target.hasZeroValue();
                },
                listeners: {
                    boxready: function(c) {
                        c.setDisabled(c.enabler(c, 'boxready', arguments) ? false : true);
                        if (c.target.hidden) {
                            c.target.on('boxready', function(){
                                var w = 0; c.ownerCt.items.each(function(item){w += item.getWidth();});
                                c.ownerCt.setWidth(w + (c.ownerCt.items.length - 1) * 2 + 1);
                                c.ownerCt.setHeight((me.triggerWrap || me.inputEl).getHeight() - 1);
                            });
                        } else {
                            var w = 0; c.ownerCt.items.each(function(item){w += item.getWidth();});
                            c.ownerCt.setWidth(w + (c.ownerCt.items.length - 1) * 2 + 1);
                        }
                        c.enablerEvents.split(',').forEach(function(eventName){
                            c.target.on(eventName, function(newValue){
                                c.setDisabled(c.enabler(c, eventName, arguments) ? false : true);
                            }, me);
                        })
                    }
                }
            },
            renderTo: me.labelCell,
            border: 0
        });

        // Return left-bar
        return me.lbar;
    },

    /**
     * Add component into left-bar
     *
     * @param cfg
     */
    addBtn: function(cfg) {
        this.getLbar().add(cfg);
    },

    // @inheritdoc
    onDestroy: function() {
        var me = this;
        if (me.lbar) me.lbar.destroy();
        me.callParent(arguments);
    }
});
Ext.override(Ext.form.field.Text, {

    // @inheritdoc
    constructor: function(config) {
        var me = this; config.plugins = config.plugins || [];

        // Add InputMask plugin
        if (config.inputMask)
            config.plugins.push(new Ext.ux.form.field.plugin.InputMask(config.inputMask, { placeholder: '*' }));

        // Call parent
        me.callParent(arguments);
    }
});
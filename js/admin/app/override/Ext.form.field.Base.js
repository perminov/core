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
     * The list of consider-fields, e.g. fields that current field rely on
     */
    considerOn: [],

    /**
     * Append `zeroValue` property initialisation
     */
    constructor: function() {
        var me = this; me.considerOn = [];
        me.callParent(arguments);
        me._constructor();
    },

    // @inheritdoc
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

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent();

        // Do additional things, related to considerOn config
        me._afterRender();

        // Init left toolbar
        me.initLbar();
    },

    /**
     * Check if any of required consider-fields currently has a zero-value,
     * and if so - disable current field, and assign a zero-value to it
     *
     * @return {Boolean} Result of a check
     */
    disableBySatellites: function() {
        var me = this, disable = false, sbl;

        // Check if any of required consider-fields currently has a zero-value,
        // and therefore current field should be disabled and zero-valued
        me.considerOn.forEach(function(item){
            if (!item.required) return;
            if (disable) return;
            if (!(sbl = me.sbl(item.name))) return;
            disable = sbl.hasZeroValue();
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
        var me = this, data = {}, v, s, sbl;

        // Collect consider-fields values
        if (Ext.isArray(me.considerOn)) {
            me.considerOn.forEach(function(stl){
                if ((sbl = me.sbl(stl.name)) || me.row) {

                    // Get submit value
                    v = sbl ? sbl.getSubmitValue() : me.row[stl.name];

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
     * Enable current field and fire 'considerchange' event, passing an object containing all consider-fields values
     */
    enableBySatellites: function(cfg) {
        var me = this, data;

        // Enable field
        if (!cfg.hasOwnProperty('enable') || cfg.enable) me.enable();

        // Clear value
        if (!cfg.hasOwnProperty('clear') || cfg.clear) me.clearValue();

        // Get data
        data = me.considerOnData();

        // Fire 'enablebysatellite' event (kept temporarily, for backwards compatibility)
        me.fireEvent('enablebysatellite', me, data);

        // Fire 'considerchange' event
        me.fireEvent('considerchange', me, data);

        // Call 'onConsiderChange' method
        me.onConsiderChange(cfg, data);
    },

    /**
     *
     * @param сfg
     * @param data
     */
    onConsiderChange: function(сfg, data) {

    },

    /**
     * Check whether or not current field's consider-fields are in state, that allows to enable/disable current field
     */
    toggleBySatellites: function(cfg) {
        var me = this; if (!me.disableBySatellites(cfg)) me.enableBySatellites(cfg);
    },

    /**
     * Lookup consider-fields changes
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
        this.addEvents('enablebysatellite', 'considerchange');
    },

    /**
     * Mixin afterRender() function, for usage in cases if current class is a mixin for another class
     *
     * @private
     */
    _afterRender: function() {
        var me = this;
        if ((!me.disabled || me.readOnly) && !me.disableBySatellites()) {
            me.fireEvent('enablebysatellite', me, me.considerOnData());
            me.fireEvent('considerchange', me, me.considerOnData());
        }
    },

    /**
     * Mixin onChange() function, for usage in cases if current class is a mixin for another class
     *
     * @private
     */
    _onChange: function() {
        var me = this;

        // Lookup current field's dependent fields, and toggle them
        if (me.ownerCt) me.ownerCt.query('> *').forEach(function(sbl){
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
        if (!me.lbar) me.lbar = Ext.create('Ext.toolbar.Toolbar', Ext.merge({
            autoShow: true,
            margin: 0,
            padding: 0,
            height: (me.triggerWrap || me.inputEl).getHeight() - 1,
            width: 60,
            border: 0,
            items: ['->'],
            style: {
                background: 'none',
                float: 'right'
            },
            defaults: {
                xtype: 'button',
                padding: 0,
                height: 17,
                target: me,
                enablerEvents: 'change',
                enabler: function(c, eventName, args) {
                    return !c.target.hasZeroValue();
                },
                initComponent: function() {
                    this.on('boxready', function(c) {
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
                    });
                }
            },
            renderTo: me.labelCell
        }, me.lbarCfg || {}));

        // Return left-bar
        return me.lbar;
    },

    /**
     * Add component into left-bar
     *
     * @param cfg
     */
    addBtn: function(cfg) {
        if (Ext.isString(cfg.tooltip)) cfg.tooltip = {html: cfg.tooltip, constrainParent: false};
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
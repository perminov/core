Ext.define('Indi.lib.form.field.Color', {
	extend: 'Ext.form.field.Picker',
	alias: 'widget.colorfield',
	matchFieldWidth: false,
    pickerOffset: [0, -1],

    // @inheritdoc
    mixins: {fieldBase: 'Ext.form.field.Base'},

    /**
     * Append `zeroValue` property initialisation
     */
    constructor: function() {
        var me = this;
        me.callParent(arguments);
        me.mixins.fieldBase._constructor.call(this, arguments);
    },

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Force click made on inputEl to be treated as click made on triggerEl
        me.inputEl.on('click', me.onTriggerClick, this);

        // Fire `considerchange` event
        me.mixins.fieldBase._afterRender.call(this, arguments);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Strip hue from value
        me.value = me.value.replace(/[0-9]{3}#/, '#');

        // Call parent
        me.callParent(arguments);

        // Call mixin's initComponent method
        me.mixins.fieldBase._initComponent.call(this, arguments);
    },

    // @inheritdoc
    createPicker: function() {
		var me = this;
		return Ext.widget({
            xtype: 'colorpicker',
			floating: true,
			focusOnShow:true,
			baseCls: Ext.baseCSSPrefix + 'colorpicker',
			listeners: {
				scope: me,
				select: me.onSelect
			}
		});
	},

    // @inheritdoc
    onSelect: function(picker, value) {
		var me = this, hex = '#' + value;

        // Set hex value
		me.setValue(hex);

        // Fire 'select' event
		me.fireEvent('select', me, hex);

        // Collapse picker
		me.collapse();
	},

    // @inheritdoc
    onExpand: function() {
		var me = this, value = me.getValue().replace(/[0-9]{3}#/, '#');

        // Set picker's value
		me.picker.setValue(value);
	},

    /**
     * @inheritdoc
     */
    onChange: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Call mixin's _onChange() method
        me.mixins.fieldBase._onChange.call(this, arguments);
    }
});
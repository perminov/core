Ext.define('Indi.lib.form.field.Color', {
	extend: 'Ext.form.field.Picker',
	alias: 'widget.colorfield',
	matchFieldWidth: false,
    pickerOffset: [0, -1],

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Force click made on inputEl to be treated as click made on triggerEl
        me.inputEl.on('click', me.onTriggerClick, this);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Strip hue from value
        me.value = me.value.replace(/[0-9]{3}#/, '#');

        // Call parent
        me.callParent(arguments);
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
	}
});
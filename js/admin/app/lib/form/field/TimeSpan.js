/**
 * PhoneField widget, created by combining native ExtJs's TextField widget with InputMask plugin
 */
Ext.define('Indi.lib.form.field.TimeSpan', {

    // @inheritdoc
    extend: 'Ext.form.field.Text',

    // @inheritdoc
    alternateClassName: 'Indi.form.TimeSpan',

    // @inheritdoc
    alias: ['widget.timespanfield', 'widget.timespan'],

    // @inheritdoc
    constructor: function(config) {
        var me = this; config.plugins = config.plugins || [];

        // Add InputMask plugin
        config.plugins.push(new Ext.ux.form.field.plugin.InputMask('99:99 - 99:99', { placeholder: '*' }));

        // Call parent
        me.callParent(arguments);
    }
});
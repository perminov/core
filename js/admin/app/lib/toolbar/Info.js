Ext.define('Indi.lib.toolbar.Info', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.infotoolbar',
    dock: 'top',

    /**
     * Info text color
     */
    infoTextColor: 'blue',

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Single item
        me.items = '<span style="color: ' + me.infoTextColor + ';">' + me.infoText + '</span>';

        // Styles
        me.minHeight = 15;
        me.padding = '0 0 0 0';
        me.bodyPadding = 0;

        // Call parent
        me.callParent();
    }
});
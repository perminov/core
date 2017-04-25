Ext.define('Indi.lib.trail.Button', {
    extend: 'Ext.button.Button',
    alias: 'widget.trailbutton',
    cls: 'x-windowbar-btn',
    initComponent: function() {
        var me = this;

        // Return
        return me.callParent();
    }
});
Ext.define('Indi.view.desktop.WindowBar', {

    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.windowbar',
    mixins: {shrink: 'Indi.util.Shrinkable'},
    baseCls: Ext.baseCSSPrefix + 'windowbar',
    maxWindows: 15,
    //enableOverflow: true,
    border: 0,
    margin: '0 0 0 0',
    padding: 0,
    defaults: {xtype: 'windowbutton'},
    minWidth: 0,

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Make tabs reorderable
        /*me.plugins = [Ext.create('Ext.ux.BoxReorderer', {
            listeners: {
                Drop: function(p, w) {
                    w.fixBorders();
                }
            }
        })];*/

        // Call parent
        me.callParent();

        // Call mixin's initComponent
        me.mixins.shrink.initComponent.apply(this, arguments);
    },

    // @inheritdoc
    onAdd: function() {
        var me = this, wA = [], maximized = 0, closed = false;

        // Call parent
        me.callParent(arguments);

        // Max windows quantity restriction
        if (me.items.getCount() > me.maxWindows) {

            // First, we try to find and close non-maximized window, because if we close maximized,
            // visually (at current stage of windows-feature development) it would look like background
            // removal at the user point of view
            me.items.each(function(item){
                if (!item.window.maximized && !closed) {
                    closed = true;
                    item.window.close();
                }
            });

            // If all windows are maximized and that is why first them was not close - force first to be closed
            if (!closed) me.items.getAt(0).window.close();
        }
    }
});
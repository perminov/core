Ext.define('Indi.view.desktop.WindowBar', {

    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.windowbar',
    mixins: {shrink: 'Indi.util.Shrinkable'},
    baseCls: Ext.baseCSSPrefix + 'windowbar',
    //enableOverflow: true,
    border: 1,
    margin: '-4 2 0 0',
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
    }
});
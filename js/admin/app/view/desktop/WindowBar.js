Ext.define('Indi.view.desktop.WindowBar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.windowbar',
    baseCls: Ext.baseCSSPrefix + 'windowbar',
    //enableOverflow: true,
    border: 1,
    margin: '-4 0 0 0',
    padding: 0,
    defaults: {
        xtype: 'windowbutton'
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Make tabs reorderable
        me.plugins = [Ext.create('Ext.ux.BoxReorderer', {
            listeners: {
                Drop: function(p, w) {
                    w.fixBorders();
                }
            }
        })];

        // Call parent
        me.callParent();
    },

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Call parent
        me.ownerCt.on('resize', me.updateMaxWidth, me);
    },

    updateMaxWidth: function() {
        var me = this;
        if (Indi.viewport) {
            me.maxWidth = Indi.viewport.getWidth() - me.getBox().x - 6;
            me.updateLayout();
        }
    },

    /**
     * Border fix
     */
    fixBorders: function() {
        var me = this, btnA = me.getAll();

        if (btnA.length > 1) {
            btnA.forEach(function(btn, i){
                btn.setBorder(i == btnA.length - 1 ? '0 1 1 1' : '0 0 1 1');
            });
        } else {
            btnA.forEach(function(btn){
                btn.setBorder('0 1 1 1');
            });
        }

        //me.updateMaxWidth();
    },

    // @inheritdoc
    onAdd: function(b) {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Fix borders
        me.fixBorders();
    },

    // @inheritdoc
    onRemove: function(b) {

        var me = this;

        // Call parent
        me.callParent(arguments);

        // Fix borders
        me.fixBorders();
    },

    /**
     * Get all buttons
     *
     * @return {*}
     */
    getAll: function() {
        return this.query(' > [cls*="x-windowbar-btn"]');
    }
});
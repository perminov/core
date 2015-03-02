Ext.define('Indi.lib.view.action.Tab', {

    // @inheritdoc
    layout: 'fit',

    /**
     * Special property for easier lookup
     */
    isTab: true,

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Provide Indi.load() call to be performed once the box for component is ready
        me.on('activate', function() {

            if (me.load && me.up('tabpanel').height != 25 && !me.loaded) {
                Indi.load(me.load, {into: me.id});
                me.loaded = true;
            }
        });
    }
});
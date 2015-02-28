Ext.define('Indi.lib.view.action.Rowset', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Panel',

    // @inheritdoc
    alias: 'widget.actionrowset',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.Rowset',

    // @inheritdoc
    afterRender: function() {
        var me = this, interval;

        // Provide store to be loaded once panel context is ready
        interval = setInterval(function(){

            // If context is ready
            if (me.ctx()) {

                // Load the store
                me.ctx().getStore().load();

                // Clear interval
                clearInterval(interval);
            }
        }, 100);

        // Call parent
        me.callParent(arguments);
    }
});
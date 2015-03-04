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
    },

    /**
     * Return and object containing the height of tab panel, and the active tab name
     *
     * @return {Object}
     */
    forScope: function() {
        var me = this, tp, ctx, section, o = {};

        // If there is no tabpanel - return
        if (!(tp = me.down('tabpanel'))) return o;

        // If context does not exists - return
        if (!(ctx = me.ctx())) return o;

        // Set up section shortcut
        section = me.ctx().route.last().section;

        // Return
        return {
            section: section.alias,
            hash: section.primaryHash,
            actionrowset: {
                south: tp.items.getCount() ? {
                    activeTab: tp.getActiveTab().name,
                    tabs: tp.items.collect('name').join(','),
                    height: tp.height
                } : {}
            }
        };
    }
});
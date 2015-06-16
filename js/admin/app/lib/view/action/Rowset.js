Ext.define('Indi.lib.view.action.Rowset', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Panel',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.Rowset',

    // @inheritdoc
    alias: 'widget.actionrowset',

    /**
     * Whether or not automatically start trying to load the store once component is rendered
     */
    autoLoadStore: false,

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Load store
        if (me.autoLoadStore) me.loadStore();

        // Call parent
        me.callParent(arguments);
    },

    /**
     * Start trying to load the store. 'Trying' here mean that function will try to do it until success,
     * because the environment may be not sufficient at the early stages of initialization, so function will check
     * it for sufficiency each 100ms, and once sufficiency detected - will load the store
     */
    loadStore: function() {
        var me = this, interval;

        // Load the store
        me.$ctx.getStore().load();
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
        section = ctx.route.last().section;

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
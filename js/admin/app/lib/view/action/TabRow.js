Ext.define('Indi.lib.view.action.TabRow', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Row',

    // @inheritdoc
    alias: 'widget.actiontabrow',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.TabRow',

    // @inheritdoc
    mixins: {tab: 'Indi.lib.view.action.Tab'},

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Setup `insteadOf` property the same as `id`
        me.insteadOf = me.id;

        // Call tab mixin initComponent method
        me.mixins.tab.afterRender.call(me);
    },

    // @inheritdoc
    onLoad: function(ctx) {
        var me = this;

        // Call tab mixin initComponent method
        me.mixins.tab.onLoad.call(me, ctx);

        // Update `name` property for the tab, to provide tabs remember at it's most recent state
        me.up('[isSouthItem]').name = parseInt(ctx.ti().row.id) || 0;

        // Fit
        me.up('[isSouth]').getHeightUsage();
        me.up('[isWrapper]').fitSouth();
        me.up('[isSouth]').setHeight();
    },

    /**
     * Check if there is no need to do an actual request for loading tab contents,
     * as tab contents may have been already loaded and should be just picked up
     *
     * @param cfg
     */
    checkPreloadedResponse: function(cfg) {
        var me = this, scope, panel;

        // Wrap this block into try..catch to prevent error messages
        try {

            // Get scope's south panel settings
            scope = me.up('[isWrapper]').$ctx.ti().scope.actionrowset.south;

            // Get south panel
            panel = me.up('[isSouth]');

            // Assign `responseText` property to `cfg` argument
            cfg.responseText = scope.activeTabResponse[panel.getActiveTab().name];

        // Catch and do nothing
        } catch (e) {}
    }
});
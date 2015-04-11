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
    }
});
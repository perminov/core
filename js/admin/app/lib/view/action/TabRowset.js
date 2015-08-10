Ext.define('Indi.lib.view.action.TabRowset', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Rowset',

    // @inheritdoc
    alias: 'widget.actiontabrowset',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.TabRowset',

    // @inheritdoc
    autoLoadStore: false,

    // @inheritdoc
    mixins: {tab: 'Indi.lib.view.action.Tab'},

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call tab mixin initComponent method
        me.mixins.tab.afterRender.call(me);

        // Call parent
        me.callParent(arguments);
    },

    // @inheritdoc
    onLoad: function() {
        var me = this;

        // Call tab mixin same method
        me.mixins.tab.onLoad.call(me);

        // Load store
        me.loadStore();
    },

    /**
     * Check if there is no need to do an actual request for loading tab contents,
     * as tab contents may have been already loaded and should be just picked up
     *
     * @param cfg
     */
    checkPreloadedResponse: function(cfg) {
        try { cfg.responseText = this.up('[isWrapper]').$ctx.ti().sections.r(this.name, 'alias').responseText; } catch (e) {}
    }
});
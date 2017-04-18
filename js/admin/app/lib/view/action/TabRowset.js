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
        var me = this, activeTabName, activeTabHeight, w = me.up('[isWrapper]').getWindow();

        // Try detect active tab name
        try {
            activeTabName = me.up('[isWrapper]').$ctx.ti().scope.actionrow.south.activeTab;
            activeTabHeight = me.up('[isWrapper]').$ctx.ti().scope.actionrow.south.height;
        } catch (e) {}

        // Call tab mixin same method
        me.mixins.tab.onLoad.call(me);

        // Load store
        me.loadStore();

        // Fit
        if ((activeTabName && me.$ctx.ti().section.alias != activeTabName) || activeTabHeight == 25) {
            if (!w.maximized) {
                me.up('[isWrapper]').fitWindow();
            } else {
                me.up('[isSouth]').getHeightUsage();
                me.up('[isWrapper]').fitSouth();
                me.up('[isSouth]').setHeight();
            }
        }

        // Set up `actionrow` obj within scope
        var s = me.up('[isWrapper]').$ctx.ti().scope;
        if (!s.actionrow) s.actionrow = {south: {activeTab: me.$ctx.ti().section.alias}}
    },

    /**
     * Check if there is no need to do an actual request for loading tab contents,
     * as tab contents may have been already loaded and should be just picked up
     *
     * @param cfg
     */
    checkPreloadedResponse: function(cfg) {
        if (this.back) return;
        try { cfg.responseText = this.up('[isWrapper]').$ctx.ti().sections.r(this.name, 'alias').responseText; } catch (e) {}
    }
});
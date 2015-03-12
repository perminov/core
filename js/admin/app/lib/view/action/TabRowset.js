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
    }
});
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

        // Call tab mixin initComponent method
        me.mixins.tab.afterRender.call(me);
    }
});
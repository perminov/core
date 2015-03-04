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
    closable: true,

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Call tab mixin initComponent method
        me.mixins.tab.initComponent.call(me);

        // Call parent
        me.callParent();
    }
});
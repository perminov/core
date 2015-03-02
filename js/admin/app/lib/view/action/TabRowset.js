Ext.define('Indi.lib.view.action.TabRowset', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Rowset',

    // @inheritdoc
    alias: 'widget.actiontabrowset',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.TabRowset',

    // @inheritdoc
    mixins: {tab: 'Indi.lib.view.action.Tab'},

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Call tab mixin initComponent method
        me.mixins.tab.initComponent.call(me);

        // Call parent
        me.callParent();
    }
});
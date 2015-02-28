Ext.define('Indi.lib.view.action.TabRowset', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Rowset',

    // @inheritdoc
    alias: 'widget.actiontabrowset',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.TabRowset',

    // @inheritdoc
    mixins: ['Indi.lib.view.action.Tab'],

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Provide Indi.load() call to be performed once the box for component is ready
        me.on('boxready', function() { if (me.load) Indi.load(Indi.pre + me.load, {into: me.id});});

        // Call parent
        me.callParent();
    }
});
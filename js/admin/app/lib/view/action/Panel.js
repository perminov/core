Ext.define('Indi.lib.view.action.Panel', {

    // @inheritdoc
    extend: 'Ext.panel.Panel',

    // @inheritdoc
    alias: 'widget.actionpanel',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.Panel',

    // @inheritdoc
    border: 0,

    /**
     * Special property for easier lookup
     */
    isWrapper: true,

    /**
     * Get the context. Context here is an instance of a Indi.Controller.Action class,
     * or instance of one of it's child classes
     *
     * @return {*}
     */
    ctx: function() {
        return Ext.getCmp(this.id.replace('-wrapper', ''))
    },

    /**
     * Force context to be destroyed along with wrapper
     */
    onDestroy: function() {
        var me = this;

        // Destroy context
        if (me.ctx()) me.ctx().destroy();

        // Call parent
        me.callParent();
    },

    /**
     * Provide bread crumbs to be created/updated
     */
    afterRender: function() {
        var me = this;

        // Set up bread crubms
        if (!me.isTab) Ext.defer(function() {
            Indi.trail(true).breadCrumbs(me.ctx().route);
        }, 1);

        // Call parent
        me.callParent(arguments);
    },

    /**
     * This is for override in child classes
     */
    forScope: function() {
        return {}
    }
});
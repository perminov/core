Ext.define('Indi.lib.view.action.Row', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Panel',

    // @inheritdoc
    alias: 'widget.actionrow',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.Row',

    /**
     * Return and object containing the height of tab panel, and the active tab name
     *
     * @return {Object}
     */
    forScope: function() {
        var me = this, tp, ctx, section, o = {};

        // If there is no tabpanel - return
        if (!(tp = me.down('tabpanel'))) return o;

        // If context does not exists - return
        if (!(ctx = me.ctx())) return o;

        // Set up section shortcut
        section = me.ctx().route.last().section;

        // Return
        return {
            section: section.alias,
            hash: section.primaryHash,
            actionrow: {
                south: {
                    activeTab: tp.getActiveTab().name,
                    height: tp.height
                }
            }
        };
    }
});
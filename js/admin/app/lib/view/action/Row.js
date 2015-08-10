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
    },

    /**
     * Set up a height for south panel, for it to be as user-friendly as it possible
     */
    fitSouth: function() {
        var me = this, center = me.down('[region="center"]'), south = me.down('[isSouth]');

        // If there is no south panel, or it is minimied - return
        if (!south || !me.heightUsage || south.getHeight() == 25) return;

        // If total usage is less than total height
        if (me.getHeight() >= me.heightUsage.total) {

            // Set up south panel's to have height not exceeding actual height usage
            south.height = me.getHeight() - me.heightUsage.docked - center.heightUsage.total - 1;
            south.heightPercent = Math.round(south.height/(me.getHeight() - me.heightUsage.docked) * 100) + '%';

        // Else
        } else {

            // If south panel's height usage is less or equal than 60% of total height
            if (center.heightUsage.total <= (me.getHeight() - me.heightUsage.docked) * 0.7) {

                // Set up south panel's to have height not exceeding actual height usage
                //south.height = center.heightUsage.total + 1;
                south.height = me.getHeight() - me.heightUsage.docked - center.heightUsage.total - 1;
                south.heightPercent = Math.round(center.height/(me.getHeight() - me.heightUsage.docked) * 100) + '%';
            }
        }
    }
});
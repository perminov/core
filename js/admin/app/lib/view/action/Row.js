Ext.define('Indi.lib.view.action.Row', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.Panel',

    // @inheritdoc
    alias: 'widget.actionrow',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.Row',

    /**
     * Count of center panel's inner items, that will surely
     * not be overlapped by south panel as a result of resizable
     * handles usage
     */
    minVisibleItems: 3,

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
    fitSouth: function(force) {
        var me = this, center = me.down('[region="center"]'), south = me.down('[isSouth]'), meH = me.getHeight(), os, centerMinHeight;

        // If height usage was not yet calculated - calculate
        if (!me.heightUsage) me.getHeightUsage();

        // If there is no south panel, or it is minimied - return
        if (!south || !me.heightUsage || south.getHeight() == 25) return;

        // If center/row panel's required height does not exceed 70% of total available height
        if (center.heightUsage.total/(meH - me.heightUsage.docked) <= 0.7) {

            // Set south panel to have all remaining height
            south.height = meH - me.heightUsage.docked - center.heightUsage.total - 1;
            south.heightPercent = Math.round(south.height/(meH - me.heightUsage.docked) * 100) + '%';

            // Get height summary usage of first 3 items within center/row panel
            centerMinHeight = 0; center.items.each(function(item, i){
                if (i < me.minVisibleItems) centerMinHeight += item.getHeightUsage();
            });

            // Set max height to prevent center panel from being overlapped too much by south panel
            south.maxHeight = meH - me.heightUsage.docked - center.bodyPadding - centerMinHeight - 1;

            // Set min height to prevent center panel from having more height than it require
            south.minHeight = Math.max(25, meH - me.heightUsage.docked - center.heightUsage.total - 1);

            // Remember the height, that south panel should have when
            // current tab is a active tab for tab contents to sit by best fit
            south.getActiveTab().down('[isTab]').ownerSouth = {
                height: south.height,
                heightPercent: south.heightPercent,
                resizable: false
            };

            // Show resizer's handle
            south.resizer.north.show();

        // Else if south/rowset panel's required height does not exceed 60% of total available height
        } else if (south.getHeightUsage()/(meH - me.heightUsage.docked) <= 0.6) {

            // Set south panel to have the full height that it require
            south.height = south.heightUsage.total;
            south.heightPercent = Math.round(south.height/(meH - me.heightUsage.docked) * 100) + '%';

            // Remember the height, that south panel should have when
            // current tab is a active tab for tab contents to sit by best fit
            south.getActiveTab().down('[isTab]').ownerSouth = {
                height: south.height,
                heightPercent: south.heightPercent,
                resizable: false
            };

            // Hide resizer's handle
            south.resizer.north.hide();

        // Else
        } else {

            // If `ownerSouth` prop had been set, and it's `height` and `heightPercent` props are equal
            if ((os = south.getActiveTab().down('[isTab]').ownerSouth) && os.height == os.heightPercent) {

                // Set south panel to have percentage height
                south.heightPercent = south.height = os.heightPercent;

            // Else
            } else {

                // Set south panel to have the full height that it require
                south.height = Math.round((meH - me.heightUsage.docked) * 0.6);
                south.heightPercent = Math.round(south.height/(meH - me.heightUsage.docked) * 100) + '%';
            }

            // Get height summary usage of first 3 items within center/row panel
            centerMinHeight = 0; center.items.each(function(item, i){
                if (i < me.minVisibleItems) centerMinHeight += item.getHeightUsage();
            });

            // Set max height to prevent center panel from being overlapped too much by south panel
            south.maxHeight = meH - me.heightUsage.docked - center.bodyPadding - centerMinHeight - 1;

            // Set min height to prevent center panel from having more height than it require
            south.minHeight = Math.max(25, meH - me.heightUsage.docked - center.heightUsage.total - 1);

            // Remember the height, that south panel should have when
            // current tab is a active tab for tab contents to sit by best fit
            south.getActiveTab().down('[isTab]').ownerSouth = {
                height: south.height,
                heightPercent: south.heightPercent,
                resizable: true
            };

            // Show resizer's handle
            south.resizer.north.show();
        }

        // Force update height
        if (force) south.setHeight();
    },

    // @inheritdoc
    onLoad: function() {
        var me = this;

        me.fitSouth(true);
    }
});
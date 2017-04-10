Ext.define('Indi.lib.view.action.south.Rowset', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.south.South',

    // @inheritdoc
    alias: 'widget.rowsetactionsouth',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.South.Rowset',

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Make tabs reorderable
        me.plugins = [Ext.create('Ext.ux.TabReorderer', {})];

        // Provide rowset panel's paging toolbar to be removed once new tab added to tabpanel
        me.on('add', function(container, component) {

            // If added component is not a tabpanel item, or is, but has `isFromScope` property set to `true` - return
            if (!component.isSouthItem || component.isFromScope) return;

            // Setup shortcuts
            var ctx = container.up('[isWrapper]').ctx(), rowset = Ext.getCmp(ctx.rowset.id), paging = rowset.down('[alias="paging"]');

            // Remove paging toolbar from rowset panel
            if (paging) rowset.removeDocked(paging);

            // Show tabpanel and set added tab as active
            container.show().setActiveTab(component);

            // If `maxTabs` config is set, and current count of tabs exceeds the limit
            if (me.maxTabs) while (container.items.getCount() > me.maxTabs)
                container.remove(container.items.getAt(0));
        });

        // Provide rowset panel's paging toolbar to be re-added back once there is no more tabs left within tabpanel,
        // and or if still left any - erase removed tab mentions from scope
        me.on('remove', function(container, component) {

            // If removed component is a tabpanel item
            if (!component.isSouthItem) return;

            // Setup shortcuts for wrapper panel and for tabs array within scope's settings
            var wrp = container.up('[isWrapper]'), tabs = [];
            
            // Try to set up a shortcut to wrp.ctx().ti().scope.actionrowset.south.tabs
            try {
                tabs = wrp.ctx().ti().scope.actionrowset.south.tabs;
            } catch (e) {}

            // Erase mention from me.ti().scope.actionrowset.south.tabs array, because that array is the place
            // there system takes info for deciding whether or not to create paging toolbar for wrapper's rowset panel
            if (Ext.isArray(tabs)) Ext.Array.erase(tabs, tabs.column('id').indexOf(component.name), 1);

            // If removed tab was the last remaining tab within tabpanel
            if (!container.items.getCount()) {

                // Hide tabpanel
                container.hide();

                // Re-add paging toolbar as a docked item within wrapper's rowset panel
                Ext.getCmp(wrp.ctx().rowset.id).addDocked(wrp.ctx().rowsetDockedA());
            }
        });

        // Call parent
        me.callParent();
    },

    /**
     * Setup initial value for `heightPercent` property
     */
    initHeight: function() {
        var me = this; me.heightPercent = me.height != me.minHeight ? me.height : me.self.prototype.height;
    }
});
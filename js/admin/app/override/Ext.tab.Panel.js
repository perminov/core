Ext.override(Ext.tab.Panel, {

    /**
     * Get inner items summary height usage
     *
     * @return {Number}
     */
    getInnerItemsHeightUsage: function() {
        var me = this, activeTab = me.getActiveTab();

        // Return
        return activeTab ? activeTab.getHeightUsage() : 0;
    }
});
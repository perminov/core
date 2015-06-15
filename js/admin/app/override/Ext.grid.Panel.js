Ext.override(Ext.grid.Panel, {

    /**
     * This value will be involved in the process of real width
     * usage detection in case if there is currently no rows in grid
     */
    emptyTableHeightUsage: 300,

    /**
     * Get inner items summary height usage
     *
     * @return {Number}
     */
    getInnerItemsHeightUsage: function() {
        var me = this, el = me.items.getAt(0).getEl(), table = el.down('.x-grid-table');

        // Return
        return table ? table.getHeight() : me.emptyTableHeightUsage;
    }
});
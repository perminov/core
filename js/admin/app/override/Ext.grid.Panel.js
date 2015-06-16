Ext.override(Ext.grid.Panel, {

    /**
     * This value will be involved in the process of real width
     * usage detection in case if there is currently no rows in grid
     */
    emptyTableHeightUsage: 300,

    /**
     * Get inner items summary width usage. Here we assume that at the moment of this function call,
     * gridColumnAFit was already called, so grid has `widthUsage` prop, containing best width,
     * that was calculated regarding a lot of params
     *
     * @return {Number}
     */
    getInnerItemsWidthUsage: function() {
         return this.widthUsage;
    },

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
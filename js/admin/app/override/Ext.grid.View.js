/**
 * Grid view adjustment
 */
Ext.override(Ext.grid.View, {
    hasScrollY: function() {
        var me = this, gridTable = me.getEl().select('.x-grid-table').first();
        return gridTable && (gridTable.getHeight() > me.getHeight());
    }
});
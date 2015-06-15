Ext.override(Ext.toolbar.Toolbar, {

    /**
     * Get real width usage
     *
     * @return {*}
     */
    getWidthUsage: function() {
        var me = this, tbfill = me.down('tbfill');

        // Return
        return me.getWidth() - (tbfill ? tbfill.getWidth() : 0);
    }
});
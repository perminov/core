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

Ext.override(Ext.toolbar.TextItem, {

    /**
     * `text` prop was NOT updated if me.rendered==true, and this caused overflow menu item problem
     */
    setText : function(text) {
        var me = this;
        this.text = text;
        if (me.rendered) {
            me.el.update(text);
            me.updateLayout();
        }
    }
});
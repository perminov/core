Ext.override(Ext.panel.Panel, {

    /**
     * Get summary height usage
     *
     * @return {Number}
     */
    getHeightUsage: function() {
        var me = this;

        // Get current panel's docked and inner items height usage
        me.heightUsage = {
            docked: me.getDockedItemsHeightUsage(),
            inner: me.getInnerItemsHeightUsage()
        }

        // Get total height usage
        me.heightUsage.total = me.heightUsage.docked + me.heightUsage.inner;

        // Return
        return me.heightUsage.total;
    },

    /**
     * Get docked items summary height usage
     *
     * @return {Number}
     */
    getDockedItemsHeightUsage: function() {
        var me = this, height = 0;

        // Collect dockedItems heights
        me.getDockedItems().forEach(function(item){
            height += item.getHeight();
        });

        // Return
        return height;
    },

    /**
     * Get inner items summary height usage
     *
     * @return {Number}
     */
    getInnerItemsHeightUsage: function() {
        var me = this, height = 0;

        // Collect dockedItems heights
        me.items.each(function(item){
            height += item.getHeightUsage();
        });

        // Return
        return height;
    },

    /**
     * Get summary width usage
     *
     * @return {Number}
     */
    getWidthUsage: function() {
        var me = this;
        me.widthUsage = Math.max(this.getDockedItemsWidthUsage(),  this.getInnerItemsWidthUsage());
        return me.widthUsage;
    },

    /**
     * Get docked items summary width usage
     *
     * @return {Number}
     */
    getDockedItemsWidthUsage: function() {
        var me = this, width = 0;

        // Collect dockedItems width usages
        me.getDockedItems().forEach(function(item){
            width = Math.max(width, item.getWidthUsage());
        });

        // Return
        return width;
    },

    /**
     * Get inner items summary width usage
     *
     * @return {Number}
     */
    getInnerItemsWidthUsage: function() {
        var me = this, width = 0;

        // Collect dockedItems heights
        me.items.each(function(item){
            width = Math.max(width, item.getWidthUsage());
        });

        // Return
        return width;
    }
});
Ext.override(Ext.form.Panel, {

    /**
     * Get inner items summary height usage
     *
     * @return {Number}
     */
    getInnerItemsHeightUsage: function() {
        return this.callParent() + this.bodyPadding * 2;
    },

    /**
     * Get inner items summary width usage
     *
     * @return {Number}
     */
    getInnerItemsWidthUsage: function() {
        var me = this, labelWidth = 0, inputWidth = 0, itemWidth;

        // Walk through items and detect width usage
        me.items.each(function(item){

            // Get item's width usage
            itemWidth = item.getWidthUsage();

            // Update widths if they are greater than we faced before
            labelWidth = Math.max(labelWidth, itemWidth.label);
            inputWidth = Math.max(inputWidth, itemWidth.input);
        });

        // Return
        return Math.max(labelWidth, inputWidth) * 2 + me.bodyPadding * 2;
    }
});
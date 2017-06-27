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
        var me = this, labelWidth = 0, inputWidth = 0, itemWidth, bothWidth = 0;

        // Walk through items and detect width usage
        me.items.each(function(item){

            // Get item's width usage
            itemWidth = item.getWidthUsage();

            // Skip hidden item
            if (item.hidden) return;

            // If item's `labelAlign` is 'top' (field's label is above field's input)
            if (item.labelAlign == 'top') {

                // Update max width for both label and input
                bothWidth = Math.max(bothWidth, itemWidth.label, itemWidth.input);

            // Else if field's label is at the left/right side of field's input
            } else {

                // Update widths if they are greater than we faced before
                labelWidth = Math.max(labelWidth, itemWidth.label);
                inputWidth = Math.max(inputWidth, itemWidth.input);
            }
        });

        // Return
        return Math.max(Math.max(labelWidth, inputWidth) * 2, bothWidth) + me.bodyPadding * 2;
    }
});
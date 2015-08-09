Ext.define('Indi.lib.toolbar.Filter', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.filtertoolbar',
    dock: 'top',
    layout: 'auto',

    /**
     * Get width, minimum required for this toolbar to be displayed in full
     *
     * @return {Number}
     */
    getWidthUsage: function() {
        var me = this, fs = me.down('fieldset'), usage = 0, sum = 0;

        // Collect fieldset's inner items width usage
        fs.items.each(function(c) {

            // Get current inner items width usage
            usage = c.getWidthUsage();

            // Append those numbers
            sum += (Ext.isObject(usage) ? usage.input + usage.label : usage) + c.el.getMargin('lr');
        });

        // Append fieldset's constant width
        sum += fs.el.getMargin('lr') + fs.el.getPadding('lr') + fs.el.getBorderWidth('lr');

        // Return
        return sum;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        me.padding = '1 5 5 5';

        // Call parent
        me.callParent();
    }
});
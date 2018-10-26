Ext.define('Indi.lib.toolbar.Info', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.infotoolbar',
    dock: 'top',

    /**
     * Info text color
     */
    infoTextColor: 'blue',

    // @inheritdoc
    renderSelectors: {
        innerEl: '.i-infotb-inner'
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Single item
        me.items = '<span style="color: ' + me.infoTextColor + ';" class="i-infotb-inner">' + me.infoText + '</span>';

        // Styles
        me.minHeight = 15;
        me.padding = '0 0 0 0';
        me.bodyPadding = 0;

        // Call parent
        me.callParent();
    },

    /**
     * Make inner content scrollable if need
     */
    onBoxReady: function() {
        var me = this;

        // Call parent
        me.callParent();

        // Add 'mouseenter' handler
        me.el.down('.i-infotb-inner').on('mouseenter', function(e, dom){
            var indent = me.innerEl.getTextWidth() - me.getWidth() + 4;
            if (indent > 0) Ext.get(dom).css('text-indent', '-' + indent + 'px');
        });

        // Add 'mouseleave' handler
        me.el.down('.i-infotb-inner').on('mouseleave', function(e, dom){
            Ext.get(dom).css('text-indent', '0');
        });
    }
});
/**
 * Tooltips adjustments
 */
Ext.override(Ext.picker.Date, {

    /**
     * Set up empty ariaTitle config by default
     */
    ariaTitle: '',

    // @inheritdoc
    onRender: function(container, position) {
        var me = this;

        // Setup tooltip type for todayBtn
        me.todayBtn.tooltipType = 'qtip';

        // Call parent
        me.callParent(arguments);

        // Setup tooltips for prev and next month buttons
        me.prevEl.createTooltip({staticOffset: [0, 2]});
        me.nextEl.createTooltip({staticOffset: [0, 2]});
    }
});
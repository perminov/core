/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.SiblingCombo', {

    // @inheritdoc
    extend: 'Indi.form.Combo',

    // @inheritdoc
    alternateClassName: 'Indi.form.SiblingCombo',

    // @inheritdoc
    alias: 'widget.combo.sibling',

    /**
     * System property. Is used in cases when current combo has a satellite. For example, we changed value of
     * satellite combo, so options of current combo are fetched with refresh-children mode. After children were
     * refreshed, we should adjust current combo width for it to take in attention the width of longest child.
     * But if we change satellite combo value one more time, and current combo data will be refreshed again,
     * the width of longest child may be less than we faced earlier, and when new longest child width will be
     * taken in attention while adjusting current combo width - it (current combo width) will decrease, and
     * i don't like that. So this property - optionContentsMaxWidth - will be used to solve that problem: each time
     * we refresh children - combo will calculate the longest child width, and if that width is greater than value
     * of optionContentsMaxWidth property - it will be used while calculating new width for combo width, and
     * value of optionContentsMaxWidth property will be updated to match maximum faced child width. Otherwise,
     * current value of optionContentsMaxWidth property will be used without any update.
     */
    optionContentsMaxWidth: 0,

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Default after render handler
        me.callParent(arguments);

        // Fit combo width
        me.fitWidth();
    },

    // @inheritdoc
    afterFetchAdjustments: function() {
        var me = this;

        // Default adjustments
        me.callParent(arguments);

        // Fit combo width
        me.fitWidth();
    },

    /**
     * Adjust combo width, so all involved things are taken into consideration while calculating least combo width
     */
    fitWidth: function() {
        var me = this, width = 0, optionContentsMaxWidth = 0, optionContentsWidth, color;

        // Append labelWidth
        width += me.labelCell.getWidth();

        // Append borders (left and right) widths
        width += parseInt(me.comboInner.css('border-left-width')) + parseInt(me.comboInner.css('border-right-width'));

        // Append option paddings (left and right)
        width += 2 + 2;

        // If optgroups are used - append optgroup indent
        if (me.store.optgroup) width += 15;

        // Detect maximum option contents width
        for (var i = 0; i < me.store.data.length; i++) {

            // Get current option indent width
            optionContentsWidth = Indi.metrics.getWidth(me.store.data[i].system.indent);

            // Detect color box and non-html title for current option, and append their widths to current option width
            color = me.color(me.store.data[i], me.store.ids[i]);
            optionContentsWidth += (color.box ? 14 : 0) + Indi.metrics.getWidth(color.title);

            // If current value of 'optionContentsMaxWidth' variable is less
            // than current option contents width - increase it
            if (optionContentsWidth > optionContentsMaxWidth) optionContentsMaxWidth = optionContentsWidth;
        }

        if (me.store.data.length == 0) optionContentsMaxWidth = 23;

        // Prevent possible width decrease
        if (optionContentsMaxWidth > me.optionContentsMaxWidth) me.optionContentsMaxWidth = optionContentsMaxWidth;

        // Append maximum option contents width
        width += me.optionContentsMaxWidth;

        // Append additonal space
        width += 10;

        // Append .i-combo-info width
        if (!(me.keywordEl.attr('no-lookup') == 'true' || me.store.enumset)) width += 30;

        // Append trigger width
        me.triggerEl.each(function(el){
            width += el.getWidth();
        });

        // Set width
        me.setWidth(width);
    }
});
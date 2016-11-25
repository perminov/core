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
     * Calculate width, required by combo
     *
     * @return {Number}
     */
    getFitWidth: function() {

        var me = this, width = 0, maxPseudoTitle = '', pseudoTitle = '', color, optionContentsMaxWidth;

        // Append labelWidth
        if (me.labelCell) width += me.labelCell.getWidth();

        // Append borders (left and right) widths
        width += parseInt(me.comboInner.css('border-left-width')) + parseInt(me.comboInner.css('border-right-width'));

        // Append option paddings (left and right)
        width += 2 + 2;

        // If optgroups are used - append optgroup indent
        if (me.store.optgroup) width += 15;

        // Detect maximum option contents length
        for (var i = 0; i < me.store.data.length; i++) {

            // Get current option indent width
            pseudoTitle = me.store.data[i].system.indent ? me.store.data[i].system.indent.replace('&nbsp;', ' ') : '';

            // Detect color box and non-html title for current option,
            color = me.color(me.store.data[i], me.store.ids[i]);

            // And append their length to current option width, assuming that color box is equal to '---' by width
            pseudoTitle += (color.box ? '---' : '') + color.title;

            // If length 'pseudoTitle' variable is less than length of 'maxPseudoTitle' - renew las one
            if (pseudoTitle.length > maxPseudoTitle.length) maxPseudoTitle = pseudoTitle;
        }

        // Find actual width of longest option, using Indi.metrics.getWidth() call, or set up default if store is empty
        optionContentsMaxWidth = me.store.data.length == 0 ? 23 : Indi.metrics.getWidth(maxPseudoTitle);

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

        // Return required width
        return width;
    },

    /**
     * Adjust combo width, so all involved things are taken into consideration while calculating least combo width
     */
    fitWidth: function() {
        var me = this, width = me.getFitWidth();

        // Set width
        me.setWidth(width);
    }
});
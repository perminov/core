Ext.define('Indi.combo.filter', {

    // @inheritdoc
    extend: 'Indi.combo.form',

    // @inheritdoc
    alias: 'widget.combo.filter',

    /**
     * We need this to be able to separate options div visibility after keyword was erased
     * At form combos options wont be hidden, but here same param is set to true
     *
     * @type {Boolean}
     */
    hideOptionsAfterKeywordErased: true,

    /**
     * Here we do not exec options-specific javascript (as it would be done at indi.proto.combo.form) but here we do
     * things, that are especially related to filters
     */
    onHiddenChange: function() {
        var me = this, name = me.name, sComboName, sCombo;

        // Remove attributes from hidden field, if it's value became 0. We do it here only for single-value combos
        // because multiple-value combos have different way of how-and-when the same aim should be reached -
        // attributes deletion for multiple-value combos is implemented in me.bindDelete() function of this script
        if (!me.multiSelect && me.hiddenEl.val() == '0') {
            if (me.store.attrs && me.store.attrs.length) {
                for (var n = 0; n < me.store.attrs.length; n++) {
                    me.hiddenEl.removeAttr(me.store.attrs[n]);
                }
            }

            // Also we remove a .i-combo-color-box element, related to previously selected option
            if (me.keywordEl.val() == '#' || me.keywordEl.val() == '') me.colorDiv.update('');
        }

        if (me.hiddenEl.val() == '0' && me.hiddenEl.attr('boolean') != 'true') me.hiddenEl.val('');

        // If combo is running in multiple-values mode and is rendered - empty keyword input element
        if (me.multiSelect && me.el) me.keywordEl.dom.value = Ext.emptyString;

        // Call superclass setValue method to provide 'change' event firing
        me.superclass.superclass.setValue.call(me, me.hiddenEl.val());

        // If current combo is a satellite for one or more other combos, we should refresh data in that other combos
        me.el.up('fieldset').select('.i-combo-info[satellite="'+name+'"]').each(function(el, c){
            sComboName = el.up('.i-combo').select('[type="hidden"]').first().attr('name');
            sCombo = Ext.getCmp('tr-' + sComboName);
            sCombo.hiddenEl.attr('change-by-refresh-children', 'true');
            sCombo.setDisabled();
            if (!sCombo.disabled) {

                // Here we are emptying the satellited combo selected values, either hidden and visible
                // because if we would do it in afterFetchAdjustmetns, there would be a delay until fetch
                // request would be completed
                if (sCombo.multiSelect) {
                    sCombo.el.select('.i-combo-selected-item-delete').attr('no-change', 'true').click();
                    sCombo.hiddenEl.val('');
                } else {
                    sCombo.hiddenEl.val(0);
                }

                sCombo.keywordEl.val('');
            }
        });
        me.el.up('fieldset').select('.i-combo-info[satellite="'+name+'"]').each(function(el, c){
            sComboName = el.up('.i-combo').select('[type="hidden"]').first().attr('name');
            sCombo = Ext.getCmp('tr-' + sComboName);
            sCombo.hiddenEl.attr('change-by-refresh-children', 'true');
            sCombo.setDisabled();
            if (!sCombo.disabled) {

                sCombo.remoteFetch({
                    satellite: me.hiddenEl.val(),
                    mode: 'refresh-children'
                });
            }
        });

        // We should do the check, because if combo has a dependent combos, they are also call their change handlers
        // but here we do not need that
        if (!me.hiddenEl.attr('change-by-refresh-children')) {

            // We fire indi.action.index.filterChange only if noReload flag if turned off
            if (!me.noReload) me.ctx().filterChange({noReload: false, xtype: 'combo.filter'});

        } else {
            me.hiddenEl.removeAttr('change-by-refresh-children');
        }
    },

    /**
     * Instead just of making keyword fields (related to enumset combos) readonly, we also make an ability
     * to erase selected values
     *
     * @param name
     */
    setReadonlyIfNeeded: function() {
        var me = this;
        if (me.store.enumset) {
            me.keywordEl.attr('no-lookup', 'true');
        }
    },

    listeners: {
        resize: function(me, width, height, oldWidth, oldHeight) {
            me.getPicker().setWidth(me.triggerWrap.getWidth());
        }
    },

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

        // Append maximum option contents width
        width += optionContentsMaxWidth;

        // Append additonal space
        width += 10;

        // Append .i-combo-info width
        if (!(me.keywordEl.attr('no-lookup') == 'true' || me.store.enumset)) width += 30;

        // Append trigger width
        var t = 0;
        me.triggerEl.each(function(el){
            width += el.getWidth();
            t += el.getWidth();
        });

        // Set width
        me.setWidth(width);

        // Return
        return {
            labelCell: me.labelCell.getWidth(),
            paddings: 4,
            optgroup: me.store.optgroup ? 15: 0,
            optionContentsMaxWidth: optionContentsMaxWidth,
            info: !(me.keywordEl.attr('no-lookup') || me.store.enumset) ? 30: 0,
            trigger: t
        }
    },

    afterRender: function() {
        var me = this;

        // Default after render handler
        me.callParent(arguments);

        // Fit combo width
        me.fitWidth();
    }
});
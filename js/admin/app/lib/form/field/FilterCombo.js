/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.FilterCombo', {

    // @inheritdoc
    extend: 'Indi.lib.form.field.SiblingCombo',

    // @inheritdoc
    alias: 'widget.combo.filter',

    // @inheritdoc
    grow: false,

    // @inheritdoc
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

        // Here we implement different logic for combo empty value, because in case if
        // current combo is not a boolean combo, and it's hidden value is '0', we set value to
        // empty string, as filter name and value will be sent as one of store fetch params otherwise,
        // so we prevent it
        if (me.hiddenEl.val() == '0' && me.hiddenEl.attr('boolean') != 'true' && !me.store.enumset) me.hiddenEl.val('');

        // Call superclass setValue method to provide 'change' event firing
        me.getNative().setValue.call(me, me.hiddenEl.val());

        // If combo is running in multiple-values mode and is rendered - empty keyword input element,
        // because call of native setValue method will assign a value to keywordEl.dom. This is ok
        // for single-value combos, but for multiple-value combos we should prevent it
        if (me.multiSelect && me.el) me.keywordEl.dom.value = Ext.emptyString;

        // We should do the check, because if combo has a dependent combos, they are also call their change handlers
        // but here we do not need that
        if (!me.hiddenEl.attr('change-by-refresh-children')) {

            // We fire Indi.controller.action.filterChange only if noReload flag if turned off
            if (!me.noReload) me.ctx().filterChange({noReload: false, xtype: 'combo.filter'});
        }
    },

    /**
     * Instead just of making keyword fields (related to enumset combos) readonly, we also make an ability
     * to erase selected values
     */
    setReadonlyIfNeeded: function() {
        var me = this;
        if (me.store.enumset || me.allowClear === false) {
            me.keywordEl.attr('no-lookup', 'true');
            me.keywordEl.attr('readonly', 'readonly');
        }
    },

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Prevent rowset panel store reload. Here we do that, because in case if current combo store is empty,
        // parent's afterRender function will call setDisabled(true), that, in it's turn, will fire onHiddenChange,
        // that may use me.ctx() call, in some cases
        me.noReload = true;

        // Default after render handler
        me.callParent(arguments);

        // Restore 'noReload' property value to boolean 'false'
        me.noReload = false;

        // Fit combo width
        me.fitWidth();
    },

    // @inheritdoc
    bid: function() {
        var me = this; return me.id.replace(new RegExp(me.field.alias+'$'), '');
    },

    /**
     * Get combo value
     *
     * @return {*}
     */
    getValue: function() {
        return this.boolean || this.multiSelect ? this.value : (this.value + '' == '0' ? (this.store.enumset ? this.value : '') : this.value || '');
    },

    /**
     * All filter combos are clearable, in opposite to form combos
     *
     * @return {Boolean}
     */
    isClearable: function() {
        return this.allowClear !== false;
    },

    // @inheritdoc
    clearComboValue: function() {
        var me = this;

        // If combo is multiple, we fire 'click' event on each .i-combo-selected-item-delete item, so hidden
        // value will be cleared automatically
        if (me.multiSelect) me.el.select('.i-combo-selected-item-delete').attr('no-change', 'true').click();

        // Else if combo is single and is not boolean, we set it's value to 0, '' otherwise
        else me.hiddenEl.val('');

        // Call setValue
        me.setValue(me.hiddenEl.val());
    }
});
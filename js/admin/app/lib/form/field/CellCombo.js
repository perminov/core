/**
 * Special version of Indi.form.SiblingCombo, created for being used as a grid-cell editor
 */
Ext.define('Indi.lib.form.field.CellCombo', {

    // @inheritdoc
    extend: 'Indi.lib.form.field.SiblingCombo',

    // @inheritdoc
    alias: 'widget.combo.cell',

    // @inheritdoc
    grow: false,

    // @inheritdoc
    hideOptionsAfterKeywordErased: false,

    // @inheritdoc
    hideTrigger: true,

    // @inheritdoc
    margin: '0 2 0 3',

    // @inheritdoc
    height: 18,

    // @inheritdoc
    value: '',

    /**
     * Empty prop
     */
    subTplData: {attrs: null, pageUpDisabled: "true", selected: {title: null, value: 0}},

    // @inheritdoc
    fitWidth: function() {
        var me = this;

        // Set minWidth
        me.minWidth = me.getFitWidth();

        // If current width is less than min width - increase
        if (me.minWidth > me.width) me.setWidth(me.minWidth);
    },

    resetInfo: function(value, store) {
        var me = this;
        if (me.infoEl) {
            me.infoEl.attr('page-top', 0);
            me.infoEl.attr('page-btm', 0);
            me.infoEl.attr('page-top-reached', value ? 'false' : 'true');
            me.infoEl.attr('page-btm-reached', 'false');
            me.keywordEl.attr('selectedIndex', 1);
            me.fetchedByPageUps = 0;
        }
        me.subTplData.pageUpDisabled = value ? 'false' : 'true';
        if (me.picker) {
            me.picker.destroy();
            delete me.picker;
        }
        me.optionContentsMaxWidth = 0;
        me.store = store;
        if (me.comboInner) {
            me.width = 0;
            me.fitWidth();
        }
    }
});
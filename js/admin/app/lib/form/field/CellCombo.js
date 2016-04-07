/**
 * Special version of Indi.form.SiblingCombo, created for being used as a grid-cell editor
 */
Ext.define('Indi.lib.form.field.CellCombo', {

    // @inheritdoc
    extend: 'Indi.form.SiblingCombo',

    // @inheritdoc
    alternateClassName: 'Indi.form.CellCombo',

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
    //value: '',

    // @inheritdoc
    fitWidth: function() {
        var me = this;

        // Set minWidth
        me.minWidth = me.getFitWidth();

        // If current width is less than min width - increase
        if (me.minWidth > me.width) {
            console.log('me.minWidth > me.width', me.minWidth, me.width);
            me.setWidth(me.minWidth);
        }
    },

    resetInfo: function(value, store) {
        var me = this;
        if (me.infoEl) {
            me.infoEl.attr('page-top', 0);
            me.infoEl.attr('page-btm', 0);
            me.infoEl.attr('page-top-reached', value ? 'false' : 'true');
            me.infoEl.attr('page-btm-reached', 'false');
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
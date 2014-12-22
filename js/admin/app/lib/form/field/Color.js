/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.Color', {

    // @inheritdoc
    extend: 'Ext.form.field.Trigger',

    // @inheritdoc
    alternateClassName: 'Indi.form.Color',

    // @inheritdoc
    alias: 'widget.colorfield',

    expanded: false,

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Bind third-party color picker
        me.inputEl.attr('onclick', 'colorPicker(event, null, null, null, null, null, null, null, 1, "0")');
    },

    onTriggerClick: function() {
        this.inputEl.dom.click();
    },

    onBlur: function(){
        if (this.el.select('.cPSkin').first().css('display') == 'block') {
            this.el.select('.cPSkin').first().css('display', 'none');
            this.expanded = false;
        }
    }
});
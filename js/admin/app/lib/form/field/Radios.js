/**
 */
Ext.define('Indi.lib.form.field.Radios', {

    // @inheritdoc
    extend: 'Ext.form.RadioGroup',

    // @inheritdoc
    alternateClassName: 'Indi.form.Radios',

    // @inheritdoc
    alias: 'widget.radios',

    // @inheritdoc
    columns: 1,

    // @inheritdoc
    vertical: true,

    // @inheritdoc
    initComponent: function() {
        var me = this;
        me.items = me.itemA();
        me.callParent();
    },

    /**
     * Build and return radio items array
     *
     * @return {Array}
     */
    itemA: function() {

        // Setup auxiliary variables
        var me = this, itemI, itemA = [], inputValue;

        // For each store data item
        me.row.view(me.name).store.data.forEach(function(enumset, index){

            // Get radio input value
            inputValue = me.row.view(me.name).store.ids[index] + '';

            // Prepare initial radio item cfg
            itemI = {
                name: me.name,
                id: me.id + '$' + inputValue,
                inputValue: inputValue,
                checked: inputValue == me.row[me.name],
                enumset: enumset,
                listeners: {
                    change: function(rb, now) {
                        if (now) {
                            try {
                                Indi.eval(rb.enumset.system.js, rb.ownerCt);
                                Indi.eval(me.field.javascript, rb.ownerCt);
                            } catch (e) {
                                throw e;
                            }
                        }
                    }
                }
            }

            // Append item cfg to the items array
            itemA.push(itemI);
        });

        return itemA;
    },

    // @inheritdoc
    afterRender: function() {

        // Get checked radio
        var me = this, checked = me.getChecked()[0];

        // If checked radio exists - fire 'change' event for it
        if (checked) checked.fireEvent('change', checked, true);

        // Call parent
        me.callParent();
    },

    /**
     * Function that will be called after combo value change. Provide dependent-combos reloading in case
     * if current field is a satellite for one or more combos, that are siblings to current field
     */
    onChange: function() {

        // Setup auxilliary variables
        var me = this, name = me.name, dComboName, dCombo;

        // Call parent
        me.callParent(arguments);

        // If current field is a satellite for one or more sibling combos, we should refresh data in that sibling combos
        Ext.get(me.ctx().ti().bid() + '-form').select('.i-combo-info[satellite="'+name+'"]').each(function(el){
            dComboName = el.up('.i-combo').select('[type="hidden"]').first().attr('name');
            dCombo = me.sbl(dComboName);
            dCombo.setDisabled(false, true);
            if (!dCombo.disabled) {
                dCombo.remoteFetch({
                    satellite: me.getValue(),
                    mode: 'refresh-children'
                });
            }
        });
    }
});
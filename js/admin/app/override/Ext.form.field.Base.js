/**
 * Make up that hidden fields to be treated as valid
 */
Ext.override(Ext.form.field.Base, {
    isValid: function() {
        return this.hidden || this.callParent();
    }
});
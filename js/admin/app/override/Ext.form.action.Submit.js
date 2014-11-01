/**
 * 'dirtyOnly' config support added
 */
Ext.override(Ext.form.action.Submit, {

    /**
     * Whether or not submit only dirty fields
     */
    dirtyOnly: false,

    /**
     * The only difference with the native method is that second argument for 'this.form.getValues()' call can now be configured
     *
     * @param value
     * @return {Object/String}
     */
     getParams: function() {
         var nope = false,
             configParams = this.callParent(),
             fieldParams = this.form.getValues(nope, this.dirtyOnly, this.submitEmptyText !== nope);
        return Ext.apply({}, fieldParams, this.dirtyOnly ? {} : configParams);
     }
});
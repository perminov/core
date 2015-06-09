/**
 * Here we override this class to make the click listener working, as it, for some reason, was not working
 */
Ext.override(Ext.tab.Bar, {
    onClick: function(e) {
        this.fireClickEvent('click', e);
        this.callParent(arguments);
    }
});
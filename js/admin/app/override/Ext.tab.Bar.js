/**
 * Here we override this class to make the click listener working, as it, for some reason, was not working
 */
Ext.override(Ext.tab.Bar, {
    onClick: function(e) {
        this.fireClickEvent('click', e);
        this.callParent(arguments);
    }
});

Ext.override(Ext.tab.Tab, {
    onCloseClick: function() {
        var me = this;
        console.log('zxc');
        return false;
        if (me.fireEvent('beforeclose', me) !== false) {
            if (me.tabBar) {
                if (me.tabBar.closeTab(me) === false) {
                    // beforeclose on the panel vetoed the event, stop here
                    return;
                }
            } else {
                // if there's no tabbar, fire the close event
                me.fireClose();
            }
        }
    }
});
Ext.define('Ext.ux.desktop.TaskBar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.taskbar',
    border: 0,
    height: '100%',
    padding: 0,
    margin: 0,
    style: {
        background: 'transparent'
    },

    /**
     *
     */
    initComponent: function () {
        var me = this;

        // Instantiate windowbar
        me.wbar = Ext.widget({
            xtype: 'windowbar',
            maxWindows: 15,
            margin: '0 0 0 0'
        });

        // Add windowbar
        me.items = [me.wbar];

        // Call parent
        me.callParent();
    },

    /**
     * Set active button
     *
     * @param btn
     */
    setActiveButton: function(btn) {

        // If `btn` argument given
        if (btn) {

            // Toggle given button
            btn.toggle(true);

            // Update bread crumb trail contents
            Indi.app.updateTrail();

        // Else depress all buttons
        } else this.wbar.items.each(function (item) {
            item.toggle(false);
        });
    },

    /**
     * Remove window button from
     *
     * @param btn
     * @return {*}
     */
    removeTaskButton: function (btn) {
        var found, me = this;

        // Find
        me.wbar.items.each(function (item) {
            if (item === btn) {
                found = item;
            }
            return !found;
        });

        // Remove, if found
        if (found) me.wbar.remove(found);

        // Return removed
        return found;
    }
});
Ext.define('Ext.ux.desktop.TaskBar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.taskbar',
    border: 0,
    padding: 0,

    initComponent: function () {
        var me = this;

        me.wbar = Ext.widget({
            xtype: 'toolbar',
            flex: 1,
            border: 0,
            padding: 0,
            margin: 0,
            height: 18,
            defaults: {
                toggleGroup: 'all',
                padding: 0
            }
        });

        me.items = [me.wbar];

        me.callParent();
    },

    setActiveButton: function(btn) {
        if (btn) {
            btn.toggle(true);
        } else {
            this.wbar.items.each(function (item) {
                if (item.isButton) {
                    item.toggle(false);
                }
            });
        }
    },

    removeTaskButton: function (btn) {
        var found, me = this;
        me.wbar.items.each(function (item) {
            if (item === btn) {
                found = item;
            }
            return !found;
        });
        if (found) {
            me.wbar.remove(found);
        }
        return found;
    }
});
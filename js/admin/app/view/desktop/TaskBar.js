Ext.define('Ext.ux.desktop.TaskBar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.taskbar',
    border: 0,
    padding: 0,
    style: {
        background: 'none'
    },
    initComponent: function () {
        var me = this;

        me.ubar = {
            xtype: 'panel',
            html: '<div id="i-center-north-admin">' + Indi.user + ' <a href="' + Indi.pre + '/logout/">' + Indi.lang.I_LOGOUT + '</a></div>',
            padding: 0,
            border: 0,
            margin: '0 5 0 0',
            flex: 1,
        };
        me.wbar = Ext.widget({
            xtype: 'toolbar',
            enableOverflow: true,
            //flex: 1,
            border: 0,
            padding: 0,
            margin: 0,
            //height: 22,
            //height: 20,
            cls: 'i-wbar',
            style: {
                background: 'none'
            },
            width: '70%',
            defaults: {
                toggleGroup: 'all',
                textAlign: 'left',
                //minWidth: 100,
                height: 21,
                height: 16,
                shrinkWrap: 1,
                //maxWidth: 100,
                padding: '0 0 0 3',
                cls: 'i-taskbar-btn',
                pressedCls: 'pressed',
                //margin: 0
                //padding: 0
            }
        });

        me.items = [me.ubar, me.wbar];

        me.callParent();
    },

    setActiveButton: function(btn) {
        if (btn) {
            btn.toggle(true);
            Ext.get('i-center-north-trail-panel-body').setHTML(Indi.trail(true).breadCrumbs(btn.window.ctx.route));
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
Ext.define('Ext.ux.desktop.TaskBar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.taskbar',
    border: 0,
    height: 20,
    padding: 0,
    margin: 0,
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
            margin: '10 20 0 0'
        };

        me.wbar = Ext.widget({
            xtype: 'windowbar'
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
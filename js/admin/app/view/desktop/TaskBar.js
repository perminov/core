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
            margin: '0 5 0 0'
        };
        me.ubar1 = {
            xtype: 't',
            /*height: 20,
            width: 300,
            style: {
                fontSize: '12px'
            },
            tpl:
                '<div>' +
  //                  '<div id="i-center-north-date">{date}</div>' +
                    '<div id="i-center-north-admin">{admin} <a href="{pre}/logout/">{logout}</a></div>' +
//                    '<div id="i-center-north-trail"></div>' +
                '</div>',
            //minHeight: 36,
            border: 0,
            afterRender: function() {
                this.tpl.overwrite(this.el, {
//                    date: Ext.Date.format(new Date(Indi.time * 1000), Indi.view.Viewport.dateUpdaterFormat),
                    admin: Indi.user,
                    pre: Indi.pre,
                    logout: Indi.lang.I_LOGOUT
                });
                this.superclass.afterRender.apply(this, arguments);
            }*/
        };
        me.wbar = Ext.widget({
            xtype: 'toolbar',
            flex: 1,
            border: 0,
            padding: 0,
            margin: 0,
            //height: 22,
            //height: 20,
            cls: 'i-wbar',
            style: {
                background: 'none'
            },
            defaults: {
                toggleGroup: 'all',
                height: 21,
                padding: '0 0 0 3',
                //margin: 0
                //padding: 0
            }
        });

        me.items = [me.wbar, me.ubar];

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
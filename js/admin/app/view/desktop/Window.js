Ext.define('Indi.view.desktop.Window', {
    extend: 'Ext.window.Window',
    alias: 'widget.desktopwindow',
    //renderTo: 'i-center-center-body',
    //constrainTo: 'body',
    //constrain: true,
    width: '80%',
    height: '80%',
    maximizable: true,
    minimizable: true,
    autoRender: true,
    autoShow: true,

    initComponent: function() {
        var me = this;

        me.x = Math.random() * 400;
        me.y = Math.random() * 100;
        me.maximized = me.ctx.route.last().action.mode.toLowerCase() == 'rowset';
        if (me.ctx.route.last().action.mode.toLowerCase() == 'row') {
            me.width = '60%';
            me.height = 150;
        }
        Indi.app.taskbar.wbar.query('> [pressed]').forEach(function(btn){btn.toggle();});

        me.taskButton = Indi.app.taskbar.wbar.add({
            text: me.title,
            enableToggle: true,
            pressed: true,
            window: me,
            iconCls: 'i-btn-icon-' + me.ctx.route.last().action.view.toLowerCase(),
            listeners: {
                click: function(btn) {
                    var win = btn.window;
                    if (win.minimized || win.hidden) {
                        win.show();
                    } else if (win.active) {
                        win.minimize();
                    } else {
                        win.toFront();
                    }
                }
            }
        });
        me.iconCls = me.taskButton.iconCls;
        me.callParent();
    },

    getWrapper: function() {
        var me = this;
        if (!me.wrapper) me.wrapper = Ext.getCmp(me.id.replace(/-window$/, '-wrapper'));
        return me.wrapper;
    },

    fitContainer: function() {
        var me = this, container = Ext.get('i-center-center-body'), xy = container.getXY();
        me.setSize(container.getViewSize(false));
        me.setPosition.apply(me, [xy[0]+1, xy[1]+1]);
    },

    setSize: function() {
        var me = this, wrapper = me.getWrapper();
        me.callParent(arguments);
        if (wrapper) wrapper.updateLayout();
    },

    onDestroy: function() {
        var me = this;

        me.getWrapper().destroy();
        Indi.app.windows.remove(me);
        Indi.app.taskbar.removeTaskButton(me.taskButton);
        me.callParent(arguments);
        Indi.app.updateActiveWindow();
    },

    afterRender: function() {
        var me = this;
        me.callParent(arguments);

        Indi.app.windows.add(me.id, me);
        //me.animateTarget = me.taskButton.el;
        me.on('activate', Indi.app.updateActiveWindow, Indi.app);
        me.on('beforeshow', Indi.app.updateActiveWindow, Indi.app);
        me.on('deactivate', Indi.app.updateActiveWindow, Indi.app);
        me.on('minimize', Indi.app.updateActiveWindow, Indi.app);
        me.center();
    },

    minimize: function() {
        this.hide();
        this.fireEvent('minimize', this);
        return this;
    }
});
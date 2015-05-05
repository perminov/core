Ext.define('Indi.view.desktop.Window', {
    extend: 'Ext.window.Window',
    alias: 'widget.desktopwindow',
    renderTo: 'i-center-center-body',
    constrain: true,
    width: '80%',
    height: '80%',
    maximizable: true,
    maximized: true,
    minimizable: true,
    autoRender: true,
    autoShow: true,
    x: 0,
    y: 0,

    getWrapper: function() {
        var me = this;
        if (!me.wrapper) me.wrapper = Ext.getCmp(me.id.replace(/-window$/, '-wrapper'));
        return me.wrapper;
    },

    fitContainer: function() {
        var me = this,
            parent = me.floatParent,
            container = parent ? parent.getTargetEl() : me.container;

        me.setSize(container.getViewSize(false));
        me.setPosition.apply(me, [0, 0]);
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
        Indi.app.taskbar.wbar.query('> [pressed]').forEach(function(btn){btn.toggle();});
        me.taskButton = Indi.app.taskbar.wbar.add({
            text: me.title,
            enableToggle: true,
            pressed: true,
            window: me,
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

        Indi.app.windows.add(me.id, me);

        me.on('activate', Indi.app.updateActiveWindow, Indi.app);
        me.on('beforeshow', Indi.app.updateActiveWindow, Indi.app);
        me.on('deactivate', Indi.app.updateActiveWindow, Indi.app);
        me.on('minimize', Indi.app.updateActiveWindow, Indi.app);
    },

    minimize: function() {
        this.hide();
        this.fireEvent('minimize', this);
        return this;
    }
});
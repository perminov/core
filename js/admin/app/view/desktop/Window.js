Ext.define('Indi.view.desktop.Window', {
    extend: 'Ext.window.Window',
    alias: 'widget.desktopwindow',
    maximized: false,
    maximizable: true,
    minimizable: true,
    autoRender: true,
    autoShow: true,

    // @inheritdoc
    initComponent: function() {
        var me = this, center = Ext.getCmp('i-center-center');

        // Set default width and height to be same as center panel
        me.width = center.getWidth();
        me.height = center.getHeight();

        // Depress currently pressed windowbutton(s)
        Indi.app.taskbar.wbar.query('> [pressed]').forEach(function(btn){btn.toggle();});

        // Add new windowbutton
        me.taskButton = Indi.app.taskbar.wbar.add({
            window: me,
            handler: function(btn, e) {
                var win = btn.window, menu = btn.up('menu');
                if (menu && e.getTarget('.i-btn-icon-close')) {
                    menu.remove(btn);
                    if (!menu.items.getCount()) menu.hide();
                    win.close();
                } else {
                    // Show/minimize/toFront
                    if (win.minimized || win.hidden) win.show();
                    else if (win.active) win.minimize();
                    else win.toFront();
                }
            }
        });

        // Set iconCls
        me.iconCls = me.getIconCls();

        // Call parent
        me.callParent();
    },

    /**
     * Detect proper iconCls
     *
     * @return {String}
     */
    getIconCls: function() {
        var me = this, prefix = 'i-btn-icon-', action = me.ctx.route.last().action, icon;

        // Detect icon
        icon = me.ctx.route.last().action.mode.toLowerCase() == 'row' && !me.ctx.ti().row.id
            ? 'create'
            : action.view.toLowerCase();

        // Return
        return prefix + icon;
    },

    /**
     * Get window's wrapper panel
     *
     * @return {*}
     */
    getWrapper: function() {
        return Ext.getCmp(this.wrapperId);
    },

    /**
     * Here we override fitContainer method, as it acts not (for some reason) how we need
     */
    fitContainer: function() {
        var me = this, container = Ext.get('i-center-center-body'), xy = container.getXY();

        // Set size to fit container
        me.setSize(container.getViewSize(false));

        // Set position
        me.setPosition.apply(me, [xy[0]+1, xy[1]+1]);
    },

    // @inheritdoc
    setSize: function() {
        var me = this, wrapper = me.getWrapper();

        // Call parent
        me.callParent(arguments);

        // Update wrapper panel's layout
        if (wrapper) {

            // Fit south panel for it to be sized as user-friendly as it possible
            wrapper.fitSouth();

            // Update layout
            wrapper.updateLayout();
        }
    },

    // @inheritdoc
    onDestroy: function() {
        var me = this;

        // Destroy action
        me.getWrapper().ctx().destroy();

        // Remove current window from Indi.app.windows collection
        Indi.app.windows.remove(me);

        // Remove taskbar button, associated with current window
        Indi.app.taskbar.removeTaskButton(me.taskButton);

        // Call parent
        me.callParent(arguments);

        // Set up new active window
        Indi.app.updateActiveWindow();
    },


    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Add current window to Indi.app.windows collection
        Indi.app.windows.add(me.id, me);

        // Init event handlers
        me.on('activate', Indi.app.updateActiveWindow, Indi.app);
        me.on('beforeshow', Indi.app.updateActiveWindow, Indi.app);
        me.on('deactivate', Indi.app.updateActiveWindow, Indi.app);
        me.on('minimize', Indi.app.updateActiveWindow, Indi.app);
        me.on('maximize', Indi.app.updateTrail, Indi.app);
        me.on('restore', Indi.app.updateTrail, Indi.app);
        me.on('beforeclose', me.onBeforeClose, me);
        me.on('close', Indi.app.updateTrail, Indi.app);

        // Set window to be centered
        me.center();
    },

    /**
     * Check if there is a wrapper-holder for the wrapper-panel, rendered into current window,
     * and if so - replace holder with wrapper. This is currently used to get back wrappers
     * into south-panel tabs
     *
     * @return {Boolean}
     */
    onBeforeClose: function() {
        var me = this, holder = Ext.getCmp(me.wrapperId + '-holder');

        // If holder exists
        if (holder && !me.isGettingBack) {

            // Get wrapper back to tab
            Indi.app.putWindowBackToTab(me.wrapperId);

            // Return false to prevent window closing at this time,
            // as it will be done within Indi.app.putWindowBackToTab method
            return false;
        }

        // Return true
        return true;
    },

    /**
     * Apply new props
     *
     * @param cfg
     */
    apply: function(cfg) {
        var me = this, ot = me.taskButton.getText(), wbar = me.taskButton.up('windowbar');

        // Set window-title and windowbutton-text
        me.setTitle(cfg.title);
        me.taskButton.setText(cfg.title);

        // Destroy existing wrapper
        if (me.getWrapper()) me.getWrapper().destroy();

        // Set up new wrapperId
        me.wrapperId = cfg.wrapperId;

        // Set up context
        me.ctx = cfg.ctx;
        me.maximize();

        // Set icons
        me.setIconCls(me.getIconCls());

        // Prevent wbar width sizing from being refreshed, as if title haven't changed then there is no need
        if (cfg.title != ot) {
            wbar.calcWidthUsage();
            wbar.setWidth(wbar.getMaxWidth());
        }

        // Return
        return me;
    },

    /**
     * Minimize window
     * @return {*}
     */
    minimize: function() {
        var me = this;

        // Hide
        me.hide();

        // Fire 'minimize' event
        me.fireEvent('minimize', me);

        // Return itself
        return me;
    },

    /**
     * Hide all other non-maximized windows, if current window is maximized
     */
    hideOthers: function(){

        // If current window is not maximized - return
        if (!this.maximized) return;

        // Hide other non-maximized windows
        Ext.ComponentQuery
            .query('desktopwindow[maximized=false]')
            .forEach(function(w){
                if (w.id != window.id && !w.maximized) w.minimize();
            });
    }
});
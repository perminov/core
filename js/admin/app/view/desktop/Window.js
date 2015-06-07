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

    // @inheritdoc
    initComponent: function() {
        var me = this, icon = me.ctx.route.last().action.view.toLowerCase();

        // Positioning
        me.x = Math.random() * 400;
        me.y = Math.random() * 100;

        // Sizing
        me.maximized = me.ctx.route.last().action.mode.toLowerCase() == 'rowset';
        if (me.ctx.route.last().action.mode.toLowerCase() == 'row') {
            me.width = '60%';
            me.height = 150;
        }

        // Create taskbar button
        Indi.app.taskbar.wbar.query('> [pressed]').forEach(function(btn){btn.toggle();});
        me.taskButton = Indi.app.taskbar.wbar.add({window: me});

        me.iconCls = me.getIconCls();
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
        if (wrapper) wrapper.updateLayout();
    },

    // @inheritdoc
    onDestroy: function() {
        var me = this;

        // Destroy wrapper panel
        me.getWrapper().destroy();

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

        // Set window to be centered
        me.center();
    },

    /**
     * Apply new props
     *
     * @param cfg
     */
    apply: function(cfg) {
        var me = this;

        // Set title
        me.setTitle(cfg.title);
        me.taskButton.setText(cfg.title);

        // Destroy existing wrapper
        me.getWrapper().destroy();

        // Set up new wrapperId
        me.wrapperId = cfg.wrapperId;

        // Set up context
        me.ctx = cfg.ctx;

        // Set icons
        me.setIconCls(me.getIconCls());
        //me.getHeader().hide();
        //me.taskButton.setIconCls(me.getIconCls());

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
    }
});
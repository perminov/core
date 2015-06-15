Ext.define('Indi.lib.view.action.Panel', {

    // @inheritdoc
    extend: 'Ext.panel.Panel',

    // @inheritdoc
    alias: 'widget.actionpanel',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.Panel',

    // @inheritdoc
    border: 0,

    /**
     * Special property for easier lookup
     */
    isWrapper: true,

    /**
     * Get the context. Context here is an instance of a Indi.Controller.Action class,
     * or instance of one of it's child classes
     *
     * @return {*}
     */
    ctx: function() {
        return Ext.getCmp(this.id.replace('-wrapper', ''))
    },

    /**
     * Force context to be destroyed along with wrapper
     */
    onDestroy: function() {
        var me = this;

        // Destroy context
        if (me.ctx()) me.ctx().destroy();

        // Call parent
        me.callParent();
    },

    /**
     * Provide bread crumbs to be created/updated
     */
    afterRender: function() {
        var me = this;

        // Set up bread crubms
        if (!me.isTab) Ext.defer(function() {
            Indi.trail(true).breadCrumbs(me.ctx().route);
        }, 1);

        // Call parent
        me.callParent(arguments);
    },

    /**
     * This is for override in child classes
     */
    forScope: function() {
        return {}
    },

    /**
     * Get window, that handles all of the UI for current ctx
     *
     * @return {*}
     */
    getWindow: function() {
        var me = this, i, app = Indi.app;

        // Try to check if current windows collection already contains a window that we're going to create
        i = app.windows.collect('wrapperId').indexOf(me.id);

        // Return window if found
        if (i != -1) return app.windows.getAt(i);
    },

    // @inheritdoc
    onBoxReady: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Fit window size to match inner contents
        me.fitWindow();
    },

    /**
     * Set window's height to fit actual content's height
     *
     */
    fitWindow: function() {
        var me = this, window = me.getWindow(), windowFrameHeight, windowFrameWidth, height, width;

        // If window exists
        if (window && !window.maximized) {

            // Get real height usage
            width = me.getWidthUsage();

            // Get window's frame height usage
            windowFrameWidth = me.getWindow().getWidth() - me.getWidth();

            // Set window's height to fit actual content's height
            window.setWidth(windowFrameWidth + width);

            // Get real height usage
            height = me.getHeightUsage();

            // Get window's frame height usage
            windowFrameHeight = me.getWindow().getHeight() - me.getHeight();

            // Set window's height to fit actual content's height
            window.setHeight(windowFrameHeight + height + 1);

            // Center window
            window.center();
        }
    }
});
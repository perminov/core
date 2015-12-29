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
    fitWindow: function(delta) {

        var me = this, window = me.getWindow(), windowFrameHeight, windowFrameWidth, height, width,
            center = Ext.getCmp('i-center-center'), maxWidth = center.getWidth(), maxHeight = center.getHeight();

        // If window exists
        if (window && !window.maximized) {

            // Get window's frame width usage
            windowFrameWidth = window.getWidth() - me.getWidth();

            // Get window's frame height usage
            windowFrameHeight = window.getHeight() - me.getHeight();

            // Get real height usage
            width = (arguments.length ? me.widthUsage : me.getWidthUsage()) + windowFrameWidth;

            // Set window's height to fit actual content's height
            window.setWidth(width);

            // Get real height usage
            height = (arguments.length ? me.heightUsage.total + delta : me.getHeightUsage()) + windowFrameHeight + 1;

            // Decide whether to maximize window or make it fit-sized
            if (height > maxHeight * 0.9) {

                // Set height to be bit smaller than maximum height
                window.setHeight(maxHeight * 0.9);

                // Here we do center window before maximizing, that will provide window
                // to be positioned at center if user will pop-out window from being maximized
                window.center();

                // Maximize window
                window.maximize();

            // Else if height does not exceed maximum height
            } else {

                // Set that height for window
                window.setHeight(height);

                // If width exceeds maximum width
                if (width > maxWidth) {

                    // Set window to have maximum possible width
                    window.setWidth(maxWidth);

                    // Here we do center window before maximizing, that will provide window
                    // to be positioned at center if user will pop-out window from being maximized
                    window.center();

                    // Maximize window
                    window.maximize();

                // Else just center window
                } else window.center();
            }
        }
    },

    /**
     * Set up a height for south panel, for it to be as user-friendly as it possible
     */
    fitSouth: Ext.emptyFn
});
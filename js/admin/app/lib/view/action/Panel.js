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
     * Special flag for skipping fitWindow() call
     * This can be used for wrappers having huge number of contents,
     * for example form with a lot of fields, etc, as in such cases  fitWindow() take a lot of time
     */
    skipFitWindow: false,

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
        if (!me.skipFitWindow) me.fitWindow();

        // Fade out loader
        Indi.app.loader(false);
    },

    // @inheritdoc
    completeLayout: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        //Indi.mt('afterlayout'); console.log(new Error().stack);
    },

    /**
     * Set window's height to fit actual content's height
     *
     */
    fitWindow: function(delta) {

        var me = this, window = me.getWindow(), height, width, center = Ext.getCmp('i-center-center'),
            maxWidth = center.getWidth(), maxHeight = center.getHeight();

        // If window not yet exists - return
        if (!window) return;

        // We're in a section located deeper than at 1st level, or current action is not 'index'
        if (me.$ctx.route.length > 2 || (me.$ctx.ti().action.mode.toLowerCase() != 'rowset')) {

            // Get real width usage
            width = (arguments.length ? me.widthUsage : me.getWidthUsage()) + 12;

            // If real width usage less or equal than/to 0.9 of total width
            if (width <= maxWidth * 0.9) {

                // Restore the window for it to be non-maximized
                if (window.maximized) {
                    window.restore();
                    window.maximized = false;
                }

                // Set width
                window.setWidth(width);

                // Get real height usage
                height = (arguments.length ? me.heightUsage.total + (delta || 0) : me.getHeightUsage()) + 32 + 1;

                // Set height
                window.setHeight(Math.min(height, maxHeight));

                // Make window to appear at center
                window.center();
            }
        }

        // Ensure that there will be no windows shown behind current/active maximized window
        window.on('activate', window.hideOthers, window);
        window.on('maximize', window.hideOthers, window);
        window.hideOthers();
    },

    /**
     * Set up a height for south panel, for it to be as user-friendly as it possible
     */
    fitSouth: Ext.emptyFn,

    /**
     * Restore wrapper within a south-panel tab
     */
    onDestroy: function() {
        var me = this, tab;

        // Call parent
        me.callParent();

        // Restore tab's wrapper
        if (me.tabDraft && (tab = Ext.getCmp(me.tabDraft.containerId))
            && tab.up('[isSouth]').height == tab.up('[isSouth]').collapsedHeight) {
            Ext.defer(function(){
                if (tab) tab.add(me.tabDraft.itemConfig);
            }, 1);
        }
    }
});
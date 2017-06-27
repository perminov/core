Ext.define('Indi.lib.view.action.Tab', {

    // @inheritdoc
    layout: 'fit',

    // @inheritdoc
    closable: false,

    /**
     * Special property for easier lookup
     */
    isTab: true,

    /**
     * Flag, indicating whether or not Indi.load() call was made with this.load prop usage as an arguments
     */
    loaded: false,

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Provide Indi.load() call to be performed once the box for component is ready
        me.doLoad();
    },

    /**
     * Make Indi.load() call with self `load` property as a first argument, assuming it is an url,
     * for retrieving the complete trail/route info, efficient for creation a new instance of
     * Indi.lib.controller.action.Action class, or an instance of one of it's child classes, that
     * will represent the required workspace area, encapsulated inside a panel within a tabpanel,
     * representing a collection of tabs in a south region of main screen
     */
    doLoad: function() {
        var me = this, cfg;

        // If no `load` property - return
        if (!me.load) return;

        // If Indi.load() call was already made - return
        if (me.loaded || me.isLoading) return;

        // If south panel is minified - return
        if (me.up('tabpanel').height == me.up('tabpanel').collapsedHeight) return;

        // Build load config
        cfg = {into: me.up('[isSouthItem]').id, insteadOf: me.insteadOf};

        // Check if there is no need to do an actual request for loading tab contents,
        // as tab contents may have been already loaded and should be just picked up
        me.checkPreloadedResponse(cfg);

        // Load
        Indi.load(me.load, cfg);

        // Setup is loading
        me.isLoading = true;
    },

    /**
     * This function is called once wrapper-panel is loaded
     */
    onLoad: function() {
        var me = this;

        // Set up `loaded` flag as `true`
        me.loaded = true;

        // Set up `isLoading` flag as `false`
        me.isLoading = false;
    },

    /**
     * Check if current context has a preloaded response for active tab within south panel
     * To be implemented via different approaches separately for TabRow.js and TabRowset.js
     */
    checkPreloadedResponse: Ext.emptyFn
});
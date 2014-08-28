/**
 * Base class for all controller actions instances
 */
Ext.define('Indi.lib.controller.action.Action', {

    // @inheritdoc
    extend: 'Ext.Component',

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action',

    // @inheritdoc
    mcopwso: ['panel'],

    /**
     * Main panel config
     */
    panel: {
        id: 'i-center-center-wrapper',
        renderTo: 'i-center-center-body',
        border: 0,
        height: '100%',
        closable: true,
        layout: 'fit'
    },

    /**
     * Get the current trail item, or upper trail item - if `up` argument is given
     *
     * @param up
     * @return {Indi.lib.Trail.Item}
     */
    ti: function(up) {
        return Indi.trail(this.trailLevel - (Indi.trail(true).store.length - 1) + (up ? up : 0));
    },

    /**
     * Get the base id for all components, created while controller's action execution
     * If `up` argument is given, function will return base id of upper-level controller's action
     *
     * @param up
     * @return {String}
     */
    bid: function(up) {
        return this.ti(up).bid();
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Append tools and toolbars to the main panel
        me.panel = Ext.merge({
            tools: me.panelToolA(),
            dockedItems: me.panelDockedA()
        }, this.panel);

        // Setup main panel title, contents and trailLevel property
        Ext.create('Ext.Panel', Ext.merge({
            title: me.ti().section.title,
            items: me.panel.items,
            trailLevel: me.trailLevel
        }, me.panel));

        // Call parent
        me.callParent();
    },

    /**
     * Panel tools array builder. This method is for use in subclasses of Indi.Controller.Action
     *
     * @return {Array}
     */
    panelToolA: function() {
        return []
    },

    /**
     * Panel toolbars array builder. This method is for use in subclasses of Indi.Controller.Action
     *
     * @return {Array}
     */
    panelDockedA: function() {
        return []
    }
});

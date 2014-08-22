/**
 * Indi Engine controller concept implementation
 */
Ext.define('Indi.lib.controller.Controller', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller',

    // @inheritdoc
    extend: 'Ext.app.Controller',

    /**
     * Dictionary of default modes and views for different actions
     */
    statics: {
        defaultMode: {index: 'rowset', form: 'row'},
        defaultView: {index: 'grid', form: 'form'}
    },

    /**
     * Actions configuration. This config is for use in subclasses of current class
     */
    actionsConfig: {},

    /**
     * Action dispatcher function
     *
     * @param {String} action Action name/alias
     * @param {String} uri The uri, that current action data was got by request to
     */
    dispatch: function(action, uri){
        var me = this, a, aCfg;

        // Setup initial action config
        aCfg = Ext.clone(this.actionsConfig[action]);
        aCfg = aCfg || {};
        aCfg.trailLevel = this.trailLevel;
        aCfg.uri = Ext.clone(uri);

        // Setup mode and view for current trail item action
        if (!this.trail().action.mode) this.trail().action.mode = Indi.Controller.defaultMode[action];
        if (!this.trail().action.view) this.trail().action.view = Indi.Controller.defaultView[action];

        // Build the action class name
        a = 'Indi.Controller.Action.' + Indi.ucfirst(this.trail().action.mode) + '.' + Indi.ucfirst(this.trail().action.view);

        // Setup `actions` property, for instantiated action classes storage
        this.actions = this.actions || {};

        // Create an certain action class instance, related to current action
        this.actions[action] = Ext.create(a, aCfg);
    },

    // @inheritdoc
    constructor: function(config){

        // Setup trail level
        this.trailLevel = Indi.trail().level;

        // Call parent
        this.callParent(arguments);
    },

    /**
     * Gets the current trail item, or one of it's parents - if `up` argumentis given
     *
     * @param up
     * @return {Indi.lib.trail.Item}
     */
    trail: function(up) {
        return Indi.trail(this.trailLevel - (Indi.trail(true).store.length - 1) + (up ? up : 0));
    }
});

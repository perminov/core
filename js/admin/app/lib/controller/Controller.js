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
     * See docs at same Ext.Component class property
     */
    mcopwso: ['actionsConfig'],

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
        var me = this, actionExtendCmpName, actionCmpName;

        // Setup `actions` property, for being a storage for action classes instances
        me.actions = me.actions || {};

        // Setup mode and view for current trail item action
        if (!me.trail().action.mode) me.trail().action.mode = Indi.Controller.defaultMode[action];
        if (!me.trail().action.view) me.trail().action.view = Indi.Controller.defaultView[action];

        // Build the action parent component name
        actionExtendCmpName = 'Indi.Controller.Action';
        if (me.trail().action.mode) actionExtendCmpName += '.' + Indi.ucfirst(me.trail().action.mode);
        if (me.trail().action.view) actionExtendCmpName += '.' + Indi.ucfirst(me.trail().action.view.replace('_', '.'));

        // Build the action component name
        actionCmpName = 'Indi.controller.' + me.trail().section.alias + '.action.' + me.trail().action.alias;

        // Define the action component
        Ext.define(actionCmpName, Ext.merge({extend: actionExtendCmpName}, me.actionsConfig[action]));

        // Create action component instance, related to current action
        me.actions[action] = Ext.create(actionCmpName, {
            trailLevel: me.trailLevel,
            uri: Ext.clone(uri),
            clr: me
        });
    },

    // @inheritdoc
    constructor: function(config){
        var me = this;

        // Setup trail level
        me.trailLevel = Indi.trail().level;

        // Merge parent
        me.mergeParent(config);

        // Call parent
        me.callParent(arguments);
    },

    /**
     * Gets the current trail item, or one of it's parents - if `up` argument is given
     *
     * @param up
     * @return {Indi.lib.trail.Item}
     */
    trail: function(up) {
        return Indi.trail(this.trailLevel - (Indi.trail(true).store.length - 1) + (up ? up : 0));
    }

}, function() {

    // Borrow 'mergeParent' method from Ext.Component class
    this.borrow(Ext.Component, ['mergeParent']);
});

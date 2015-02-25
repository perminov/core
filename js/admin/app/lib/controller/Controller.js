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
    dispatch: function(action, uri, route) {
        var me = this, actionExtendCmpName, actionCmpName;

        // Setup `actions` property, for being a storage for action classes instances
        me.actions = me.actions || {};

        // Convert route items to Indi.trail.Item instances
        route.forEach(function(r, i, a){
            a[i] = Ext.create('Indi.trail.Item', r);
        });

        // Setup mode and view for current trail item action
        if (!route.last().action.mode) route.last().action.mode = Indi.Controller.defaultMode[action];
        if (!route.last().action.view) route.last().action.view = Indi.Controller.defaultView[action];

        // Build the action parent component name
        actionExtendCmpName = 'Indi.Controller.Action';
        if (route.last().action.mode) actionExtendCmpName += '.' + Indi.ucfirst(route.last().action.mode);
        if (route.last().action.view) actionExtendCmpName += '.' + Indi.ucfirst(route.last().action.view.replace('_', '.'));

        // Build the action component name
        actionCmpName = 'Indi.controller.' + route.last().section.alias + '.action.' + route.last().action.alias;

        // Define the action component
        Ext.define(actionCmpName, Ext.merge({extend: actionExtendCmpName}, me.actionsConfig[action]));

        // Build the id for action object
        var id = 'i-section-' + route.last().section.alias + '-action-' + route.last().action.alias;
        if (route.last().row) {
            id += '-row-' + (route.last().row.id || 0);
        } else if (route.last(1).row) {
            id += '-parentrow-' + route.last(1).row.id;
        }

        // Create action component instance, related to current action
        me.actions[action] = Ext.create(actionCmpName, {
            id: id,
            trailLevel: me.trailLevel,
            route: route,
            uri: Ext.clone(uri),
            clr: me,
            //panel: Indi.trail(true).wrapper
        });
    },

    // @inheritdoc
    constructor: function(config){
        var me = this;

        // Setup trail level
        me.trailLevel = Indi.trail(true).tree[config.id].level;

        // Merge parent
        me.mergeParent(config);

        // Call parent
        me.callParent(arguments);
    }
}, function() {

    // Borrow 'mergeParent' method from Ext.Component class
    this.borrow(Ext.Component, ['mergeParent']);
});

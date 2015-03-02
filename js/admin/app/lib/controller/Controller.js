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
     * @param {Object} scope Object, containing `route`, `plain`, `uri` and `cfg` properties
     */
    dispatch: function(scope) {
        var me = this, action = scope.route.last().action.alias, actionExtendCmpName, actionCmpName;

        // Setup `actions` property, for being a storage for action classes instances
        me.actions = me.actions || {};

        // Convert route items to Indi.trail.Item instances
        scope.route.forEach(function(r, i, a) {a[i] = Ext.create('Indi.trail.Item', r);});

        // Setup mode and view for current trail item action
        if (!scope.route.last().action.mode) scope.route.last().action.mode = Indi.Controller.defaultMode[action];
        if (!scope.route.last().action.view) scope.route.last().action.view = Indi.Controller.defaultView[action];

        // Build the action parent component name
        actionExtendCmpName = 'Indi.Controller.Action';
        if (scope.route.last().action.mode) actionExtendCmpName += '.' + Indi.ucfirst(scope.route.last().action.mode);
        if (scope.route.last().action.view) actionExtendCmpName += '.' + Indi.ucfirst(scope.route.last().action.view.replace('_', '.'));

        // Build the action component name
        actionCmpName = 'Indi.controller.' + scope.route.last().section.alias + '.action.' + scope.route.last().action.alias;

        // Define the action component
        Ext.define(actionCmpName, Ext.merge({extend: actionExtendCmpName}, me.actionsConfig[action]));

        // Build the id for action object
        scope.id = 'i-section-' + scope.route.last().section.alias + '-action-' + scope.route.last().action.alias;
        if (scope.route.last().row) {
            scope.id += '-row-' + (scope.route.last().row.id || 0);
        } else if (scope.route.last(1).row) {
            scope.id += '-parentrow-' + scope.route.last(1).row.id;
        }

        // Create action component instance, related to current action
        me.actions[action] = Ext.create(actionCmpName, scope);
    },

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Merge parent
        me.mergeParent(config);

        // Call parent
        me.callParent(arguments);
    }
}, function() {

    // Borrow 'mergeParent' method from Ext.Component class
    this.borrow(Ext.Component, ['mergeParent']);
});

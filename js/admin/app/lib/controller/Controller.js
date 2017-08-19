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
    mcopwso: ['actionsConfig', 'actionsSharedConfig', 'actionsSharedConfig$Row', 'actionsSharedConfig$Rowset'],

    /**
     * Actions configuration. This config is for use in subclasses of current class
     */
    actionsConfig: {},

    /**
     * Actions shared configuration. This config will be applied to any action, rather than to certain action
     */
    actionsSharedConfig: {},

    /**
     * Row-actions shared configuration. This config will be applied to any row-action
     */
    actionsSharedConfig$Row: {},

    /**
     * Rowset-actions shared configuration. This config will be applied to any rowset-action
     */
    actionsSharedConfig$Rowset: {},

    /**
     * Empty function, for `scope` arg adjustments
     *
     * @param scope
     */
    preDispatch: function(scope) {
    },

    /**
     * Action dispatcher function
     *
     * @param {Object} scope Object, containing `route`, `plain`, `uri` and `cfg` properties
     */
    dispatch: function(scope) {
        var me = this, action, actionExtendCmpName, actionCmpName, exst;

        // Pre-dispatch
        me.preDispatch(scope);

        // Init model
        me.initModel(scope);

        // Get action alias
        action = scope.route.last().action.alias;

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

        /*// Build array of current controller's parents
        var parentControllerA = [];
        while ((me = me.superclass) && (me.$className != 'Indi.lib.controller.Controller'))
            parentControllerA.push(me); parentControllerA.reverse(); me = this;

        // Define parent actions
        var parentActionName, parentActionCfg = [];
        for (var i = 0; i < parentControllerA.length; i++) {
            parentActionName = parentControllerA[i].$className + '.action.' + scope.route.last().action.alias;
            parentActionCfg[i] = parentControllerA[i].actionsConfig[scope.route.last().action.alias];
            Ext.define(parentActionName, Ext.merge({extend: actionExtendCmpName}, parentActionCfg[i] || {}));
            actionExtendCmpName = parentActionName;
        }*/

        // Define the action component
        Ext.define(actionCmpName, Ext.merge(
            {extend: actionExtendCmpName},
            me.actionsSharedConfig,
            me['actionsSharedConfig$' + Indi.ucfirst(scope.route.last().action.mode)],
            me.actionsConfig[action]
        ));

        // Build the id for action object
        scope.id = 'i-section-' + scope.route.last().section.alias + '-action-' + scope.route.last().action.alias;

        if (scope.route.last().row) {
            scope.id += '-row-' + (scope.route.last().row.id || 0);
        } else if (scope.route.last(1) && scope.route.last(1).row) {
            scope.id += '-parentrow-' + scope.route.last(1).row.id;
        }

        // If wrapper and scope already exists for current action - destroy scope before re-instantiate
        if (Ext.getCmp(scope.id + '-wrapper') && (exst = Ext.getCmp(scope.id))) exst.destroy();

        // Create action component instance, related to current action
        Ext.create(actionCmpName, scope);
    },

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Merge parent
        me.mergeParent(config);

        // Call parent
        me.callParent(arguments);
    },

    initModel: function(scope) {
        var me = this;
    }
}, function() {

    // Borrow 'mergeParent' method from Ext.Component class
    this.borrow(Ext.Component, ['mergeParent']);
});

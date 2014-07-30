Ext.define('Indi.lib.controller.Controller', {
    alternateClassName: 'Indi.Controller',
    extend: 'Ext.app.Controller',
    statics: {
        defaultMode: {index: 'rowset', form: 'row'},
        defaultView: {index: 'grid', form: 'form'}
    },
    actionsConfig: {},
    dispatch: function(action, uri){
        var a, aCfg = Ext.clone(this.actionsConfig[action]); aCfg = aCfg || {}; aCfg.trailLevel = this.trailLevel;
        aCfg.uri = Ext.clone(uri);
        if (!this.trail().action.mode) this.trail().action.mode = Indi.Controller.defaultMode[action];
        if (!this.trail().action.view) this.trail().action.view = Indi.Controller.defaultView[action];
        a = 'Indi.Controller.Action.' + Indi.ucfirst(this.trail().action.mode) + '.' + Indi.ucfirst(this.trail().action.view);
        this.actions = this.actions || {};
        this.actions[action] = Ext.create(a, aCfg);
    },
    constructor: function(config){
        this.trailLevel = Indi.trail().level;
        this.callParent(arguments);
    },
    trail: function(up) {
        return Indi.trail(this.trailLevel - (Indi.trail(true).store.length - 1) + (up ? up : 0));
    }
});

/**
 * Here we override Ext.Component component, to provide an ability for 'tooltip' config properties to be used for
 * creating Ext.tip.ToolTip objects instead of standart Ext.tip.QuickTip objects
 */
Ext.override(Ext.Component, {

    /**
     * This property's name is an abbreviation that stands for 'Merge Config Object-Properties With Superclass Ones'.
     * Property represents the list of properties, that should be merged through all superclass hierarchy, starting
     * from current component instance and up to it's most top superclass, instead of simple overwriting that properties
     */
    mcopwso: [],

    /**
     * Get the context of a component. Context here mean the current action object fired within certain controller object
     *
     * @return {Indi.lib.controller.action.Action}
     */
    ctx: function() {
        var me = this, hasCtx, wrapper = me.isWrapper ? me : me.up('[isWrapper]');
        if (wrapper) return wrapper.ctx(); else if (hasCtx = me.up('[hasCtx]')) return hasCtx.$ctx;
    },

    /**
     * Get the current trail item, that was in power at the moment of component instantiation
     *
     * @return {Indi.lib.trail.Item}
     */
    ti: function(){
        return this.ctx().ti();
    },

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Define tooltip getter
        me.getToolTip = function() {
            return Ext.getCmp(me.id + '-tooltip');
        };

        // If 'tooltip' property was defined, create the tooltip object
        if (me.tooltip) Ext.tip.ToolTip.create(me);

        // Call parent
        me.callParent(arguments);

        // Set position on the page
        if (!(me.x && me.y) && (me.pageX || me.pageY)) me.setPagePosition(me.pageX, me.pageY);
    },

    /**
     * Provide taking in effect for `mcopwso` property
     *
     * @param config Config object, passed to `constructor` method call
     */
    mergeParent: function(config) {
        var initialMcopwso = this.mcopwso.join(',').split(',');
        var obj = this;
        while (obj.superclass) {
            if (obj.superclass.mcopwso && obj.superclass.mcopwso.length)
                for (var i = 0; i < obj.superclass.mcopwso.length; i++)
                    if (this.mcopwso.indexOf(obj.superclass.mcopwso[i]) == -1)
                        this.mcopwso.push(obj.superclass.mcopwso[i]);
            obj = obj.superclass;
        }
        obj = this;
        if (this.mcopwso.length) while (obj.superclass) {
            for (var i = 0; i < this.mcopwso.length; i++)
                if (this[this.mcopwso[i]] && obj.superclass && obj.superclass[this.mcopwso[i]])
                    this[this.mcopwso[i]]
                        = Ext.merge(Ext.clone(obj.superclass[this.mcopwso[i]]), this[this.mcopwso[i]]);

            obj = obj.superclass;
        }
        for (var i = 0; i < initialMcopwso.length; i++) {
            if (typeof config == 'object' && typeof config[initialMcopwso[i]] == 'object') {
                this[initialMcopwso[i]] = Ext.merge(this[initialMcopwso[i]], config[initialMcopwso[i]]);
                delete config[initialMcopwso[i]];
            }
        }
    },

    // @inheritdoc
    constructor: function(config){
        this.mergeParent(config);
        this.callParent(arguments);
    },

    /**
     * Allows addition of behavior to the 'destroy' operation.
     * After calling the superclass’s onDestroy, the Component will be destroyed.
     *
     * @template
     * @protected
     */
    onDestroy: function() {
        var me = this;

        // Destroy the tooltip, if exists
        if (me.tooltip && me.getToolTip()) {
            if (me.getToolTip().getEl() && me.getToolTip().getEl().getActiveAnimation())
                me.getToolTip().getEl().getActiveAnimation().end();
            me.getToolTip().destroy();
        }

        // Call parent
        me.callParent();
    },
    
    /**
     * Special function for short-hand access to any component, that is a sibling to current component, 
     * e.g. has the same value of `ownerCt` property
     *
     * @param alias
     * @return {*}
     */
    sbl: function(name, fn) {
        var me = this,
            selector = '[name="' + name.split(',').join('"], [name="') + '"]',
            found = me.ownerCt.query(selector);

        // Call the given `fn` function for each found component
        if (fn) for (var i = 0; i < found.length; i++) {
            if (typeof fn == 'function') fn.call(found[i]);
            else if (typeof fn == 'string') found[i][fn]();
        }

        // Return first found component, or all found components, depending on comma presense in `name` arg
        return name.match(/,/) ? found : found[0];
    },
    
    /**
     * jQuery-styled shortcut for getValue/setValue members
     * 
     * @param value
     * @return {*}
     */
    val: function(value) {
        if (arguments.length) {
            if (typeof this.setValue == 'function') return this.setValue(value);
        } else {
            if (typeof this.getValue == 'function') return this.getValue();
        }
    }
});
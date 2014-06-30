/**
 * Override Ext.tip.ToolTip, for append static 'lastFade' property, to be able to skip fadings,
 * if user move mouse from one tooltipped componend to another enough fast
 */
Ext.override(Ext.tip.ToolTip, {
    statics: {
        lastFade: null,
        lastFadeTimeout: null,
        lastTip: null,
        isFast: 350
    }
});

/**
 * Here we override Ext.Component component, to provide an ability for 'tooltip' config properties to be used for
 * creating Ext.tip.ToolTip objects instead of standart Ext.tip.QuickTip objects
 */
Ext.override(Ext.Component, {

    ctx: function() {
        var trailLevel = this.trailLevel != undefined ? this.trailLevel : Ext.getCmp('i-center-center-wrapper').trailLevel;
        var trailItem = Indi.trail(trailLevel - (Indi.trail(true).store.length - 1));
        return Indi.app.getController(trailItem.section.alias).actions[trailItem.action.alias];
    },

    ti: function(){
        var trailLevel = this.trailLevel != undefined ? this.trailLevel : Ext.getCmp('i-center-center-wrapper').trailLevel;
        return Indi.trail(trailLevel - (Indi.trail(true).store.length - 1));
    },

    // AfterRender
    afterRender: function() {
        var me = this;

        // Define tooltip getter
        me.getToolTip = function() {
            return Ext.getCmp(me.id + '-tooltip');
        }

        me.ctx = function() {
            var trailLevel = Ext.getCmp('i-center-center-wrapper').trailLevel;
            var trailItem = Indi.trail(trailLevel - (Indi.trail(true).store.length - 1));
            return Indi.app.getController(trailItem.section.alias).actions[trailItem.action.alias];
        }

        // If 'tooltip' property was defined
        if (me.tooltip) {

            // Setup initial arrow tooltip config
            var tooltipCfg = {
                id: this.id + '-tooltip',
                hideDelay: 0,
                showDelay: 0,
                dismissDelay: 0,
                staticOffset: [0, 0],
                anchor: 'top',
                cls: 'i-tip',
                target: this.id,
                isFast: Ext.ToolTip.isFast,
                listeners: {
                    // Setup tooltip positioning
                    afterlayout: function(){
                        if (this.anchor == 'top' || this.anchor == 'bottom') {
                            var offsetX = (this.getWidth() - this.target.getWidth())/2;
                            this.mouseOffset = [-offsetX + this.staticOffset[0], this.staticOffset[1]];
                            this.anchorOffset = -20;
                            this.anchorOffset += this.getWidth()/2;

                            if (this.target.lastBox.x < offsetX) {
                                this.mouseOffset = [-this.target.lastBox.x + this.staticOffset[0], this.staticOffset[1]];
                                this.anchorOffset -= offsetX - this.target.lastBox.x;
                            }
                        } else {
                            this.mouseOffset = [this.staticOffset[0], this.staticOffset[1]];
                        }
                    },

                    // Do not show tooltips for disabled targets
                    beforeshow: function(){
                        if (this.target.hasCls('x-item-disabled')) return false;
                    },

                    // Provide fading in as per Gmail style
                    show: function(){
                        this.noFadeOut = false;
                        if (Ext.ToolTip.lastTip && Ext.ToolTip.lastTip.id != this.id) {
                            if (new Date().getTime() - Ext.ToolTip.lastFade < Ext.ToolTip.isFast) {
                                this.getEl().setStyle({opacity: 1});
                            } else {
                                this.getEl().fadeIn();
                            }
                            Ext.ToolTip.lastTip.noFadeOut = true;
                            Ext.ToolTip.lastTip.hide();
                        } else {
                            this.getEl().fadeIn();
                        }
                        Ext.ToolTip.lastFade = new Date().getTime();
                    },

                    // Provide fading out as per Gmail style
                    beforehide: function(){
                        var me = this;
                        Ext.ToolTip.lastTip = me;
                        if (!me.noFadeOut) {
                            me.getEl().fadeOut({callback: function(){
                                me.noFadeOut = true;
                                me.hide();
                            }});
                            return false;
                        }
                        Ext.ToolTip.lastFade = new Date().getTime();
                    }
                }
            };

            // If 'tooltip' property is an object
            if (typeof me.tooltip == 'object') tooltipCfg =  Ext.Object.merge(tooltipCfg, me.tooltip);

            // Else we assume it is just a tooltip string
            else tooltipCfg.html = me.tooltip;

            // Create tooltip
            new Ext.tip.ToolTip(tooltipCfg);
        }

        // Call parent
        me.callParent();

        // Set position on the page
        if (!(me.x && me.y) && (me.pageX || me.pageY)) {
            me.setPagePosition(me.pageX, me.pageY);
        }
    },

    /**
     * Allows addition of behavior to the destroy operation.
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
    }
});

/**
 * Here we override Ext.Component for being
 */
Ext.define('Ext.Component', {
    extend: 'Ext.Component',
    mcopwso: [], // Merge Config Object-Properties With Superclass Ones
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

        if (this.mcopwso.length)
            while (obj.superclass) {
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
    constructor: function(config){
        this.mergeParent(config);
        this.callParent(arguments);
    }
});
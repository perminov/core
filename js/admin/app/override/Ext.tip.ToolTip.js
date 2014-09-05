/**
 * Override Ext.tip.ToolTip, for append static 'lastFade' property, to be able to skip fadings,
 * if user move mouse from one tooltipped component to another enough fast
 */
Ext.override(Ext.tip.ToolTip, {
    statics: {

        /**
         * Timestamp of last tooltip fadeout. Not matter which tooltip was fade out.
         */
        lastFade: null,

        /**
         * Keeps the javasctip timeout-resource, created at the stage of tooltip fadeout
         */
        lastFadeTimeout: null,

        /**
         * Keeps the Ext.tip.ToolTip object instance which was the last active
         */
        lastTip: null,

        /**
         * Number of milliseconds, for detecting whether or not current tooltip became active
         * too fast since the moment of previous tooltip became inactive. This property is used
         * to determine, whether or not simple show/hide should be used instead of fadin/fadout
         */
        isFast: 350,

        /**
         * Tooltip creating method
         *
         * @param me Target, that tooltip is being created for. It can be any instance of Ext.Component/Ext.dom.Element
         */
        create: function(me) {

            // Setup initial arrow tooltip config
            var tooltipCfg = {
                id: me.id + '-tooltip',
                hideDelay: 0,
                showDelay: 0,
                dismissDelay: 0,
                staticOffset: [0, 0],
                anchor: 'top',
                cls: 'i-tip',
                target: me.id,
                constrainParent: true,
                isFast: Ext.ToolTip.isFast,
                listeners: {

                    // Setup tooltip positioning
                    afterlayout: function(){
                        if (this.anchor == 'top' || this.anchor == 'bottom') {
                            var offsetX = (this.getWidth() - this.target.getWidth())/2;
                            this.mouseOffset = [-offsetX + this.staticOffset[0], this.staticOffset[1]];
                            this.anchorOffset = -20;
                            this.anchorOffset += this.getWidth()/2;

                            if (this.constrainParent && this.target.lastBox && this.target.lastBox.x < offsetX) {
                                this.mouseOffset = [-this.target.lastBox.x + this.staticOffset[0], this.staticOffset[1]];
                                this.anchorOffset -= offsetX - this.target.lastBox.x;
                            }

                            /*if (true) {
                                this.mouseOffset[0] = 0;
                                this.anchorOffset = -7;
                            }*/
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
    }
});

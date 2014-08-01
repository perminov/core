/**
 * Override Ext.tip.ToolTip, for append static 'lastFade' property, to be able to skip fadings,
 * if user move mouse from one tooltipped component to another enough fast
 */
Ext.override(Ext.tip.ToolTip, {
    statics: {
        lastFade: null,
        lastFadeTimeout: null,
        lastTip: null,
        isFast: 350,

        /**
         * Tooltip creating method
         *
         * @param me
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
                isFast: Ext.ToolTip.isFast,
                listeners: {

                    // Setup tooltip positioning
                    afterlayout: function(){
                        if (this.anchor == 'top' || this.anchor == 'bottom') {
                            var offsetX = (this.getWidth() - this.target.getWidth())/2;
                            this.mouseOffset = [-offsetX + this.staticOffset[0], this.staticOffset[1]];
                            this.anchorOffset = -20;
                            this.anchorOffset += this.getWidth()/2;

                            if (this.target.lastBox && this.target.lastBox.x < offsetX) {
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
    }
});

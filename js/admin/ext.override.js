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
 * Here we override Ext.button.Split component, because Ext gives (for some unknown reason) and error
 * in case if splitbutton's 'arrowTooltip' property is set. Bit investigation resulted what Ext is trying
 * to deal with 'arrowEl' element, but it does not exist within original component template, so here we
 * override the 'renderTpl' and 'childEls' properties for appending that 'arrowEl' element
 */
Ext.override(Ext.button.Split, {

    // Append 'arrowEl' item to 'childEls' array-property
    childEls: ['btnEl', 'btnWrap', 'btnInnerEl', 'btnIconEl', 'arrowEl'],

    // Insert <span> for arrow element at the top of the template
    renderTpl: [
        '<span id="{id}-arrowEl" style="width: 12px; height: 16px; float: right;"><span class="{baseCls}-arrow {arrowCls}"<tpl if="iconUrl"> style="background-image:url({arrowUrl})"</tpl>></span></span>',
        '<em id="{id}-btnWrap"<tpl if="splitCls"> class="{splitCls}"</tpl>>',
            '<tpl if="href">',
                '<a id="{id}-btnEl" href="{href}" class="{btnCls}" target="{hrefTarget}"',
                '<tpl if="tabIndex"> tabIndex="{tabIndex}"</tpl>',
                '<tpl if="disabled"> disabled="disabled"</tpl>',
                ' role="link">',
                '<span id="{id}-btnInnerEl" class="{baseCls}-inner">',
                    '{text}',
                '</span>',
                '<span id="{id}-btnIconEl" class="{baseCls}-icon {iconCls}"<tpl if="iconUrl"> style="background-image:url({iconUrl})"</tpl>></span>',
                '</a>',
            '<tpl else>',
                '<button id="{id}-btnEl" type="{type}" class="{btnCls}" hidefocus="true"',
                '<tpl if="tabIndex"> tabIndex="{tabIndex}"</tpl>',
                '<tpl if="disabled"> disabled="disabled"</tpl>',
                ' role="button" autocomplete="off">',
                '<span id="{id}-btnInnerEl" class="{baseCls}-inner" style="{innerSpanStyle}">',
                    '{text}',
                '</span>',
                '<span id="{id}-btnIconEl" class="{baseCls}-icon {iconCls}"<tpl if="iconUrl"> style="background-image:url({iconUrl})"</tpl>></span>',
                '</button>',
            '</tpl>',
        '</em>',
        '<tpl if="closable">',
        '<a id="{id}-closeEl" href="#" class="{baseCls}-close-btn" title="{closeText}"></a>',
        '</tpl>'
    ],

    // AfterRender
    afterRender: function() {
        var me = this;

        // If 'arrowTooltip' property was defined
        if (me.arrowTooltip) {

            // Setup initial arrow tooltip config
            var arrowTooltipCfg = {
                staticOffset: [0, 3],
                hideDelay: 0,
                showDelay: 0,
                dismissDelay: 0,
                isFast: Ext.ToolTip.isFast,
                anchor: 'top',
                cls: 'i-tip',
                target: me.arrowEl,
                listeners: {

                    // Setup tooltip positioning
                    afterlayout: function(){
                        var offsetX = (this.getWidth() - this.target.getWidth())/2;
                        this.mouseOffset = [-offsetX + this.staticOffset[0], this.staticOffset[1]];
                        this.anchorOffset = -20;
                        this.anchorOffset += this.getWidth()/2;
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

            // If 'arrowTooltip' property is an object
            if (typeof me.arrowTooltip == 'object') arrowTooltipCfg =  Ext.Object.merge(arrowTooltipCfg, me.arrowTooltip);

            // Else we assume it is just a tooltip string
            else arrowTooltipCfg.html = me.arrowTooltip;

            // Create arrow tooltip
            new Ext.tip.ToolTip(arrowTooltipCfg);
        }

        // Call parent
        me.callParent();

        // Set position on the page
        if (!(me.x && me.y) && (me.pageX || me.pageY)) {
            me.setPagePosition(me.pageX, me.pageY);
        }
    }
});

/**
 * Here we override Ext.Component component, to provide an ability for 'tooltip' config properties to be used for
 * creating Ext.tip.ToolTip objects instead of standart Ext.tip.QuickTip objects
 */
Ext.override(Ext.Component, {

    // AfterRender
    afterRender: function() {
        var me = this;

        // If 'tooltip' property was defined
        if (me.tooltip) {

            // Setup initial arrow tooltip config
            var tooltipCfg = {
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
                        var offsetX = (this.getWidth() - this.target.getWidth())/2;
                        this.mouseOffset = [-offsetX + this.staticOffset[0], 0];
                        this.anchorOffset = -20;
                        this.anchorOffset += this.getWidth()/2;

                        if (this.target.lastBox.x < offsetX) {
                            this.mouseOffset = [-this.target.lastBox.x + this.staticOffset[0], 0];
                            this.anchorOffset -= offsetX - this.target.lastBox.x;
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

            // Create arrow tooltip
            new Ext.tip.ToolTip(tooltipCfg);
        }

        // Call parent
        me.callParent();

        // Set position on the page
        if (!(me.x && me.y) && (me.pageX || me.pageY)) {
            me.setPagePosition(me.pageX, me.pageY);
        }
    }
});

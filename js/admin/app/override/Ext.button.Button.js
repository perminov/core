Ext.override(Ext.button.Button, {

    /**
     * Provide programmatical click ability, keeping css 'clicked-style' in force
     */
    press: function() {
        var me = this;
        if (!me.disabled) {
            me.onMouseDown({button: 0});
            Ext.defer(function(){try{me.onMouseUp({button: 0});} catch(e){}}, 300);
            me.handler(me);
        }
    },

    /**
     * Here we disable function body, as we have own way to create tooltips
     *
     * @param tooltip
     * @param initial
     * @return {*}
     */
    setTooltip: function(tooltip, initial) {
        var me = this;
        /*if (me.rendered) {
            if (!initial) {
                me.clearTip();
            }
            if (Ext.isObject(tooltip) && tooltip.id) {
                Ext.tip.QuickTipManager.register(Ext.apply({
                    target: me.btnEl.id
                }, tooltip));
                me.tooltip = tooltip;
            } else {
                //me.btnEl.dom.setAttribute(me.getTipAttr(), tooltip);
            }
        } else {
            me.tooltip = tooltip;
        }*/
        return me;
    },

    /**
     * Here we disable function body, as we have own wayto destroy tooltips
     */
    clearTip: function() {
        /*if (Ext.isObject(this.tooltip) && this.tooltip.id) {
            Ext.tip.QuickTipManager.unregister(this.btnEl);
        }*/
    },

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Add icon and move text to tooltip, if specified
        if (config.icon) {
            config.icon = Indi.std + config.icon;
            config.tooltip = config.text + '';
            config.text = '';
        } 

        // Else if `iconCls` is specified with '!'-sign at the beginning - button text will be also moved to tooltip
        else if (config.iconCls && config.iconCls.match(/^!/)) {
            config.iconCls = config.iconCls.replace(/^!/, '');
            config.tooltip = config.text + '';
            config.text = '';
        }

        // Call parent
        me.callParent(arguments);
    },

    /**
     * Overridden to pick me.menuOffset if defined
     *
     * @return {*}
     */
    showMenu: function() {
        var me = this;
        if (me.rendered && me.menu) {
            if (me.tooltip && me.getTipAttr() != 'title') {
                Ext.tip.QuickTipManager.getQuickTip().cancelShow(me.btnEl);
            }
            if (me.menu.isVisible()) {
                me.menu.hide();
            }

            me.menu.showBy(me.el, me.menuAlign, me.menuOffset || (((!Ext.isStrict && Ext.isIE) || Ext.isIE6) ? [-20, -2] : undefined));
        }
        return me;
    }
});


Ext.override(Ext.window.MessageBox, {
    reconfigure: function(cfg) {
        var me = this,
            buttons = 0,
            hideToolbar = true,
            initialWidth = me.maxWidth,
            oldButtonText = me.buttonText,
            i;

        // Restore default buttonText before reconfiguring.
        me.updateButtonText();

        cfg = cfg || {};
        me.cfg = cfg;
        if (cfg.width) {
            initialWidth = cfg.width;
        }

        // Default to allowing the Window to take focus.
        delete me.defaultFocus;

        // clear any old animateTarget
        me.animateTarget = cfg.animateTarget || undefined;

        // Defaults to modal
        me.modal = cfg.modal !== false;

        // Show the title
        if (cfg.title) {
            me.setTitle(cfg.title||'&#160;');
        }

        // Extract button configs
        if (Ext.isObject(cfg.buttons)) {
            me.buttonText = cfg.buttons;
            buttons = 0;
        } else {
            me.buttonText = cfg.buttonText || me.buttonText;
            buttons = Ext.isNumber(cfg.buttons) ? cfg.buttons : 0;
        }

        // Apply custom-configured buttonText
        // Infer additional buttons from the specified property names in the buttonText object
        buttons = buttons | me.updateButtonText();

        // Restore buttonText. Next run of reconfigure will restore to prototype's buttonText
        me.buttonText = oldButtonText;

        // During the on render, or size resetting layouts, and in subsequent hiding and showing, we need to
        // suspend layouts, and flush at the end when the Window's children are at their final visibility.
        Ext.suspendLayouts();
        me.hidden = false;
        if (!me.rendered) {
            me.width = initialWidth;
            me.render(Ext.getBody());
        } else {
            me.setSize(initialWidth, me.maxHeight);
        }

        // Hide or show the close tool
        me.closable = cfg.closable && !cfg.wait;
        me.header.child('[type=close]').setVisible(cfg.closable !== false);

        // Hide or show the header
        if (!cfg.title && !me.closable) {
            me.header.hide();
        } else {
            me.header.show();
        }

        // Default to dynamic drag: drag the window, not a ghost
        me.liveDrag = !cfg.proxyDrag;

        // wrap the user callback
        me.userCallback = Ext.Function.bind(cfg.callback ||cfg.fn || Ext.emptyFn, cfg.scope || Ext.global);

        // Hide or show the icon Component
        me.setIcon(cfg.icon);

        // Hide or show the message area
        if (cfg.msg) {
            me.msg.setValue(cfg.msg);
            me.msg.show();
        } else {
            me.msg.hide();
        }

        // flush the layout here to pick up
        // height adjustments on the msg field
        Ext.resumeLayouts(true);
        Ext.suspendLayouts();

        // Hide or show the input field
        if (cfg.prompt || cfg.multiline) {
            me.multiline = cfg.multiline;
            if (cfg.multiline) {
                me.textArea.setValue(cfg.value);
                me.textArea.setHeight(cfg.defaultTextHeight || me.defaultTextHeight);
                me.textArea.show();
                me.textField.hide();
                me.defaultFocus = me.textArea;
            } else {
                me.textField.setValue(cfg.value);
                me.textArea.hide();
                me.textField.show();
                me.defaultFocus = me.textField;
            }
        } else {
            me.textArea.hide();
            me.textField.hide();
        }

        // Hide or show the progress bar
        if (cfg.progress || cfg.wait) {
            me.progressBar.show();
            me.updateProgress(0, cfg.progressText);
            if(cfg.wait === true){
                me.progressBar.wait(cfg.waitConfig);
            }
        } else {
            me.progressBar.hide();
        }

        // Hide or show buttons depending on flag value sent.
        for (i = 0; i < 4; i++) {
            if (buttons & Math.pow(2, i)) {

                // Default to focus on the first visible button if focus not already set
                if (!me.defaultFocus) {
                    me.defaultFocus = me.msgButtons[i];
                }
                me.msgButtons[i].show();
                hideToolbar = false;
            } else {
                me.msgButtons[i].hide();
            }
        }

        // Hide toolbar if no buttons to show
        if (hideToolbar) {
            me.bottomTb.hide();
        } else {
            me.bottomTb.show();
        }

        Ext.resumeLayouts(true);
        me.appendCustomItems(cfg);
    },

    /**
     *
     *
     * @param cfg
     */
    appendCustomItems: function(cfg) {
        var me = this;

        // Destroy existing 'form' component if it exists within `promptContainer`
        if (me.promptContainer.down('form')) me.promptContainer.down('form').destroy();

        // Append new 'form' component into `promptContainer`, if cfg.form prop is given
        if (cfg.form) me.promptContainer.add(Ext.merge({
            xtype: 'form',
            border: 0,
            cls: 'i-prompt',
            layout: 'vbox',
            defaults: {style: {borderTopWidth: '0px !important;'}},
            bodyStyle: {
                background: 'transparent'
            }
        }, cfg.form));
    }
});
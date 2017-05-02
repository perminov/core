Ext.define('Indi.view.desktop.WindowBar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.windowbar',
    mixins: {shrink: 'Indi.util.Shrinkable'},
    baseCls: Ext.baseCSSPrefix + 'windowbar ' + Ext.baseCSSPrefix + 'shrinkbar',
    maxWindows: 15,
    enableOverflow: {
        menu: {
            defaults: {
                xtype: 'menuitem',
                minWidth: 0,
                iconCls: 'i-btn-icon-close'
            },
            cls: 'x-shrinkbar-overflow-menu x-windowbar-overflow-menu'
        },
        menuTrigger: {
            listeners: {
                mouseover: function(b){
                    b.showMenu();
                }
            },
            menuAlign: 'tr-br?'
        }
    },
    border: 0,
    margin: 0,
    padding: 0,
    defaults: {xtype: 'windowbutton'},
    minWidth: 0,
    shrinkCfg: {
        item: {
            closable: 10
        }
    },

    /**
     * Call mixin's afterRender method
     */
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Call mixin's initComponent
        me.mixins.shrink.afterRender.apply(this, arguments);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Call parent
        me.callParent();

        // Adjust shrink config
        me.shrinkCfg = Ext.merge({}, me.mixins.shrink.shrinkCfg, me.shrinkCfg);
    },

    /**
     * Adjust width before passing it to parent's setWidth method
     *
     * @return {*}
     */
    setWidth: function() {
        var me = this, fixed = me.fixWidth(arguments.length ? arguments[0] : me.width);

        // If width is given as an arg - overwrite that arg, else overwrite `width` prop
        if (arguments.length) arguments[0] = fixed; else me.width = fixed;

        // Call parent
        return me.callParent(arguments);
    },

    // @inheritdoc
    onAdd: function(item) {
        var me = this, wA = [], maximized = 0, closed = false;

        // Call parent
        me.callParent(arguments);

        // Max windows quantity restriction
        if (me.items.getCount() > me.maxWindows) {

            // First, we try to find and close non-maximized window, because if we close maximized,
            // visually (at current stage of windows-feature development) it would look like background
            // removal at the user point of view
            me.items.each(function(item){
                if (!item.window.maximized && !closed) {
                    closed = true;
                    item.window.close();
                }
            });

            // If all windows are maximized and that is why first them was not close - force first to be closed
            if (!closed) me.items.getAt(0).window.close();
        }

        // Recalc width usage
        me.calcWidthUsage();

        // Fix for cases when window-button is shown in bar, but overflow-menu item,
        // that had been representing that window-button - was not removed
        item.on('show', function(){
            if (item.up('windowbar')) {
                var oh = item.up('windowbar').layout.overflowHandler;
                var menu = oh.menu;
                Ext.defer(function(){
                    if (!oh.menuItems.length) menu.hide();
                }, 100);
            }
        });
    }
});
Ext.define('Indi.lib.toolbar.Shrinkbar', {
    extend: 'Ext.toolbar.Toolbar',
    alias: 'widget.shrinkbar',
    mixins: {shrink: 'Indi.util.Shrinkable'},
    margin: 0,
    padding: 0,
    minWidth: 23,
    enableOverflow: {
        menu: {
            defaults: {
                xtype: 'menuitem',
                minWidth: 0,
                iconCls: 'no-icon'
            },
            cls: 'x-shrinkbar-overflow-menu'
        },
        menuTrigger: {
            listeners: {
                mouseover: function(b){
                    b.showMenu();
                }
            },
            menuAlign: 'tr-br?'
        }
    },
    baseCls: Ext.baseCSSPrefix + 'shrinkbar',
    shrinkCfg: {
        item: {
            closable: 0
        }
    },

    constructor: function(config) {
        var me = this;

        if (config.shrinkCfg.prop) {
            config.items.forEach(function(item){
                item.text = item[config.shrinkCfg.prop];
            });
        }

        // Delete id
        config.items.forEach(function(item){
            delete item.id;
        });

        // Call parent
        me.callParent(arguments);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Call parent
        me.callParent();

        //
        me.shrinkCfg = Ext.merge({}, me.mixins.shrink.shrinkCfg, me.shrinkCfg);
    },

    /**
     * Adjust width before passing it to parent's setWidth method
     *
     * @return {*}
     */
    setWidth: function() {
        var me = this, fixed = me.fixWidth(arguments.length ? arguments[0] : me.width);

        // If width is given as an arg - overwrite that arg, else overwrite `width` prop
        if (arguments.length) arguments[0] = fixed; else me.width = fixed;

        // Call parent
        return me.callParent(arguments);
    },

    /**
     * Append call of mixin's same method
     */
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Call mixin's afterRender method
        me.mixins.shrink.afterRender.apply(this, arguments);
    }
});
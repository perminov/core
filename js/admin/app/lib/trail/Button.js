Ext.define('Indi.lib.trail.Button', {
    extend: 'Ext.button.Button',
    alias: 'widget.trailbutton',
    cls: 'x-windowbar-btn',
    initComponent: function() {
        var me = this;

        // Provide menu opening on button mouseover
        me.on('mouseover', function(){
            me.showMenu();
        });

        // Provide menu hiding on button mouseout,
        // but only in case if mouse is not over the menu
        me.on('mouseout', function(){
            Ext.defer(function(){
                if (me.hasVisibleMenu() && !me.menu.mouseover) me.hideMenu();
            }, 100);
        });

        // Create siblings menu
        if (me.menuItems && me.menuItems.length) {
            me.menu = {
                plain: true,
                cls: me.menuCls || 'i-trail-item-menu',
                style: me.menuStyle || 'border-top-width:0',
                margin: 0,
                padding: 0,
                border: 1,
                defaults: {
                    padding: 0,
                    margin: 0,
                    height: 15,
                    border: 0,
                    handler: function(btn) {
                        if (btn.load) Indi.load(btn.load, me.loadCfg);
                    }
                },
                items: me.menuItems,
                listeners: {
                    mouseover: function() {
                        this.mouseover = true;
                    },
                    mouseleave: function() {
                        this.mouseover = false;
                        this.hide();
                    }
                }
            }
        }

        // Return
        return me.callParent();
    }
});
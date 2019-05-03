Ext.override(Ext.toolbar.Toolbar, {

    /**
     * Get real width usage
     *
     * @return {*}
     */
    getWidthUsage: function() {
        var me = this, tbfill = me.down('tbfill');

        // Return
        return me.getWidth() - (tbfill ? tbfill.getWidth() : 0);
    }
});

Ext.override(Ext.toolbar.TextItem, {

    /**
     * `text` prop was NOT updated if me.rendered==true, and this caused overflow menu item problem
     */
    setText : function(text) {
        var me = this;
        this.text = text;
        if (me.rendered) {
            me.el.update(text);
            me.updateLayout();
        }
    }
});

Ext.override(Ext.layout.container.boxOverflow.Menu, {

    /**
     * @private
     * Adds the given Toolbar item to the given menu. Buttons inside a buttongroup are added individually.
     * @param {Ext.menu.Menu} menu The menu to add to
     * @param {Ext.Component} component The component to add
     * TODO: Implement overrides in Ext.layout.container.boxOverflow which create overrides
     * for SplitButton, Button, ButtonGroup, and TextField. And a generic one for Component
     * which create clones suitable for use in an overflow menu.
     */
    addComponentToMenu : function(menu, component) {
        var me = this,
            i, items, iLen, overflowCfg = {};
        if ('overflowCfg' in component) {
            if (component.overflowCfg === false) return;
            if (component.overflowCfg) overflowCfg = component.overflowCfg;
            if ('icon' in overflowCfg || 'iconCls' in overflowCfg) menu.body.addCls('i-icon');
        }
        if (component instanceof Ext.toolbar.Separator) {
            menu.add('-');
        } else if (component.isComponent) {
            if (component.isXType('splitbutton')) {
                menu.add(Ext.merge(me.createMenuConfig(component, true), overflowCfg));

            } else if (component.isXType('button')) {
                menu.add(Ext.merge(me.createMenuConfig(component, !component.menu), overflowCfg));

            } else if (component.isXType('buttongroup')) {
                items = component.items.items;
                iLen  = items.length;

                for (i = 0; i < iLen; i++) {
                    me.addComponentToMenu(menu, items[i]);
                }
            } else {
                menu.add(Ext.create(Ext.getClassName(component), Ext.merge(me.createMenuConfig(component), overflowCfg)));
            }
        }
    }
});
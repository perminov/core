/**
 * Here we override `_activateLast` method.
 * It's source code is almost the same, and the only one difference is in line 32:
 *
 * was: "if (comp.isVisible() && comp.modal) {"
 * now: "if (comp && comp.isVisible() && comp.modal) {"
 *
 * Such a workaround was implemented to avoid error from being thrown
 */
Ext.override(Ext.ZIndexManager, {
    _activateLast: function() {
        var me = this,
            stack = me.zIndexStack,
            i = stack.length - 1,
            oldFront = me.front,
            comp;

        // There may be no visible floater to activate
        me.front = undefined;

        // Go down through the z-index stack.
        // Activate the next visible one down.
        // If that was modal, then we're done
        for (; i >= 0 && stack[i].hidden; --i);
        if ((comp = stack[i])) {
            me._setActiveChild(comp, oldFront);
            if (comp.modal) {
                return;
            }
        }

        // If the new top one was not modal, keep going down to find the next visible
        // modal one to shift the modal mask down under
        for (; i >= 0; --i) {
            comp = stack[i];
            // If we find a visible modal further down the zIndex stack, move the mask to just under it.
            if (comp && comp.isVisible() && comp.modal) {
                me._showModalMask(comp);
                return;
            }
        }

        // No visible modal Component was found in the run down the stack.
        // So hide the modal mask
        me._hideModalMask();
    }
});

/**
 * Added ability to configure overflow menu, by passing object-value as an enableOverflow сап param, instead of bool-value
 */
Ext.override(Ext.layout.container.boxOverflow.Menu, {

    // We don't define a prefix in menu overflow.
    getSuffixConfig: function() {
        var me = this,
            layout = me.layout,
            oid = layout.owner.id,
            menuCfg = Ext.isSimpleObject(layout.owner.enableOverflow) ? layout.owner.enableOverflow : {};

        /**
         * @private
         * @property {Ext.menu.Menu} menu
         * The expand menu - holds items for every item that cannot be shown
         * because the container is currently not large enough.
         */
        me.menu = new Ext.menu.Menu(Ext.merge({
            listeners: {
                scope: me,
                beforeshow: me.beforeMenuShow
            }
        }, menuCfg));

        /**
         * @private
         * @property {Ext.button.Button} menuTrigger
         * The expand button which triggers the overflow menu to be shown
         */
        me.menuTrigger = new Ext.button.Button({
            id      : oid + '-menu-trigger',
            cls     : Ext.layout.container.Box.prototype.innerCls + ' ' + me.triggerButtonCls,
            hidden  : true,
            ownerCt : layout.owner, // To enable the Menu to ascertain a valid zIndexManager owner in the same tree
            ownerLayout: layout,
            iconCls : Ext.baseCSSPrefix + me.getOwnerType(layout.owner) + '-more-icon',
            ui      : layout.owner instanceof Ext.toolbar.Toolbar ? 'default-toolbar' : 'default',
            menu    : me.menu,
            getSplitCls: function() { return '';}
        });

        return me.menuTrigger.getRenderTree();
    }
});
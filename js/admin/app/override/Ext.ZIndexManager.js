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
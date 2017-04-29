Ext.define('Indi.util.Shrinkable', {

    /**
     * Width calculation config
     */
    shrinkCfg: {
        prop: 'text',
        item: {
            border: 1,
            closable: 0,
            inner: {
                padding: {right: 4, left: 4}
            },
            margin: {right: 0, left: 0},
            firstChar: 9,
            ellipsis: 10
        }
    },

    /**
     * Bind event listeners, that will recalculate possible width and refresh it if need
     */
    afterRender: function() {
        var me = this;

        // Once box is ready - calc width usage and fit items
        me.on('boxready', function(c){
            c.calcWidthUsage();
            c.setWidth(me.getMaxWidth());
        }, me);

        // Refresh fit each time new item was added
        me.on('add', function(c){
            if (c.calcWidthUsage) c.calcWidthUsage();
            c.setWidth(me.getMaxWidth());
        });

        // Refresh fit each time new item was added
        me.on('remove', function(c){
            if (c.calcWidthUsage) c.calcWidthUsage();
            c.setWidth(me.getMaxWidth());
        });

        // Refresh fit each parent container width changed
        me.ownerCt.on('resize', function(){
            me.setWidth(me.getMaxWidth());
        });
    },

    /**
     * Fix width for it to be exact:
     *
     * 1. avoid white space between non-hidden items and overflow-menu trigger button)
     * 2. display only overflow-menu trigger button, if desired width does not allow to fit any actual item
     * 3. prevent width to be greater than actual usage
     *
     * @param width
     * @return {*}
     */
    fixWidth: function(width) {
        var me = this, wu = me.widthUsage, iwu = 0, fixed, exceed = false, onlyTrigger = true;

        // If width, that is being attempted to setup - is greater or equal than actual/required width usage - return it
        if (width >= wu || !wu) return wu;

        // Get total width usage of inner items
        me.items.each(function(item){ iwu += item.widthUsage; });

        // Setup fixed width as a width of overflow-menu trigger button, initially
        fixed = me.layout.overflowHandler.menuTrigger.getWidth() || 21;

        // Append difference between outer width usage and inner width usage to the fixed width
        fixed += wu - iwu;

        // Walk through items
        me.items.each(function(item){

            // If we haven't yet exceeded the desired width, and we won't exceed it
            // by increasing it with next item's width usage
            if (!exceed && (fixed + item.widthUsage <= width)) {

                // Increase fixed with with current item's width usage
                fixed += item.widthUsage;

                // Turn off `onlyTrigger` flag
                onlyTrigger = false;

            // Turn on `exceed` flag
            } else exceed = true;
        });

        // Return
        return fixed + (onlyTrigger ? 1 : 0);
    },

    /**
     * Calc width usage for all inner components
     */
    calcWidthUsage: function() {
        var me = this;
        me.widthUsage = 0;
        if (!me.items.getCount()) return;
        me.items.each(function(b, i) {
            b.widthUsage = Indi.metrics.getWidth(b[me.shrinkCfg.prop])
                + me.shrinkCfg.item.inner.padding.right
                + me.shrinkCfg.item.inner.padding.left
                + me.shrinkCfg.item.closable
                + (i ? 1 : 2);
            me.widthUsage += b.widthUsage;
        });
        me.widthUsage += parseInt(me.getEl().getStyle('border-right-width'));
        me.widthUsage += parseInt(me.getEl().getStyle('border-left-width'));
    },

    /**
     * Determine the width that is available for current component instance within it's parent container,
     * that can be a toolbar, for example
     *
     * @return {Number}
     */
    getMaxWidth: function() {

        // Declare auxilliary variables
        var me = this, parent = me.isContained || me.ownerCt, item, self, next, tbfill,
            base = parent.getWidth() - parent.getEl().getPadding('lr') - (me.lastBox ? me.lastBox.x: 0), last, avail;

        // Walk through siblings of current shrinklist's parent container,
        // and try to find one that is rendered next to shrinklist
        for (var i = 0; i < parent.items.getCount(); i++) {

            // Get current item
            item = parent.items.getAt(i);

            // Try to get last
            if (next && (i == parent.items.getCount() - 1)) last = item;

            // If iteration have already went through current shrinkable-component, and iteration's sibling
            // is not a 'tbfill' item, and it's y-offset is the same as current shrinklist have
            if (!next && self && item.xtype != 'tbfill' && item.lastBox)
                next = item;

            // Setup `self` flag, that mean that current sibling is the current shrinklist itself,
            // so this will mean that one of the remaining siblings can be the the one that we can
            // use for further x-offsets calculation
            if (item.id == me.id) self = true;

            // If iteration have already went through current shrinklist itself, but proper next sibling
            // (proper mean has the same y-offset as shrinklist, and is not a 'tbfill' item) is not yet
            // found not, and current iteration's sibling is a 'tbfill' item - remember that
            if (self && item.xtype == 'tbfill' && !next) tbfill = Ext.getCmp(item.id + '');
        }

        // If last sibling is not the sibling that is next to current shrinkable component
        if (last) {
            avail = base - (last.lastBox.x + last.lastBox.width - next.lastBox.x);

        // Else if sibling, next to current shrinkable component - is the last sibling
        } else if (next) {
            avail = base
                - next.getWidth() - next.getEl().getMargin('lr') - 2
                - me.getEl().getMargin('lr') - (tbfill ? 9 : 0);

        // Else if current shrinkable component - is the last within owner component
        } else avail = base;

        // Set maxWidth
        me.maxWidth = avail;

        // Return
        return me.maxWidth;
    }
});
Ext.define('Indi.util.Shrinkable', {

    shrinkCfg: {
        root: 'items',
        prop: 'text',
        last: 'border',
        item: {
            border: 1,
            closable: 10,
            inner: {
                padding: {right: 4, left: 4}
            },
            margin: {right: 0, left: 0},
            firstChar: 9,
            ellipsis: 10
        }
    },

    layout: {
        type: 'auto'
    },
    maximized: false,
    minimized: false,

    initComponent: function() {
        var me = this;

        // Ensure that each time shrinkable container's width was changed  - this will lead to removal
        // of all css inline width definitions for all child items of shrinkable container. But, hovewer
        // css inline width definition for each child's root element won't be removed


        // Ensure that each time child item is rendered/removed - minWidth should be updated
        me.on('add', function(shrinkable, item) {
            item.on({
                scope: me,
                afterrender: me.updateMinWidth,
                removed: me.updateMinWidth,
                mouseover: me.onItemMouseOver
            });
        });

        me.on('render', function(){
            Ext.getCmp('i-center-north').on('resize', function(){
                me.adjustWidth();
            });
            me.getEl().on('mouseleave', me.onMouseLeave, me);
        });

        me.on('afterlayout', function(){
            me.adjustWidth();
        });
    },

    /**
     * Determine the minimum width, what the whole box can have
     *
     * @return {Number}
     */
    getMinWidth: function() {
        var me = this;

        // Calc min width
        return me.getItemMinWidth() * me.items.length;// + me.shrinkCfg.item[me.shrinkCfg.last];
    },

    /**
     * Determine the minimum width, that any item can have
     *
     * @return {Number}
     */
    getItemMinWidth: function() {
        var me = this;

        // Calc min item width
        return me.shrinkCfg.item.margin.left + me.shrinkCfg.item.border + me.shrinkCfg.item.inner.padding.left
            + me.shrinkCfg.item.firstChar + me.shrinkCfg.item.ellipsis + me.shrinkCfg.item.inner.padding.right
            + me.shrinkCfg.item.closable + me.shrinkCfg.item.margin.right;
    },

    /**
     * Determine the maximum width, that certain item can have
     *
     * @param {Object} item
     * @return {Number}
     */
    getItemMaxWidth: function(shrinkPropVal) {
        var me = this;

        // Calc max item width
        return me.getItemMinWidth() - me.shrinkCfg.item.firstChar - me.shrinkCfg.item.ellipsis
            + Indi.metrics.getWidth(shrinkPropVal);
    },

    /**
     * Determine the width that is required for all items to be displayed with no shrinking
     *
     * @return {Number}
     */
    getMaxWidth: function(){
        var me = this, root = me[me.shrinkCfg.root], i,
            innerA = root[Ext.isFunction(root.collect) ? 'collect' : 'column'](me.shrinkCfg.prop), maxWidth = 0;

        // Sum all items max widths
        for (i = 0; i < innerA.length; i++) maxWidth += me.getItemMaxWidth(innerA[i]);

        // Append last
        maxWidth +=  me.shrinkCfg.item[me.shrinkCfg.last];

        // Return
        return maxWidth;
    },

    /**
     * Determine the width that is available for current component instance within it's parent container,
     * that can be a toolbar, for example
     *
     * @return {Number}
     */
    getAvailableWidth: function() {

        // Declare auxilliary variables
        var me = this, parent = me.isContained || me.ownerCt, item, self, next, selfMarginRight, nextMarginLeft, tbfill;

        // Walk through siblings of current shrinklist's parent container,
        // and try to find one that is rendered next to shrinklist
        for (var i = 0; i < parent.items.getCount(); i++) {
            item = parent.items.getAt(i);

            // If iteration have already went through current shrinklist, and iteration's sibling
            // is not a 'tbfill' item, and it's y-offset is the same as current shrinklist have
            if (self && item.xtype != 'tbfill' && item.lastBox && item.lastBox.y == me.lastBox.y) {

                // Assume that current item is a proper next item, and stop iteration
                next = item;
                break;
            }

            // Setup `self` flag, that mean that current sibling is the current shrinklist itself,
            // so this will mean that one of the remaining siblings can be the the one that we can
            // use for further x-offsets calculation
            if (item.id == me.id) self = true;

            // If iteration have already went through current shrinklist itself, but proper next sibling
            // (proper mean has the same y-offset as shrinklist, and is not a 'tbfill' item) is not yet
            // found not, and current iteration's sibling is a 'tbfill' item - remember that
            if (self && item.xtype == 'tbfill' && !next) tbfill = Ext.getCmp(item.id + '');
        }

        // Get shrinklist right margin as integer
        selfMarginRight = parseInt(me.getEl().getStyle('margin-right'));

        // If proper next item for found after current shrinklist within it's parent container items
        if (next) {

            // Get next item's left margin as integer
            nextMarginLeft = parseInt(next.getEl().getStyle('margin-left'));

            // Calc and return width, available for current shrinklist. Here we just take a difference
            // between offsets of current shrinklist and it's next proper sibling, and, also, here we
            // take in attention possible possible margins and possible existence of 'tbfill' item -
            // between shrinklist and proper next item. Actually tbfill-item, if found - is a really
            // next item, but it doesn't suit for our aim, so here we can't call it 'proper'
            return next.lastBox.x - me.lastBox.x - selfMarginRight - nextMarginLeft - (tbfill ? 9 : 0);

            // If proper next item for not found, this mean that there is nothing at the right-hand-side
            // of current shrinklist, so we involve it's parent container width in calculations
        } else return parent.getWidth() - me.lastBox.x - 0 - selfMarginRight;
    },

    /**
     * Main width-adjusting function. Accept 'hover' argument, as an index of item, that
     * currently is hovered, so it should have full width, by decresing widths of other items
     *
     * @param hover
     */
    adjustWidth: function(hover){

        // Setup auxiliary variables
        var me = this, easing = !!(arguments.length > 0), exclusive = undefined,
            availableWidth = me.getAvailableWidth(), requiredWidth = me.getMaxWidth(),
            constant = 0, ignoreA = [], avgItemWidth = 0, itemTitleRequiredWidth, lost, itemWidth,
            itemTitleWidth, noTitle, noDots, hoverItemTitleRequiredWidth, hoverOverflow = 0;

            me.clearInlineWidth();

        // Setup 'constant' variable for collecting width, that should not be involved
        // in the process of width adjustment amount calculation
        constant = (me.getItemMinWidth() - me.shrinkCfg.item.firstChar - me.shrinkCfg.item.ellipsis)
            * me.items.getCount();

        // If some item is currently hovered
        if (hover != undefined) {

            // Push index of hovered item into ignoreA array
            ignoreA.push(hover);

            // Get hover item width
            hoverItemTitleRequiredWidth = Indi.metrics.getWidth(me.items.getAt(hover).text);

            // Increment value of 'constant' variable by hovered item title text width
            constant += hoverItemTitleRequiredWidth;

            // If hover item width is too large
            if (hoverItemTitleRequiredWidth > availableWidth - me.items.getCount() * 15) {
                hoverOverflow = hoverItemTitleRequiredWidth - (availableWidth - me.items.getCount() * 15);
                constant -= hoverOverflow;
            }
        }

        // Get initial average width of items
        avgItemWidth = Math.floor((availableWidth - constant)/(me.items.getCount() - ignoreA.length));

        // Run average width calculation twice, as there may be case when some items have width
        // smaller than initial average width, but the difference between initial average width
        // and such item width is - was not used for increase of 'constant' variable value, and
        // therefore not involved at the moment of initial calculation of 'avgItemWidth' value
        for (var j = 0; j < 2; j++) {

            // For each non-hovered item
            me.items.each(function(item, index) {
                if (index != hover) {

                    // If that width is smaller than average width
                    //itemTitleRequiredWidth = el.first('.' + me.itemCls + '-title').getTextWidth();
                    itemTitleRequiredWidth = Indi.metrics.getWidth(item.text);

                    // If that width is smaller than average width
                    if (itemTitleRequiredWidth <= avgItemWidth && ignoreA.indexOf(index) == -1) {

                        // Push that item's index into 'ignoreA' array for being sure that item's with
                        // won't be adjusted, because there is no need for that item
                        ignoreA.push(index);

                        // Increase 'constant' variable by the value of 'itemTitleRequiredWidth' variable,
                        // as widths of items, that are smaller than average width - should not be involved
                        // in the process of width adjustment amount calculation
                        constant += itemTitleRequiredWidth;
                    }

                }
            });

            // Get adjusted average width of items
            avgItemWidth = Math.floor((availableWidth - constant)/(me.items.getCount() - ignoreA.length));
        }

        // Get lost value. We need this because there may be undistributed number of pixels, that
        // we need to redistribute to items by a one-lost-pixel-to-one-item logic. This will provide
        // a visual impression that all items have dimensions that allow them to have exact fit within
        // the component, despite on mathematically it's impossible
        lost = (availableWidth - constant) - avgItemWidth * (me.items.getCount() - ignoreA.length);

        // For each item
        me.items.each(function(item, index) {

            // If item should be ignored
            if (ignoreA.indexOf(index) != -1) {

                // We restore it's width state to full required width
                itemWidth = me.getItemMaxWidth(item.text);
                item.removeCls('i-btn-ellipsis');
                if (index === hover) {
                    itemWidth -= hoverOverflow;
                    if (hoverOverflow) item.addCls('i-btn-ellipsis');
                }
                item.getEl().setWidth(itemWidth, easing);
                item.closeEl.setStyle('visibility', null);
                item.btnInnerEl.setStyle('visibility', null);

            // Else if item's width should be adjusted
            } else {

                // Get item title width and item width
                itemTitleWidth = avgItemWidth + me.shrinkCfg.item.border;
                itemWidth = itemTitleWidth + me.shrinkCfg.item.ellipsis + me.shrinkCfg.item.inner.padding.right * 2 + (lost > 0 ? 1 : 0);

                // Decrease lost
                lost--;

                // Reset 'noTitle' variable to 'false'
                noTitle = false;

                item.getEl().setWidth(itemWidth, easing);
                if (itemWidth < me.getItemMinWidth()) {
                    item.removeCls('i-btn-ellipsis');
                    item.closeEl.setStyle('visibility', 'hidden');
                    item.btnInnerEl.setStyle('visibility', avgItemWidth < me.shrinkCfg.item.firstChar ? 'hidden' : null);
                } else {
                    item.addCls('i-btn-ellipsis');
                    item.closeEl.setStyle('visibility', null);
                    item.btnInnerEl.setStyle('visibility', null);
                }
            }
            item.getEl().down('button').setStyle('width', null);
            item.getEl().down('.x-btn-inner').setStyle('width', null);
        });

        me.clearInlineWidth();
    },

    /**
     * @inheritdoc
     * Also, additionally imlemented not-so-often check for mouse-entered item shrinking
     *
     * @param row
     * @param dom
     * @param index
     * @param e
     */
    onItemMouseOver: function(item, e) {
        var me = this;

        // Provide not-so-ofter check before shrinking
        clearTimeout(me.lastHoverTimeout);
        me.lastHoverTimeout = setTimeout(function(me){
            me.adjustWidth(me.items.indexOf(item));
        }, 200, me);
    },

    /**
     * Function is for usage as a handler current component element's `mouseleave` event
     */
    onMouseLeave: function(){
        var me = this;

        // Provide not-so-ofter check before shrinking
        clearTimeout(me.lastHoverTimeout);
        me.lastHoverTimeout = setTimeout(function(){
            me.adjustWidth(null);
        }, 100);
    },

    clearInlineWidth: function() {
        this.items.each(function(item){
            item.getEl().setWidth(item.getWidth());
            item.getEl().down('button').setStyle('width', null);
            item.getEl().down('.x-btn-inner').setStyle('width', null);
        });
    },

    updateMinWidth: function() {
        var me = this; me.minWidth = me.getMinWidth();
    }
});
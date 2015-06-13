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
        me.on('resize', function() {
            me.clearInlineWidth();
            me.adjustWidth();
            me.clearInlineWidth();
        });

        // Ensure that each time child item is rendered/removed - minWidth should be updated
        me.on('add', function(shrinkable, item) {
            item.on({
                scope: me,
                afterrender: me.updateMinWidth,
                removed: me.updateMinWidth
            });
        });

        me.on('render', function(){
            Ext.getCmp('i-center-north-trail-panel').on('resize', function(){
                me.adjustWidth();
                console.log('resize');
                //me.maxWidth = me.getAvailableWidth();
            });
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
        var me = this, easing = !!(arguments.length > 0), exclusive = undefined, totalItemsWidth = 0,
            availableWidth = me.getAvailableWidth(), requiredWidth = me.getMaxWidth(),
            constant = 0, ignoreA = [], avgItemWidth = 0, itemTitleRequiredWidth, lost, itemWidth,
            itemTitleWidth, noTitle, noDots, deferedHover = false, tmp = 0;

        me.itemCls = 'x-windowbar-btn';

        // If we have sufficient width for all items with no need to minify
        if (requiredWidth <= availableWidth) {me.maximize();}

        // Else if we do not have sufficient width, but available width is larger
        // than minimum width or it's even smaller, but some item is currently hovered
        else if (availableWidth > me.minWidth || hover != undefined) {

            // If available width is even smaller than minimum width and some item is currently hovered
            if (hover != undefined && !(availableWidth > me.minWidth)) {

                // Increase availableWidth for it to be at least the same as minimum width
                availableWidth = me.minWidth;

                // Setup 'exclusive' variable as true, because situation, that makes possible that setup
                // assumes that there will be no sufficient width for items dots to be displayed, as
                // available width is already equal to minimum width (which consider that item inner width
                // consists from width of item title first character width plus dots width), but the fact
                // that we have some item hovered mean that we have to get additional width for being able
                // to set full required width for that hovered item, and the first possible way to get
                // that additional width - is to get it through sum of other items dots widths at least
                exclusive = hover;
            }

            // Setup 'constant' variable for collecting width, that should not be involved
            // in the process of width adjustment amount calculation
            constant = (me.getItemMinWidth() - me.shrinkCfg.item.firstChar - me.shrinkCfg.item.ellipsis)
                * me.items.getCount();

            // If some item is currently hovered
            if (hover != undefined) {

                // Push index of hovered item into ignoreA array
                ignoreA.push(hover);

                // Increment value of 'constant' variable by hovered item title text width
                // constant += me.getEl().select('.' + me.itemCls).item(hover).first('.' + me.itemCls+'-title').getTextWidth();
                constant += Indi.metrics.getWidth(me.items.getAt(hover).text);
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

                        // Get width, required for current item's title
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
                    /*itemTitleWidth = el.first('.' + me.itemCls + '-title').getTextWidth();
                    itemWidth = itemTitleWidth + me.shrinkCfg.item.inner.padding.right * 2;
                    el.first('.' + me.itemCls + '-dots').setStyle('display', 'none');
                    el.first('.' + me.itemCls + '-title').setWidth(itemTitleWidth, easing);
                    el.setWidth(itemWidth, easing);*/
                    itemWidth = me.getItemMaxWidth(item.text);
                    item.removeCls('i-btn-ellipsis');
                    item.getEl().setWidth(itemWidth);
                    item.getEl().down('button').setStyle('width', null);
                    item.getEl().down('.x-btn-inner').setStyle('width', null);

                    // Else if item's width should be adjusted
                } else {

                    // Get item title width and item width
                    itemTitleWidth = avgItemWidth + me.shrinkCfg.item.border;// - me.shrinkCfg.item.ellipsis;
                    itemWidth = itemTitleWidth + me.shrinkCfg.item.ellipsis + me.shrinkCfg.item.inner.padding.right * 2 + (lost > 0 ? 1 : 0);

                    // Decrease lost
                    lost--;

                    // Reset 'noTitle' variable to 'false'
                    noTitle = false;

                    // If item title width is smaller than first character width
                    /*if (itemTitleWidth < me.shrinkCfg.item.firstChar) {

                        // Check if item title width will be not smaller in case if we won't display dots
                        // and therefore we can use dot's width for item title purpose instead of dots
                        // purpose. So if check is successful
                        if (itemTitleWidth + me.shrinkCfg.item.ellipsis >= me.shrinkCfg.item.firstChar) {

                            // Setup 'noDots' flag to 'true' to provide an
                            // signal for dots to be not displayed
                            noDots = true;

                            // Increase item title width for it to be at least
                            // the same as item title first char width
                            itemTitleWidth = me.shrinkCfg.item.firstChar;

                            // Else if even after dots width use for title purposes we have no effect -
                            // setup 'noTitle' flag as a signal for item title to be not displayed at all
                        } else noTitle = true;
                    }
                    console.log('noDots', noDots);*/

                    // Setup 'display' css property for current item's dots node
                    //el.first('.' + me.itemCls + '-dots').setStyle('display', noDots ? 'none' : 'inline-block');
                    item[noDots ? 'removeCls' : 'addCls']('i-btn-ellipsis');

                    // Apply width for current item's title
                    //el.first('.' + me.itemCls + '-title').setWidth(itemTitleWidth, easing);

                    // Apply width for whole current item
                    //el.setWidth(itemWidth, easing ? {callback: function(item){
                    /*item.setWidth(itemWidth, easing ? {callback: function(item){
                        //item.target.target.first('.' + me.itemCls + '-dots').setStyle('visibility', exclusive ? 'hidden' : 'visible');
                        //item.target.target.first('.' + me.itemCls + '-title').setStyle('visibility', noTitle ? 'hidden' : 'visible');
                        item.down('.x-btn-inner').setStyle('visibility', noTitle ? 'hidden' : 'visible');
                    }}: false);*/

                    if (hover === undefined) {
                        item.getEl().setWidth(itemWidth);
                        item.getEl().down('button').setStyle('width', null);
                        item.getEl().down('.x-btn-inner').setStyle('width', null);
                    } else if (index === hover) {
                        deferedHover = {
                            index: hover,
                            width: itemWidth + 0
                        };
                    } else {
                        item.getEl().setWidth(itemWidth, easing);
                        item.getEl().down('button').setStyle('width', null);
                        item.getEl().down('.x-btn-inner').setStyle('width', null);

                        if (me.items.getCount() - 1 === index && Ext.isObject(deferedHover)) {
                            me.items.getAt(deferedHover.index).getEl().down('button').setStyle('width', null);
                            me.items.getAt(deferedHover.index).getEl().down('.x-btn-inner').setStyle('width', null);
                        }
                    }

                    //me.ellipsis(item, itemWidth);
                }

                // Increase value of 'totalItemsWidth' variable by current item width
                //totalItemsWidth += itemWidth + me.calc.item.marginLeft;
                //totalItemsWidth += itemWidth;// + me.shrinkCfg.item.margin.left;
                //console.log(index, itemWidth);
            });

            // If no item is currently hovered
            if (hover === undefined) {

                // Apply width for the whole component
                //console.log(totalItemsWidth);
                //me.getEl().setWidth(totalItemsWidth + me.shrinkCfg.item.border);
                //me.setWidth(totalItemsWidth + me.shrinkCfg.item.border + me.shrinkCfg.item.inner.padding.right);
            }

        // Else if available width is smaller than minimum width, and no item is currently hovered
        } else {
            me.minimize();
        }
    },

    maximize: function() {
        var me = this;

        // Setup maximum-width style for items, and for the whole component
        me.items.each(function(item, i, len){
            if (item.getWidth() < me.getItemMaxWidth(item.text)) {
                item.getEl().animate({
                    to: {width: me.getItemMaxWidth(item.text)},
                    callback: function() {
                        me.items.getAt(i).removeCls('i-btn-ellipsis');
                        if (i == len - 1) {
                            me.items.each(function(item, j) {
                                me.items.getAt(j).getEl().setWidth(me.getItemMaxWidth(item.text));
                            });
                            me.minimized = false;
                            me.maximized = true;
                        }
                    }
                });
            }
        });
    },

    minimize: function() {
        var me = this;

        // Setup minimum-width style for items, and for the whole component
        me.items.each(function(item, i, len) {
            item.addCls('i-btn-ellipsis');
            item.getEl().animate({
                to: {width: me.getItemMinWidth()},
                callback: function() {
                    if (i == len - 1) {
                        me.items.each(function(item, j) {
                            me.items.getAt(j).getEl().setWidth(me.getItemMinWidth());
                        });
                        me.maximized = false;
                        me.minimized = true;
                    }
                }
            });
        });
    },

    ellipsis: function(item, width) {
        item.addCls('i-btn-ellipsis');
        item.getEl().animate({
            to: {width: width},
            callback: function() {
                //if (i == len - 1) {
                    //me.items.each(function(item, j) {
                        //me.items.getAt(j).getEl().setWidth(me.getItemMinWidth());
                        item.getEl().setWidth(width);
                    //});
                //}
            }
        });
    },

    tmp: function() {
        this[this.minimized ? 'maximize' : 'minimize']();
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
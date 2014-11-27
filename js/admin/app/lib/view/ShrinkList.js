/**
 * ShrinkList - is a horizontal BoundList, and it displays all items in single line,
 * with ability to shrink/unshrink each item and the whole box, depending on parent
 * container width, resizing and items hover
 */
Ext.define('Indi.lib.view.ShrinkList', {
    extend: 'Ext.view.BoundList',
    alternateClassName: 'Indi.view.ShrinkList',
    alias: 'widget.shrinklist',
    height: 19,
    baseCls: Ext.baseCSSPrefix + 'shrinklist',
    itemCls: Ext.baseCSSPrefix + 'shrinklist-item',

    /**
     * Object containing params for proper sizing calculation operations
     */
    calc: {
        border: 2,
        paddingRight: 1,
        item: {
            dots: 8,
            padding: 6,
            marginLeft: 1,
            firstChar: 7
        }
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;
        me.callParent();
        me.minWidth = me.getMinWidth();
        me.width = me.getMinWidth();
    },

    /**
     * Determine the minimum width, what any item can have
     *
     * @return {Number}
     */
    getMinItemWidth: function(){
        return this.calc.item.dots + this.calc.item.padding + this.calc.item.marginLeft + this.calc.item.firstChar;
    },


    /**
     * Determine the minimum width, what the whole box can have
     *
     * @return {Number}
     */
    getMinWidth: function(){
        return this.calc.border + this.calc.paddingRight + this.getMinItemWidth() * this.store.getCount();
    },

    /**
     * Determine the width that is required for all items to be displayed with no shrinking
     *
     * @return {Number}
     */
    getRequiredWidth: function(){
        var me = this, requiredWidth = me.calc.border + me.calc.paddingRight;

        for (var i = 0; i < me.store.getCount(); i++)
            requiredWidth += Indi.metrics.getWidth(me.store.getAt(i).get('title'))
                + me.calc.item.marginLeft + me.calc.item.padding;

        return requiredWidth;
    },

    /**
     * Determine the width that is available for current component instance within it's parent container,
     * that can be a toolbar, for example
     *
     * @return {Number}
     */
    getAvailableWidth: function() {

        // Declare auxilliary variables
        var me = this, parent = me.isContained, item, self, next, selfMarginRight, nextMarginLeft, tbfill;

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
        } else return parent.getWidth() - me.lastBox.x - 5 - selfMarginRight;
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
            availableWidth = me.getAvailableWidth(), requiredWidth = me.getRequiredWidth(),
            constant = 0, ignoreA = [], avgItemWidth = 0, itemTitleRequiredWidth, lost, itemWidth,
            itemTitleWidth, noTitle, noDots;

        // Restore visibility
        me.getEl().select('.' + me.itemCls +' > *').setStyle('visibility', 'visible');

        // If we have sufficient width for all items with no need to minify
        if (requiredWidth <= availableWidth) {

            // Setup full required width for the component, and appropriate inner styles
            me.setWidth(requiredWidth);
            me.getEl().select('.' + me.itemCls).setStyle('width', 'auto');
            me.getEl().select('.' + me.itemCls + '-dots').setStyle('display', 'none');
            me.getEl().select('.' + me.itemCls + '-title').setStyle('width', 'auto');

        // Else if we do not have sufficient width, but available width is larger
        // than minimum width or it's even smaller, but some item is currently hovered
        } else if (availableWidth > me.minWidth || hover != undefined) {

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
            constant = me.calc.border + me.calc.paddingRight
                + (me.calc.item.marginLeft + me.calc.item.padding) * me.store.getCount();

            // If some item is currently hovered
            if (hover != undefined) {

                // Push index of hovered item into ignoreA array
                ignoreA.push(hover);

                // Increment value of 'constant' variable by hovered item title text width
                constant += me.getEl().select('.' + me.itemCls).item(hover)
                    .first('.' + me.itemCls+'-title').getTextWidth();
            }

            // Get initial average width of items
            avgItemWidth = Math.floor((availableWidth - constant)/(me.store.getCount() - ignoreA.length));

            // Run average width calculation twice, as there may be case when some items have width
            // smaller than initial average width, but the difference between initial average width
            // and such item width is - was not used for increase of 'constant' variable value, and
            // therefore not involved at the moment of initial calculation of 'avgItemWidth' value
            for (var j = 0; j < 2; j++) {

                // For each non-hovered item
                me.getEl().select('.' + me.itemCls).each(function(el, c, index) {
                    if (index != hover) {

                        // Get width, required for current item's title
                        itemTitleRequiredWidth = el.first('.' + me.itemCls + '-title').getTextWidth();

                        // If that width is smaller than average width
                        if (itemTitleRequiredWidth <= avgItemWidth && ignoreA.indexOf(index) == -1) {

                            // Push that item's index into 'ignoreA' array for being sure that item's with
                            // won't be adjusted, because there is no need for that item
                            ignoreA.push(index);

                            // Increase 'constant' variable by the value of 'itemTitleRequiredWidth' variable,
                            // as widths of items, that are smaller thatn average width - should not be involved
                            // in the process of width adjustment amount calculation
                            constant += itemTitleRequiredWidth;
                        }
                    }
                });

                // Get adjusted average width of items
                avgItemWidth = Math.floor((availableWidth - constant)/(me.store.getCount() - ignoreA.length));
            }

            // Get lost value. We need this because there may be undistributed number of pixels, that
            // we need to redistribute to items by a one-lost-pixel-to-one-item logic. This will provide
            // a visual impression that all items have dimensions that allow them to have exact fit within
            // the component, despite on mathematically it's impossible
            lost = (availableWidth - constant) - avgItemWidth * (me.store.getCount() - ignoreA.length);

            // For each item
            me.getEl().select('.' + me.itemCls).each(function(el, c, index) {

                // If item should be ignored
                if (ignoreA.indexOf(index) != -1) {

                    // We restore it's width state to full required width
                    itemTitleWidth = el.first('.' + me.itemCls + '-title').getTextWidth();
                    itemWidth = itemTitleWidth + me.calc.item.padding;
                    el.first('.' + me.itemCls + '-dots').setStyle('display', 'none');
                    el.first('.' + me.itemCls + '-title').setWidth(itemTitleWidth, easing);
                    el.setWidth(itemWidth, easing);

                // Else if item's width should be adjusted
                } else {

                    // Get item title width and item width
                    itemTitleWidth = avgItemWidth - me.calc.item.dots;
                    itemWidth = itemTitleWidth + me.calc.item.dots + me.calc.item.padding + (lost > 0 ? 1 : 0);

                    // Decrease lost
                    lost--;

                    // Reset 'noTitle' variable to 'false'
                    noTitle = false;

                    // If item title width is smaller than first character width
                    if (itemTitleWidth < me.calc.item.firstChar) {

                        // Check if item title width will be not smaller in case if we won't display dots
                        // and therefore we can use dot's width for item title purpose instead of dots
                        // purpose. So if check is successful
                        if (itemTitleWidth + me.calc.item.dots >= me.calc.item.firstChar) {

                            // Setup 'noDots' flag to 'true' to provide an
                            // signal for dots to be not displayed
                            noDots = true;

                            // Increase item title width for it to be at least
                            // the same as item title first char width
                            itemTitleWidth = me.calc.item.firstChar;

                            // Else if even after dots width use for title purposes we have no effect -
                            // setup 'noTitle' flag as a signal for item title to be not displayed at all
                        } else noTitle = true;
                    }

                    // Setup 'display' css property for current item's dots node
                    el.first('.' + me.itemCls + '-dots').setStyle('display', noDots ? 'none' : 'inline-block');

                    // Apply width for current item's title
                    el.first('.' + me.itemCls + '-title').setWidth(itemTitleWidth, easing);

                    // Apply width for whole current item
                    el.setWidth(itemWidth, easing ? {callback: function(item){
                        item.target.target.first('.' + me.itemCls + '-dots').setStyle('visibility', exclusive ? 'hidden' : 'visible');
                        item.target.target.first('.' + me.itemCls + '-title').setStyle('visibility', noTitle ? 'hidden' : 'visible');
                    }}: false);
                }

                // Increase value of 'totalItemsWidth' variable by current item width
                totalItemsWidth += itemWidth + me.calc.item.marginLeft;
            });

            // If no item is currently hovered
            if (hover == undefined) {

                // Apply width for the whole component
                me.setWidth(totalItemsWidth + me.calc.border + this.calc.paddingRight);
            }

            // Else if available width is smaller than minimum width, and no item is currently hovered
        } else {

            // Setup minimum-width style for items, and for the whole component
            me.getEl().select('.' + me.itemCls).each(function(el, c, index){
                el.setWidth(me.calc.item.dots+me.calc.item.padding+me.calc.item.firstChar, easing);
                el.first('.' + me.itemCls+'-dots').setStyle('display', 'inline-block');
                el.first('.' + me.itemCls+'-title').setWidth(me.calc.item.firstChar, easing);
            });
            me.setWidth(me.minWidth);
        }
    },

    /**
     * A method that returns the inner template for displaying items in the list.
     * This method is useful to override when using a more complex display value, for example
     * inserting an icon along with the text.
     * @param {String} displayField The {@link #displayField} for the BoundList.
     * @return {String} The inner template
     */
    getInnerTpl: function(displayField) {
        return '<span class="'+this.itemCls+'-title">{' + displayField + '}</span>' +
               '<span class="'+this.itemCls+'-dots">..</span>';
    },

    /**
     * Here we override parent's onBoxReady function to additionally implement:
     * 1. Call adjustWidth()
     * 2. Bind handler for `mouseleave` event
     * 3. Bind handler for parent container's `resize` event
     */
    onBoxReady: function() {
        var me = this;
        me.adjustWidth();
        me.callParent(arguments);
        me.getEl().on('mouseleave', me.onMouseLeave, me);
        if (me.isContained) me.isContained.on('resize', function(){
            me.hide();
            me.adjustWidth();
            me.show();
        });
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
    onItemMouseEnter: function(row, dom, index, e) {
        var me = this;

        // Provide not-so-ofter check before shrinking
        clearTimeout(me.lastHoverTimeout);
        me.lastHoverTimeout = setTimeout(function(me){
            for (var i = 0; i < me.store.getCount(); i++) {
                if (me.store.getAt(i).get('alias') == row.get('alias')) {
                    me.adjustWidth(i);
                }
            }
        }, 200, me);

        // Call parent
        me.callParent(arguments);
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

    /**
     * Ability to programmatically click on some of the items within current shrinklist,
     * with forcing visual css clicked-styles to be in use
     *
     * @param index Zero-based index of the item within the shrinklist
     */
    press: function(index) {
        var me = this, r, el;

        // If shrinklist is disabled - return
        if (me.disabled) return;

        // If shrinklist's store does not contain a record, related to given index - return
        if (!(r = me.store.getAt(index))) return;

        // If shrinklist's current layout doe not have HTMLElement, related to given index - return
        if (!(el = me.el.select('.x-shrinklist-item:nth('+(index+1)+')').first())) return;

        // Assign 'clicked-style' css class
        el.addCls('x-shrinklist-item-active');

        // Provide 'clicked-style' css class autoremove after 300ms
        Ext.defer(function(){try{el.removeCls('x-shrinklist-item-active')} catch(e){}}, 300);

        // Fire 'itemclick' event
        me.fireEvent('itemclick', me, r, el.dom, index);
    }
});
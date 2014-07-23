Ext.define('Indi.ux.SubsectionList', {
    extend: 'Ext.Container',
    alias: 'widget.subsectionlist',
    height: 19,
    cls: 'i-subsections-wrapper',
    margin: '1 0 0 0',
    widthAmend: 0,
    calc: { // Setup object containing params for proper sizing calculation operations
        border: 2,
        paddingRight: 1,
        item: {
            dots: 8,
            padding: 6,
            marginLeft: 1,
            firstChar: 7
        }
    },
    disabled: true,
    itemClick: function(){},
    initiallyDisabled: function(){return false},
    initComponent: function() {
        this.store = Indi.trail().sections;
        for (var i = 0; i < this.store.length; i++) this.store[i].title = this.store[i].title.replace(/ /g, '&nbsp;');
        this.tpl = new Ext.XTemplate(
            '<div class="i-subsections">' +
                '<tpl for=".">' +
                '<span class="i-subsections-item" alias="{alias}">' +
                '<span class="i-subsections-item-title">{title}</span>' +
                '<span class="i-subsections-item-dots">..</span>' +
                '</span>' +
                '</tpl>' +
                '</div>'
        );
        this.superclass.initComponent.apply(this, arguments);
        this.minWidth = this.getMinWidth();
    },
    afterRender: function(){
        var me = this;
        if (this.initiallyDisabled()) this.disable(); else this.enable();
        this.tpl.overwrite(this.el, this.store);
        this.superclass.afterRender.apply(this, arguments);
        this.getEl().select('.i-subsections-item').on('click', function(){
            me.fireEvent('itemclick', Ext.get(this));
        });
        this.getEl().select('.i-subsections-item').on('mouseover', function(){
            me.fireEvent('itemmouseover', Ext.get(this));
        });
        this.getEl().on('mouseleave', function(){
            me.fireEvent('mouseleave', Ext.get(this));
        });
    },
    getMinItemWidth: function(){
        return this.calc.item.dots + this.calc.item.padding + this.calc.item.marginLeft + this.calc.item.firstChar;
    },
    getMinWidth: function(){
        return this.calc.border + this.calc.paddingRight + this.getMinItemWidth() * this.store.length;
    },
    getRequiredWidth: function(){
        var requiredWidth = this.calc.border + this.calc.paddingRight;
        for (var i = 0; i < this.store.length; i++)
            requiredWidth += Indi.metrics.getWidth(this.store[i].title)
                + this.calc.item.marginLeft + this.calc.item.padding;
        return requiredWidth;
    },
    getAvailableWidth: function(){
        var i, busyWidth = 0, toolbar = Ext.getCmp(this.toolbarId);
        for (i = 0; i < toolbar.items.items.length; i++)
            if (toolbar.items.items[i].id != this.id && !toolbar.items.items[i].id.toString().match(/^tbfill/)) {
                busyWidth += toolbar.items.items[i].getWidth();
                if (toolbar.items.items[i].id.toString().match(/-button-[a-z]+$/)) busyWidth += 2;
                else if (toolbar.items.items[i].id.toString().match(/^tbseparator-/)) busyWidth += 4 + 1;
                else if (toolbar.items.items[i].id.toString().match(/-keyword$/)) busyWidth += 4 + 3;
            }

        return Ext.getCmp('i-center-center-wrapper').getWidth() - busyWidth - 5 + this.widthAmend;
    },

    // Main width-adjusting function. Accept 'hover' argument, as an index of item, that
    // currently is hovered, so it should have full width, by decresing widths of other items
    adjustWidth: function(hover){

        // Setup auxiliary variables
        var me = this, easing = arguments.length > 0 ? true : false, exclusive = undefined, totalItemsWidth = 0,
            availableWidth = this.getAvailableWidth(), requiredWidth = this.getRequiredWidth(),
            constant = 0, ignoreA = [], avgItemWidth = 0, itemTitleRequiredWidth, lost, itemWidth,
            itemTitleWidth, noTitle, noDots;

        // Restore visibility
        this.getEl().select('.i-subsections-item > *').setStyle('visibility', 'visible');

        // If we have sufficient width for all items with no need to minify
        if (requiredWidth <= availableWidth) {

            // Prevent layout loop
            this.preventLayoutLoop = true;

            // Setup full required width for the component, and appropriate inner styles
            this.setWidth(requiredWidth);
            this.getEl().select('.i-subsections-item').setStyle('width', 'auto');
            this.getEl().select('.i-subsections-item-dots').setStyle('display', 'none');
            this.getEl().select('.i-subsections-item-title').setStyle('width', 'auto');

            // Else if we do not have sufficient width, but available width is larger
            // than minimum width or it's even smaller, but some item is currently hovered
        } else if (availableWidth > this.minWidth || hover != undefined) {

            // If available width is even smaller than minimum width and some item is currently hovered
            if (hover != undefined && !(availableWidth > this.minWidth)) {

                // Increase availableWidth for it to be at least the same as minimum width
                availableWidth = this.minWidth;

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
            constant = this.calc.border + this.calc.paddingRight
                + (this.calc.item.marginLeft + this.calc.item.padding) * this.store.length;

            // If some item is currently hovered
            if (hover != undefined) {

                // Push index of hovered item into ignoreA array
                ignoreA.push(hover);

                // Increment value of 'constant' variable by hovered item title text width
                constant += this.getEl().select('.i-subsections-item').item(hover)
                    .first('.i-subsections-item-title').getTextWidth();
            }

            // Get initial average width of items
            avgItemWidth = Math.floor((availableWidth - constant)/(me.store.length - ignoreA.length));

            // Run average width calculation twice, as there may be case when some items have width
            // smaller than initial average width, but the difference between initial average width
            // and such item width is - was not used for increase of 'constant' variable value, and
            // therefore not involved at the moment of initial calculation of 'avgItemWidth' value
            for (var j = 0; j < 2; j++) {

                // For each non-hovered item
                this.getEl().select('.i-subsections-item').each(function(el, c, index) {
                    if (index != hover) {

                        // Get width, required for current item's title
                        itemTitleRequiredWidth = el.first('.i-subsections-item-title').getTextWidth();

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
                avgItemWidth = Math.floor((availableWidth - constant)/(me.store.length - ignoreA.length));
            }

            // Get lost value. We need this because there may be undistributed number of pixels, that
            // we need to redistribute to items by a one-lost-pixel-to-one-item logic. This will provide
            // a visual impression that all items have dimensions that allow them to have exact fit within
            // the component, despite on mathematically it's impossible
            lost = (availableWidth - constant) - avgItemWidth * (me.store.length - ignoreA.length)

            // For each item
            this.getEl().select('.i-subsections-item').each(function(el, c, index) {

                // If item should be ignored
                if (ignoreA.indexOf(index) != -1) {

                    // We restore it's width state to full required width
                    itemTitleWidth = el.first('.i-subsections-item-title').getTextWidth();
                    itemWidth = itemTitleWidth + me.calc.item.padding;
                    el.first('.i-subsections-item-dots').setStyle('display', 'none');
                    el.first('.i-subsections-item-title').setWidth(itemTitleWidth, easing);
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
                    el.first('.i-subsections-item-dots').setStyle('display', noDots ? 'none' : 'inline-block');

                    // Apply width for current item's title
                    el.first('.i-subsections-item-title').setWidth(itemTitleWidth, easing);

                    // Apply width for whole current item
                    el.setWidth(itemWidth, easing ? {callback: function(item){
                        item.target.target.first('.i-subsections-item-dots').setStyle('visibility', exclusive ? 'hidden' : 'visible');
                        item.target.target.first('.i-subsections-item-title').setStyle('visibility', noTitle ? 'hidden' : 'visible');
                    }}: false);
                }

                // Increase value of 'totalItemsWidth' variable by current item width
                totalItemsWidth += itemWidth + me.calc.item.marginLeft;
            });

            // If no item is currently hovered
            if (hover == undefined) {

                // Prevent layout loop
                this.preventLayoutLoop = true;

                // Apply width for the whole component
                this.setWidth(totalItemsWidth + this.calc.border + this.calc.paddingRight);
            }

            // Else if available width is smaller than minimum width, and no item is currently hovered
        } else {

            // Prevent layout loop
            this.preventLayoutLoop = true;

            // Setup minimum-width style for items, and for the whole component
            this.getEl().select('.i-subsections-item').each(function(el, c, index){
                el.setWidth(me.calc.item.dots+me.calc.item.padding+me.calc.item.firstChar, easing);
                el.first('.i-subsections-item-dots').setStyle('display', 'inline-block');
                el.first('.i-subsections-item-title').setWidth(me.calc.item.firstChar, easing);
            });
            this.setWidth(this.minWidth);
        }
    },
    listeners: {

        // Handler for a subsection click
        itemclick: function(item){

            // Call item click handler
            this.itemClick(item);
        },

        itemmouseover: function(item){
            clearTimeout(this.lastHoverTimeout);
            this.lastHoverTimeout = setTimeout(function(me){
                item.parent().select('.i-subsections-item').each(function(el, c, index){
                    if (item.getAttribute('alias') == el.getAttribute('alias')) {
                        me.adjustWidth(index);
                    }
                });
            }, 200, this);
        },

        afterlayout: function(){
            if (!this.preventLayoutLoop) this.adjustWidth();
            this.preventLayoutLoop = false;
        },

        mouseleave: function(){
            clearTimeout(this.lastHoverTimeout);
            var me = this;
            this.lastHoverTimeout = setTimeout(function(){
                me.adjustWidth(null);
            }, 100);
        }
    }
});
/**
 * Append some jQuery-style methods to Ext.dom.Element component, and apply a small fix for getAlignToXY() method.
 * Also, new method 'createTooltip' defined
 */
Ext.override(Ext.dom.Element, {

    /**
     * Create the new Ext.tip.ToolTip object for current element
     *
     * @param {Object|String} tooltip
     */
    createTooltip: function(tooltip) {
        var me = this;

        // If 'tooltip' argument is an object
        if (Ext.isObject(tooltip)) {

            // If that object doesn't have 'html' property
            if (!tooltip.hasOwnProperty('html')) {

                // If current element has 'title' attribute
                if (me.attr('title')) {

                    // Set up 'html' property of 'tooltip' object as the value of 'title' attrubute of current element
                    tooltip.html = me.attr('title');

                    // Remove 'title' attrubite, as tooltip will be used instead
                    me.removeAttr('title');
                }
            }

        // Else 'tooltip' argument is neither not an object nor a string
        } else if (!Ext.isString(tooltip)) {

            // Set up 'tooltip' argument as the value of 'title' attrubute of current element
            // despite 'title' attribute may be not-specified/null/empty
            tooltip = me.attr('title');

            // Remove 'title' attrubite, as tooltip will be used instead,
            // despite 'title' attribute may be not-specified/null/empty
            me.removeAttr('title');
        }

        // If, after all tries, tooltip is still inconsistent - return
        if ((Ext.isObject(tooltip) && !tooltip.hasOwnProperty('html')) || !tooltip) return;

        // Assign 'tooltip' property
        me.tooltip = tooltip;

        // Create tooltip
        Ext.tip.ToolTip.create(me);

        // Define a 'getToolTip' method for easy access to tooltip object
        me.getToolTip = function() {
            return Ext.getCmp(me.id + '-tooltip');
        }
    },

    /**
     * jQuery style
     */
    attr: function(name, value) {
        if (arguments.length == 1) {
            return typeof name == 'object' ? this.set(name) : this.getAttribute(name);
        } else if (arguments.length == 2) {
            var attrO = {}; attrO[name] = value;
            return this.set(attrO);
        }
    },
    css: function(name, value) {
        if (arguments.length == 1) {
            return typeof name == 'object' ? this.setStyle(name) : this.getStyle(name);
        } else if (arguments.length == 2) {
            var styleO = {}; styleO[name] = value;
            return this.setStyle(styleO);
        }
    },
    removeAttr: function(name) {
        this.dom.removeAttribute(name);
        return this;
    },
    val: function(val) {
        if (arguments.length) {
            this.dom.value = val;
            this.attr('value', val);
            return this;
        } else {
            return this.dom.value;
        }
    },
    click: function() {
        this.dom.click();
    },

    /**
     * This function is almost the same as native, but with one small fix: y-offset is not taken into consideration
     * position specification contains the '?' sign at the ending (e.g constrain should take effect). Additional
     * condition for the problem to appear is case when that constrain affects so aligned element is placed at the top
     * of alignToEl element, that is why the native code was picked from source, and small adjustment was implemented
     * to fix the described problem
     */
    getAlignToXY : function(alignToEl, posSpec, offset) {
        var doc = document,
            alignRe = /^([a-z]+)-([a-z]+)(\?)?$/,
            round = Math.round;

        alignToEl = Ext.get(alignToEl);

        if (!alignToEl || !alignToEl.dom) {
            //<debug>
            Ext.Error.raise({
                sourceClass: 'Ext.dom.Element',
                sourceMethod: 'getAlignToXY',
                msg: 'Attempted to align an element that doesn\'t exist'
            });
            //</debug>
        }

        offset = offset || [0,0];
        posSpec = (!posSpec || posSpec == "?" ? "tl-bl?" : (!(/-/).test(posSpec) && posSpec !== "" ? "tl-" + posSpec : posSpec || "tl-bl")).toLowerCase();

        var me = this,
            myPosition,
            alignToElPosition,
            x,
            y,
            myWidth,
            myHeight,
            alignToElRegion,
            viewportWidth = Ext.dom.Element.getViewWidth(),
            viewportHeight = Ext.dom.Element.getViewHeight(),
            p1y,
            p1x,
            p2y,
            p2x,
            swapY,
            swapX,
            docElement = doc.documentElement,
            docBody = doc.body,
            scrollX = (docElement.scrollLeft || docBody.scrollLeft || 0),// + 5, WHY was 5 ever added?
            scrollY = (docElement.scrollTop  || docBody.scrollTop  || 0),// + 5, It means align will fail if the alignTo el was at less than 5,5
            constrain, //constrain to viewport
            align1,
            align2,
            alignMatch = posSpec.match(alignRe);

        //<debug>
        if (!alignMatch) {
            Ext.Error.raise({
                sourceClass: 'Ext.dom.Element',
                sourceMethod: 'getAlignToXY',
                el: alignToEl,
                position: posSpec,
                offset: offset,
                msg: 'Attemmpted to align an element with an invalid position: "' + posSpec + '"'
            });
        }
        //</debug>

        align1 = alignMatch[1];
        align2 = alignMatch[2];
        constrain = !!alignMatch[3];

        //Subtract the aligned el's internal xy from the target's offset xy
        //plus custom offset to get this Element's new offset xy
        myPosition = me.getAnchorXY(align1, true);
        alignToElPosition = alignToEl.getAnchorXY(align2, false);

        x = alignToElPosition[0] - myPosition[0] + offset[0];
        y = alignToElPosition[1] - myPosition[1] + offset[1];

        // If position spec ended with a "?", then constrain to viewport is necessary
        if (constrain) {
            myWidth = me.getWidth();
            myHeight = me.getHeight();
            alignToElRegion = alignToEl.getRegion();
            //If we are at a viewport boundary and the aligned el is anchored on a target border that is
            //perpendicular to the vp border, allow the aligned el to slide on that border,
            //otherwise swap the aligned el to the opposite border of the target.
            p1y = align1.charAt(0);
            p1x = align1.charAt(align1.length - 1);
            p2y = align2.charAt(0);
            p2x = align2.charAt(align2.length - 1);
            swapY = ((p1y == "t" && p2y == "b") || (p1y == "b" && p2y == "t"));
            swapX = ((p1x == "r" && p2x == "l") || (p1x == "l" && p2x == "r"));

            if (x + myWidth > viewportWidth + scrollX) {
                x = swapX ? alignToElRegion.left - myWidth : viewportWidth + scrollX - myWidth;
            }
            if (x < scrollX) {
                x = swapX ? alignToElRegion.right : scrollX;
            }
            if (y + myHeight > viewportHeight + scrollY) {
                y = swapY ? alignToElRegion.top - myHeight : viewportHeight + scrollY - myHeight;
                y -= offset[1]; // This line is thу only difference with original getAlignToXY function source code
            }
            if (y < scrollY) {
                y = (swapY ? alignToElRegion.bottom : scrollY) + offset[1];
            }
        }
        return [x,y];
    }
});
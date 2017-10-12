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
     *
     * @param alignToEl
     * @param posSpec
     * @param offset
     * @return {Array}
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
    },

    /**
     * Animate qty
     *
     * @param {Number} qty
     */
    qty: function(qty) {
        var el = this.dom, newCount = Math.max(parseInt(qty) || 0, 0);

        // Get counts
        var curCount = Math.max(parseInt(el.innerHTML) || 0, 0), nextCount = el.getAttribute('data-nextCount');

        // If same - return
        if (curCount == newCount) return;

        // Set data-curCount
        el.setAttribute('data-curCount', newCount);

        var incr = curCount < newCount, large = (incr ? newCount : curCount).toString(), small = (incr ? curCount : newCount).toString(),
            constPart = [], constEndPart = [], largePart, smallPart, i, l, j;

        small = ((new Array(large.length - small.length + 1)).join('0')) + small;

        for (i = 0, l = large.length; i < l; i++) {
            if ((j = large.charAt(i)) !== small.charAt(i)) break;
            constPart.push(j);
        }

        largePart = large.substr(i); smallPart = small.substr(i);
        constPart = constPart.join('').replace(/\s$/, '&nbsp;');
        constEndPart = constEndPart.join('').replace(/^\s/, '&nbsp;');

        if (!Ext.String.trim(el.innerHTML)) el.innerHTML = '&nbsp;';

        var h = el.clientHeight || el.offsetHeight; el.innerHTML = '<div class="menu-qty-wrap menu-qty-inbl"></div>';
        var wrapEl = el.firstChild, constEl1, constEl2, animwrapEl, animEl, vert = true;

        if (!constPart.length) smallPart = smallPart.replace(/^0+/, '');
        else wrapEl.appendChild(constEl1 = Ext.DomHelper.createDom({tag: 'div', cls: 'menu-qty-inbl', html: constPart}));

        if (!smallPart || smallPart == '0' && !constPart.length) {
            smallPart = '&nbsp;';
            vert = !!constPart.length;
        }

        wrapEl.appendChild(animwrapEl = Ext.DomHelper.createDom({tag: 'div', cls: 'menu-qty-anim-wrap menu-qty-inbl'}));
        animwrapEl.appendChild(animEl = Ext.DomHelper.createDom({
            tag: 'div',
            cls: 'menu-qty-anim ' + (incr ? 'menu-qty-anim_inc' : 'menu-qty-anim_dec'),
            html:       '<div class="menu-qty-anim_large"><span class="menu-qty-anim_large_c">' + largePart + '</span></div>' +
                (vert ? '<div class="menu-qty-anim_small"><span class="menu-qty-anim_small_c">' + smallPart + '</span></div>' : ''),
            style: vert ? {marginTop: incr ? -h +'px' : 0} : {right: 0}
        }));

        var largeW = Ext.get(animEl).down('span.menu-qty-anim_large_c').getWidth(),
            smallW = vert ? (smallPart == '&nbsp;' ? largeW : Ext.get(animEl).down('span.menu-qty-anim_small_c').getWidth()) : 0;

        if (constEndPart.length) wrapEl.appendChild(constEl2 = Ext.DomHelper.createDom({tag: 'div', cls: 'menu-qty-inbl', innerHTML: constEndPart}));

        Ext.get(wrapEl).setStyle({
            width: (constEl1 && Ext.get(constEl1).getWidth() || 0) + (constEl2 && Ext.get(constEl2).getWidth() || 0) + largeW + 0
        });

        Ext.get(animwrapEl).setStyle({width: incr ? smallW : largeW});

        var onDone = function () {
            el.innerHTML = newCount || ' '; el.removeAttribute('data-curCount'); el.removeAttribute('data-nextCount');
        }, margin = vert ? {marginTop: incr ? 0 : -h + 'px'} : {marginRight: incr ? -smallW : 0};

        if (!Ext.supports.Transitions) onDone(); else {
            Ext.get(animwrapEl).addCls('menu-qty-anim-wrap-css3');
            if (largeW != smallW) Ext.get(animwrapEl).setStyle({width: incr ? largeW : smallW});
            if (vert) Ext.get(animEl).setStyle(margin);
            setTimeout(onDone, 300);
        }
    }
});
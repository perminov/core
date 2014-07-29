Ext.override(Ext.form.field.Text, {

    /**
     * Sets a data value into the field and runs the change detection and validation. Also applies any configured
     * {@link #emptyText} for text fields. To set the value directly without these inspections see {@link #setRawValue}.
     * @param {Object} value The value to set
     * @return {Ext.form.field.Text} this
     */
    setValue: function(value) {
        var me = this,
            inputEl = me.inputEl;

        if (inputEl && me.emptyText && !Ext.isEmpty(value)) {
            inputEl.removeCls(me.emptyCls);
            me.valueContainsPlaceholder = false;
        }
        //console.log(me.name, value);

        me.callParent(arguments);

        me.applyEmptyText();
        return me;
    }
});

Ext.override(Ext.dom.Element, {
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
     * Gets the x,y coordinates to align this element with another element. See {@link #alignTo} for more info on the
     * supported position values.
     * @param {String/HTMLElement/Ext.Element} element The element to align to.
     * @param {String} [position="tl-bl?"] The position to align to (defaults to )
     * @param {Number[]} [offsets] Offset the positioning by [x, y]
     * @return {Number[]} [x, y]
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
Ext.override(Ext.dom.CompositeElementLite, {
    attr: function(name, value) {
        if (arguments.length == 1) {
            if (typeof name == 'object') {
                this.each(function(el){
                    el.set(name);
                });
            }
            return this;
        } else if (arguments.length == 2) {
            var attrO = {}; attrO[name] = value;
            return this.each(function(el){
                el.set(attrO);
            });
        }
    },

    css: function(name, value) {
        if (arguments.length == 1) {
            if (typeof name == 'object') {
                this.each(function(el){
                    el.setStyle(name);
                });
            }
            return this;
        } else if (arguments.length == 2) {
            var styleO = {}; styleO[name] = value;
            return this.each(function(el){
                el.setStyle(styleO);
            });
        }
    },

    click: function() {
        return this.each(function(el){
            el.dom.click();
        })
    },

    removeCls: function(cls) {
        cls = cls.split(' ');
        for (var i = 0; i < cls.length; i++) {
            this.each(function(el){
                el.removeCls(cls[i]);
            });
        }
        return this;
    },

    on: function(eventName, fn, scope) {
        this.each(function(el){
            elScope = scope ? scope : Ext.get(el);
            el.on(eventName, fn, elScope);
        });
    }
});

/**
 * Override Ext.tip.ToolTip, for append static 'lastFade' property, to be able to skip fadings,
 * if user move mouse from one tooltipped componend to another enough fast
 */
Ext.override(Ext.tip.ToolTip, {
    statics: {
        lastFade: null,
        lastFadeTimeout: null,
        lastTip: null,
        isFast: 350
    }
});

/**
 * Here we override Ext.Component component, to provide an ability for 'tooltip' config properties to be used for
 * creating Ext.tip.ToolTip objects instead of standart Ext.tip.QuickTip objects
 */
Ext.override(Ext.Component, {

    ctx: function() {
        var trailLevel = this.trailLevel != undefined ? this.trailLevel : Ext.getCmp('i-center-center-wrapper').trailLevel;
        var trailItem = Indi.trail(trailLevel - (Indi.trail(true).store.length - 1));
        return Indi.app.getController(trailItem.section.alias).actions[trailItem.action.alias];
    },

    ti: function(){
        var trailLevel = this.trailLevel != undefined ? this.trailLevel : Ext.getCmp('i-center-center-wrapper').trailLevel;
        return Indi.trail(trailLevel - (Indi.trail(true).store.length - 1));
    },

    // AfterRender
    afterRender: function() {
        var me = this;

        // Define tooltip getter
        me.getToolTip = function() {
            return Ext.getCmp(me.id + '-tooltip');
        }

        me.ctx = function() {
            var trailLevel = Ext.getCmp('i-center-center-wrapper').trailLevel;
            var trailItem = Indi.trail(trailLevel - (Indi.trail(true).store.length - 1));
            return Indi.app.getController(trailItem.section.alias).actions[trailItem.action.alias];
        }

        // If 'tooltip' property was defined
        if (me.tooltip) {

            // Setup initial arrow tooltip config
            var tooltipCfg = {
                id: this.id + '-tooltip',
                hideDelay: 0,
                showDelay: 0,
                dismissDelay: 0,
                staticOffset: [0, 0],
                anchor: 'top',
                cls: 'i-tip',
                target: this.id,
                isFast: Ext.ToolTip.isFast,
                listeners: {
                    // Setup tooltip positioning
                    afterlayout: function(){
                        if (this.anchor == 'top' || this.anchor == 'bottom') {
                            var offsetX = (this.getWidth() - this.target.getWidth())/2;
                            this.mouseOffset = [-offsetX + this.staticOffset[0], this.staticOffset[1]];
                            this.anchorOffset = -20;
                            this.anchorOffset += this.getWidth()/2;

                            if (this.target.lastBox.x < offsetX) {
                                this.mouseOffset = [-this.target.lastBox.x + this.staticOffset[0], this.staticOffset[1]];
                                this.anchorOffset -= offsetX - this.target.lastBox.x;
                            }
                        } else {
                            this.mouseOffset = [this.staticOffset[0], this.staticOffset[1]];
                        }
                    },

                    // Do not show tooltips for disabled targets
                    beforeshow: function(){
                        if (this.target.hasCls('x-item-disabled')) return false;
                    },

                    // Provide fading in as per Gmail style
                    show: function(){
                        this.noFadeOut = false;
                        if (Ext.ToolTip.lastTip && Ext.ToolTip.lastTip.id != this.id) {
                            if (new Date().getTime() - Ext.ToolTip.lastFade < Ext.ToolTip.isFast) {
                                this.getEl().setStyle({opacity: 1});
                            } else {
                                this.getEl().fadeIn();
                            }
                            Ext.ToolTip.lastTip.noFadeOut = true;
                            Ext.ToolTip.lastTip.hide();
                        } else {
                            this.getEl().fadeIn();
                        }
                        Ext.ToolTip.lastFade = new Date().getTime();
                    },

                    // Provide fading out as per Gmail style
                    beforehide: function(){
                        var me = this;
                        Ext.ToolTip.lastTip = me;
                        if (!me.noFadeOut) {
                            me.getEl().fadeOut({callback: function(){
                                me.noFadeOut = true;
                                me.hide();
                            }});
                            return false;
                        }
                        Ext.ToolTip.lastFade = new Date().getTime();
                    }
                }
            };

            // If 'tooltip' property is an object
            if (typeof me.tooltip == 'object') tooltipCfg =  Ext.Object.merge(tooltipCfg, me.tooltip);

            // Else we assume it is just a tooltip string
            else tooltipCfg.html = me.tooltip;

            // Create tooltip
            new Ext.tip.ToolTip(tooltipCfg);
        }

        // Call parent
        me.callParent();

        // Set position on the page
        if (!(me.x && me.y) && (me.pageX || me.pageY)) {
            me.setPagePosition(me.pageX, me.pageY);
        }
    },

    /**
     * Allows addition of behavior to the destroy operation.
     * After calling the superclass’s onDestroy, the Component will be destroyed.
     *
     * @template
     * @protected
     */
    onDestroy: function() {
        var me = this;

        // Destroy the tooltip, if exists
        if (me.tooltip && me.getToolTip()) {
            if (me.getToolTip().getEl() && me.getToolTip().getEl().getActiveAnimation())
                me.getToolTip().getEl().getActiveAnimation().end();
            me.getToolTip().destroy();
        }

        // Call parent
        me.callParent();
    }
});

/**
 * Here we override Ext.Component for being
 */
Ext.define('Ext.Component', {
    extend: 'Ext.Component',
    mcopwso: [], // Merge Config Object-Properties With Superclass Ones
    mergeParent: function(config) {
        var initialMcopwso = this.mcopwso.join(',').split(',');
        var obj = this;

        while (obj.superclass) {
            if (obj.superclass.mcopwso && obj.superclass.mcopwso.length)
                for (var i = 0; i < obj.superclass.mcopwso.length; i++)
                    if (this.mcopwso.indexOf(obj.superclass.mcopwso[i]) == -1)
                        this.mcopwso.push(obj.superclass.mcopwso[i]);
            obj = obj.superclass;
        }
        obj = this;

        if (this.mcopwso.length)
            while (obj.superclass) {
                for (var i = 0; i < this.mcopwso.length; i++)
                    if (this[this.mcopwso[i]] && obj.superclass && obj.superclass[this.mcopwso[i]])
                        this[this.mcopwso[i]]
                            = Ext.merge(Ext.clone(obj.superclass[this.mcopwso[i]]), this[this.mcopwso[i]]);

                obj = obj.superclass;
            }

        for (var i = 0; i < initialMcopwso.length; i++) {
            if (typeof config == 'object' && typeof config[initialMcopwso[i]] == 'object') {
                this[initialMcopwso[i]] = Ext.merge(this[initialMcopwso[i]], config[initialMcopwso[i]]);
                delete config[initialMcopwso[i]];
            }
        }
    },
    constructor: function(config){
        this.mergeParent(config);
        this.callParent(arguments);
    }
});
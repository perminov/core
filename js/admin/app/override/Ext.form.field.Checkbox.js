/**
 * This component is overrided for adding the ability to recognize color or color-box definitions within
 * `enumset`, `inputValue` and `boxLabel` config properties, and reflect it within the component layout
 */
Ext.override(Ext.form.field.Checkbox, {

    /**
     * Regular expression for color detecting
     *
     * @type {RegExp}
     */
    colorReg: new RegExp('^[0-9]{3}(#[0-9a-fA-F]{6})$', 'i'),

    /**
     * Template for color box
     */
    colorBoxTpl: new Ext.XTemplate(
        '<span class="{colorBoxCls}" <tpl if="colorBoxColor">style="background: {colorBoxColor};"</tpl>></span>'),

    /**
     * Css class for label color box
     */
    colorBoxCls: 'i-radio-color-box',

    /**
     * Css class for label color box
     */
    colorBoxColor: 'transparent',

    // @inheritdoc
    initComponent: function() {
        this.initColor();
        this.callParent();
    },

    /**
     * Special function that will be called before initComponent() call, to provide additional
     * setup, related to color detection
     */
    initColor: function() {
        var me = this, color;

        // Normalize `enumset` property
        me.enumset = me.enumset || {title: me.boxLabel};

        // Detect color
        color = me.color(me.enumset, me.inputValue);

        // Setup box label itself, without any color definition
        me.boxLabel = color.title;

        // Prepend box label with color box definition, if color box definition found
        if (['boxColor', 'value'].indexOf(color.src) != -1) me.beforeBoxLabelTextTpl = me.colorBoxTpl.apply(me);

        // Or wrap box label with 'span' element, with inline style color definition
        else if (color.color) me.boxLabelAttrTpl = 'style="color: ' + color.color + '"';

        // Append new render selector - `colorBoxEl`
        Ext.merge(me.renderSelectors, {
            colorBoxEl: '.' + me.colorBoxCls
        });
    },

    /**
     * Try to find a color declaration within some of certain radio properties
     * and if found, get that color and build .i-combo-color-box element
     *
     * @param data
     * @param value
     * @return {Object}
     */
    color: function(data, value) {
        var me = this;

        // Normalize arguments
        data = data || {};
        value = value || '';

        // Declare `info` object
        var info = {title: data.title ? data.title.trim() : '', color: '', src: '', box: '', css: {color: ''}}, color;

        // Check if `title` or `value` contain a color definition
        if (color = value.toString().match(this.colorReg)) {
            info.src = 'value';
        } else if (color = info.title.match(this.colorReg)) {
            info.src = 'title';
        } else if (data.system) {
            if (data.system['boxColor'] && typeof data.system['boxColor'] == 'string') {
                info.src = 'boxColor';
                color = [true, data.system['boxColor']];
            } else if (data.system['color'] && typeof data.system['color'] == 'string') {
                info.src = 'color';
                color = [true, data.system['color']];
            }
        }

        // If contains, we prepare a color box element, to be later inserted in dom - before keyword field
        if (color && color.length && color[1]) {

            // Setup color
            info.color = color[1];

            // Setup colorBoxColor property
            me.colorBoxColor = ['boxColor', 'value'].indexOf(info.src) != -1 ? info.color : 'transparent';

            // If color was got from title (means that title was in format hue#rrggbb),
            // set title as same color but without hue
            if (info.src == 'title') info.title = info.color;
        }

        // Return `info` object
        return info;
    },

    /**
     * Set the color of the component, either as color-box, or as inline color for radio box label
     *
     * @param color
     * @param type
     * @return {*}
     */
    setColor: function(color, type) {
        var me = this;

        // If no `type` argument given, we assume that type of color representation should be detected automatically
        if (!type || type == 'auto') {

            // If color box element exists, we assume that type of color representation - is color box,
            // so we just change the backgroung color of that color box
            if (me.colorBoxEl) me.colorBoxEl.css('background', color);

            // Else we setup do an inline css declaration for the label color
            else me.boxLabelEl.css('color', color);

        // Else if `type` argument is given and is 'box' - update the value of `background` css property for the color box,
        // otherwise update `color` css property of the box label element
        } else if (type == 'box') me.getColorBox().css('background', color); else me.boxLabelEl.css('color', color);

        // Return component instance itself
        return me;
    },


    /**
     * Get the colorBoxEl element. If it not yet exists - create and return it
     *
     * @return {*}
     */
    getColorBox: function() {
        var me = this;

        // If colorBoxEl element not yet exists - create it
        if (!me.colorBoxEl) me.colorBoxEl = me.boxLabelEl.insertFirst(Indi.fly(me.colorBoxTpl));

        // Return colorBoxEl element
        return me.colorBoxEl;
    }
});
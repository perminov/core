/**
 * Custom alternative for Ext.form.field.ComboBox.
 * Supports a lot of additonal features, such as options disabling, grouping, advanced option trees merging,
 * multiSelect mode, color detection and etc.
 * todo: implement Ext.data.Store usage as a data source instead of custom store implementation
 */
Ext.define('Indi.lib.form.field.Combo', {

    // @inheritdoc
    extend: 'Ext.form.field.Picker',

    // @inheritdoc
    alternateClassName: 'Indi.form.Combo',

    // @inheritdoc
    alias: 'widget.combo.form',

    // @inheritdoc
    multiSelect: false,

    // @inheritdoc
    pickerOffset: [0, -1],

    /**
     * Array of option ids/keys, that should be disabled
     */
    disabledOptions: [],

    /**
     * The maximum allowed number of selected/chosed options. This config takes effect only if `multiSelect` is `true`
     */
    maxSelected: 0,

    /**
     * The text that will be displayed if the number of currently selected options is already reached the maximum
     * allowed count. This config takes effect only if `multiSelect` is `true`
     */
    maxSelectedText: Indi.lang.I_COMBO_MISMATCH_MAXSELECTED,

    /**
     * We need this to be able to separate options list visibility after keyword was erased
     * At form combos options wont be hidden, but at Indi.combo.filter same param is set to true
     *
     * @type {Boolean}
     */
    hideOptionsAfterKeywordErased: false,

    /**
     * This config differs from the same native config. At first, it's only take effect only if multiSelect
     * config is set to true, so, if 'grow' config is set to true too, after each append new selected item
     * to the list of currently selected items - combo's height will grow to fit all selected items. Otherwise,
     * combo's height won't grow, and selected items will be displayed in one line, even despite on that some of
     * them would be clipped by parent div's overflow: hidden css property
     */
    grow: true,

    /**
     * Number of items, that will be available by default, assuming optionHeight = 14px
     *
     * @type {Number}
     */
    optionsOnPage: 20,

    /**
     * Default option height in pixels
     *
     * @type {Number}
     */
    defaultOptionHeight: 14,

    /**
     * Measure of width, related to single '&nbsb;' symbol. Used to convert &nbsp;-indends to padding-left-indents
     */
    nbspPx: 4,

    /**
     * Regular expression for color detecting
     *
     * @type {RegExp}
     */
    colorReg: new RegExp('^[0-9]{3}(#[0-9a-fA-F]{6})$', 'i'),

    // @inheritdoc
    renderSelectors: {
        comboEl: '.i-combo',
        comboInner: '.x-form-text',
        multipleEl: '.i-combo-multiple',
        wrapperEl: '.i-combo-table-wrapper',
        tableEl: '.i-combo-table',
        colorDiv: '.i-combo-color-box-div',
        keywordEl: '.i-combo-keyword',
        hiddenEl: '[type="hidden"]',
        infoDiv: '.i-combo-info-div',
        infoEl: '.i-combo-info',
        loadingCell: '.i-combo-info-loadingCell',
        countEl: '.i-combo-count',
        ofEl: '.i-combo-of',
        foundEl: '.i-combo-found'
    },

    /**
     * Template for use in case if combo runs in single-value mode
     */
    tplSingle: [
        '<div class="i-combo i-combo-form">',
            '<div class="i-combo-single x-form-text">',
                '<table class="i-combo-table"><tr>',
                    '<td class="i-combo-color-box-cell">',
                        '<div class="i-combo-color-box-div">',
                            '{selected.box}',
                        '</div>',
                    '</td>',
                    '<td class="i-combo-keyword-cell">',
                        '<div class="i-combo-keyword-div">',
                            '<input id="{me.id}-keyword" class="i-combo-keyword" autocomplete="off" {selected.style} type="text" lookup="{me.field.alias}" value="{selected.keyword}" no-lookup="{me.field.params.noLookup}" placeholder="{me.field.params.placeholder}"<tpl if="me.readOnly"> readonly="readonly"</tpl>/>',
                            '<input id="{me.id}-value" type="hidden" value="{selected.value}" name="{me.field.alias}" <tpl if="me.boolean">boolean="true"</tpl>/>',
                        '</div>',
                    '</td>',
                    '<td class="i-combo-infoCell">',
                        '<div class="i-combo-info-div">',
                            '<table class="i-combo-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="{pageUpDisabled}" page-btm-reached="false" satellite="{satellite}" changed="false"><tr>',
                                '<td class="i-combo-info-loadingCell"><img src="{[Indi.std]}/i/admin/combo-data-loading.gif"></td>',
                                '<td class="i-combo-info-countCell"><span class="i-combo-count"></span></td>',
                                '<td class="i-combo-info-ofCell"><span class="i-combo-of">{[Indi.lang.I_COMBO_OF]}</span></td>',
                                '<td class="i-combo-info-foundCell"><span class="i-combo-found"></span></td>',
                            '</tr></table>',
                        '</div>',
                    '</td>',
                '</tr></table>',
            '</div>',
        '</div>'
    ],

    /**
     * Template for use in case if combo runs in multiple-values mode
     */
    tplMultiple: [
        '<div class="i-combo i-combo-form">',
            '<div class="i-combo-multiple x-form-text<tpl if="me.grow != true"> i-combo-multiple-inlined</tpl>">',
                '<tpl if="me.grow != true"><div></tpl>',
                '<tpl for="selected.items">',
                    '<span class="i-combo-selected-item" selected-id="{id}"<tpl if="style">{style}<tpl elseif="font">style="{font}"</tpl>>',
                        '{box}{title}',
                        '<span class="i-combo-selected-item-delete"></span>',
                    '</span>',
                '</tpl>',
                '<div class="i-combo-table-wrapper">' +
                    '<table class="i-combo-table"><tr>',
                        '<td class="i-combo-color-box-cell">',
                            '<div class="i-combo-color-box-div">',
                                '{selected.box}',
                            '</div>',
                        '</td>',
                        '<td class="i-combo-keyword-cell">',
                            '<div class="i-combo-keyword-div">',
                                '<input id="{me.id}-keyword" class="i-combo-keyword" autocomplete="off" type="text" lookup="{me.field.alias}" value="" lookup="{me.field.params.noLookup}" placeholder="{me.field.params.placeholder}"<tpl if="me.readOnly"> readonly="readonly"</tpl>/>',
                                '<input id="{me.id}-value" type="hidden" value="<tpl if="selected.value">{selected.value}</tpl>" name="{me.field.alias}"/>',
                            '</div>',
                        '</td>',
                        '<td class="i-combo-infoCell">',
                            '<div class="i-combo-info-div">',
                                '<table class="i-combo-info" page-top="0" page-btm="0" fetch-mode="no-keyword" page-top-reached="{pageUpDisabled}" page-btm-reached="false" satellite="{satellite}" changed="false"><tr>',
                                    '<td class="i-combo-info-loadingCell"><img src="{[Indi.std]}/i/admin/combo-data-loading.gif"></td>',
                                    '<td class="i-combo-info-countCell"><span class="i-combo-count"></span></td>',
                                    '<td class="i-combo-info-ofCell"><span class="i-combo-of">{[Indi.lang.I_COMBO_OF]}</span></td>',
                                    '<td class="i-combo-info-foundCell"><span class="i-combo-found"></span></td>',
                                '</tr></table>',
                            '</div>',
                        '</td>',
                    '</tr></table>',
                '</div>',
                '<tpl if="me.grow != true"></div></tpl>',
                '<div class="i-combo-clear" style="clear: both;"></div>',
            '</div>',
        '</div>'
    ],

    /**
     * Init disabled options
     */
    initDisabledOptions: function() {
        var me = this, i;

        // If `disabledOptions` prop is not an array - setup it as empty array
        if (!Ext.isArray(me.disabledOptions)) me.disabledOptions = [];

        // Else convert each value within `disabledOptions` array to a string, for a better compatibility
        else for (i = 0; i < me.disabledOptions.length; i++) me.disabledOptions[i] = String(me.disabledOptions[i]);
    },

    /**
     * Provide usage of this.getValue() result as a way of getting submit value, instead of this.getRawValue
     *
     * @return {*}
     */
    getSubmitValue: function() {
        return this.getValue();
    },

    /**
     * Provide getValue()'s return value to be used instead of keywordEl.value, in case if combo is multi-select
     *
     * @return {*}
     */
    getRawValue: function() {
        return this.multiSelect ? this.getValue() + '' : this.callParent();
    },

    /**
     * Provide use of this.keywordEl as input element, that should be listened for focus/blur
     *
     * @return {*}
     */
    getFocusEl: function(){
        return this.keywordEl;
    },

    /**
     * Create custom picker component, with 'Ext.Panel' instead of 'xtype: boundlist' usage
     *
     * @return {*}
     */
    createPicker: function() {
        var me = this;

        // Create Ext.Panel object, for use a a picker
        return Ext.create('Ext.Panel', {
            cls: 'x-boundlist x-boundlist-default',
            border: 0,
            floating: true,
            maxHeight: me.getPickerMaxHeight(),
            autoScroll: true,
            listeners: {
                afterrender: function() {
                    this.body.on('scroll', function(){
                        me.keywordEl.focus();
                    });
                },
                show: function() {
                    me.rebuildComboData();
                }
            }
        })
    },

    /**
     * Get data 'title' property or whole data object, depending on dataShouldBeReturned is boolean true, by a given value
     *
     * @param value
     * @param dataShouldBeReturned
     * @return {*}
     */
    valueToRaw: function (value, dataShouldBeReturned) {
        var me = this, index = me.store.ids.indexOf(value), store = me.store;
        if (index == -1) index = me.store.ids.indexOf(parseInt(value));
        if (index == -1 && me.store.backup) {
            index = me.store.backup.options.ids.indexOf(value);
            if (index == -1) index = me.store.backup.options.ids.indexOf(parseInt(value));
            store = me.store.backup.options;
        }
        var data = store.data[index];
        return dataShouldBeReturned ? (data ? data : {}) : (data ? data.title : '');
    },

    /**
     * Get combo value
     *
     * @return {*}
     */
    getValue: function() {
        return this.value + '';
    },

    /**
     * Sets a data value into the field and runs the change detection and validation. Also applies any configured
     * {@link #emptyText} for text fields. To set the value directly without these inspections see {@link #setRawValue}.
     * @param {Object} value The value to set
     * @return {Ext.form.field.Text} this
     */
    setValue: function(value) {
        var me = this, data;

        // If combo is already rendered
        if (me.el) {

            // If combo is running in multiple-values mode
            if (me.multiSelect) {

                // Normalize me.value and `value` argument
                me.value = me.value + ''; value = value + '';

                // Detect difference between old value and new value
                var was = me.value ? me.value.split(',') : [], now = value ? value.split(',') : [],
                    remove = Ext.Array.difference(was, now), append = Ext.Array.difference(now, was);

                // Remove items that should be removed
                for (var i = 0; i < remove.length; i++) {

                    // Try to find .i-combo-selected-item-delete child node within the item that should be removed
                    var d = me.el.select('.i-combo-selected-item[selected-id="' + remove[i] +'"] .i-combo-selected-item-delete').first();

                    // If found - setup 'no-change' attribute to 'true' to prevent 'onHiddenChange' call and 'change'
                    // event listener firing (because those will be fired bit later) and do programmatically click
                    // on found .i-combo-selected-item-delete child, as there is already binded the listener that will
                    // do item deletion with all additional concomitant operations
                    if (d) d.attr('no-change', 'true').click();
                }

                // Append items that should be appended
                for (i = 0; i < append.length; i++) me.insertSelectedItem(append[i]);

                // Else if combo is running in single-value mode
            } else {

                // Get the whole option data object by option value
                data = me.valueToRaw(value, true);

                // Detect option color (style or box) and apply it
                me.color(data, value).apply();

                // Set up 'selectedIndex' attribute for keywordEl
                if (value !== null)me.getPicker().el.select('.x-boundlist-item:not(.x-boundlist-item-disabled)')
                    .each(function(el, c, index){
                        if (el.attr(me.name) == value.toString()) me.keywordEl.attr('selectedIndex', index+1);
                    });
                else me.keywordEl.attr('selectedIndex', '0');
            }

            // Setup value for hiddenEl element
            me.hiddenEl.val(value);

            // Call parent
            me.getNative().setValue.call(me, me.hiddenEl.val());

            // If combo is running in multiple-values mode is rendered - empty keyword input element
            if (me.multiSelect) me.keywordEl.dom.value = Ext.emptyString;

        // Call parent
        } else me.getNative().setValue.call(me, value);

        // Return combo itself
        return me;
    },

    /**
     * Walk through current instance's superclasses until found with $className property equal
     * to 'Ext.form.field.Picker', and return that superclass instance. Currently this method is used to
     * call Ext.form.field.Picker's native setValue method within current Indi.lib.form.field.Combo instance, and
     * all other instances, created using a classes, extended from 'Indi.lib.form.field.Combo' class, e.g 'Indi.combo.filter'
     * and 'Indi.combo.sibling'
     *
     * @return {*}
     */
    getNative: function() {
        var me = this, parent = me.superclass;
        while (parent.$className != 'Ext.form.field.Picker') {
            parent = parent.superclass;
        }
        return parent;
    },

    /**
     * Append new selected item to the list of selected items.
     * This function is for use only for 'multiSelect = true' combos
     *
     * @param key
     */
    insertSelectedItem: function(key) {
        var me = this, data;

        // Get the whole option data object by option value
        data = me.valueToRaw(key, true);

        // Detect option color (style or box) and declare 'css' object with empty 'color' property
        var color = me.color(data, key), css = {color: ''};

        // If color was detected in option data object, and that color is not a color-box color
        if (color.color && color.src != 'boxColor') css.color = color.color;

        // Set the width of .i-combo-table element to 1px, to prevent combo label jumping for cases if
        // adding the new item leads to height-increase of an area, that contains all currently selected
        // items
        me.tableEl.setWidth(1);

        // Append new selected item after the last existing selected item
        Ext.DomHelper.insertHtml('beforeBegin', me.wrapperEl.dom,
            '<span class="i-combo-selected-item" selected-id="'+key+'">'+
                color.box + color.title +
                '<span class="i-combo-selected-item-delete"></span>' +
                '</span>');

        // Get that newly inserted selected item as an already appended dom node
        var a = me.el.select('.i-combo-selected-item[selected-id="' + key +'"]').last();

        // Apply color
        a.css(css);

        // Bind a click event handler for a .i-combo-selected-item-delete
        // child node within newly appended item
        me.el.select('.i-combo-selected-item-delete').last().on('click', me.onItemDelete, me);

        // Execute javascript-code, assigned to appended item
        if (me.store.enumset) {
            var index = me.store['ids'].indexOf(key);
            if (index != -1 && me.store['data'][index].system.js) {
                Indi.eval(me.store['data'][index].system.js, me);
            }
        }

        // Adjust width of .i-combo-table element for it to fit all available space
        me.comboTableFit();
    },

    /**
     * Returns the input id for this field.
     */
    getInputId: function() {
        return this.inputId || (this.inputId = this.id + '-keyword');
    },

    /**
     * Constructor
     *
     * @param config
     */
    constructor: function(config) {
        var me = this;

        // Setup multiSelect and fieldSubTpl properties depending on config.field.storeRelationAbility value
        if (config.field.storeRelationAbility == 'many' || config.multiSelect) {
            me.multiSelect = true;
            me.fieldSubTpl = me.tplMultiple;
            if (!config.hasOwnProperty('hideTrigger')) me.hideTrigger = true;
        } else {
            me.fieldSubTpl = me.tplSingle;
        }

        // Setup `boolean` property
        me.boolean = config.field.storeRelationAbility == 'none';

        // Call parent
        me.callParent(arguments);

        // Setup `forceValidation` property. We should do this, because some combos may be dependent (have satellites)
        // and there can be situations then combo is not allowed to be blank, but if it is disabled (due to several
        // reasons, related to satellite-component's state) - validation won't run. So here we provide it to be forced
        me.forceValidation = !!(me.satellite != '0' || Ext.JSON.encode(me.considerOn) != '[]');

        // Setup noLookup property
        me.setupNoLookup();

        // Setup a link to current combo instance, within subTplData object
        me.subTplData.me = me;

        // If combo is running in single-value mode, setup keyword input element value
        if (!me.multiSelect)
            me.subTplData.selected.keyword
                = (me.subTplData.selected.input || me.subTplData.selected.title || '').replace(/"/g, '&quot;');

        // Setup `fetchedByPageUps` store property
        me.store.fetchedByPageUps = 0;

        // Init disabled options feature
        me.initDisabledOptions();
    },

    /**
     * Setup noLookup property within me.field.params object, is there was no such a property, or if there was
     * no such an object
     */
    setupNoLookup: function() {
        var me = this;

        // Setup params object, if it was not set
        me.field.params = me.field.params || {};

        // Setup noLookup property, if it was not set within me.field.params object
        me.field.params.noLookup = me.field.params.noLookup || me.store.enumset.toString();
    },

    /**
     * Clear satellited combo, for example in case if satellite (master) combo value was changed, so satellited combos
     * should be cleared before their data will be reloaded
     */
    clearSatellitedCombo: function() {
        var me = this;

        // Restore default values for auxiliary attributes
        me.infoEl.attr({
            'fetch-mode': 'no-keyword',
            'page-top-reached': 'false',
            'page-btm-reached': 'false',
            'page-top': 0,
            'page-btm': 0
        });
        me.keywordEl.attr('selectedIndex', 0);

        // Clear combo
        me.clearCombo();
    },

    /**
     * Function acts identical as same parent class function, but with one difference - if 'clear' argument is set to
     * boolean 'true', then combo will be not only disabled, but cleared, mean there will be zero-value set up for combo
     *
     * @param force
     */
    setDisabled: function(force, clear){
        var me = this, sComboName = me.infoEl.attr('satellite').toString(), sCombo = Ext.getCmp(me.bid() + sComboName);

        // If current combo has a satellite, and satellite combo is an also existing component
        if (sCombo) {

            // Get satellite value
            var sv = sCombo.getValue() + ''; sv = sv.length == 0 ? 0 : parseInt(sv);

            // If satellite value is 0, or 'force' argument is boolean 'true'
            if (sv == 0) {

                // Disable combo
                me.callParent([true]);

                // If 'clear' argument is boolean true
                if (clear) me.clearSatellitedCombo();


                // Else if satellite value is non-zero
            } else {

                // Disable/Enable combo
                me.callParent([force]);

                // If 'clear' argument is boolean true
                if (clear) me.clearSatellitedCombo();
            }

            // Else if current combo does not have a satellite
        } else {

            // Disable/Enable combo
            me.callParent([force]);

            // Clea combo, if it should be cleared
            if (clear) me.clearCombo();
        }
    },

    /**
     * Do the most general things
     */
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Setup keyboard handlers
        me.keywordEl.on({
            keyup: {
                fn: me.keyUpHandler,
                scope: me
            },
            keydown: {
                fn: me.keyDownHandler,
                scope: me
            }
        });

        // If options store is empty - disable combo
        if (me.store['ids'].length == 0) me.setDisabled(true);

        // Initially, we setup each combo as not able to lookup if there take place one of conditions:
        // 1. combo is used in enumset field and is not disabled ('non-disabled' condition is here due to css styles
        // conflict between input[disabled] and input[readonly]. ? - think about need)
        // 2. combo lookup ability was manually switched off by special param
        me.setReadonlyIfNeeded();

        // Set previous keyword input value as current keyword value at initialization
        me.keywordEl.attr('prev', me.keywordEl.val());

        // Bind a handler for 'click' event for .i-combo element
        me.comboEl.on('click', me.onKeywordClick, me);
        me.comboEl.on('click', me.onTriggerClick, me);

        // Adjust width of .i-combo-table element for it to fit all available space
        me.comboTableFit();

        // Bind a deletion click handler for .i-combo-selected-item-delete items
        me.el.select('.i-combo-selected-item-delete').on('click', me.onItemDelete, me);

        // Execute javascript code, if it was assigned to default selected option/options
        if (me.store.enumset) {
            if (me.multiSelect) {
                me.el.select('.i-combo-selected-item').each(function(el){
                    var index = me.store['ids'].indexOf(el.attr('selected-id'));
                    if (index != -1 && me.store['data'][index].system.js) {
                        Indi.eval(me.store['data'][index].system.js, me);
                    }
                });
            } else {
                var index = me.store['ids'].indexOf(me.hiddenEl.val());
                if (index != -1 && this.store['data'][index].system.js) {
                    Indi.eval(this.store['data'][index].system.js, me);
                }
            }
        }

        // Execute javascript code, assigned as an additional handler for 'select' event
        if (me.store.js) {
            if (typeof me.store.js == 'function') me.store.js.call(me); else Indi.eval(me.store.js, me);
        }
    },

    /**
     * Keyword element click handler
     *
     * @param e
     * @param dom
     */
    onKeywordClick: function(e, dom) {

        // Setup 'el' and 'me' shortcuts
        var me = this, el = me.keywordEl;

        // If there currently is no options - return
        if (me.hasActiveError() || me.disabled || el.hasCls('i-combo-selected-item-delete')) return;
    },

    /**
     * Trigger click handler
     */
    onTriggerClick: function() {
        var me = this;

        // If current combo is a filter-combo, and ctrl key is pressed - clear combo
        if (arguments.length && !me.readOnly && arguments[0].ctrlKey && (!me.store.enumset || me.xtype == 'combo.filter')) {
            me.clearCombo();
            return;
        }

        // If combo is not read-only and is not disabled
        if (!me.readOnly && !me.disabled
            && !Ext.get(Ext.EventObject.getTarget()).hasCls('i-combo-selected-item-delete')) {

            // Expand/collapse combo options boundlist
            if (!me.lastCollapsed || (new Date().getTime() - me.lastCollapsed > 250) || me.inEditor) {
                if (me.isExpanded) {
                    me.collapse();
                } else {
                    me.expand();
                }
            }
            me.focus(false, true);
        }
    },

    /**
     * Performs the alignment on the picker using the class defaults.
     *
     * The only difference with parent class's doAlign method is that here we change the element,
     * that picker should be aligned to - was me.inputEl, became me.inputCell.
     * We do that because current combo component multiSelect ability implementation assumes,
     * that inputEl is a html Input element with css 'float' property set to 'left', and if
     * we have at least one selected item in the combo, inputEl is positioned after that selected item,
     * i mean it's actual offsetX position is shifted, so it's a bad idea to still use it as an element
     * that picker should be aligned to
     *
     * @private
     */
    doAlign: function() {
        var me = this, picker = me.picker, aboveSfx = '-above', isAbove;

        me.picker.alignTo(me.inputCell, me.pickerAlign, me.pickerOffset);
        // add the {openCls}-above class if the picker was aligned above
        // the field due to hitting the bottom of the viewport
        isAbove = picker.el.getY() < me.inputEl.getY();
        me.bodyEl[isAbove ? 'addCls' : 'removeCls'](me.openCls + aboveSfx);
        picker[isAbove ? 'addCls' : 'removeCls'](picker.baseCls + aboveSfx);
    },

    // @inheritdoc
    expand: function() {
        var me = this, selected;

        // Prevent combo from being expanded, in case if number of currently selected options is greater or equal
        // than `maxSelected` value
        if (me.multiSelect && me.maxSelected && me.getValue() && me.maxSelected <= me.getValue().split(',').length) {
            Ext.Msg.show({
                title: Indi.lang.I_MSG,
                msg: me.maxSelectedText + ' ' + me.maxSelected,
                icon: Ext.Msg.WARNING,
                buttons: Ext.Msg.OK
            });
            return false;
        }

        // Call parent
        me.callParent(arguments);
        if (me.isInfoShowable()) me.infoEl.addCls('i-combo-info-expanded');
        if (selected = me.getPicker().body.select('.x-boundlist-item-over').first())
            selected.scrollIntoView(me.getPicker().body)
    },

    // @inheritdoc
    collapse: function() {
        var me = this;
        this.callParent(arguments);
        me.lastCollapsed = new Date().getTime();
        if (me.isInfoShowable()) me.infoEl.removeCls('i-combo-info-expanded');
    },

    isInfoShowable: function() {
        var me = this, notShowable;

        notShowable = me.keywordEl.attr('no-lookup') == 'true'
            || me.store.enumset
            || me.found() <= me.optionsOnPage;

        return !me.disabled && (me.infoEl.attr('fetch-mode') == 'keyword' || !notShowable);
    },

    /**
     * This is an extraction, having the aim to be able to setup a different logic in Indi.combo.filter
     */
    setReadonlyIfNeeded: function() {
        var me = this;
        if ((me.keywordEl.attr('disabled') != 'disabled' && me.store.enumset && !me.multiSelect)) {
            me.keywordEl.attr('readonly', 'readonly');
        }
    },

    /**
     * Alias for clearCombo(), for compatibility.
     *
     * @return {*}
     */
    clearValue: function() {
        return this.clearCombo();
    },

    /**
     * Clear combo's keyword and value
     */
    clearCombo: function() {
        var me = this;

        // If current combo is not clearable - return
        if (!me.isClearable()) return me;

        // If combo is rendered
        if (me.rendered) {

            // Remove color-box
            me.colorDiv.setHTML('');

            // Remove color
            me.keywordEl.setStyle({color: ''});
        }

        // Erase keyword
        me.setRawValue('');

        // Clear combo hidden value
        me.clearComboValue();

        // Return
        return me;
    },

    /**
     * Check if current combo can be cleared.
     *
     * Unclearable combos:
     * 1. Boolean combos (e.g combo is representing a checkbox)
     * 2. Single enumset combos
     *
     * So, if current combo is not of any of these types - it is clearable
     *
     * @return {Boolean}
     */
    isClearable: function() {
        return !this.boolean && (!this.store.enumset || this.multiSelect);
    },

    clearComboValue: function() {
        var me = this;

        // If combo is already rendered
        if (me.rendered) {

            // If combo is multiple, we fire 'click' event on each .i-combo-selected-item-delete item, so hidden
            // value will be cleared automatically
            if (me.multiSelect) me.el.select('.i-combo-selected-item-delete').attr('no-change', 'true').click();

            // Else if combo is single and is not boolean, we set it's value to 0, '' otherwise
            else me.hiddenEl.val(0);

            // Call setValue
            me.getNative().setValue.call(me, me.hiddenEl.val());

        // Else
        } else me.getNative().setValue.call(me, me.multiSelect ? '' : 0);
    },

    /**
     * Rebuild html of options list of combo data, apply some styles, props, attrs and events
     */
    rebuildComboData: function() {
        var me = this;

        // Set initial 'index' and 'selectedIndex' attribs values
        if (me.keywordEl.attr('selectedIndex') == undefined) me.keywordEl.attr('selectedIndex', 0);

        // Rebuild html for options
        var html = me.suggestions(me.store);

        // Update picker panel contents
        me.getPicker().update(html);

        // If picker contents is not empty - Bind 'hover' and 'click' event handlers for boundlist items
        if (html) me.bindItemHoverClick();
    },

    // @inheritdoc
    getPicker: function() {
        var me = this;
        if (!me.picker) {
            me.picker = me.createPicker();
            me.picker.doAutoRender();
            me.rebuildComboData();
            me.getPicker().hide();
        }
        return me.picker;
    },

    /**
     * Determine options height. This value will be involved in a number of calculations
     *
     * @param mode
     * @return {String}
     */
    getOptionHeight: function(mode) {
        var me = this, height = parseInt(me.field.params.optionHeight)
            ? parseInt(me.field.params.optionHeight) : me.defaultOptionHeight;

        return me.defaultOptionHeight == height
            ? (mode == 'style' ? '' : height)
            : (mode == 'style' ? 'height: ' + height + 'px;' : height);
    },

    /**
     * Get picker maximum height
     *
     * @return {String}
     */
    getPickerMaxHeight: function() {
        var me = this, height = me.getOptionHeight();
        return (me.optionsOnPage * me.defaultOptionHeight/height) * height + 1
    },

    /**
     * Build options html
     *
     * @param json Source data for html building
     * @return {String} html-code for options list
     */
    suggestions: function(json){
        var me = this, name = me.name, items = [],
            groups = json.optgroup ? json.optgroup.groups : {none: {title: 'none'}},
            groupIndent = json.optgroup ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '',
            disabledCount = 0, color = {}, item, height = me.getOptionHeight('style'), css, disabledOptionsCount = 0;

        // Foreach options groups
        for (var j in groups) {
            if (j != 'none') {

                // Reset inline styles array
                css = [];

                // Open <li>
                item = '<li class="x-boundlist-item x-boundlist-item-group x-boundlist-item-disabled"';

                // Apply css color, if it was passed within store. Currently this feature is used for
                // cases then item title got from database was something like
                // <span style="color: red">Some title</span>. At such cases, php code which is preparing
                // combo data, strips that html from option, but detect defined color and
                // store it in ...['data'][i].system['color'] property
                if (groups[j].system && groups[j].system['color'] && typeof groups[j].system['color'] == 'string')
                    css.push('color: ' + groups[j].system['color'] + ';');

                // Insert inline styles
                if (css.length)  item += ' style="' + css.join(' ') + '"';

                // Close <li>
                item += '>';

                // Detect and apply color to '<li>', and append '<li>' to 'items' array
                color = me.color(groups[j], j);
                item += color.box + color.title + '</li>';
                items.push(item);
            }

            // Foreach data item
            for (var i = 0; i < json['ids'].length; i++) {

                // If data item is owner by current group
                if (json['ids'][i] != undefined && (j == 'none' || json['data'][i].system.group == j)) {

                    // Classes for option
                    var cls = ['x-boundlist-item'], indent = groupIndent + '';

                    // Reset inline styles array
                    css = [];

                    // Open <li>, and append value attribute
                    item = '<li' + ' ' + name + '="' + json['ids'][i] + '"';

                    // Additional attributes for option
                    if (json.attrs && json.attrs.length) {
                        for (var n in json['data'][i].attrs) {
                            item += ' ' + n + '="' + json['data'][i].attrs[n] + '"';
                        }
                    }

                    // If data option is disabled
                    if (json['data'][i].system && json['data'][i].system['disabled']
                        || me.disabledOptions.indexOf(String(json['ids'][i])) != -1) {

                        // Append x-boundlist-item-disabled class to the css classes array
                        cls.push('x-boundlist-item-disabled');

                        // We are counting disabled options, to further valid calculations
                        disabledCount++;

                        // Else if data option is in the internal list of disabled option
                        if (me.disabledOptions.indexOf(String(json['ids'][i])) != -1) disabledOptionsCount++;
                    }

                    // If one this option is selected
                    if (me.hiddenEl.val() == json['ids'][i]) {

                        // We need to cover situation then we had two searches: at first search some element was
                        // selected and at next search same element is disabled, so while constructing html we
                        // shoud not mark disabled element as selected
                        if (cls.indexOf('x-boundlist-item-disabled') == -1) {

                            // Mark as selected
                            cls.push('x-boundlist-item-over');
                        }
                    }

                    // Color detection
                    color = json['data'][i].option ? null : me.color(json['data'][i], json['ids'][i]);

                    // Append css classes list as 'class' attribute for an option
                    if (cls.length) item += ' class="' + cls.join(' ') + '"';

                    // Apply css color, if it was passed within store. Currently this feature is used for
                    // cases then item title got from database was something like
                    // <span style="color: red">Some title</span>. At such cases, php code which is preparing
                    // combo data, strips that html from option, but detect defined color and
                    // store it in ...['data'][i].system['color'] property
                    if (color && color.src == 'color' && color.color) css.push('color: ' + color.color);

                    // Prepend option title with indent if needed
                    if (json['data'][i].system && json['data'][i].system['indent']
                        && typeof json['data'][i].system['indent'] == 'string')
                        indent += json['data'][i].system['indent'];

                    // Setup left padding, as it will be used to represent the indents
                    if (indent) css.push('padding-left: ' + (indent.length/6 * me.nbspPx) + 'px;');

                    // Setup height
                    if (height) css.push(height);

                    // Insert inline styles
                    if (css.length)  item += ' style="' + css.join(' ') + '"';

                    // Enclose opening <li> tag
                    item += '>';

                    // If 'option' property exists (mean that 'template' combo param is used),
                    // we use 'option' property contents as <li> inner contents, instead of 'title' contents
                    if (json['data'][i].option) {
                        item += json['data'][i].option;
                    } else {
                        item += color.box;
                        item += color.title;
                    }

                    // Close <li> tag
                    item += '</li>';

                    // Apend item to 'items' array
                    items.push(item);
                }
            }
        }

        // If optgroups are used and we are deaing with items tree, we should distibute items by optgroups,
        // but insert all parents for options, if these parents not in same groups as child options
        if (json.optgroup != undefined && json.tree) items = me.appendNotSameGroupParents(items, json);

        // Stat info
        me.countEl.removeCls('i-combo-count-visible').update(Indi.numberFormat(json['ids'].length - disabledCount));
        me.foundEl.update(Indi.numberFormat(json['found'] - disabledOptionsCount));

        // Check if all possible options are already exist in store
        // and append/remove .i-combo-info-fetched-all class
        if (json['ids'].length - disabledCount == json['found'] - disabledOptionsCount) {
            me.infoEl.addCls('i-combo-info-fetched-all');
        } else {
            me.infoEl.removeCls('i-combo-info-fetched-all');
        }

        // Get the html blob
        var html = items.length ? '<ul>'+items.join("\n")+'</ul>' : '';

        // Instantiate html blob as a new (uninserted) dom node
        var dom = Ext.DomHelper.createDom({tag: 'ul', html: items.join("\n")});

        // Get the array of 'li' dom elements
        var liA = Ext.query('li', dom);

        // If options length is non-zero
        if (liA.length) {

            // Get current selectedIndex
            var currentSelectedIndex = parseInt(me.keywordEl.attr('selectedIndex'));

            // If current selectedIndex is 0, calculate and setup it
            if (currentSelectedIndex == 0) {

                // We reset disabledCount here, because now, we should count all disabled html-items, not only json-items
                // because now in options html there can be another disabled options, appeared as a result of using 'group'
                // (mean 'optgroup') ability and as result of me.appendNotSameGroupParents() function execution
                disabledCount = 0;
                var selectedIndex = 0;
                var selectedFound = false;
                liA.forEach(function(li, index){
                    if (Ext.fly(li).hasCls('x-boundlist-item-over')) {
                        selectedIndex = index - disabledCount + 1;
                        selectedFound = true;

                        // We increment disabledCount until selected value is found
                    } else if (selectedFound == false && Ext.fly(li).hasCls('x-boundlist-item-disabled')) {
                        disabledCount++;
                    }
                });
                me.keywordEl.attr('selectedIndex', selectedIndex);
            }

            // Else if options length is zero
        } else me.keywordEl.attr('selectedIndex', 0);

        // Return html blob
        return html;
    },

    /**
     * Special function for implementing (if need) a custom logic of how options should be coloured
     *
     * @param data
     * @param value
     * @param info
     * @return {*}
     */
    getOptionColor: function(data, value, info) {
        return info;
    },

    /**
     * Try to find a color declaration in option title or option value or option.system.boxColor,
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
            info.src = 'value'; info.color = color[1];
        } else if (color = info.title.match(this.colorReg)) {
            info.src = 'title'; info.color = color[1];
        } else if (data.system) {
            if (data.system['boxColor'] && typeof data.system['boxColor'] == 'string') {
                info.src = 'boxColor'; info.color = data.system['boxColor'];
            } else if (data.system['color'] && typeof data.system['color'] == 'string') {
                info.src = 'color'; info.color = data.system['color'];
            }
        }

        // Apply custom logic
        info = me.getOptionColor(data, value, info);

        // If contains, we prepare a color box element, to be later inserted in dom - before keyword field
        if (info.color) {

            // Build color box
            if (['boxColor', 'value'].indexOf(info.src) != -1)
                info.box = '<span class="i-combo-color-box" style="background: ' + info.color + ';"></span> ';

            // If color was got from title (means that title was in format hue#rrggbb), set title as same color
            // but without hue
            if (info.src == 'title') info.title = info.color;
        }

        // Setup an internal 'apply' function that will create/update/remove .i-combo-color-box element
        // and setup/update/remove css 'color' definition
        info.apply = function() {

            // Update color box/style
            me.colorDiv.update(this.box);
            me.keywordEl.css('color', ['boxColor', 'value'].indexOf(this.src) == -1 ? this.color : '');

            // Set keyword text
            me.keywordEl.val(this.title);
            me.keywordEl.attr('prev', this.title);
        }

        // Return `info` object
        return info;
    },

    /**
     * If optgroups is used and we are dealing with items tree, we should distibute items by optgroups,
     * but insert all parents for options, if these parents not in same groups as child options
     *
     * @param items
     * @param json
     * @return []
     */
    appendNotSameGroupParents: function(items, json) {
        var me = this, name = me.name, i;

        // Store info about parents that were aready added, to prevent adding them more that once
        var addedParents = [];

        // Html for seaching <li> of parent options
        var html = '<ul>' + items.join('') + '</ul>';

        // Foreach element in 'items' array
        for (i = 0; i < items.length; i++) {

            // If item is a option, not optgroup
            if (Indi.fly(items[i]).attr(name)) {

                // Get some basic data about current option
                var id = parseInt(Indi.fly(items[i]).attr(name));
                var index = json['ids'].indexOf(id);
                var group = json['data'][index].system.group;
                var parentId = parseInt(json['data'][index].system.parentId);

                // We check all-level parents of current option
                while (parentId) {

                    // Get index of parent option within json['ids']
                    var parentIndex = json['ids'].indexOf(parentId);

                    // If we are dealing with page of that was started from certain selected option,
                    // there is a possibility that parent option can be not found
                    if (parentIndex != -1) {

                        // Get group of parent option
                        var parentGroup = json['data'][parentIndex].system.group;

                        // If groups of current option and parent option do not match, we add parent
                        if (group != parentGroup && addedParents.indexOf(parentId) == -1) {

                            // Get html for parent option
                            var parentOption = Indi.fly(html).select('li['+name+'="'+parentId+'"]').first().
                                addCls('x-boundlist-item-disabled').removeCls('x-boundlist-item-over').dom.outerHTML;

                            // Insert parent in certain position within options
                            items.splice(i, 0, parentOption);

                            // Collect added parents
                            addedParents.push(parentId);
                        }

                        // Replace parentId for next upper level check
                        parentId = parseInt(json['data'][parentIndex].system.parentId);
                    } else break;
                }
            }
        }

        // After parents were found and used for insertion in not-same groups, there is a possibility
        // that some of them not more needed

        // Array for colecting needed options indexes
        var neededIndexes = [];

        // Variable for stepping up once non-disabled option is catched
        var groupIndex = 0, reg, level, previousLevel;
        for (i = 0; i < items.length; i++) {

            // We do this action only if it is a non-disabled option
            if (!Indi.fly(items[i]).hasCls('x-boundlist-item-disabled')) {

                // Collect needed ids
                neededIndexes.push(i);

                // We check all-level parents of current option.
                reg = items[i].match(/<li[^>]+style="[^"]*padding-left: ([0-9]+)px;[^"]*"[^>]*>/);
                level = reg ? parseInt(reg[1])/(me.nbspPx*5) : 0;
                for (var j = i - 1; j >= groupIndex; j--) {
                    reg = items[j].match(/<li[^>]+style="[^"]*padding-left: ([0-9]+)px;[^"]*"[^>]*>/);
                    previousLevel = reg ? parseInt(reg[1])/(me.nbspPx*5) : 0;
                    if (previousLevel < level) {
                        if (neededIndexes.indexOf(j) == -1) {
                            neededIndexes.push(j);
                        }
                    }
                }

                // Else if current item is not only disabled, but also is a group
            } else if (Indi.fly(items[i]).hasCls('x-boundlist-item-group')) {

                // Setup groupIndex variable value
                groupIndex = i;
            }
        }

        // Get needed items by needed indexes
        var neededItems = [];
        for (i = 0; i < items.length; i++) {
            if (neededIndexes.indexOf(i) != -1) {
                neededItems.push(items[i]);
            }
        }

        // Return
        return neededItems;
    },

    /**
     * Adjust .i-combo-table element width, so it will use all available space within selected item area,
     * after each append of newly selected item to list of selected items or delete it from list
     * Function is used only if combo is running in multiple-values mode
     */
    comboTableFit: function() {
        var me = this;

        // We do not setup widths for single-value combos
        if (!me.multiSelect) {

            // Restore default width for tableEl, as it may had been set up as '1px' previously
            if (me.tableEl) me.tableEl.setStyle('width', '');

            // Return
            return;
        }

        // Define auxiliary variables
        var staticDecrease = 0, dynamicDecrease = 0;

        // Here we do width adjust using a Ext.defer, because there is some strange thing happens with the
        // results of comboEl.width() call. For some reason, outside the setTimeout body it gives result, that
        // differs from the same one, got inside. I guess it is caused by some browser rendering particularity
        Ext.defer(function(){
            if (!me.comboEl) return;
            staticDecrease += parseInt(me.comboEl.css('padding-right')) + parseInt(me.comboEl.css('padding-left'));
            staticDecrease += parseInt(me.multipleEl.css('margin-right')) + parseInt(me.multipleEl.css('margin-left'));
            staticDecrease += parseInt(me.multipleEl.css('padding-right')) + parseInt(me.multipleEl.css('padding-left'));
            if (me.multipleEl.select('.i-combo-selected-item').getCount()) {
                var last = me.multipleEl.select('.i-combo-selected-item').last();
                dynamicDecrease += last.getOffsetsTo(me.comboEl)[0];
                dynamicDecrease += last.getWidth();
            }
            staticDecrease += (me.hideTrigger && last ? 1 : 0);

            var w = me.multipleEl.getWidth(true) - staticDecrease - dynamicDecrease;
            var fraction = Math.ceil((me.multipleEl.getWidth(true) - staticDecrease) * 0.25);
            if (me.grow) {
                me.tableEl.setWidth(w);
                if ((last && (last.getOffsetsTo(me.comboEl)[0] > me.keywordEl.getOffsetsTo(me.comboEl)[0])) ||
                    me.tableEl.getWidth() == me.multipleEl.getWidth(true)) {
                    me.tableEl.setWidth(me.multipleEl.getWidth(true) - 1 + (me.hideTrigger ? 1 : 0));
                }
            } else {
                var usage = 0; me.multipleEl.select('.i-combo-selected-item').each(function(el){
                    usage += el.getWidth();
                });
                if (fraction > me.multipleEl.getWidth(true) - staticDecrease - usage + (usage ? 1 : 0)) {
                    me.tableEl.up('div[class=""]').css({
                        position: 'relative',
                        right: (fraction - (me.multipleEl.getWidth(true) - staticDecrease - usage + (usage ? 1 : 0))) + 'px'
                    });
                    me.tableEl.setWidth(fraction);
                } else {
                    me.tableEl.up('div[class=""]').css({
                        position: 'relative',
                        right: '0px'
                    });
                    me.tableEl.setWidth(me.multipleEl.getWidth(true) - staticDecrease - usage + (usage ? 1 : 0));
                }
                //me.tableEl.setWidth(fraction);
                //me.keywordEl.scrollIntoView(me.comboInner, true, true);
            }
        }, 10);
    },

    /**
     * Handler for item deletion from the list of selected items
     *
     * @param evt
     * @param dom
     */
    onItemDelete: function(evt, dom){
        var me = this;

        // If combo is disabled, no selected item deletion should be performed
        if (me.disabled && !Ext.get(dom).attr('no-change')) return;

        // Get needed .i-combo-selected-item-delete element
        var deleteEl = Ext.get(dom).hasCls('i-combo-keyword')
            ? me.el.select('.i-combo-selected-item-delete').last()
            : Ext.get(dom);

        // Get .i-combo-selected-item element, appropriate for .i-combo-selected-item-delete element,
        // that was found bit ealier
        var itemEl = deleteEl.up('.i-combo-selected-item');

        // Set up auxilary variables
        var selected = me.hiddenEl.val().split(',');
        var deleted = itemEl.attr('selected-id');
        var index = selected.indexOf(deleted);

        // Unset item from selected items array
        selected.splice(index, 1);

        // Check if me.onHiddenChange() handler for current combo should not be fired. Currently there is a only one
        // case there this feature is used - in case if current combo is multiple and have a satellite, which
        // value has just changed, so current combo data will should be reloaded and currently selected options
        // should be removed. Usually, me.onHiddenChange() fires each time when .i-combo-selected-item-delete was clicked
        // and if we clicked on several items with such class, me.onHiddenChange() handler will be fired several times,
        // not once - as we need in that situation. So noChange variable will prevent me.onHiddenChange() handler firing.
        // me.onHiddenChange() handler will be fired, but only once, and separately from current (current - mean click
        // handler for .i-combo-selected-item-delete items) handler
        var noChange = deleteEl.attr('no-change') ? true : false;

        // Remove visual representation of deleted item from combo
        itemEl.remove();

        // Adjust width of .i-combo-table element for it to fit all available space
        me.comboTableFit();

        // Remove attributes
        if (me.store.attrs && me.store.attrs.length) {
            for(var n = 0; n < me.store.attrs.length; n++) {
                me.hiddenEl.removeAttr(me.store.attrs[n]+'-'+deleted);
            }
        }

        // Set the updated value and call 'onHiddenChange' function
        me.hiddenEl.val(selected.join(','));

        if (noChange == false) me.getNative().setValue.call(me, me.hiddenEl.val());
    },

    /**
     * Do several thins after keyword was erased
     *
     * @param mode
     */
    keywordErased: function(mode) {
        var me = this;

        // Correct value of 'prev' attr
        me.keywordEl.attr('prev', me.keywordEl.val());

        // We need to fire 'change' event only if combo is running in single-value mode.
        // In that mode no keyword = no value. But in multiple-value mode combo may have a
        // value without a keyword. Also, we fire change only if previous value was not 0
        if (!me.multiSelect) me.clearCombo();

        // We restore combo state, that is had before first run of 'keyword' fetch mode
        if (me.store.backup) {
            me.infoEl.remove();
            me.infoDiv.update(me.store.backup.info);
            me.infoEl = me.infoDiv.first(me.renderSelectors.infoEl);
            me.countEl = me.infoDiv.select(me.renderSelectors.countEl).first();
            me.foundEl = me.infoDiv.select(me.renderSelectors.foundEl).first();
            me.ofEl = me.infoDiv.select(me.renderSelectors.ofEl).first();
            var restore = Indi.copy(me.store.backup.options);
            me.store = {};
            me.store = restore;
        }

        // If user erases wrong keyword, remove 'i-combo-keyword-no-results' class and show options list, that was available
        // before first run of 'keyword' fetch mode
        if (me.keywordEl.hasCls('i-combo-keyword-no-results')) {
            me.keywordEl.removeCls('i-combo-keyword-no-results');
            me.clearInvalid();
        }

        // Rebuild combo and show it
        if (mode == 'only-erased-not-selected' && this.hideOptionsAfterKeywordErased == false) {
            me.expand();
            Ext.defer(function(){me.keywordEl.focus();}, 10);

            // Rebuild combo but do not show at this time
        } else if (mode == 'selected-but-found-with-lookup'){
            me.rebuildComboData();
        }
    },

    /**
     * Set keyboard keys handling, related to data fetch (lookup, results pagination, etc)
     *
     * @param event Used to get code of pressed key on keyboard
     */
    keyUpHandler: function (event) {
        var me = this, eo = Ext.EventObject, k = eo.getKey();

        // If combo is read-only - return
        if (me.readOnly) return;
        
        // We will be fetching results with a timeout, so fetch requests will be
        // sent after keyword typing is finished (or seems to be finished)
        clearTimeout(me.timeout);

        // Variable for detecting fetch mode. Fetch mode can be 'keyword' and 'no-keyword', and is 'no-keyword' by default
        var fetchMode = me.infoEl.attr('fetch-mode');

        // Setup variables for range of pages that's results are already fetched and displayed in combo as options
        // This variables will be used if current fetchMode is 'no-keyword', because for 'keyword' fetchMode will be
        // used different logic
        var pageTop = parseInt(me.infoEl.attr('page-top'));
        var pageBtm = parseInt(me.infoEl.attr('page-btm'));

        // Variable for detection if next|prev page of results should be fetched
        var moreResultsNeeded = event.keyCode.toString().match(/^(34|33)$/) && me.getPicker().el.attr('more') && me.getPicker().el.attr('more').toString().match(/^(upper|lower)$/) ? me.getPicker().el.attr('more') : false;

        // We are detecting the change of keyword value by using 'keyup' event, instead of 'input' event, because 'input'
        // is supported by not al browsers. But with 'keyup' event there is a small problem - if we will be inputting
        // too fast
        var tooFastKeyUp = me.store.lastTimeKeyUp && (new Date().getTime() - me.store.lastTimeKeyUp < 200);

        // Here we explicitly set up 'prev' variable as empty string in case if 'prev' arrtibute of keyword element is null
        // There was no need in such hack, because earlier this (combo) component was using jQuery instead of Ext
        var prev = me.keywordEl.attr('prev') || '';

        // Variable for detection if keyword was changed and first page of related results should be fetched
        var keywordChanged = ((prev != me.keywordEl.val() || tooFastKeyUp) && me.keywordEl.val() != '' && (!Ext.EventObject.isSpecialKey() || (k == eo.BACKSPACE || k == eo.DELETE)));

        // Check if keyword was emptied
        var keywordChangedToEmpty = ((prev != me.keywordEl.val() || tooFastKeyUp) && me.keywordEl.val() == '' && (!Ext.EventObject.isSpecialKey() || (k == eo.BACKSPACE || k == eo.DELETE)));

        // Renew lastTimeKeyUp
        if (!Ext.EventObject.isSpecialKey() || (k == eo.BACKSPACE || k == eo.DELETE)) me.store.lastTimeKeyUp = new Date().getTime();

        // If keyword was at least once changed, we switch fetch mode to 'keyword'.
        // We need to take it to attention, because PgUp fetching is impossible in case
        // if we have no keyword
        if (keywordChanged) {

            // Here we have a situation when we are going to run 'keyword' fetch mode at first time.
            // At this moment we backup current me.store object - we will need it if keyword
            // will be changed to '' (empty string), and in this case it will be user-friendly to display last
            // available results got by 'no-keyword' fetch mode, and we will be able to restore them from backup
            if (me.infoEl.attr('fetch-mode') == 'no-keyword') {
                var backup = {
                    options: Indi.copy(me.store),
                    info: me.infoEl.dom.outerHTML
                };
                me.store.backup = backup;
            }

            // Update fetch mode and remember the keyword for further changes detection
            me.infoEl.attr('fetch-mode', 'keyword');
            me.infoEl.attr('keyword', me.keywordEl.val());

            // Temporary strip red color from input, as we do not know if there will be at least
            // one result related to specified keyword, and if no - keyword will be coloured in red
            me.keywordEl.removeCls('i-combo-keyword-no-results');
            me.clearInvalid();

            // Reset selected index
            me.keywordEl.attr('selectedIndex', 0);

            // Scroll options list to the most top
            me.getPicker().body.scrollTo({x: 0, y: 0});
        }

        // We will fetch data only if keyword was changed or if next|prev page of results
        // related to current keyword should be fetched
        if (keywordChanged || moreResultsNeeded) {

            // Get field satellite
            var satellite = me.infoEl.attr('satellite');

            // Get satellite as Ext combo object
            var he = Ext.getCmp(me.bid() + satellite);

            // Prepare data for fetch request
            var data = {};

            // Pass satellite value only if it was at east one time changed. Otherwise default satellite value will be used
            if (he && he.infoEl.attr('changed') == 'true') data.satellite = he.hiddenEl.val();

            // If we are paging
            if (moreResultsNeeded) {

                // If previous page needed
                if (event.keyCode == '33') {

                    // If keyword was at least once changed
                    if (fetchMode == 'keyword') {
                        me.keywordEl.attr('selectedIndex', 1);
                        me.keyDownHandler(33);
                        return;

                        // Else if we are still walking through pages of all (not filtered by keyword) results
                    } else if (fetchMode == 'no-keyword') {

                        // If top border of range of displayed pages is not yet 1
                        // we will be requesting decremented page. Attribute 'page-top',
                        // there pageTop variable value was got, will be decremented
                        // later - after request will be done and results fetched
                        if (me.infoEl.attr('page-top-reached') == 'false') {
                            data.page = pageTop - 1;

                            // Otherwise, if top border of range of displayed pages is already 1
                            // so it is smallest possible value and therefore we won't do any request,
                            // and we only should move selection to first option
                        } else {

                            me.keywordEl.attr('selectedIndex', 1);
                            me.keyDownHandler(33);
                            return;
                        }
                    }

                    // If next page needed
                } else if (event.keyCode == '34') {

                    // If keyword was at least once changed
                    if (fetchMode == 'keyword') {
                        data.keyword = me.infoEl.attr('keyword');
                    }

                    // If requested page of results is out of range of already fetched options
                    // and bottom border of range of displayed pages is not already reached
                    if (me.infoEl.attr('page-btm-reached') == 'false') {
                        data.page = pageBtm + 1;

                        // Otherwise, if bottom border of range of displayed pages is already reached,
                        // so it is biggest possible value for page number and therefore we won't do any request
                    } else {
                        me.keywordEl.attr('selectedIndex', me.getPicker().el.select('.x-boundlist-item:not(.x-boundlist-item-disabled)').getCount());
                        me.keyDownHandler(34);
                        return;
                    }
                }
                data.more = moreResultsNeeded;

                // Fetch request
                me.remoteFetch(data);

                // If we are searching by keyword
            } else if (event.keyCode != '33') {

                // Setup request keyword
                data.keyword = me.keywordEl.val();

                // Setup previous keyword
                me.keywordEl.attr('prev', me.keywordEl.val());

                // Setup range borders as they were by default
                me.infoEl.attr('page-top', '0');
                me.infoEl.attr('page-btm', '0');

                // Here we check if all possible results are already fetched, and if so, we will use local fetch
                // instead of remote fetch, so we will search keyword within currently loaded set of options. Such
                // scheme is useful for situations then number of results is not too large, and all results ARE already
                // collected (initially, by first combo load, and/or by additional hoarding while upper/lower pages fetching)
                if (me.store.backup &&
                    me.store.backup.options.data.length >= parseInt(me.store.backup.options.found) &&
                    data.keyword.length) {
                    me.localFetch(data);
                } else {
                    me.timeout = Ext.defer(me.remoteFetch, 500, me, [data]);
                }
            }
        }

        // If keyword was changed to empty we fire 'change' event. We do that for being sure
        // that dependent combos (combos that are satellited by current combo) are disabled. Also,
        // after keyword was changed to empty, hidden value was set to 0, so we should call me.onHiddenChange() anyway
        // Note: 'change' event firing is need only if combo is running in non-multiple mode.
        if (keywordChangedToEmpty) {

            // Hide options ist
            me.collapse();

            // Do some things after keyword was erased
            me.keywordErased('only-erased-not-selected');
        }
    },

    /**
     * Converts a given string to version, representing this string as is it was typed in a different keyboard
     * layout
     *
     * @param string
     * @return string
     */
    convertWKL: function(string){

        // Define object, containing characters that are located on the
        // same keyboard buttons, but within another keyboad layouts
        var kl = {

            // Define an array for english alphabetic characters
            en: ['~','Q','W','E','R','T','Y','U','I','O','P','{','}',
                'A','S','D','F','G','H','J','K','L',':','"',
                'Z','X','C','V','B','N','M','<','>',

                '`','q','w','e','r','t','y','u','i','o','p','[',']',
                'a','s','d','f','g','h','j','k','l',';',"'",
                'z','x','c','v','b','n','m',',','.'],

            // Define an array for russian alphabetic characters
            ru: ['Ё','Й','Ц','У','К','Е','Н','Г','Ш','Щ','З','Х','Ъ',
                'Ф','Ы','В','А','П','Р','О','Л','Д','Ж','Э',
                'Я','Ч','С','М','И','Т','Ь','Б','Ю',

                'ё','й','ц','у','к','е','н','г','ш','щ','з','х','ъ',
                'ф','ы','в','а','п','р','о','л','д','ж','э',
                'я','ч','с','м','и','т','ь','б','ю']
        }

        // Define a variable for converted equivalent, and index variable
        var converted = '', j, names = Object.keys(kl);

        // For each character within given string find its equvalent and append to 'converted' variable
        for (var i = 0; i < string.length; i++) {

            // Get character
            var c = string.substr(i, 1);

            // Define/reset and detect character source keyboard layout, and reset destination layout
            for (var k = 0, src = '', dst = ''; k < names.length; k++)
                if (kl[names[k]].indexOf(c) != -1)
                    src = names[k];

            // If no source was detected - try another next character
            if (!src) converted += c; else {

                // If source layout differs from current language
                // - setup current language as destination layout
                if (src != Indi.lang.name) dst = Indi.lang.name;

                // Else if source layout is 'ru' - setup destination layout as 'en'
                else if (src == 'ru') dst = 'en';

                // Get converted character
                if (dst) converted += kl[dst][kl[src].indexOf(c)];
            }
        }

        // Return keyword value, converted to a different keyboard layout
        return converted;
    },

    /**
     * Set keyboard keys handling (Up, Down, PgUp, PgDn, Esc, Enter), related to visual appearance
     *
     * @param evt
     * @return {Boolean}
     */
    keyDownHandler: function(evt){

        // Setup 'code' and 'name' variables
        var me = this, name = me.name, code = Ext.isNumeric(evt) ? evt : evt.keyCode;

        // If combo is read-only - return
        if (me.readOnly) return;

        // Enter - select an option
        if (code == Ext.EventObject.ENTER) {
            if (me.isExpanded || arguments[1] === true) me.onItemSelect();
            return false;

            // Up or Down arrows
        } else if (code == Ext.EventObject.DOWN || code == Ext.EventObject.UP || code == Ext.EventObject.PAGE_DOWN || code == Ext.EventObject.PAGE_UP) {

            // If Down key was pressed but picker is not shown
            if (code == Ext.EventObject.DOWN && !me.isExpanded && !(arguments[1] === true)) {

                // Call onTriggerClick, so picker contents will be rebuilded and shown
                me.onTriggerClick();

                // Adjust width of .i-combo-table element for it to fit all available space
                me.comboTableFit();

            // Else
            } else {

                // Get items count for calculations
                var size = me.getPicker().el.select('.x-boundlist-item:not(.x-boundlist-item-disabled)').getCount();

                // Down key
                if (code == Ext.EventObject.DOWN){
                    if (parseInt(me.keywordEl.attr('selectedIndex')) < size) {
                        me.keywordEl.attr('selectedIndex', parseInt(me.keywordEl.attr('selectedIndex'))+1);
                    }

                    // Up key
                } else  if (code == Ext.EventObject.UP){
                    if (parseInt(me.keywordEl.attr('selectedIndex')) > 1) {
                        me.keywordEl.attr('selectedIndex', parseInt(me.keywordEl.attr('selectedIndex'))-1);
                    }

                    // PgDn key
                } else if (code == Ext.EventObject.PAGE_DOWN) {
                    if (parseInt(me.keywordEl.attr('selectedIndex')) < size - me.optionsOnPage) {
                        me.keywordEl.attr('selectedIndex', parseInt(me.keywordEl.attr('selectedIndex'))+me.optionsOnPage);
                        me.getPicker().el.attr('more', '');
                    } else if (parseInt(me.keywordEl.attr('selectedIndex')) <= size) {
                        if (me.count() < me.found()){
                            me.getPicker().el.attr('more', 'lower');
                        } else {
                            me.keywordEl.attr('selectedIndex', size);
                            me.getPicker().el.attr('more', '');
                        }
                    }

                    // Prevent page scrolldown, so picker contents will be scrolled instead of form panel contents
                    if (evt.preventDefault) evt.preventDefault();

                    // PgUp key
                } else if (code == Ext.EventObject.PAGE_UP) {
                    if (parseInt(me.keywordEl.attr('selectedIndex')) > me.optionsOnPage) {
                        me.keywordEl.attr('selectedIndex', parseInt(me.keywordEl.attr('selectedIndex'))-me.optionsOnPage);
                        me.getPicker().el.attr('more', '');
                    } else {
                        if (me.count() < me.found()){
                            me.getPicker().el.attr('more', 'upper');
                        } else {
                            me.keywordEl.attr('selectedIndex', 1);
                            me.getPicker().el.attr('more', '');
                        }
                    }

                    // Prevent page scrolldown, so picker contents will be scrolled instead of form panel contents
                    if (evt.preventDefault) evt.preventDefault();
                }

                // Set up selected item, depending on what key was pressed, and deal scroll list of options if need
                var disabledCount = 0;

                // Provide picker contents appropriate scrolling, depending on currently selected item
                me.getPicker().el.select('.x-boundlist-item').each(function(el, c, liIndex){
                    var si = parseInt(me.keywordEl.attr('selectedIndex'));

                    // Increment disabled count
                    if (el.hasCls('x-boundlist-item-disabled')) disabledCount++;

                    // If current item is active
                    if (!el.hasCls('x-boundlist-item-disabled') && si > 0 && liIndex == si - 1 + disabledCount) {

                        // Add hover class
                        el.addCls('x-boundlist-item-over');

                        // Setup selectedIndex attribute
                        me.keywordEl.attr('selectedIndex', liIndex + 1 - disabledCount);

                        // Reset disabled count
                        disabledCount = 0;

                        // Setup scrolling
                        if (liIndex == 1) me.getPicker().body.scrollTo('top', 0);
                        else el.scrollIntoView(me.getPicker().body);

                    // Else remove hover class
                    } else el.removeCls('x-boundlist-item-over');
                });

                // Get hidden value while walking trough options list
                var selectedLi = me.getPicker().el.select('.x-boundlist-item-over').first();
                var id = selectedLi ? selectedLi.attr(name) : null;

                // Set temporary value for combo. It will mean that combo will look the same
                // as if value was assigned, but without actual value change
                me.setTemporaryValue(id);
            }
            return false;

        // Esc key or Tab key
        } else if (code == Ext.EventObject.TAB) {

            // If there is no currently selected option, we just hide suggestions list,
            // Else if there is - we select it by the same way as it would clicked,
            // but only if combo is not running in multiple-values mode
            if (me.multiSelect || me.onItemSelect() === false) me.collapse();

        // Esc key
        } else if (code == Ext.EventObject.ESC) {

            // Get combo back to the state, that combo had before expanding.
            // Here we use setTemporaryValue() function, with no argument, that
            // will be threated by this function as a directive, meaning that
            // actual combo `value` property should be used instead of missing argument
            me.setTemporaryValue();

        // Other keys
        } else {

            // If combo is multiple
            if (me.multiSelect) {

                // If Delete or Backspace is pressed and current keyword value is '' - we should delete last selected
                // value from list of selected values. We will do it by firing 'click' event on .i-combo-selected-item-delete
                // because this element has a handler for that event, and that handler will perform all necessary operations
                if ((code == Ext.EventObject.BACKSPACE || code == Ext.EventObject.DELETE) && !me.keywordEl.val()) {

                    // Remove last selected item
                    if (me.el.select('.i-combo-selected-item-delete').last())
                        me.el.select('.i-combo-selected-item-delete').last().dom.click();

                    // Hide picker
                    if (me.hideOptionsAfterKeywordErased) me.collapse();

                    // Otherwise, is any other key was pressed and no-lookup is true then ignore that key
                } else if (me.keywordEl.attr('no-lookup') == 'true') {
                    return false;
                }

                // If combo is not multiple
            } else {

                // We provide necessary operations if combo is running with no-lookup option
                if (me.keywordEl.attr('no-lookup') == 'true') {

                    // If Backspace or Del key is pressed, we should set current value as 0 and set keyword to '',
                    // but only if me.store.enumset == false, because there can be only one case then
                    // both "me.store.enumset == true" and "multiple" are used - combo field is dealing
                    // with ENUM database table column type and within that type no empty or zero values allowed,
                    // except empty or zero value is in the list of ENUM values, specified in the process of column
                    // declaration
                    if ((code == Ext.EventObject.BACKSPACE || code == Ext.EventObject.DELETE) && (!me.store.enumset || me.xtype == 'combo.filter')){
                        me.clearCombo();

                        // If any other key was pressed, there should be no reaction
                    } else {
                        evt.preventDefault();
                        return false;
                    }
                }
            }
        }
    },

    /**
     * Perform some additional things after option was selected
     *
     * @param li
     */
    postSelect: function(li) {
        var me = this, name = me.name;

        // Apply selected option additional attributes to a hidden input,
        // so attributes and their values to be accessible within hidden input context
        if (me.store.attrs && me.store.attrs.length) {
            for(var n = 0; n < me.store.attrs.length; n++) {

                // If combo is running in multiple mode, we add a postfix to attribute names, for making a posibillity
                // of picking up attributes, related to each separate selected value from the whole list of selected values
                if (me.multiSelect) {
                    me.hiddenEl.attr(this.store.attrs[n]+'-'+li.attr(name), li.attr(me.store.attrs[n]));
                } else {
                    me.hiddenEl.attr(this.store.attrs[n], li.attr(me.store.attrs[n]));
                }
            }
        }

        // Adjust width of .i-combo-table element for it to fit all available space
        me.comboTableFit();

        // Fire 'change' event
        me.getNative().setValue.call(me, me.hiddenEl.val());

        // Get focus back to keywordEl
        me.keywordEl.focus();
    },



    /**
     * Get the data item, related to currently selected value
     */
    r: function(id) {
        var me = this, index;

        // Setup default value of `id` argument
        if (!id) id = me.val().split(',')[0];

        // Get the index of selected option id in me.store.ids
        index = me.store.ids.indexOf(me.store.enumset && !id.toString().match(/^[1-9][0-9]{0,9}$/) ? id : parseInt(id));

        // Build the data-row object and return it
        return Ext.merge({id: id}, me.store.data[index]);
    },

    /**
     * Set some option as selected, autosets value for hidden field
     */
    onItemSelect: function (e, dom) {
        var li, index, color, css = {color: ''}, me = this, name = me.name;

        if (arguments.length == 0) {
            li = me.getPicker().el.select('.x-boundlist-item.x-boundlist-item-over').first();
            if (!li) return false;
        } else {
            li = Ext.get(dom);

            // If click event target is actually not an item, but some it's child item - go upper the DOM and find
            if (!li.hasCls('x-boundlist-item')) li = li.up('.x-boundlist-item');
        }

        // Get the index of selected option id in me.store.ids
        if (me.store.enumset) {
            if (!li.attr(name).toString().match(/^(-?[1-9][0-9]{0,9}|0)$/) && !me.boolean) {
                index = me.store.ids.indexOf(li.attr(name));
            } else {
                index = me.store.ids.indexOf(parseInt(li.attr(name)));
            }
        } else {
            index = me.store.ids.indexOf(parseInt(li.attr(name)));
        }

        // Apply css color, if it was passed within store. Currently this feature is used for
        // cases then item title got from database was something like
        // <span style="color: red">Some title</span>. At such cases, php code which is preparing
        // combo data, strips that html from option, but detect defined color and
        // store it in ...['data'][i].system['color'] property
        if (me.store.data[index].system && me.store.data[index].system['color']
            && typeof me.store.data[index].system['color'] == 'string')
            css.color = me.store.data[index].system['color'];

        // Detect if colorbox should be applied
        color = me.color(me.store.data[index], li.attr(name));

        // If combo is in multiple-value mode
        if (me.multiSelect) {

            // Get array of selected items keys
            var selected = me.hiddenEl.val() ? me.hiddenEl.val().split(',') : [];

            // If option, that is going to be added to selected list, is not already exists there
            if (selected.indexOf(li.attr(name)) == -1) {

                // Set the width of .i-combo-table element to 1px, to prevent combo label jumping for cases if
                // adding the new item leads to height-increase of an area, that contains all currently selected
                // items
                me.tableEl.setWidth(1);

                // Append new selected item after the last existing selected item
                Ext.DomHelper.insertHtml('beforeBegin', me.wrapperEl.dom,
                    '<span class="i-combo-selected-item" selected-id="'+li.attr(name)+'">'+
                        color.box + color.title +
                        '<span class="i-combo-selected-item-delete"></span>' +
                        '</span>');

                // Get that newly inserted selected item as an already appended dom node
                var a = me.el.select('.i-combo-selected-item[selected-id="' + li.attr(name) +'"]').last();

                // Apply color
                a.css(css);

                // Bind a click event handler for a .i-combo-selected-item-delete
                // child node within newly appended item
                me.el.select('.i-combo-selected-item-delete').last().on('click', me.onItemDelete, me);

                // Determine way of how to deal with .i-combo-data (rebuild|rebuild-and-show|no-rebuild)
                var mode = me.keywordEl.val() ? 'selected-but-found-with-lookup' : '';

                // Reset keyword field and it's 'prev' attr, append just selected value to already selected and
                // adjust keyword field width
                me.keywordEl.val('');
                me.keywordEl.attr('prev', '');
                selected.push(li.attr(name));
                me.hiddenEl.val(selected.length > 1 ? selected.join(',') : selected[0]);

                // Hide options, is ctrlKey was not pressed
                if (!Ext.EventObject.ctrlKey) me.collapse();

                // Restore list of options
                me.keywordErased(mode);

                // Execute javascript-code, assigned to selected item
                if (me.store.enumset) {
                    var index = me.store['ids'].indexOf(li.attr(name));
                    if (index != -1 && me.store['data'][index].system.js) {
                        Indi.eval(me.store['data'][index].system.js, me);
                    }
                }

                // Additional operations, that should be done after some option was selected
                me.postSelect(li);

            // Indicate that option can't be once more selected because it's already selected
            } else {
                var existing = me.el.select('.i-combo-selected-item[selected-id="'+li.attr(name)+'"] .i-combo-selected-item-delete').first();
                if (existing) existing.fadeOut({opacity: 0.25, duration: 200}).fadeIn();
            }

            // Else if combo is running in single-value mode
        } else {

            // Apply selected color
            color.apply(name);

            // Apply color got from store, or unset css color property, if no color
            me.keywordEl.setStyle(css);

            // Set keyword text
            me.keywordEl.val(color.title).attr('prev', color.title);

            // Update field value
            me.hiddenEl.val(li.attr(name));

            // Hide options
            me.collapse();

            // Additional operations, that should be done after some option was selected
            me.postSelect(li);
        }
    },

    // @inheritdoc
    onChange: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Call combo's special onHiddenChange()
        me.onHiddenChange();
    },

    /**
     * Function that will be called after combo value change. Contain auxiliary operations such as
     * dependent-combos reloading, javascript execution and others
     */
    onHiddenChange: function() {
        var me = this;

        // We set 'changed' attribute to 'true' to remember the fact of at least one time change.
        // We will need this fact in request data prepare process, because if at the moment of sending
        // request 'changed' will still be 'false' (initial value), satellite property won't be set in
        // request data object. We need this to get upper and lower page results fetched from currently selected
        // value as startpoint. And after 'changed' attribute set to 'false', upper and lower page results will
        // have start point different to selected value, and based on most top alphabetic order.
        me.infoEl.attr('changed', 'true');

        // Remove attributes from hidden field, if it's value became 0. We do it here only for single-value combos
        // because multiple-value combos have different way of how-and-when the same aim should be reached -
        // attributes deletion for multiple-value combos is implemented in me.bindDelete() function of this script
        if (!me.multiSelect && me.hiddenEl.val() == '0') {
            if (me.store.attrs && me.store.attrs.length) {
                for (var n = 0; n < me.store.attrs.length; n++) {
                    me.hiddenEl.removeAttr(me.store.attrs[n]);
                }
            }

            // Also we remove a .i-combo-color-box element, related to previously selected option
            if (me.keywordEl.val() == '#' || me.keywordEl.val() == '') me.colorDiv.update('');
        }

        // Execute javascript code, if it was assigned to selected option. The additional clause for execution
        // is that combo should run in single-value mode, because if it's not - we do not know what exactly item
        // was selected and we are unable to get js, related to that exactly item. Even more - we do not exactly
        // know about the fact of new item was added, it also could be removed, because me.onHiddenChange() (if combo is
        // running in multiple-value mode) if firing in both cases. So, for the aim of selected item assigned javascript
        // execution to be reached, we need this execution to be provided at me.onItemSelect() function of this script
        if (me.store.enumset && !me.multiSelect) {
            var index = me.store['ids'].indexOf(me.hiddenEl.val());
            if (index != -1 && me.store['data'][index].system.js) {
                Indi.eval(me.store['data'][index].system.js, me);
            }
        }

        // Execute javascript code, assigned as an additional handler for 'select' event
        if (me.store.js) {
            if (typeof me.store.js == 'function') me.store.js.call(me); else Indi.eval(me.store.js, me);
        }

        // If combo is running in multiple-values mode and is rendered - empty keyword input element
        if (me.multiSelect && me.el) me.keywordEl.dom.value = Ext.emptyString;

        // Align picker
        Ext.defer(me.alignPicker, 10, me);

        // If current field is a satellite for one or more sibling combos, we should refresh data in that sibling combos
        if (me.ownerCt) me.ownerCt.query('[satellite="' + me.field.id + '"]').forEach(function(d){
            if (d.xtype == 'combo.form') {
                d.setDisabled(false, true);
                if (!d.disabled) {
                    d.remoteFetch({
                        satellite: me.hiddenEl.val(),
                        mode: 'refresh-children'
                    });
                }
            }
        });
    },

    /**
     * Inject additional possible error check, related to
     * 1. Conformance of current value to `maxSelected` property (only if `multiSelect` is `true`)
     * 2. None of currently selected values are in the `disabledOptions` list
     *
     * @return {*}
     */
    getErrors: function() {
        var me = this, errorA = me.callParent(), v = me.getValue() + '';

        // Check for `maxSelected` conformance
        if (me.multiSelect && v && me.maxSelected && me.maxSelected < v.split(',').length)
            errorA.push(Indi.lang.I_COMBO_MISMATCH_MAXSELECTED + ' - ' + me.maxSelected);

        // Check for value should not be/contain disabled value
        if (me.disabledOptions)
            if (!me.multiSelect) {
                if (me.disabledOptions.indexOf(v) != -1) errorA.push(Indi.lang.I_COMBO_MISMATCH_DISABLED_VALUE);
            }

        return errorA;
    },

    /**
     * This function retrieves the property, that was additionally passed within combo data. It's only usable in cases,
     * when special param was set up for that combo. Such param cannot be set up within javascript-code, it's existence
     * is provided by special data row within special mysql table, and is can be set there in a special cms section.
     *
     * Example: we have combo with cities, New York, Moscow, etc. Titles of these cities assumed to be got from `city`
     * mysql table. So, if that table have some additional column, for example `population` INT(11), and combo settings
     * are set up in a way that considers that this property should be available for each data-item within the combo. So,
     * if we have 'New York' as current combo value, this function can return the value of `population` by
     * Ext.getCmp('my-city-combo-id').prop('population'); If prop is an string, represinting an integer value - it will
     * be converted to integer using javascript's parseInt() function, before returning
     *
     * Warning: Currently function is working properly only if combo's `multiSelect` property is not `true`
     *
     * @param name
     */
    prop: function(name) {
        var me = this, r = me.r(me.val()), propS;

        // If data object, representing current value was not found, return null
        if (!Ext.isObject(r)) return null;

        // If data object, representing current value's 'attrs' prop - was not found, return null
        if (!Ext.isObject(r.attrs)) return null;

        // Setup prop shortcut
        propS = r.attrs[name].toString();

        // If propS is a string, representing an integer number - convert it into interger-type and return
        //if (propS.match(/^-?[0-9]{0,10}$/)) return parseInt(propS);
        if (propS.match(/^(-?[1-9][0-9]{0,9}|0)$/)) return parseInt(propS);

        // If propS is a string, representing a floating-point number - convert it into float-type and return
        //if (propS.match(/^-?[0-9]{0,10}\.[0-9]{2}$/)) return parseFloat(propS);
        if (propS.match(/^(-?[0-9]{1,8})(\.[0-9]{1,2})?$/)) return parseFloat(propS);

        // If propS is a string, representing a floating-point number, containing
        // up to 10 digits in integer part, optionally prepended with an '-' sign,
        // and containing up to 3 digits in fractional part - convert it into float-type and return
        if (propS.match(/^(-?[0-9]{1,10})(\.[0-9]{1,3})?$/)) return parseFloat(propS);

        // Retun as is
        return propS;
    },

    /**
     * Builds html for new options list, bind events and do some more things
     *
     * @param requestData
     * @param responseData
     */
    afterFetchAdjustments: function(requestData, responseData) {
        var me = this;

        // Remove more attribute
        me.getPicker().el.removeAttr('more');

        // Rebuild options list
        var html = me.suggestions(me.store);

        // Update picker contents
        me.getPicker().update(html);

        // If at least one result was found
        if (responseData['found']) {

            // We get json['found'] value only in case if we are running 'keyword' fetch mode,
            // and in json is stored first portion of results and this mean that paging up should be disabled
            me.infoEl.attr('page-top-reached', 'true');

            // Also, we should renew 'page-btm-reached' attribute value
            me.infoEl.attr('page-btm-reached', responseData['found'] <= me.optionsOnPage ? 'true' : 'false');
        }

        // Bind 'hover' and 'click' event handlers for boundlist items
        me.bindItemHoverClick();

        // If results set is not empty
        if (me.getPicker().el.select('.x-boundlist-item:not(.x-boundlist-item-disabled)').getCount()) {

            // Show options list after keyword typing is finished
            if (me.isExpanded) {

                // Align picker
                me.alignPicker();

                // Show results
            } else if (requestData.mode != 'refresh-children') {
                me.keywordEl.dom.click();
            }

            // Enable combo if children were found
            if (requestData.mode == 'refresh-children') me.setDisabled();

            // Options selected adjustments
            if (requestData.more && requestData.more.toString().match(/^(upper|lower)$/)) {

                // If these was no more results
                if (responseData.ids['length'] <= me.optionsOnPage) {

                    // We mark that top|bottom range is reached
                    if (responseData.ids['length'] < me.optionsOnPage)
                        me.infoEl.attr('page-'+(requestData.more == 'upper' ? 'top' : 'btm')+'-reached', 'true');

                    // Move selectedIndex at the most top
                    if (requestData.more == 'upper') {
                        me.keywordEl.attr('selectedIndex', 1);

                        // Move selectedIndex at the most bottom
                    } else if (requestData.more == 'lower' && responseData.ids['length'] < me.optionsOnPage) {
                        me.keywordEl.attr('selectedIndex', me.getPicker().el.select('.x-boundlist-item:not(.x-boundlist-item-disabled)').getCount());
                    }
                }

                // If the aim of the latest user request was to fetch upper page of results
                if (requestData.more.toString() == 'upper') {

                    // Setup and increase the number of data items, fetched by page ups
                    me.store.fetchedByPageUps += responseData.data.length;

                    // Decrease the number of data items, fetched by page ups, by count
                    // of disabled data items, found in responseData
                    for (var i = 0; i < responseData.data.length; i++)
                        if (responseData['data'][i].system && responseData['data'][i].system['disabled'])
                            me.store.fetchedByPageUps--;
                }

                // Adjust selection based on selectedIndex
                me.keyDownHandler(requestData.more == 'upper' ? 33 : 34);

                // Update page-top|page-btm value
                me.infoEl.attr('page-'+ (requestData.more == 'upper' ? 'top' : 'btm'), requestData.page);
            }

            // Else if results set is empty (no non-disabled options), we hide options, and set red
            // color for keyword, as there was no related results found
        } else {

            // Hide options list div
            me.collapse();

            // If just got resuts are result for satellited combo, autofetched after satellite value was changed
            // and we have no results related to current satellite value, we disable satellited combo
            if (requestData.mode == 'refresh-children') {
                me.setDisabled(true);

            // Else if reason of no results was not in satellite, we add special css class for that case
            } else {
                me.keywordEl.addCls('i-combo-keyword-no-results');
                me.markInvalid('Ничего не найдено');
            }
        }
    },

    /*
     * Bind 'hover' and 'click' event handlers for boundlist items
     */
    bindItemHoverClick: function() {
        var me = this, query = '.x-boundlist-item:not(.x-boundlist-item-disabled)';

        // Bind a 'x-boundlist-item-over' class adding on hover
        me.getPicker().el.select(query).hover(function(e, dom){
            Ext.get(dom).up('ul').select('li').removeCls('x-boundlist-item-over');
            Ext.get(dom).addCls('x-boundlist-item-over');
            var k = Ext.get(dom).up('ul').select(query).indexOf(dom);
            me.keywordEl.attr('selectedIndex', k+1);
        }, Ext.emptyFn);

        // Bind a click event to each option
        me.getPicker().el.select(query).on('click', me.onItemSelect, me);
    },

    /**
     * Function is used in case if all possible options, within which keyword-search will be processing - are already collected.
     * They can be collected initially (if their total count <= Indi_Db_Table_Row::$comboOptionsVisibleCount) or
     * can be collected step by step while paging upper/lower. So, since they all are a got, any keyword search will run
     * without requests to database, and will be completely handled by javascript. Such scheme will be used until next
     * database request - this can happen if current combo field has a satellite, and satellite value was changed
     *
     * @param data Request data object, containing same properties, as per remote-fetch scheme
     */
    localFetch: function(data) {
        var me = this;

        // Empty store
        me.store.data = [];
        me.store.ids = [];

        // Prepare regular expression for keyword search
        var keywordReg = new RegExp(Indi.pregQuote(data.keyword, '/'), 'i');

        // Prepare regular expression for keyword, if it was typed in wrong keyboard layout
        var keywordRegWKL = new RegExp(Indi.pregQuote(me.convertWKL(data.keyword), '/'), 'i');

        // This variable will contain a title, which will be tested against a keyword
        var against;

        // If we are dealing with tree of options, we should find not only search results, but also all level parents
        // for user to be able to view all parents of each result
        if (me.store.tree) {
            var results = [];
            var parents = [];
            var parentId, currentIndex;

            // Collect ids of options that are primary results of search, and collect ids of all their distinct parents
            for (var i = 0; i < me.store.backup.options.data.length; i++) {

                // If tested title is a color, we should strip hue part of title, before keyword match will be performed
                against = me.color(me.store.backup.options.data[i]).title;

                // Test title against a keyword
                if (keywordReg.test(against) || keywordRegWKL.test(against)) {
                    results.push(me.store.backup.options.ids[i]);
                    currentIndex = i;
                    while (parentId = parseInt(me.store.backup.options.data[currentIndex].system.parentId)) {
                        if (parents.indexOf(parentId) == -1) {
                            parents.push(parentId);
                        }
                        currentIndex = me.store.backup.options.ids.indexOf(parentId);
                    }
                    parentId = 0;
                }
            }

            // Remove items (from parents array), that also are primary results
            for (var i = 0; i < results.length; i++) {
                if (parents.indexOf(results[i]) != -1) {
                    parents.splice(parents.indexOf(results[i]), 1);
                }
            }

            // Walk though full backuped options list and pick items, that are primary results or are parents for
            // primary results
            for (var i = 0; i < me.store.backup.options.data.length; i++) {
                var optionId = me.store.backup.options.ids[i];
                if (results.indexOf(optionId) != -1 || parents.indexOf(optionId) != -1) {
                    me.store.ids.push(me.store.backup.options.ids[i]);
                    me.store.data.push(Indi.copy(me.store.backup.options.data[i]));

                    // Mark parents as disabled, so they will be no selectable
                    if (parents.indexOf(optionId) != -1) {
                        var disabledIndex = me.store.data.length - 1;
                        me.store.data[disabledIndex].system.disabled = true;
                    }
                }
            }

            // Set up number of found (primary) results
            me.store.found = results.length;

            // If we are dealing with non-tree list of options, all it simpler for a bit
        } else {
            for (var i = 0; i < me.store.backup.options.data.length; i++) {

                // If tested title is a color, we should strip hue part of title, before keyword match will be performed
                against = me.color(me.store.backup.options.data[i]).title;

                // Test title against a keyword
                if (keywordReg.test(against) || keywordRegWKL.test(against)) {
                    me.store.data.push(me.store.backup.options.data[i]);
                    me.store.ids.push(me.store.backup.options.ids[i]);
                }
            }
            me.store.found = me.store.ids.length;
        }

        // Here we build html for options list, setup scrolling if needed, adjust combo options div height and
        // margin-left for .i-combo-info, bind hover and click handlers on each option and do other things
        me.afterFetchAdjustments(data, me.store);
    },

    /**
     * Builds a path to make a fetch request to
     *
     * @return string
     */
    fetchRelativePath: function() {
        if (window.comboFetchRelativePath) {
            return Indi.std + window.comboFetchRelativePath;
        } else {
            return this.ctx().uri;
        }
    },

    /**
     * Merge two sets of optgroup info
     *
     * @param info1
     * @param info2
     * @return {*}
     */
    mergeOptgroupInfo: function (info1, info2) {
        for (var j in info2.groups) {
            if (info1.groups[j] == undefined) {
                info1.groups[j] = info2.groups[j];
            }
        }
        return info1;
    },

    /**
     * Prepare request parameters, do request, fetch data and rebuild combo
     *
     * @param data
     */
    remoteFetch: function(data) {
        var me = this, url;

        if (me.fetchUrl) url = me.fetchUrl; else {

            // Base url
            url = me.ctx().uri.split('?')[0];

            // Append 'index/' to the ajax request url, if action is 'index', but string 'index' is not mentioned
            // within me.ctx().uri, to prevent 'odata/' string (that will be appended too) to be treated as action
            // name rather than as a separate param name within the axaj request url
            if (me.ctx().ti().action.alias == 'index' && !me.ctx().uri.match(/\/index\//)) url += 'index/';
        }

        // Append odata specification
        url += 'odata/' + me.name + '/';

        // Show loading pic
        me.infoEl.addCls('i-combo-info-loading');

        // Fetch request
        Ext.Ajax.request({
            url: Indi.pre.replace(/\/$/, '') + url,
            params: Ext.merge(data, {consider: Ext.JSON.encode(me.considerOnData())}),
            success: function(response) {

                // Convert response.responseText to JSON object
                var json = JSON.parse(response.responseText);

                // Save current options to backup
                var backupOptions = []; backupOptions = Indi.copy(me.store);

                // If current options list should be prepended with fetched options
                if (data.more == 'upper') {

                    // Empty current options
                    me.store['ids'] = [];
                    me.store['data'] = [];

                    // So now we start to fill me.store array with fetched options
                    for (var key = 0; key < json['ids'].length; key++) {
                        me.store['ids'].push(json['ids'][key]);
                        me.store['data'].push(json['data'][key]);
                    }

                    // And after that we append options from backupOptions, so as the result
                    // we will have full options list in correct order
                    for (var key = 0; key < backupOptions['ids'].length; key++) {
                        me.store['ids'].push(backupOptions['ids'][key]);
                        me.store['data'].push(backupOptions['data'][key]);
                    }

                    // Merge optgroup info
                    if (me.store.optgroup)
                        me.store.optgroup = me.mergeOptgroupInfo(me.store.optgroup, json.optgroup);

                    // Else if fetched options should be appended to current options list
                } else if (data.more == 'lower') {

                    // If we are dealing with tree of results, we should merge existing options tree
                    // with tree of just received additional page of results
                    if (me.store.tree) {

                        // Merge trees
                        me.store = me.merge(me.store, json);

                        // Else we just append fetched options to existing options
                    } else {
                        for (var key in json['ids']) {
                            me.store['ids'].push(json['ids'][key]);
                            me.store['data'].push(json['data'][key]);
                        }
                    }

                    // Merge optgroup info
                    if (me.store.optgroup) me.store.optgroup = me.mergeOptgroupInfo(me.store.optgroup, json.optgroup);

                    // Otherwise we just replace current options with fetched options
                } else {
                    var jsBackup = me.store.js;
                    var optionHeightBackup = me.store.optionHeight;
                    me.store = json;
                    me.store.js = jsBackup;
                    me.store.optionHeight = optionHeightBackup;
                }

                // Setup backup
                me.store.backup = backupOptions.backup;

                // Restore default visibility for countEl element
                me.infoEl.removeCls('i-combo-info-loading');

                // Build html for options, and do all other things
                me.afterFetchAdjustments(data, json);
            },
            failure: function() {

                // Restore default visibility for countEl element
                me.infoEl.removeCls('i-combo-info-loading');
            }
        })
    },

    /**
     * clearValue() call removed
     */
    enableBySatellites: function(cfg) {
        var me = this, data = {};

        // Enable field
        if (!cfg.hasOwnProperty('enable') || cfg.enable) me.enable();

        // Fire 'enablebysatellite' event
        me.fireEvent('enablebysatellite', me, me.considerOnData());
    },

    /**
     * Mark some options as disabled
     *
     * @param disabledIds
     */
    setDisabledOptions: function(disabledIds) {
        var me = this, i;

        // If `disabledIds` arg is not even an array - we assume that all options should be enabled
        if (!Ext.isArray(disabledIds)) disabledIds = [];

        // Convert each item within `disabledIds` array to a string, for better conformance
        for (i = 0; i < disabledIds.length; i++) disabledIds[i] = String(disabledIds[i]);

        me.disabledOptions = disabledIds;

        // Enable/disable options
        for (i in me.store.data) {
            me.store.data[i].system.disabled = disabledIds.indexOf(String(me.store.ids[i])) != -1;
        }

        // Check if current value is valid
        if (!me.hasZeroValue()) me.isValid();
    },

    /**
     * Get the number of currently selected options. If combo has `multiSelect` = false, function will return 1
     *
     * @return {Number}
     */
    getSelectedCount: function() {
        var me = this;
        if (!me.multiSelect) return 1;
        if (me.val()) return me.val().split(',').length;
    },

    /**
     * Merge two trees of options
     *
     * @param tree1
     * @param tree2
     * @return array
     */
    merge: function(tree1, tree2) {
        for (var index2 in tree2['ids']) {
            if (!isNaN(index2)) {
                var id = tree2['ids'][index2];
                var parentId = tree2['data'][index2].system.parentId;

                // If there is no such an option in existing tree, we add it
                if (tree1['ids'].indexOf(id) == -1) {

                    // If this is a one of top-level options, we just push it to the end of options list
                    if (parseInt(parentId) == 0) {
                        tree1['ids'].push(id);
                        tree1['data'].push(tree2['data'][index2]);

                    // Else we implement bit more complicated logic
                    } else {

                        // At first we are checking if in existing options there are at least one
                        // option with the same parent identifier as parent identifier of new option,
                        // and if found, insert new option after last/single existing option
                        var insertAfter = -1;
                        for (var index1 in tree1['ids']) {
                            if (parentId == tree1['data'][index1].system.parentId) {
                                insertAfter = index1;

                                // We also take in attention that new option should be inserted not simply after last sibling,
                                // but after all lower-levels children of that last sibling
                            } else if (insertAfter != -1 &&
                                tree1['data'][index1].system.indent > tree2['data'][index2].system.indent) {
                                insertAfter = index1;
                            }
                        }

                        // If such an option was not found, we are trying to find a parent option for
                        // new option in existing options
                        if (insertAfter == -1) {
                            for (var index1 in tree1['ids']) {
                                if (parentId == tree1['ids'][index1]) {
                                    insertAfter = index1;
                                } else if (insertAfter != -1 &&
                                    tree1['data'][index1].system.indent > tree2['data'][index2].system.indent) {
                                    insertAfter = index1;
                                }
                            }
                        }
                        insertAfter = parseInt(insertAfter) + 1;
                        tree1['ids'].splice(insertAfter, 0, id);
                        tree1['data'].splice(insertAfter, 0, tree2['data'][index2]);
                    }

                    // Else if such an option is already presented in existing tree, we check if it is
                    // disabled there but not in new tree and if so we set 'disabled' property to 'false'
                } else {

                    // Find index
                    index1 = tree1['ids'].indexOf(id);

                    // Set 'disabled' to true
                    if (tree1['data'][index1].system.disabled == true && tree2['data'][index2].system.disabled != true) {
                        tree1['data'][index1].system.disabled = false;
                    }
                }
            }
        }

        // Return final tree
        return tree1;
    },

    /**
     * Disable or enable combo depending on a given param
     *
     * @param disable true|false
     */
    toggle: function(disable) {
        var me = this;
        if (arguments.length) {
            if (disable) {
                me.keywordEl.attr('disabled', 'disabled');
                me.keywordEl.up('.i-combo').addCls('i-combo-disabled x-item-disabled');
                me.keywordEl.val('');

            // Enable combo
            } else {
                me.keywordEl.removeAttr('disabled');
                me.keywordEl.up('.i-combo').removeCls('i-combo-disabled x-item-disabled');
            }
        } else if (me.disabled) {
            me.enable();
        } else {
            me.disable();
        }
    },

    bid: function() {
        return this.id.replace(new RegExp(this.field.alias+'$'), '');
    },

    /**
     * Get the current count valid/non-disabled options in the combo
     * The difference between count() and found() is the same as, for example,
     * between php's mysql_num_rows() and sql's FOUND_ROWS()
     *
     * @return {Number}
     */
    count: function() {
        return parseInt(this.countEl.getHTML().replace(',', ''));
    },

    /**
     * Get the total number of found options, that may be displayed in combo.
     * The difference between count() and found() is the same as, for example,
     * between php's mysql_num_rows() and sql's FOUND_ROWS()
     *
     * @return {Number}
     */
    found: function() {
        return parseInt(this.store.found);
    },

    // @inheritdoc
    setSize: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Fit me.tableEl width
        me.comboTableFit();

        // Fit picker width to combo width
        if (me.picker) me.getPicker().setWidth(me.triggerWrap.getWidth());
    },

    /**
     * Provide combo table fit each time layout was updated
     */
    updateLayout: function() {
        this.tableEl.setWidth(1);
        this.callParent();
        this.comboTableFit();
    },

    /**
     * Do all look and feel adjustments for combo, that would happen
     * if some value would be assigned, but withoud actual assigning
     *
     * @param id
     */
    setTemporaryValue: function(id) {
        var me = this, index, color;

        // If no `id` argument was given - use current value of `value` property of current combo
        id = arguments.length ? id : me.value;

        // If we were running fetch in 'keyword' mode, but then switched to 'no-keyword' mode,
        // There can be a situation that there will be no li.selected in options list, so we wrap
        // following code with a condition of li.selected existence
        if (!me.multiSelect) {

            if (id !== null) {

                // Get the index of selected option id in me.store.ids
                index = me.store.ids.indexOf(me.store.enumset ? id : parseInt(id));

                // Setup color box if needed
                color = me.color(me.store.data[index], me.store.ids[index]);

            } else color = me.color();

            // Apply color and color-trimmed keyword value or unset both, if `id` argument is null
            color.apply();

            // Adjust width of .i-combo-table element for it to fit all available space
            me.comboTableFit();
        }
    },

    resetInfo: function(value) {
        var me = this;
        if (me.infoEl) {
            me.infoEl.attr('page-top', 0);
            me.infoEl.attr('page-btm', 0);
            me.infoEl.attr('page-top-reached', value ? 'false' : 'true');
            me.infoEl.attr('page-btm-reached', 'false');
            me.fetchedByPageUps = 0;
        }
        me.subTplData.pageUpDisabled = value ? 'false' : 'true';
        if (me.picker) {
            me.picker.destroy();
            delete me.picker;
        }
    }
});
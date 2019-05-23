/**
 * The general purpose of this component is to provide the ability to treat this
 * component instances values as strings rather than objects
 */
Ext.define('Indi.lib.form.field.MultiCheck', {

    // @inheritdoc
    extend: 'Ext.form.CheckboxGroup',

    // @inheritdoc
    alternateClassName: 'Indi.form.MultiCheck',

    // @inheritdoc
    mixins: {fieldBase: 'Ext.form.field.Base'},

    // @inheritdoc
    alias: 'widget.multicheck',

    /**
     * Css class for component
     */
    cls: 'i-field-multicheck',

    //minChecked: 0,
    maxChecked: 0,

    // @inheritdoc
    columns: 'auto',

    // @inheritdoc
    vertical: true,

    /**
     * Append `zeroValue` property initialisation
     */
    constructor: function() {
        var me = this;
        me.callParent(arguments);
        me.mixins.fieldBase._constructor.call(this, arguments);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Normalize value
        me.value = me.normalizeValue(me.value);

        // Setup disabled options
        if (Ext.isString(me.disabledOptions)) me.disabledOptions = me.disabledOptions.split(',');
        else if (me.disabledOptions === undefined) me.disabledOptions = [];

        // Setup checkboxes
        me.items = me.itemA();

        // Call parent
        me.callParent();

        // Reset original value
        me.resetOriginalValue();

        // Call mixin's '_initComponent' method
        me.mixins.fieldBase._initComponent.call(this, arguments);
    },

    // @inheritdoc
    beforeRender: function() {
        var me = this;
        if (me.columns == 'auto') me.columns = 1;
        me.callParent();
    },

    /**
     * Override native getValue() method for it to return the string, containing comma-imploded values
     * of each checked checkbox's inputValue property, instead of object-format representation
     *
     * @return {*}
     */
    getValue: function() {
        var me = this, value = me.callParent(arguments)[me.name];
        return Ext.isArray(value) ? value.join(',') : (value == undefined ? "" : value);
    },

    /**
     * Override native setValue() method for it to be able to deal with non-object `value` argument, e.g
     * not only setValue({name: ['value1', 'value2']}) calls are allowable, but also
     * setValue('value1,value2'), setValue(['value1', 'value2']), setValue('1,2'), setValue(['1', 2]) are too,
     * as this component assumes that names of each checkbox are the same within the single MultiCheck component
     *
     * @param {Object/String} value
     * @return {*}
     */
    setValue: function(value) {

        // Normalize value
        value = this.normalizeValue(value);

        // Call parent
        return this.callParent([value]);
    },

    /**
     * Build and return radio items array
     *
     * @return {Array}
     */
    itemA: function(store) {
        var me = this, itemI, itemA = [], inputValue, disabled = {};

        // If `disabledOptions` arg is a string - split it by comma
        if (Ext.isString(me.disabledOptions)) me.disabledOptions = me.disabledOptions.split(',');

        // Store disabled values as keys
        me.disabledOptions.forEach(function(value){
            disabled[value] = true;
        });

        // For each store data item
        (store || me.row.view(me.name).store).data.forEach(function(enumset, index){

            // Get radio input value
            inputValue = (store || me.row.view(me.name).store).ids[index] + '';

            // Prepare initial radio item cfg
            itemI = {
                name: me.name,
                id: me.id + '$' + inputValue,
                inputValue: inputValue,
                checked: me.row[me.name].length && me.row[me.name].split(',').indexOf(inputValue) != -1,
                tooltip: enumset.system.tooltip ? {
                    html: enumset.system.tooltip,
                    anchor: 'left',
                    staticOffset: [-2, -5]
                } : false,
                enumset: enumset,
                disabled: inputValue in disabled,

                // Here we ensure that each individual checkbox's value won't be submit,
                // mean the native data submit approach, implemented for xtype:checkboxgroup
                // "the form will get the info from the individual checkboxes themselves."
                // - will not be in use. There is used different approach for this component,
                // and that approach assumes that getSubmitData method of whole xtype:multicheck
                // won't return null value
                getSubmitData: function() {
                    return null;
                }
            }

            // Append item cfg to the items array
            itemA.push(itemI);
        });

        // Return checkboxes configs array
        return itemA;
    },

    // @inheritdoc
    afterRender: function() {
        var me = this, checked = me.getChecked();

        // If checked checkboxes exists - fire 'change' event for each
        for (var i = 0; i < checked.length; i++)
            checked[i].fireEvent('change', checked[i], true);

        // Call parent
        me.callParent();

        // Call _afterRender() method, borrowed from Indi.lib.form.field.Combo, to prepare considerOn config
        me._afterRender();

        // Fire `considerchange` event
        me.mixins.fieldBase._afterRender.call(this, arguments);
    },

    /**
     * Here we use data submit approach, that differs from the Ext's native one:
     * 1. We totally switch off submitting values of each individual checkbox grouped under this component
     * 2. We override this method for it to return object, containing imploded values of
     *    all checked checkboxes grouped under this component, rather that returning null
     *
     * @return {Object}
     */
    getSubmitData: function() {
        var me = this, data = me.normalizeValue(me.getValue());
        data[me.name] = data[me.name].join(',');
        return data;
    },

    /**
     * Get comma-separated list of values, according to checkboxes having {checked: true}
     *
     * @return string
     */
    getSubmitValue: function() {
        return this.getValue();
    },

    /**
     * Convert given `value` argument to a format, that this component's
     * parent component - xtype: checkboxgroup is used to deal
     *
     * @param value
     * @return {Object}
     */
    normalizeValue: function(value) {

        // Define auxilliary variables
        var me = this, normalized = {};

        // If `value` arg is already an object - return it
        if (Ext.isObject(value)) value = value[me.name];

        // If `value` arg is an array
        if (Ext.isArray(value)) {

            // Convert each array item as string
            for (var i = 0; i < value.length; i++) {

                // If item is null/empty/undefined - drop it from array
                if (!value[i]) value.splice(i, 1);

                // Else convert it to string
                else value[i] = value[i] + '';
            }

            // Put `value` into `normalized` object, as a value of me.name property
            normalized[me.name] = value;

            // Return normalized value
            return normalized;
        }

        // If `value` argument is a number - convert it to string
        if (Ext.isNumber(value)) value = value + '';

        // If value is a non-emtpy string - split it by comma, or setup an empy array
        normalized[me.name] = Ext.isString(value) && value.length ? value.split(','): [];

        // Return normalized value
        return normalized;
    },

    /**
     * Override native {xtype:radiogroup}'s checkChange() method, and use {Ext.form.field.Field}'s one directly,
     * for skipping several operations that assume the return value of getValue() method is object, as bit upper
     * we overrided getValue() method for it to return non-object value
     *
     * @return {*}
     */
    checkChange: function() {
        var me = this;

        // Convert last value to string
        if (Ext.isObject(me.lastValue)) me.lastValue = me.lastValue[me.name].join(',');

        // Call {Ext.form.field.Field}'s checkChange() method
        Ext.form.field.Field.prototype.checkChange.call(me);
    },

    /**
     * Function that will be called after combo value change. Provide dependent-combos reloading in case
     * if current field is a consider-field for one or more other fields, that are siblings to current field
     */
    onChange: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        if (me.maxChecked) {
            if (me.getChecked().length >= me.maxChecked) {
                me.items.filter('checked', false).each(function(box){
                    box.disable();
                });
            } else me.items.each(function(box){
                box.enable();
            })
        }

        // Call mixin's _onChange() method
        me.mixins.fieldBase._onChange.call(this, arguments);
    },

    /**
     * Prepare request parameters, do request, fetch data and rebuild combo
     *
     * @param data
     */
    remoteFetch: function(data) {
        var me = this, url;

        // If `fetchUrl` prop was set - use it, or build own othwerwise
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

        // Fetch request
        Ext.Ajax.request({
            url: Indi.pre.replace(/\/$/, '') + url,
            params: Ext.merge(data, {consider: Ext.JSON.encode(me.considerOnData())}),
            success: function(response) {

                // Convert response.responseText to JSON object
                var json = JSON.parse(response.responseText);

                // Build html for options, and do all other things
                me.afterFetchAdjustments(data, json);
            }
        })
    },

    /**
     * Rebuild options list
     *
     * @param requestData
     * @param responseData
     */
    afterFetchAdjustments: function(requestData, responseData) {
        var me = this, itemA;

        // Clear value
        me.setValue();

        // Remove all
        me.removeAll();

        // If results set is not empty
        if (parseInt(responseData.found)) {

            // Enable combo if children were found
            me.setDisabled();

            // Append children
            me.itemA(responseData).forEach(function(itemI){
                me.add(itemI);
            });

        // If just got results - are result for consider-fields-dependent field, autofetched after one of consider-field's
        // value was changed and we have no results that conform to consider-fields values, we disable dependent combo
        } else me.setDisabled(true);

        // Fire 'refreshchildren' event
        me.fireEvent('refreshchildren', me, parseInt(responseData['found']));
    },

    /**
     * Function acts identical as same parent class function, but with one difference - if 'clear' argument is set to
     * boolean 'true', then combo will be not only disabled, but cleared, mean there will be zero-value set up for combo
     *
     * @param force
     */
    setDisabled: function(force, clear, on){
        var me = this, sComboName = on ? on.name : false, sCombo;

        // If current combo has a consider-combo, and consider-combo is an also existing component
        if (sComboName && (sCombo = Ext.getCmp(me.bid() + sComboName))) {

            // Get consider-combo value
            var sv = sCombo.getValue() + ''; sv = sv.length == 0 ? 0 : parseInt(sv);

            // If consider-combo's value is 0, or 'force' argument is boolean 'true'
            if (sv == 0) {

                // Disable combo
                if (!on || on.required) me.callParent([true]);

                // If 'clear' argument is boolean true
                if (clear) me.setValue('');

            // Else if consider value is non-zero
            } else {

                // Disable/Enable combo
                me.callParent([force]);

                // If 'clear' argument is boolean true
                if (clear) me.setValue('');
            }

        // Else if current combo does not have a consider-combo
        } else {

            // Disable/Enable combo
            me.callParent([force]);

            // Clea combo, if it should be cleared
            if (clear) me.setValue('');
        }
    },

    /**
     * Here we override native method, for handling situations when boxes list changed
     *
     * @return {Boolean}
     */
    isDirty: function(){
        var c = this.getBoxes(), a, d = c.length, me = this;

        // If any of boxes are dirty - return true
        for (a = 0; a < d; a++) if (c[a].isDirty()) return true;

        // Return
        return !me.isEqual(me.getValue(), me.originalValue) && (!me.disabled || !d);
    },

    /**
     * If some consider-field was changed - reload current field's combo data
     *
     * @param sbl
     * @param data
     */
    onConsiderChange: function (sbl, data) {
        var me = this, stl, request = {mode: 'refresh-children'};

        // Do not refresh children if `satellite` flag within consider-config is non-true
        if (!sbl.satellite) return;

        // Check whether it will be good to disable, and if so - do it
        me.setDisabled(false, true, sbl);

        // If still not disabled - refresh options
        if (!me.disabled) me.remoteFetch(request);
    }
}, function(){
    var me = this;

    // Borrow `getInputWidthUsage` and 'setDisabledOptions' functions from Indi.lib.form.field.Radios
    me.borrow(Indi.lib.form.field.Radios, ['getInputWidthUsage', 'setDisabledOptions']);

    // Borrow `_afterRender` and 'bid' functions from Indi.lib.form.field.Combo
    me.borrow(Indi.lib.form.field.Combo, ['_afterRender', 'bid']);

    // Borrow other dimension-usage-detection functions from Ext.form.field.Base
    me.borrow(Ext.form.field.Base, ['getHeightUsage', 'getWidthUsage', 'getLabelWidthUsage']);
});
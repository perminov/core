/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.CkEditor', {

    // @inheritdoc
    mcopwso: ['editorCfg'],

    // @inheritdoc
    extend: 'Ext.form.field.TextArea',

    // @inheritdoc
    alternateClassName: 'Indi.form.CkEditor',

    // @inheritdoc
    alias: 'widget.ckeditor',

    /**
     * These prop names are used in situation when:
     * 1. Current component is used to represent a field of a certain row, lets call it 'current row'.
     * 2. If that current row have any properties, that are in 'editorCfgPickFromRowProps' list, their values
     *    will be used and applied to CKEDITOR instance, instead of default values
     *
     * Currently, this featured is used when current component is representing 'detailsHtml' field of any
     * row, instantiated from Staticblock_Row class (it's a php class, not javascript class)
     */
    editorCfgPickFromRowProps: ['width', 'height', 'bodyClass', 'style', 'script', 'sourceStripper'],

    /**
     * Default config for CKEDITOR instance, that current component is daling with
     */
    _editorCfg: {
        toolbar: [
            {items: ['Source', 'Print']},
            {items: [ 'Paste', 'PasteText', 'PasteFromWord', 'Table']},
            {items: [ 'Image', 'Flash', 'oembed','Link', 'Unlink', 'Anchor']},
            {items: [ 'Bold', 'Italic', 'Underline']},
            {items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent']},
            {items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
            {items: ['Format']},
            {items: ['Font']},
            {items: ['FontSize' ]},
            {items: [ 'TextColor', 'BGColor', '-', 'Blockquote', 'CreateDiv' ]},
            {items: [ 'Maximize', 'ShowBlocks', 'Find', '-', 'RemoveFormat'  ]}
        ],
        toolbarNowrapWidth: 1254,
        enterMode: (typeof CKEDITOR == 'undefined') ? 2 : CKEDITOR.ENTER_BR,
        uiColor: '#B8D1F7',
        defaultWidth: 600,
        defaultHeight: 50,
        resize_minWidth: 100,
        resize_minHeight: 50,
        language: Indi.lang.name
    },

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Prevent editorCfg to be overridden in case is more than 1 instance exists
        me.editorCfg = Ext.Object.merge({}, this._editorCfg);

        // Merge field params into editor config
        if (config.field && config.field.params) Ext.Object.merge(me.editorCfg, config.field.params);

        // Merge field params, specified especially for current row, into editor config
        me.pickEditorCfgFromRowProps(config);

        // Call parent constructor
        me.callParent(arguments);

        // Do some adjustments for editor config
        me.adjustEditorCfg();
    },

    /**
     * Do some adjustments for editor config
     */
    adjustEditorCfg: function() {
        var me = this, i;

        // Setup labelAlign as 'top', so editor will be wide, if needed
        if (me.editorCfg.wide == 'true') me.labelAlign = 'top';

        // 1.Convert contentsCss param to json array, if currently it is a stringified json
        if (Ext.isString(me.editorCfg.contentsCss) && me.editorCfg.contentsCss.match(/^\[/))
            me.editorCfg.contentsCss = Ext.JSON.decode(me.editorCfg.contentsCss);

        // 2.If contentsCss param is not an array - convert it to array with itself as first array item
        // 3.Push 'style' param to the contentsCss array
        // 4. Prepend contentsCss paths with Indi.std
        if (!me.editorCfg.contentsCss) me.editorCfg.contentsCss = [];
        else if (!Array.isArray(me.editorCfg.contentsCss)) me.editorCfg.contentsCss = [me.editorCfg.contentsCss];
        if (me.editorCfg.style) me.editorCfg.contentsCss.push(me.editorCfg.style);
        me.editorCfg.contentsCss.push('body{min-width: 100%; margin: 0;}');
        for (i = 0; i < me.editorCfg.contentsCss.length; i++)
            if (me.editorCfg.contentsCss[i].match(/^\/.*\.css$/))
                me.editorCfg.contentsCss[i] = Indi.std + me.editorCfg.contentsCss[i];

        // 1.Convert contentsJs param to json array, if currently it is a stringified json
        if (Ext.isString(me.editorCfg.contentsJs) && me.editorCfg.contentsJs.match(/^\[/))
            me.editorCfg.contentsJs = Ext.JSON.decode(me.editorCfg.contentsJs);

        // 2.If contentsJs param is not an array - convert it to array with itself as first array item
        // 3.Push 'script' param to the contentsJs array
        // 4.Prepend contentsJs paths with Indi.std
        if (!me.editorCfg.contentsJs) me.editorCfg.contentsJs = [];
        else if (!Array.isArray(me.editorCfg.contentsJs)) me.editorCfg.contentsJs = [me.editorCfg.contentsJs];
        if (me.editorCfg.script) me.editorCfg.contentsJs.push(me.editorCfg.script);
        for (i = 0; i < me.editorCfg.contentsJs.length; i++)
            if (me.editorCfg.contentsJs[i].match(/^\/.*\.js$/))
                me.editorCfg.contentsJs[i] = Indi.std + me.editorCfg.contentsJs[i];

        // Setup readOnly mode if needed
        if (me.readOnly) {
            me.editorCfg.removeDialogTabs = 'link:upload;image:Upload;flash:Upload';
            me.editorCfg.readOnly = true;
        }
    },

    /**
     * Prepare an object, containing editor config properties, picked from corresponding properties of current row
     *
     * @param config
     */
    pickEditorCfgFromRowProps: function(config) {
        var me = this, picked = {}, rowProp;

        // If config.row is not an object - return
        if (!Ext.isObject(config.row)) return;

        // Try pick props
        for (var i = 0; i < me.editorCfgPickFromRowProps.length; i++) {
            rowProp = config.name + Indi.ucfirst(me.editorCfgPickFromRowProps[i]);

            // If width and/or height are picked from row props, but picker values are 0 - use default values
            if (['width', 'height'].indexOf(me.editorCfgPickFromRowProps[i]) != -1)
                if (config.row[rowProp] && !parseInt(config.row[rowProp]))
                    config.row[rowProp] = me.editorCfg['default' + Indi.ucfirst(me.editorCfgPickFromRowProps[i])];

            if (config.row[rowProp]) picked[me.editorCfgPickFromRowProps[i]] = config.row[rowProp];
        }

        // Merge existing editor config properties with picked properties
        Ext.Object.merge(me.editorCfg, picked);
    },

    /**
     * Get the CKEDITOR instance
     *
     * @return {*}
     */
    getEditor: function() {
        return CKEDITOR.instances[this.inputId];
    },

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent();

        // Setup CKEDITOR instance
        CKFinder.setupCKEditor(null, Indi.std + '/library/ckfinder/');
        CKEDITOR.replace(me.inputId, me.editorCfg);

        // Setup center alignment for editor, if 'wide' editor config params is set to 'true'
        if (me.editorCfg.wide == 'true') me.bodyEl.setStyle('text-align', 'center');

        // Setup interval for value live pickup from editor
        me.mirrorInterval = setInterval(function(){
            me.setValue(me.getEditor().getData(), true);
        }, 250, me);
    },

    // @inheritdoc
    setValue: function(value, dontUpdateEditor) {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Set editor data
        if (me.getEditor() && !dontUpdateEditor) me.getEditor().setData(value);
    },

    // @inheritdoc
    onDestroy: function() {
        var me = this;

        // Clear mirror interval
        clearInterval(me.mirrorInterval);

        // Delete CKEDITOR instance
        me.getEditor().destroy();

        // Call parent
        me.callParent();
    },

    /**
     * Display the browser's native print dialog for printing editor contents
     */
    print: function() {
        CKEDITOR.tools.callFunction(9, this.getEditor());
    },

    /**
     * Get this field's input actual height usage
     *
     * @return {Number}
     */
    getHeightUsage: function() {
        var me = this, ihu = me.getInputHeightUsage();

        // Return
        return me.labelAlign == 'top' ? ihu + me.labelCell.getHeight() : ihu;
    },

    /**
     *
     * @return {Number}
     */
    getInputHeightUsage: function() {
        var me = this, oneLineSelfHeight = 28, btwLineSpaceHeight = 7, wu = me.getInputWidthUsage(),
            topBarLineQty = Math.ceil(me.editorCfg.toolbarNowrapWidth / wu), btmBarHeight = 27,
            topBarHeight = oneLineSelfHeight * topBarLineQty + btwLineSpaceHeight * (topBarLineQty + 1),
            bothBarsHeight = topBarHeight + btmBarHeight + 3;

        // Return
        return bothBarsHeight + parseInt(me.editorCfg.height);
    },

    /**
     * Get this field's input actual width usage
     *
     * @return {Number}
     */
    getInputWidthUsage: function() {
        var me = this;

        // If me.editorCfg.width is set - this means that exact width WAS set at one of the following stages:
        // * row-level (picked from row props)
        // * field-level (picked from field props)
        // * element-level (picked from field's element props)
        if (me.editorCfg.width) return parseInt(me.editorCfg.width) + 2 /* borders */;

        // Else if `labelAlign` is 'top' - we assume that editor will use all available width,
        // that will be equal to the width of labelCell, as it will be above editor
        else if (me.labelAlign == 'top') return me.labelCell.getWidth();

        // Else we assume that editor is at the right side from labelCell,
        // so (as both cells width are equal) we return labelCell's width
        else return me.labelCell.getWidth();
    }
});
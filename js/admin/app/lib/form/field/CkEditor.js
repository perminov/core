/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.CkEditor', {

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
    editorCfg: {
        toolbar: [
            {items: ['Source', 'Preview']},
            {items: [ 'Paste', 'PasteText', 'PasteFromWord', 'Table']},
            {items: [ 'Image', 'Flash', 'oembed','Link', 'Unlink']},
            {items: [ 'Bold', 'Italic', 'Underline']},
            {items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent']},
            {items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
            {items: ['Format']},
            {items: ['Font']},
            {items: ['FontSize' ]},
            {items: [ 'TextColor', 'BGColor', '-', 'Blockquote', 'CreateDiv' ]},
            {items: [ 'Maximize', 'ShowBlocks', 'Find', '-', 'RemoveFormat'  ]}
        ],
        enterMode: CKEDITOR.ENTER_BR,
        uiColor: '#B8D1F7'
    },

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Merge field params into editor config
        Ext.Object.merge(me.editorCfg, config.field.params);

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
        if (!Array.isArray(me.editorCfg.contentsCss)) me.editorCfg.contentsCss = [me.editorCfg.contentsCss];
        me.editorCfg.contentsCss.push(me.editorCfg.style);
        for (i = 0; i < me.editorCfg.contentsCss.length; i++)
            if (me.editorCfg.contentsCss[i].match(/^\/.*\.css$/))
                me.editorCfg.contentsCss[i] = Indi.std + me.editorCfg.contentsCss[i];

        // 1.Convert contentsJs param to json array, if currently it is a stringified json
        if (Ext.isString(me.editorCfg.contentsJs) && me.editorCfg.contentsJs.match(/^\[/))
            me.editorCfg.contentsJs = Ext.JSON.decode(me.editorCfg.contentsJs);

        // 2.If contentsJs param is not an array - convert it to array with itself as first array item
        // 3.Push 'script' param to the contentsJs array
        // 4.Prepend contentsJs paths with Indi.std
        if (!Array.isArray(me.editorCfg.contentsJs)) me.editorCfg.contentsJs = [me.editorCfg.contentsJs];
        me.editorCfg.contentsJs.push(me.editorCfg.script);
        for (i = 0; i < me.editorCfg.contentsJs.length; i++)
            if (me.editorCfg.contentsJs[i].match(/^\/.*\.js$/))
                me.editorCfg.contentsJs[i] = Indi.std + me.editorCfg.contentsJs[i];

    },

    /**
     * Prepare an object, containing editor config properties, picked from corresponding properties of current row
     *
     * @param config
     */
    pickEditorCfgFromRowProps: function(config) {
        var me = this, picked = {}, rowProp;
        for (var i = 0; i < me.editorCfgPickFromRowProps.length; i++) {
            rowProp = config.name + Indi.ucfirst(me.editorCfgPickFromRowProps[i]);
            if (config.row[rowProp]) picked[me.editorCfgPickFromRowProps[i]] = config.row[rowProp];
        }
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

        // Update component value if editor value was changed
        me.getEditor().on('blur', function(eventInfo) {
            me.setValue(eventInfo.editor.getData())
        });
    }
});
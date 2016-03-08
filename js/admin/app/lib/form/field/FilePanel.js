/**
 * Special version of Ext.form.field.File, with some extended functionality
 */
Ext.define('Indi.lib.form.field.FilePanel', {

    // @inheritdoc
    extend: 'Ext.form.FieldContainer',

    // @inheritdoc
    mixins: {
        field: 'Ext.form.field.Field',
        fieldBase: 'Ext.form.field.Base'
    },

    // @inheritdoc
    alternateClassName: 'Indi.form.FilePanel',

    // @inheritdoc
    alias: 'widget.filepanel',
    items: [],
    minHeight: 24,

    // @inheritdoc
    allowBlank: true,

    /**
     * Groups of filetypes or custom list of extensions. Predefined values for filetype groups are:
     * 'image', 'office', 'draw', 'archive'. If you want to specify custom extensions as allowed, you
     * may can use "allowTypes: 'c++,html'". You can combine both group-types and exensions, by comma-enumeration,
     * e.g allowTypes: 'image,office,html,js'
     */
    allowTypes: 'image',

    /**
     * Minimum size of file. This feature works only in browsers that have native built-in window.FileReader function.
     * Initially this config is used to specify a number of bytes, but, hovewer, you can use following expressions:
     * '1K', '2M' , '3G', assuming that 'K' - is kilobytes, 'M' - megabytes and 'G' - gigabytes
     *
     */
    minSize: 0,

    /**
     * Maximum size of file. This feature works only in browsers that have native built-in window.FileReader function.
     * Initially this config is used to specify a number of bytes, but, hovewer, you can use following expressions:
     * '1K', '2.5M' , '3G', assuming that 'K' - is kilobytes, 'M' - megabytes and 'G' - gigabytes
     */
    maxSize: '10m',

    /**
     * Here we prevent marking for this component itself, as marking of one of the child components will be used instead
     */
    preventMark: true,

    // @inheritdoc
    blankText: Indi.lang.I_UPLOAD_ERR_REQUIRED,

    // @inheritdoc
    statics: {

        /**
         * File types
         */
        types: {
            image: {
                txt: Indi.lang.I_FORM_UPLOAD_ASIMG,
                ext: 'gif,png,jpeg,jpg'
            },
            office: {
                txt: Indi.lang.I_FORM_UPLOAD_ASOFF,
                ext: 'doc,pdf,docx,xls,xlsx,txt,odt,ppt,pptx'
            },
            draw: {
                txt: Indi.lang.I_FORM_UPLOAD_ASDRW,
                ext: 'psd,ai,cdr'
            },
            archive: {
                txt: Indi.lang.I_FORM_UPLOAD_ASARC,
                ext: 'zip,rar,7z,gz,tar'
            }
        },

        /**
         * Handler function, that will be placed as the value of 'onchange' file input element attribute,
         * to cover the situation, when user clicked on '<input type="file" ...>' and native browser's
         * file selection window was opened, but, instead of selecting some file and pressing 'OK' in that
         * native window, user presses 'Cancel'. In such scenario value of '<input type="file" ...>' will
         * be set to "" (empty string), but this value change is not (for some reason) captured by Ext's
         * 'xtype: filefield' component, and 'change' event is not fired by that component, so we need to
         * implement such a solution
         *
         * @param dom
         */
        fileInputElDomOnChange: function(dom) {
            var ff, el;
            if (!dom.value){
                el = Ext.get(dom);
                ff = Ext.getCmp(el.up('table').up('table').attr('id'));
                ff.fireEvent('change', ff, dom.value);
            }
        }
    },

    /**
     * Preview container panel
     *
     * @return {Object}
     */
    previewWrap: function() {
        var me = this;
        return {
            alias: 'preview',
            html: me.preview,
            border: 0,
            minHeight: 20,
            maxHeight: 150
        }
    },

    /**
     * Config function for master toolbar's 'No'/'No change' radio-button
     *
     * @return {Object}
     */
    toolbar$Master$Nochange: function() {
        var me = this;

        // Config
        return {
            xtype: 'radio',
            alias: 'nochange',
            name: me.name,
            inputValue: 'r',
            checked: !(!me.value && !me.allowBlank),
            margin: '0 5 0 0',
            style: me.value ? 'top: 4px !important;' : '',
            disabled: (!me.value && !me.allowBlank) || me.readOnly,
            boxLabel: me.value ? Indi.lang.I_FORM_UPLOAD_NOCHANGE : Indi.lang.I_FORM_UPLOAD_NOFILE,
            boxLabel: me.value ? '' : Indi.lang.I_FORM_UPLOAD_NOFILE,
            tooltip: me.value ? {html: Indi.lang.I_FORM_UPLOAD_NOCHANGE, anchor: 'bottom'} : null
        }
    },

    /**
     * Config function for master toolbar's 'Delete' radio-button
     *
     * @return {Object}
     */
    toolbar$Master$Delete: function() {
        var me = this;

        // Config
        return me.value ? {
            xtype: 'radio',
            alias: 'delete',
            name: me.name,
            inputValue: 'd',
            margin: '0 5 0 0',
            boxLabel: Indi.lang.I_FORM_UPLOAD_DELETE,
            style: 'top: 4px !important;',
            boxLabel: '',
            disabled: !me.allowBlank || me.readOnly,
            tooltip: {html: Indi.lang.I_FORM_UPLOAD_DELETE, anchor: 'bottom'}

        } : null;
    },

    /**
     * Config function for master toolbar's 'Modify' radio-button
     *
     * @return {Object}
     */
    toolbar$Master$Modify: function() {
        var me = this;

        // Config
        return {
            xtype: 'radio',
            alias: 'modify',
            name: me.name,
            inputValue: 'm',
            style: 'top: 4px !important;',
            checked: !me.value && !me.allowBlank,
            value: !me.value && !me.allowBlank,
            margin: 0,
            tooltip: me.value ? {html: Indi.lang.I_FORM_UPLOAD_REPLACE, anchor: 'bottom'} : null,
            disabled: me.readOnly,
            listeners: {
                afterrender: function(rb){
                    rb.el.on('click', function(){
                        if (me.get('mode').getValue()) me.get('browse').buttonEl.dom.click();
                        if (!me.get('browsed').getValue()) me.get('browse').labelEl.dom.click();
                    });
                }
            }
        }
    },

    /**
     * Special function for short-hand access to any component, that have `alias` property
     *
     * @param alias
     * @return {*}
     */
    get: function(alias) {
        return this.query('[alias="'+alias+'"]')[0];
    },

    /**
     * Master toolbar 'Browsed' item config function
     *
     * @return {Object}
     */
    toolbar$Master$Browsed: function() {
        var me = this;

        // Congfig
        return {
            xtype: 'textfield',
            alias: 'browsed',
            height: 17,
            fp: me,
            fieldLabel: me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE_WITH : '',
            fieldLabel: '',
            readOnly: true,
            labelWidth: Indi.metrics.getWidth(me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE_WITH : ''),
            labelWidth: Indi.metrics.getWidth(''),
            labelPad: 3,
            labelSeparator: '',
            emptyText: Indi.lang.I_FORM_UPLOAD_MODE_LOCAL_PLACEHOLDER,
            allowBlank: me.allowBlank,
            allowTypes: me.allowTypes,
            minSize: me.minSize,
            maxSize: me.maxSize,
            blankText: me.blankText,
            style: {margin: '-1px 4px 0 0px !important'},
            padding: 0,
            disabled: me.readOnly,
            getErrors: function() {
                // Setup auxiliary variables
                var me = this, errors = [], file = me.fp.getValue(), rex, nativeFile, sizeType,
                    sizeTypeO = {K: 1, M: 2, G: 3}, maxSize, minSize, d;

                // If there wouldn't be any change, and `allowBlank` is `true` - return
                if (me.fp.get('nochange').checked && (me.allowBlank || me.fp.value)) return [];

                // If uploaded file is going to be deleted, and `allowBlank` is `true` - return
                if (me.fp.get('delete') && me.fp.get('delete').checked && me.allowBlank) return [];

                // Check if current value is not empty
                if (!me.allowBlank && !file) return [me.blankText];

                // Check file type
                if (me.allowTypes && Ext.isString(me.allowTypes)) {

                    // Get the array of type-groups, and declare auxilliary variables
                    var aTypeA = me.allowTypes.split(','), aTypeI, aTypeIExt, aTypeAExt = [], msg = '', dTypeI,
                        msgTypeA = [], msgTypeILast, aTypeAExtLast, customExtA = [], customExtILast;

                    // Get the whole list of allowed extensions
                    for (var i = 0; i < aTypeA.length; i++)
                        if (!Ext.isObject(aTypeI = Indi.form.FilePanel.types[aTypeA[i]])) customExtA.push(aTypeA[i]);
                        else if (Ext.isString(aTypeIExt = aTypeI.ext)) aTypeAExt = aTypeAExt.concat(aTypeIExt.split(','));

                    // Setup regular expression for file extension check
                    rex = new RegExp('\.(' + Indi.pregQuote(aTypeAExt.concat(customExtA).join(';')).split(';').join('|') + ')$', 'i');

                    // Check the file extension
                    if (!rex.test(file)) {

                        // Build array, containing parts of error message, each mentioning a certain allowed type group
                        for (i = 0; i < aTypeA.length; i++)
                            if (Ext.isObject(dTypeI = Indi.form.FilePanel.types[aTypeA[i]]))
                                msgTypeA.push(dTypeI.txt);

                        // Prepare the part of the error message, containing abstract list of allowed extenstions
                        if (customExtA.length) {
                            msg += Indi.lang.I_FORM_UPLOAD_OFEXT + ' ';
                            if (msgTypeA.length) msg += customExtA.join(', ').toUpperCase() + ' ' + Indi.lang.I_OR + ' '; else {
                                customExtILast = customExtA.pop();
                                msg += customExtA.length ? customExtA.join(', ').toUpperCase() + ' ' + Indi.lang.I_OR + ' ' : '';
                                msg += customExtILast.toUpperCase();
                            }
                        }

                        // Prepare the part of the error message, containing human-friendly file-type groups mentions
                        if (msgTypeA.length) {
                            msg += Indi.lang.I_BE + ' ';
                            msgTypeILast = msgTypeA.pop();
                            msg += msgTypeA.length ? msgTypeA.join(', ') + ' ' + Indi.lang.I_OR + ' ' : '';
                            msg += msgTypeILast;

                            msg += ' ' + Indi.lang.I_FORM_UPLOAD_INFMT + ' ';

                            // Prepare the part of the error message, containing merged extension list for
                            // all human-friendly file-type groups mentions
                            aTypeAExtLast = aTypeAExt.pop();
                            msg += aTypeAExt.length ? aTypeAExt.join(', ').toUpperCase() + ' ' + Indi.lang.I_OR + ' ' : '';
                            msg += aTypeAExtLast.toUpperCase();
                        }

                        // Push the error message to the error messages array
                        errors.push(msg);
                    }
                }

                // If filepanel's mode is not 'get-by-url', and browser have native built-in FileReader function/object
                if (!me.fp.get('mode').checked
                    && window.FileReader
                    && me.fp.get('browse')
                    && me.fp.get('browse').fileInputEl
                    && (nativeFile = me.fp.get('browse').fileInputEl.dom.files[0])) {

                    // Check file size doesn't exceed `maxSize` requirement
                    if (d = parseFloat(me.maxSize)) {
                        sizeType = (me.maxSize + '').replace(d.toString(), '').toUpperCase();
                        if (maxSize = d * Math.pow(1024, sizeTypeO.hasOwnProperty(sizeType) ? sizeTypeO[sizeType] : 0)) {
                            if (nativeFile.size > maxSize) {
                                errors.push(
                                    Indi.lang.I_FORM_UPLOAD_HSIZE + ' ' + Indi.lang.I_FORM_UPLOAD_NOTGT + ' '
                                        + Indi.size2str(maxSize).toUpperCase()
                                );
                            }
                        }
                    }

                    // Check file size is not less than `minSize` requirement
                    if (d = parseFloat(me.minSize)) {
                        sizeType = (me.minSize + '').replace(d.toString(), '').toUpperCase();
                        if (minSize = d * Math.pow(1024, sizeTypeO.hasOwnProperty(sizeType) ? sizeTypeO[sizeType] : 0)) {
                            if (nativeFile.size > minSize) {
                                errors.push(Indi.lang.I_FORM_UPLOAD_HSIZE + ' ' + Indi.lang.I_FORM_UPLOAD_NOTGT + ' '
                                    + Indi.size2str(minSize).toUpperCase());
                            }
                        }
                    }
                }

                // Return faced errors
                return errors.length ? [Indi.lang.I_FILE + ' ' + Indi.lang.I_SHOULD + ' ' + errors.join(', ' + Indi.lang.I_AND +' ')] : [];
            },
            listeners: {
                afterrender: function() {
                    this.inputEl.setStyle({background: 'rgba(255, 255, 255, 0.5)'});
                    this.inputEl.on('click', function(){
                        if (me.get('mode').getValue()) {
                            me.get('modify').setValue(true);
                        } else {
                            me.get('browse').labelEl.click();
                        }
                    });
                }
            }
        }
    },

    /**
     * Config builder function for the native Ext's filefiled component
     *
     * @return {Object}
     */
    toolbar$Master$Browse: function() {
        var me = this;

        // Config
        return {
            xtype: 'filefield',
            inputName: me.name,
            buttonOnly: true,
            alias: 'browse',
            name: me.name,
            buttonText: me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE : Indi.lang.I_FORM_UPLOAD_BROWSE,
            buttonText: me.value ? '&raquo;' : Indi.lang.I_FORM_UPLOAD_BROWSE,
            height: 16,
            margin: '0 5 0 0',
            margin: '0 8 0 0',
            width: Indi.metrics.getWidth(me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE : Indi.lang.I_FORM_UPLOAD_BROWSE),
            width: Indi.metrics.getWidth(me.value ? '&raquo;' : Indi.lang.I_FORM_UPLOAD_BROWSE) + 1,
            disabled: me.readOnly,
            buttonConfig: {
                border: 0,
                margin: '0 0 0 -1',
                padding: 0
            },
            listeners: {
                change: function(ff, file) {
                    me.get(file ? 'modify' : 'nochange').setValue(true);
                    me.get('browsed').setValue(file);
                    if (file) me.get('browsed').inputEl.dom.scrollLeft = me.get('browsed').inputEl.dom.scrollWidth;
                },
                afterrender: function(ff) {
                    ff.buttonEl.on('click', function(){
                        if (me.get('mode').getValue()) {
                            me.get('modify').setValue(true);
                            me.get('browsed').focus();
                        }
                    });
                }
            }
        }
    },

    /**
     * Config builder function for master toolbar 'Type' item. This item
     * provides info about extension and mime-type of an uploaded file
     *
     * @return {Object}
     */
    toolbar$Master$Type: function () {
        var me = this;

        // If component have no info about uploaded file - return
        if (!me.data) return null;

        // 'Type' item config
        return {
            xtype: 'displayfield',
            value: me.data.ext.toUpperCase(),
            width: Indi.metrics.getWidth(me.data.ext.toUpperCase()),
            tooltip: {
                html: me.data.mime,
                anchor: 'bottom',
                constrainParent: false
            }
        }
    },

    /**
     * Config builder function for master toolbar 'Size' item. This item provides info about size
     * of an uploaded file, and provide an ability to download it
     *
     * @return {Object}
     */
    toolbar$Master$Size: function () {
        var me = this, size;

        // If component have no info about uploaded file - return
        if (!me.data) return null;

        // Get the string representation of a filesize
        size = Indi.size2str(me.data.size);

        // 'Size' item config
        return {
            xtype: 'displayfield',
            value: '<a style="text-decoration: none;" href="' + Indi.pre + '/auxiliary/download/id/' + me.row.id + '/field/'+me.field.id + '/">'
                + size + '</a>',
            width: Indi.metrics.getWidth(size),
            tooltip: {
                html: Indi.lang.I_FORM_UPLOAD_SAVETOHDD,
                anchor: 'bottom',
                constrainParent: false
            }
        }
    },

    /**
     * Config builder function for master toolbar 'Dims' item. This item provides info about real dimensions
     * of an uploaded image or swf file, and provide an ability to open that file in a new browser window
     *
     * @return {Object}
     */
    toolbar$Master$Dims: function () {
        var me = this;

        // 'Dims' item config
        return {
            xtype: 'displayfield',
            value: '<a style="text-decoration: none;" href="' + Indi.std + me.value + '" target="_blank">' + me.data.width + 'x' + me.data.height + '</a>',
            width: Indi.metrics.getWidth(me.data.width + 'x' + me.data.height),
            tooltip: {
                html: Indi.lang.I_FORM_UPLOAD_ORIGINAL,
                anchor: 'bottom',
                constrainParent: false
            }
        }
    },

    /**
     * Master toolbar config setup function
     *
     * @return {Object}
     */
    toolbar$Master: function() {
        var me = this;

        // Master toolbar config
        return {
            xtype: 'toolbar',
            alias: 'toolbar',
            border: 0,
            height: 22,
            padding: '0 3 0 ' + (me.hasPreview ? '5' : '0'),
            style: {background: 'rgba(255, 255, 255, 0.7)'},
            defaults: {style: {padding: 0}},
            items: [
                me.value ? me.toolbar$Master$Type() : null,
                me.value ? {xtype: 'displayfield', value: '&nbsp;&raquo;&nbsp;'} : null,
                me.value ? me.toolbar$Master$Size() : null,
                me.value ? {xtype: 'displayfield', value: '&nbsp;&raquo;&nbsp;'}: null,
                me.preview ? me.toolbar$Master$Dims() : null,
                me.preview ? {xtype: 'displayfield', value: '&nbsp;&raquo;&nbsp;'} : null,
                me.toolbar$Master$Nochange() ,
                me.toolbar$Master$Delete(),
                me.toolbar$Master$Modify(),
                me.toolbar$Master$Browse(),
                me.toolbar$Master$Browsed(),
                me.toolbar$Master$Mode()
            ]
        }
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;
        me.data = me.row.view(me.name);
        me.preview = me.getPreview();
        me.items = [me.toolbar$Master(), me.previewWrap()];
        me.callParent();
        me.mixins.fieldBase._initComponent.call(this, arguments);
        me.initField();
    },

    // @inheritdoc
    initValue: function() {
        var me = this,
            valueCfg = me.value;
        me.originalValue = me.lastValue = valueCfg || me.getValue();
        if (valueCfg) {
            me.setValue(valueCfg);
        }
    },

    // @inheritdoc
    afterRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Adjust master toolbar position, for it to be at most top both by z-index and by Y-position
        me.get('toolbar').el.setStyle({position: 'absolute', 'z-index': 1});

        // Bind handler for field resize, and adjust label target
        me.on('resize', me.onResize);
        me.get('browse').labelEl.attr('for', me.get('browse').fileInputEl.id);

        // Provide firing 'change' event in case if user started browsing a file for upload, but then pressed 'Cancel'
        // button in the native browser's file selection window, that causes emptying of fileInputEl.dom.value, that
        // is not covered by the execution of expression
        //    me.fileInputEl.on({
        //        scope: me,
        //        change: me.onFileChange
        //    });
        // in Ext's filefield component source code
        me.get('browse').fileInputEl.attr('onchange', 'Indi.form.FilePanel.fileInputElDomOnChange(this)');

        // If component's value is previewable
        if (me.preview) {

            // Get preview inner element
            me.embed = me.bodyEl.select('[alias="embed"]').first();

            // Bind handler on 'click' event
            me.embed.on('click', me.expandEmbed, me);

            // Setup positioning
            me.bodyEl.select('[alias="embed"]').first().setStyle({position: 'absolute', top: '50%'});
        }

        // Fire `enablebysatellite` event
        me.mixins.fieldBase._afterRender.call(this, arguments);
    },

    /**
     * Provide preview auto-fit and toolbar width auto-adjustment each time field was resized
     */
    onResize: function() {

        // Setup auxiliary variables
        var me = this, availWidth = me.bodyEl.getWidth(), actualWidth, attrWidth, attrHeight;

        // If field has a value, and has a data containing dimension info
        if (me.data && me.data.width) {

            // Calculate values for 'width' and 'height' attributes of img/embed tag,
            // representing the uploaded file preview
            actualWidth = me.data.width;
            if (availWidth >= actualWidth) {
                attrWidth = actualWidth;
                attrHeight = me.data.height;
            } else  {
                attrWidth = availWidth;
                attrHeight = Math.ceil(me.data.height * (attrWidth/actualWidth));
            }

            // Apply these attributes
            me.bodyEl.select('[alias="embed"]').first().setSize(attrWidth, attrHeight);
            me.get('preview').setHeight(attrHeight);
            me.bodyEl.select('[alias="embed"]').first().setStyle({marginTop: '-' + Math.ceil(attrHeight/2) + 'px'});
        }

        // Adjust master toolbar width, for it to be the same as field body width
        me.get('toolbar').setWidth(availWidth);

        // If component's value is previewable
        if (me.preview) {

            // Get preview inner element
            me.embed = me.bodyEl.select('[alias="embed"]').first();

            // If component's resize caused the change of clipping state of embed element - setup appropriate
            // value for css 'cursor' property. It's need for collapse/expand feature toggling
            me.embed.setStyle('cursor', me.embed.getHeight() > me.get('preview').maxHeight ? 'pointer' : 'default');
        }
    },

    /**
     * Build 'mode' checkbox for filepanel master toolbar, for ability to switch between local-file and
     * remote-file file selection abilities
     *
     * @return {Object}
     */
    toolbar$Master$Mode: function() {
        var me = this;

        // 'Mode' master toolbar item config
        return {
            xtype: 'checkbox',
            alias: 'mode',
            style: 'top: 4px !important',
            disabled: me.readOnly,
            tooltip: {
                html: Indi.lang.I_FORM_UPLOAD_MODE_TIP,
                anchor: 'bottom',
                constrainOwner: false
            },
            listeners: {
                change: function(cb, value) {

                    // Setup shortcuts
                    var browsed = me.get('browsed'), browse = me.get('browse');

                    // If web-link upload mode is has just been turned On
                    if (value) {

                        // 'Browsed' item adjustments
                        browsed.setValue(browsed.urlValue);
                        browsed.inputEl.attr('placeholder', Indi.lang.I_FORM_UPLOAD_MODE_REMOTE_PLACEHOLDER);
                        browsed.emptyText = Indi.lang.I_FORM_UPLOAD_MODE_REMOTE_PLACEHOLDER;
                        browsed.setReadOnly(false);
                        browsed.name = me.name;

                        // 'Browse' item adjustments
                        browse.fileInputEl.attr('disabled', 'disabled');
                        browse.fileInputEl.hide();
                        browse.buttonEl.click();

                    // Else if web-link upload mode is has just been turned Off
                    } else {

                        // 'Browsed' item adjustments
                        browsed.urlValue = browsed.getValue();
                        browsed.inputEl.attr('placeholder', Indi.lang.I_FORM_UPLOAD_MODE_LOCAL_PLACEHOLDER);
                        browsed.emptyText = Indi.lang.I_FORM_UPLOAD_MODE_LOCAL_PLACEHOLDER;
                        browsed.setValue(browse.getValue());
                        browsed.setReadOnly(true);
                        browsed.name = browsed.id;

                        // 'Browse' item adjustments
                        browse.fileInputEl.removeAttr('disabled');
                        browse.fileInputEl.show();
                        if (!browse.fileInputEl.dom.value && !me.get('nochange').disabled)
                            me.get('nochange').setValue(true);
                    }
                }
            }
        }
    },

    /**
     * Build the uploaded file preview. Currently works only for image-files and flash-files
     * with usage of <img> or <embed> tags, respectively.
     *
     * @return {String}
     */
    getPreview: function() {
        var me = this, preview, fakeimg = ['vnd.djvu'];

        // If there is currently no value - return
        if (!me.value) preview = '';

        // If value type is 'image' - build and return image preview
        else if (me.data.mime.split('/')[0] == 'image' && fakeimg.indexOf(me.data.mime.split('/')[1]) == -1)
            preview = me.previewImg();

        // If value extenion is 'swf' - build and return flash/swf preview
        else if (me.data.ext == 'swf') preview = me.previewSwf();

        // Setup hasPreview flag
        me.hasPreview = !!preview;

        // Return preview
        return preview;
    },

    /**
     * Swf preview builder
     *
     * @return {String}
     */
    previewSwf: function() {
        var me = this, embedSpec = {
            tag: 'embed',
            alias: 'embed',
            src: Indi.std + me.data.src + '?' + me.data.mtime,
            type: 'application/x-shockwave-flash',
            pluginspace: 'http://www.macromedia.com/go/getflashplayer',
            play: 'true',
            loop: 'true',
            menu: 'true'
        };
        return Ext.DomHelper.markup(embedSpec);
    },

    /**
     * Img preview builder
     *
     * @return {String}
     */
    previewImg: function() {
        var me = this, imgSpec = {
            tag: 'img',
            alias: 'embed',
            src: Indi.std + me.data.src + '?' + me.data.mtime
        };
        return Ext.DomHelper.markup(imgSpec);
    },

    /**
     * Preview expand/collapse
     */
    expandEmbed: function() {
        var me = this, expandHeight = me.embed.getHeight();

        // If embed element's 'cursor' css property in not 'pointer' - return
        // Value of that 'cursor' property may change dynamically on each component's resize
        if (me.embed.getStyle('cursor') != 'pointer') return;

        // Setup percentage height instead of in px units for preview panel body
        me.get('preview').body.setHeight('100%');

        // Expand/collapse
        if (me.embed.attr('expanded') != 'true') {
            me.get('preview').el.setHeight(expandHeight, true);
            me.embed.attr('expanded', 'true');
        } else {
            me.get('preview').el.setHeight(me.get('preview').maxHeight, true);
            me.embed.attr('expanded', 'false');
        }
    },

    // @inheritdoc
    onFieldAdded: function(field) {
        var me = this;
        me.mon(field, 'change', me.checkChange, me);
        me.callParent(arguments);
    },

    // @inheritdoc
    checkChange: function() {
        if (!this.suspendCheckChange) {
            var me = this,
                newAction = me.query('[isRadio][checked]')[0].alias,
                oldAction = me.oldAction || 'nochange',
                newBrowsed = me.get('browsed').value,
                oldBrowsed = me.oldBrowsed || '',
                newMode = me.get('mode').checked,
                oldMode = me.oldMode || false;
            if ((!me.isEqual(newAction, oldAction) || !me.isEqual(newBrowsed, oldBrowsed) || !me.isEqual(newMode, oldMode)) && !me.isDestroyed) {
                me.lastValue = newBrowsed;
                me.oldAction = me.query('[isRadio][checked]')[0].alias;
                me.oldBrowsed = me.get('browsed').value;
                me.oldMode = me.get('mode').checked;
                me.fireEvent('change', me, newBrowsed, oldBrowsed);
                me.onChange(newBrowsed, oldBrowsed);
            }
        }
    },

    // @inheritdoc
    isDirty: function() {
        return (!this.allowBlank && !this.value ? !this.get('modify').checked : !this.get('nochange').checked) || this.get('browsed').isDirty() || this.get('mode').isDirty();
    },

    // @inheritdoc
    reset: function() {
        var me = this, hadError = me.hasActiveError(), preventMark = me.preventMark;
        me.preventMark = true;

        // Reset all child components
        me.batchChanges(function() {
            var subs = me.query('*'), s, sLen  = subs.length;
            for (s = 0; s < sLen; s++) if (typeof subs[s].reset == 'function') subs[s].reset();
        });

        // Restore value of `preventMark` property from backup
        me.preventMark = preventMark;

        // Unset errors and update layout
        me.unsetActiveError();
        if (hadError) me.updateLayout();
    },

    // @inheritdoc
    getValue: function() {
        var me = this;

        return me.get('nochange').checked
            ? me.value
            : (me.get('delete') && me.get('delete').checked
                ? ''
                : (me.get('mode').checked
                    ? me.get('browsed').value
                    : (me.get('browse').fileInputEl
                        ? me.get('browse').fileInputEl.dom.value
                        : (me.value))));
    },

    // @inheritdoc
    validate: function() {
        var me = this, wasValid = !me.hasActiveError(), isValid = me.get('browsed').validate();
        if (isValid !== wasValid) {
            me.fireEvent('validitychange', me, isValid);
            me.updateLayout();
        }
        return isValid;
    },

    /**
     * Tunnel to [alias='browsed'] component's same method
     *
     * @return {Boolean}
     */
    hasActiveError: function() {
        return this.get('browsed').hasActiveError();
    },

    /**
     * Tunnel to [alias='browsed'] component's same method
     *
     * @return {Boolean}
     */
    isValid: function() {
        return this.get('browsed').isValid();
    },

    /**
     * Tunnel to [alias='browsed'] component's same method
     *
     * @return {Boolean}
     */
    markInvalid: function(msg) {
        this.get('browsed').markInvalid(msg);
    },

    /**
     * Tunnel to [alias='browsed'] component's same method
     *
     * @return {Boolean}
     */
    clearInvalid: function() {
        this.get('browsed').clearInvalid();
    },

    /**
     * Ensure 'fieldBase'-mixin's _onChange method will be called
     */
    onChange: function() {

        // Setup auxilliary variables
        var me = this;

        // Call parent
        //me.callParent(arguments);

        // Call mixin's _onChange() method
        me.mixins.fieldBase._onChange.call(this, arguments);
    }
});

/**
 * Special version of Ext.form.field.File, with some extended functionality
 */
Ext.define('Indi.lib.form.field.FilePanel', {

    // @inheritdoc
    extend: 'Ext.form.FieldContainer',

    // @inheritdoc
    alternateClassName: 'Indi.form.FilePanel',

    // @inheritdoc
    alias: 'widget.filepanel',
    items: [],
    minHeight: 24,

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
            checked: true,
            margin: '0 5 0 0',
            style: me.value ? 'top: 4px !important;' : '',
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
            margin: 0,
            tooltip: me.value ? {html: Indi.lang.I_FORM_UPLOAD_REPLACE, anchor: 'bottom'} : null,
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
            fieldLabel: me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE_WITH : '',
            fieldLabel: '',
            readOnly: true,
            labelWidth: Indi.metrics.getWidth(me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE_WITH : ''),
            labelWidth: Indi.metrics.getWidth(''),
            labelPad: 3,
            labelSeparator: '',
            emptyText: Indi.lang.I_FORM_UPLOAD_MODE_LOCAL_PLACEHOLDER,
            style: {margin: '-1px 4px 0 0px !important'},
            padding: 0,
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

        // Setup auxiliary variables
        var me = this, pow, size, postfix = {0: 'b', 1: 'kb', 2: 'mb', 3: 'gb', 4: 'tb', 5: 'pb'};

        // If component have no info about uploaded file - return
        if (!me.data) return null;

        // Get the uploaded file size grade
        pow = Math.floor(me.data.size.toString().length/3);

        // Get the string representation of a filesize
        size = Math.floor((me.data.size/Math.pow(1024, pow))*100)/100 + postfix[pow];

        // 'Size' item config
        return {
            xtype: 'displayfield',
            value: '<a href="' + Indi.pre + '/auxiliary/download/id/' + me.row.id + '/field/'+me.field.id + '/">'
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
            value: '<a href="' + Indi.std + me.value + '" target="_blank">' + me.data.width + 'x' + me.data.height + '</a>',
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
        me.callParent(arguments);
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

        // If component's value is previewable
        if (me.preview) {

            // Get preview inner element
            me.embed = me.bodyEl.select('[alias="embed"]').first();

            // Bind handler on 'click' event
            me.embed.on('click', me.expandEmbed, me);

            // Setup positioning
            me.bodyEl.select('[alias="embed"]').first().setStyle({position: 'absolute', top: '50%'});
        }
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
                        if (!browse.fileInputEl.dom.value) me.get('nochange').setValue(true);
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
        var me = this, preview;

        // If there is currently no value - return
        if (!me.value) preview = '';

        // If value type is 'image' - build and return image preview
        else if (me.data.mime.split('/')[0] == 'image') preview = me.previewImg();

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
    }
});

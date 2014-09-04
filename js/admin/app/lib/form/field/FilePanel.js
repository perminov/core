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
        var me = this, preview = me.preview();
        return {
            alias: 'preview',
            html: preview,
            border: 0,
            minHeight: 20
        }
    },

    /**
     * Config function for master toolbar's 'File in format' xtype:displayfield item
     *
     * @return {Object}
     */
    toolbar$Master$FileInFormat: function() {
        var me = this;

        // If field currently has no value, or value is not previewable - return
        if (!me.value || me.hasPreview) return null;

        // Config
        return {
            value: '<a href="'+Indi.pre+'/auxiliary/download/id/'+me.row.id+'/field/'+me.field.id+'/">Файл</a> в формате ' + me.data.ext,
            margin: '0 5 0 0',
            xtype: 'displayfield'
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
            boxLabel: me.value ? Indi.lang.I_FORM_UPLOAD_NOCHANGE : Indi.lang.I_FORM_UPLOAD_NOFILE
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
            boxLabel: Indi.lang.I_FORM_UPLOAD_DELETE
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
            listeners: {
                afterrender: function(rb){
                    rb.el.on('click', function(){
                        if (me.get('mode').getValue()) {
                            me.get('browse').buttonEl.dom.click();
                        }
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
            readOnly: true,
            labelWidth: Indi.metrics.getWidth(me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE_WITH : ''),
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
            height: 16,
            margin: '0 5 0 0',
            width: Indi.metrics.getWidth(me.value ? Indi.lang.I_FORM_UPLOAD_REPLACE : Indi.lang.I_FORM_UPLOAD_BROWSE),
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
                me.toolbar$Master$FileInFormat() ,
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
        me.items = [me.previewWrap(), me.toolbar$Master()];
        me.callParent(arguments);
    },

    // @inheritdoc
    afterRender: function() {
        var me = this;
        me.callParent(arguments);
        me.on('resize', me.onResize);
        me.get('browse').labelEl.attr('for', me.get('browse').fileInputEl.id);
    },

    /**
     * Provide preview auto-fit and toolbar position auto-adjustment each time field was resized
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
        }

        // Adjust master toolbar position, for it to be at most top both by z-index and by Y-position
        me.get('toolbar').el.setStyle({position: 'absolute', 'margin-top': '-' + me.get('preview').getHeight() + 'px'});

        // Adjust master toolbar width, for it to be the same as field body width
        me.get('toolbar').setWidth(availWidth);
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
            tooltip: Indi.lang.I_FORM_UPLOAD_MODE_TIP,
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
    preview: function() {
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
            src: me.data.src + '?' + me.data.mtime,
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
            src: me.data.src + '?' + me.data.mtime
        };
        return Ext.DomHelper.markup(imgSpec);
    }

    /*
    toolbar$Size: function() {
        var me = this, size, postfix = {0: 'b', 1: 'kb', 2: 'mb', 3: 'gb', 4: 'tb', 5: 'pb'};
        if (!me.value || !me.data) return;
        size = Math.floor(me.data.size.toString().length/3);
        return Math.floor((me.data.size/Math.pow(1024, size))*100)/100 + postfix[size]
    },

    toolbar$Info: function() {
        var me = this;
        if (!me.data) return;
        return me.data.mime;
    },
    */
});

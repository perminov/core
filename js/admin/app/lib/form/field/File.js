/**
 * Special version of Ext.form.field.File, with some extended functionality
 */
Ext.define('Indi.lib.form.field.FilePanel', {

    // @inheritdoc
    extend: 'Ext.form.FieldContainer',

    // @inheritdoc
    alternateClassName: 'Indi.form.File',

    // @inheritdoc
    alias: 'widget.filepanel',
    items: [],

    uploaded: function() {
        var me = this;
        return {
            name: 'uploaded',
            html: me.img(),
            border: 0,
            maxHeight: 200,
            minHeight: 18,
            height: me.value ? 200 : 18
        }
    },

    toolbarRadio$Nochange: function() {
        var me = this;
        return {
            xtype: 'radio',
            inputValue: 'r',
            checked: true,
            boxLabel: me.value ? 'Оставить' : 'Отсутствует'
        }
    },

    toolbarRadio$Delete: function() {
        var me = this;
        return me.value ? {
            xtype: 'radio',
            inputValue: 'd',
            boxLabel: 'Удалить'
        } : null;
    },

    toolbarRadio$Modify: function() {
        return {
            xtype: 'radio',
            inputValue: 'm'
        }
    },

    toolbarFile$Browse: function() {
        var me = this;
        return {
            xtype: 'filefield',
            inputName: me.name,
            buttonOnly: true,
            buttonText: me.value ? 'Заменить' : 'Выбрать',
            buttonMargin: 0,
            buttonConfig: {
                height: 15,
                border: 0,
                margin: '0 0 0 -3',
                padding: 0
            },
            listeners: {
                change: function(ff, file) {
                    ff.ownerCt.query('[inputValue="'+(file?'m':'r')+'"]')[0].setValue(true);
                }
            }
        }
    },

    toolbar: function() {
        var me = this;
        return {
            xtype: 'toolbar',
            border: 0,
            padding: '1 3 3 ' + (me.value ? '3' : '0'),
            style: {background: 'rgba(255, 255, 255, 0.7)'},
            defaults: {
                name: me.name,
                style: {padding: 0}
            },
            items: [
                me.toolbarRadio$Nochange() ,
                me.toolbarRadio$Delete(),
                me.toolbarRadio$Modify(),
                me.toolbarFile$Browse()
            ]
        }
    },
    initComponent: function() {
        var me = this;
        me.items = [me.uploaded(), me.toolbar()];
        me.callParent(arguments);
    },

    afterRender: function() {
        var me = this;
        me.callParent(arguments);
        me.on('resize', function(){
            me.getToolbar().el.setStyle({position: 'absolute', 'margin-top': '-'+me.getUploaded().getHeight()+'px'});
            me.getToolbar().setWidth(me.query('[name="uploaded"]')[0].getWidth());
        });
    },

    getToolbar: function() {
        return this.query('[xtype="toolbar"]')[0];
    },

    getUploaded: function() {
        return this.query('[name="uploaded"]')[0];
    },

    img: function() {
        var data = this.row.view(this.name);
        if (!this.value) return '';
        return '<img src="' + data.src + '?' + data.mtime + '" style="max-width: 100%;"/>';
    },
});
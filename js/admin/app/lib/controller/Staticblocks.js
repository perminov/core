Ext.define('Indi.lib.controller.Staticblocks', {

    // @inheritdoc
    extend: 'Indi.Controller',

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Staticblocks',

    // @inheritdoc
    actionsConfig: {
        form: {

            /**
             * Here we provide 'detailsHtmlWidth' and 'detailsHtmlHeight' components values to be autoset after
             * each resize of ckeditor instance, for ability ckeditor sizing to be remembered by the system
             *
             * @param item
             * @return {Object}
             */
            formItem$DetailsHtml: function(item) {
                var me = this;

                return {
                    considerOn: [{
                        name: 'type',
                        clear: false
                    }],
                    editorCfg: {
                        resize_dir: 'both',
                        on: {
                            resize: function(evt) {
                                var size = Ext.get(evt.editor.ui.space('contents').$).getSize();
                                Ext.getCmp(me.row.id).query('[name="detailsHtmlWidth"]')[0].setValue(size.width);
                                Ext.getCmp(me.row.id).query('[name="detailsHtmlHeight"]')[0].setValue(size.height);
                            }
                        }
                    },
                    listeners: {
                        considerchange: function(c, d) {
                            c.setVisible(d.type == 'html');
                        }
                    }
                }
            },

            formItem$Type: {nojs: true},

            /**
             * Here we setup visibility and actual value
             *
             * @param item
             * @return {Object}
             */
            formItem$DetailsHtmlWidth: function(item) {
                var me = this;

                // Return
                return {
                    hidden: true,
                    value: me.ti().row.id && Ext.isNumeric(item.value)
                        ? (item.value < 100 ? 100 : item.value) 
                        : (Indi.form.CkEditor.prototype.editorCfg || Indi.form.CkEditor.prototype._editorCfg).defaultWidth
                }
            },

            /**
             * Here we setup visibility and actual value
             *
             * @param item
             * @return {Object}
             */
            formItem$DetailsHtmlHeight: function(item) {
                var me = this;

                // Return
                return {
                    hidden: true,
                    value: me.ti().row.id && Ext.isNumeric(item.value)
                        ? (item.value < 50 ? 50 : item.value) 
                        : (Indi.form.CkEditor.prototype.editorCfg || Indi.form.CkEditor.prototype._editorCfg).defaultHeight
                }
            },
            formItem$DetailsHtmlBodyClass: {
                considerOn: [{
                    name: 'type',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.type == 'html');
                    }
                }
            },
            formItem$DetailsHtmlStyle: {
                considerOn: [{
                    name: 'type',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.type == 'html');
                    }
                }
            },
            formItem$DetailsString: {
                considerOn: [{
                    name: 'type',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.type == 'string');
                    }
                }
            },
            formItem$DetailsTextarea: {
                considerOn: [{
                    name: 'type',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.type == 'textarea');
                    }
                }
            }
        }
    }
});
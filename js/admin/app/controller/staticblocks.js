Ext.define('Indi.controller.staticblocks', {
    extend: 'Indi.Controller',
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
                    editorCfg: {
                        resize_dir: 'both',
                        on: {
                            resize: function(evt) {
                                var size = Ext.get(evt.editor.ui.space('contents').$).getSize();
                                Ext.getCmp(me.row.id).query('[name="detailsHtmlWidth"]')[0].setValue(size.width);
                                Ext.getCmp(me.row.id).query('[name="detailsHtmlHeight"]')[0].setValue(size.height);
                            }
                        }
                    }
                }
            },

            /**
             * Remove mention of ',tr-detailsHtmlWidth,tr-detailsHtmlHeight' from radios handler javascript.
             * This it temporary solution.
             *
             * @param item
             * @return {*}
             */
            formItem$Type: function(item) {
                for (var i = 0; i < item.field.nested('enumset').length; i++) {
                    item.field.nested('enumset')[i].javascript =
                        item.field.nested('enumset')[i].javascript.replace(',tr-detailsHtmlWidth,tr-detailsHtmlHeight', '');
                }
                return item;
            },

            /**
             * Here we setup visibility and actual value
             *
             * @param item
             * @return {Object}
             */
            formItem$DetailsHtmlWidth: function(item) {
                var me = this;

                // Setup actual value and visibility
                item.hidden = 'true';
                item.value = me.ti().row.id && Ext.isNumeric(item.value)
                    ? (item.value < 100 ? 100 : item.value)
                    : Indi.form.CkEditor.prototype.editorCfg.defaultWidth;

                // Return
                return {
                    listeners: {
                        afterrender: function(cmp){
                            cmp.hide();
                        }
                    }
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

                // Setup actual value and visibility
                item.hidden = 'true';
                item.value = me.ti().row.id && Ext.isNumeric(item.value)
                    ? (item.value < 50 ? 50 : item.value)
                    : Indi.form.CkEditor.prototype.editorCfg.defaultHeight;

                // Return
                return {
                    listeners: {
                        afterrender: function(cmp){
                            cmp.hide();
                        }
                    }
                }
            }
        }
    }
});
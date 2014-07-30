Ext.define('Indi.lib.controller.action.Form', {
    alternateClassName: 'Indi.Controller.Action.Row.Form',
    extend: 'Indi.Controller.Action.Row',
    row: {
        xtype: 'form',
        bodyPadding: 10,
        closable: false,
        autoScroll: true,

        // Fields will be arranged vertically, stretched to full width
        layout: 'anchor',
        defaults: {
            anchor: '100%'
        },

        // Reset and Submit buttons
        buttons: [{
            text: 'Reset',
            handler: function() {
                this.up('form').getForm().reset();
            }
        }, {
            text: 'Submit',
            formBind: true, //only enabled once the form is valid
            disabled: true,
            handler: function() {
                var form = this.up('form').getForm();
                if (form.isValid()) {
                    console.log(form.url);
                    form.submit({
                        success: function(form, action) {
                            //Ext.Msg.alert('Success', action.result.msg);
                            if (action.result.redirect) Indi.load(action.result.redirect);
                        },
                        failure: function(form, action) {
                            Ext.Msg.alert('Failed', action.result.msg);
                        }
                    });
                }
            }
        }]
    },
    initComponent: function() {
        this.id = this.bid();
        this.row = Ext.merge({
            items: this.rowItemA(),
            url: this.trail().section.href + 'save'
                + (this.trail().row.id ? '/id/' + this.trail().row.id : '')
                //+ /*(Indi.uri.ph ? '/ph/' + Indi.uri.ph : ''*/ '/'
                + '/ph/d41d8cd98f/'
        }, this.row);
        this.panel.items = [this.row];
        this.callParent();
    },
    rowItemA: function() {

        var itemA = [], itemI, itemIDefault;
        itemA.push(this.rowItemISpan());
        for (var i = 0; i < this.trail().fields.length; i++) {
            itemI = this.rowItemIDefault(this.trail().fields[i]);
            if (itemI) {
                itemI.cls = 'i-field' + (itemI.cls ? ' ' + itemI.cls : '');
                itemA.push(itemI);
            }
        }

        return itemA;
    },

    rowItemISpan: function(field){

        return {
            id: this.trail().bid() + (field ? '-field-' + field.alias : '-header'),
            id: 'tr-' + (field ? field.alias : 'header'),
            xtype: 'displayfield',
            cls: (field ? '' : 'i-field ') + 'i-field-span',
            value: (field ? field.title : this.trail().model.title),
            align: 'center'
        }
    },

    rowItemIDefault: function(field) {
        var control = field.foreign('elementId').alias, itemIDefault, itemI;
        itemIDefault = 'rowItemI' + Indi.ucfirst(control);
        if (typeof this[itemIDefault] == 'function') itemI = this[itemIDefault](field);
        else if (control == 'move') {
        } else {
            itemI = this.rowItemIString(field);
            itemI.fieldLabel = '!!! ' + control;
        }
        return itemI;
    },

    rowItemIString: function(field) {
        return {
            id: this.trail().bid() + '-field-' + field.alias,
            id: 'tr-' + field.alias,
            xtype: 'textfield',
            fieldLabel: field.title,
            labelWidth: '100%',
            name: field.alias,
            value: this.trail().row[field.alias]
        };
    },

    rowItemINumber: function(field) {
        return {
            id: this.trail().bid() + '-field-' + field.alias,
            id: 'tr-' + field.alias,
            xtype: 'numberfield',
            fieldLabel: field.title,
            labelWidth: '100%',
            minValue: 1,
            name: field.alias,
            value: this.trail().row[field.alias]
        };
    },

    rowItemITextarea: function(field) {
        return {
            id: this.trail().bid() + '-field-' + field.alias,
            id: 'tr-' + field.alias,
            xtype     : 'textarea',
            labelWidth: '100%',
            grow      : true,
            name      : field.alias,
            fieldLabel: field.title,
            value: this.trail().row[field.alias],
            anchor    : '100%',
            minHeight: 32
        }
    },

    rowItemIRadio: function(field) {
        var optionA = [], enumset;
        for (var i = 0; i < field.nested('enumset').length; i++) {
            enumset = field.nested('enumset')[i];
            optionA.push({
                boxLabel  : enumset.title,
                name      : field.alias,
                inputValue: enumset.alias,
                id        : field.alias + Indi.ucfirst(enumset.alias),
                checked     : enumset.alias == this.trail().row[field.alias],
                enumset: enumset,
                listeners: {
                    change: function(radio, now, was) {
                        if (now) {
                            try {
                                eval(radio.enumset.javascript);
                                eval(field.javascript);
                            } catch (e) {
                                //console.log(e);
                            }
                        }
                    }
                }
            });
        }
        return {
            id: 'tr-' + field.alias,
            xtype: 'fieldcontainer',
            fieldLabel : field.title,
            defaultType: 'radio',
            labelWidth: '100%',
            defaults: {
                flex: 1,
                height: 10
            },
            layout: 'vbox',
            items: optionA,
            listeners: {
                afterlayout: function(cmp) {
                    var checked = cmp.items.findBy(function(item){return item.checked == true});
                    checked.fireEvent('change', checked, true);
                }
            }
        };
    },

    rowItemICheck: function(field) {
        return {
            id: this.trail().bid() + '-field-' + field.alias,
            id: 'tr-' + field.alias,
            xtype: 'checkbox',
            fieldLabel : field.title,
            //defaultType: 'checkbox',
            labelWidth: '100%',
            layout: 'hbox',
            height: 21,
            name      : field.alias,
            inputValue: this.trail().row[field.alias],
            checked     : this.trail().row[field.alias] == '0',
            getSubmitValue: function() {
                return this.checked ? 1 : 0;
            }
        }
    },

    rowItemICombo: function(field) {
        return {
            id: this.trail().bid() + '-field-' + field.alias,
            id: 'tr-' + field.alias,
            xtype: 'combo.form',
            fieldLabel : field.title,
            labelWidth: '100%',
            field: field,
            layout: 'hbox',
            name: field.alias,
            value: Ext.isNumeric(this.trail().row[field.alias]) ? parseInt(this.trail().row[field.alias]) : this.trail().row[field.alias],
            subTplData: this.trail().row.view(field.alias).subTplData,
            store: this.trail().row.view(field.alias).store
        }
    },

    rowItemIHtml: function(field) {
        //return null;
        var fieldCmpId = this.bid() + '-row-' + this.trail().row.id +'-field-' + field.alias;
        var config = window[fieldCmpId + '-html-config'];
        return {
            xtype: 'fieldcontainer',
            fieldLabel : field.title,
            labelWidth: '100%',
            padding: 0,
            id: fieldCmpId + '-item',
            //cls: 'i-filter-combo',
            border: 0,
            defaults: {
                flex: 1
            },
            layout: 'anchor',
            items: [{
                id: fieldCmpId,
                xtype: 'hiddenfield',
                contentEl: fieldCmpId + '-html',
                layout: 'fit',
                name: field.alias,
                border: 0,
                value: this.trail().row[field.alias],
                width: '100%',
                listeners: {
                    afterrender: function(me) {
                        setTimeout(function(){
                            var cfg = window[me.id + '-html-config'];
                            var top = me.el.select('.cke_top').first().getHeight();
                            var btm = me.el.select('.cke_bottom').first().getHeight();
                            me.setHeight(parseInt(cfg.height) + top + btm + 2);
                        }, 500);
                    }
                },
                setValue: function(value) {
                    CKEDITOR.instances[this.name].setData(value);
                    return this;
                },
                getValue: function() {
                    return CKEDITOR.instances[this.name].getData();
                },
                getSubmitValue: function() {
                    return this.getValue();
                }
            }]
        }
    }
});
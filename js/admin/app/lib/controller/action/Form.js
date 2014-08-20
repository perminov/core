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
                console.log(form.getValues());
                if (form.isValid()) {
                    form.submit({
                        success: function(form, action) {
                            //Ext.Msg.alert('Success', action.result.msg);
                            if (action.result.redirect) Indi.load(action.result.redirect);
                        },
                        failure: function(form, action) {
                            var cmp, msg;
                            Object.keys(action.result.mismatch).forEach(function(i, index, mismatch){
                                if (cmp = Ext.getCmp(form.owner.ctx().trail().bid() + '-field-' + i)) {
                                    msg = action.result.mismatch[i];
                                    msg = msg.replace(cmp.fieldLabel, '').replace(/""/g, '');
                                    cmp.markInvalid(msg);
                                }
                            });
                        }
                    });
                }
            }
        }]
    },

    // @inheritdoc
    initComponent: function() {
        this.id = this.trail().bid();
        this.row = Ext.merge({
            id: this.id + '-form',
            items: this.formItemA(),
            url: this.trail().section.href + 'save'
                + (this.trail().row.id ? '/id/' + this.trail().row.id : '')
                + (this.trail().scope.hash ? '/ph/' + this.trail().scope.hash : '') + '/',
            dockedItems: this.rowToolbarA()
        }, this.row);
        this.panel.items = this.panelItemA();
        this.callParent();
    },

    /**
     * Builds and return an array of panels, that will be used to represent the major UI contents.
     * Currently is consists only from this.row form panel configuration
     *
     * @return {Array}
     */
    panelItemA: function() {
        return [this.row];
    },

    /**
     * Builds and return an array of toolbars for the UI
     *
     * @return {Array}
     */
    rowToolbarA: function() {
        return []
    },

    /**
     * Build and return array of form panel items
     *
     * @return {Array}
     */
    formItemA: function() {

        // Declare a number of auxiliary variables
        var me = this, itemA = [], itemI, itemX, fnItemX, item$, fnItem$;

        // Setup ids-array of a fields, that are disabled and shouldn't be shown in form,
        // and ids-array of a fields, that are disabled but should be shown in form
        var disabledA = me.trail().disabledFields.select('0', 'displayInForm').column('fieldId');
        var visibleA = me.trail().disabledFields.select('1', 'displayInForm').column('fieldId');

        // Header form item
        itemA.push(me.formItemXSpan());

        // Other form items (fields)
        for (var i = 0; i < me.trail().fields.length; i++)

            // If current field is not disabled, or disabled but visible
            if (disabledA.indexOf(me.trail().fields[i].id) == -1) {

                // Setup default config
                itemI = me.formItemDefault(me.trail().fields[i]);

                // Apply specific control element config, as fields control elements/xtypes may be different
                fnItemX = 'formItemX' + Indi.ucfirst(me.trail().fields[i].foreign('elementId').alias);
                if (typeof me[fnItemX] == 'function') {
                    itemX = me[fnItemX](itemI);
                    itemI = Ext.isObject(itemX) ? Ext.merge(itemI, itemX) : itemX;
                } else Ext.merge(itemI, {
                    fieldLabel: '!!! ' + me.trail().fields[i].foreign('elementId').alias
                });

                // Apply field custom config
                fnItem$ = 'formItem$' + Indi.ucfirst(me.trail().fields[i].alias);
                if (typeof me[fnItem$] == 'function') {
                    item$ = me[fnItem$](itemI);
                    itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
                }

                // If itemI is still not empty/null/false
                if (itemI) {

                    // Setup `disabled` property as boolean true, if current field is disabled-but-visible
                    if (visibleA.indexOf(me.trail().fields[i].id) != -1) itemI.disabled = true;

                    // Prepend `cls` property with 'i-field' css class name
                    itemI.cls = 'i-field' + (itemI.cls ? ' ' + itemI.cls : '');

                    // Push item to the `itemA` array
                    itemA.push(itemI);
                }
            }

        // Return form items (fields) array
        return itemA;
    },


    /**
     * Provide initial/default config for all fields
     *
     * @param field
     * @return {Object}
     */
    formItemDefault: function(field) {
        return {
            id: this.trail().bid() + '-field-' + field.alias,
            xtype: 'textfield',
            fieldLabel: field.title,
            labelWidth: '100%',
            name: field.alias,
            value: this.trail().row[field.alias],
            field: field,
            row: this.trail().row
        }
    },

    /**
     * Span-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXSpan: function(item){
        return {
            id: this.trail().bid() + (item ? '-field-' + item.field.alias : '-header'),
            xtype: 'displayfield',
            cls: (item ? '' : 'i-field ') + 'i-field-span',
            fieldLabel: '',
            value: (item ? item.field.title : this.trail().model.title),
            align: 'center'
        }
    },

    /**
     * Move-fields config adjuster. Currently, Indi Engine has no control for 'move' fields, so adjuster returns null,
     * and that causes 'move' fields won't be shown at all
     *
     * @param item
     * @return {Object}
     */
    formItemXMove: function(item){
        return null;
    },

    /**
     * String-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXString: function(item) {
        return item.fieldLabel == 'Auto title' ? null : {}
    },

    /**
     * Calendar-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXCalendar: function(item) {
        return {
            xtype: 'datefield',
            ariaTitle: '',
            cls: 'i-field-date',
            format: item.field.params.displayFormat
        };
    },

    /**
     * Datetime-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXDatetime: function(item) {
        return {
            xtype: 'datetimefield',
            cls: 'i-field-datetime',
            format: item.field.params.displayDateFormat
        };
    },

    /**
     * Time-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXTime: function(item) {
        return {
            xtype: 'timefield',
            cls: 'i-field-time'
        };
    },

    /**
     * Number-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXNumber: function(item) {
        return {
            xtype: 'numberfield',
            minValue: 0
        };
    },

    /**
     * Textarea-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXTextarea: function(item) {
        return {
            xtype: 'textarea',
            grow: true,
            minHeight: 32
        }
    },

    /**
     * Radio-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXRadio: function(item) {
        var optionA = [], enumset;
        for (var i = 0; i < item.field.nested('enumset').length; i++) {
            enumset = item.field.nested('enumset')[i];
            optionA.push({
                boxLabel: enumset.title,
                name: item.name,
                inputValue: enumset.alias,
                id: item.name + Indi.ucfirst(enumset.alias),
                checked: enumset.alias == item.row[item.name],
                enumset: enumset,
                listeners: {
                    change: function(radio, now, was) {
                        if (now) {
                            try {
                                eval(radio.enumset.javascript);
                                eval(item.field.javascript);
                            } catch (e) {
                                //console.log(e);
                            }
                        }
                    }
                }
            });
        }
        return {
            xtype: 'fieldcontainer',
            defaultType: 'radio',
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

    /**
     * Checkbox-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXCheck: function(item) {
        return {
            xtype: 'checkbox',
            layout: 'hbox',
            height: 21,
            inputValue: item.row[item.name],
            checked: item.row[item.name] == '1',
            getSubmitValue: function() {
                return this.checked ? 1 : 0;
            }
        }
    },

    /**
     * Combo-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXCombo: function(item) {
        return {
            xtype: 'combo.form',
            layout: 'hbox',
            value: Ext.isNumeric(item.row[item.name]) ? parseInt(item.row[item.name]) : item.row[item.name],
            subTplData: item.row.view(item.name).subTplData,
            store: item.row.view(item.name).store
        }
    },

    /**
     * Html-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXHtml: function(item) {
        return {
            xtype: 'ckeditor'
        }
    }
});
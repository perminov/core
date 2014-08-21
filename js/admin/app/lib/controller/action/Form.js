Ext.define('Indi.lib.controller.action.Form', {
    alternateClassName: 'Indi.Controller.Action.Row.Form',
    extend: 'Indi.Controller.Action.Row',

    // @inheritdoc
    panel: {

        /**
         * Master toolbar config
         */
        toolbarMaster: {
            items: {
                save: 1,
                autosave: 2,
                prev: 1,
                next: 2,
                create: 3,
                offset: {
                    mode: 1,
                    width: function(found, mode) {
                        var labelWidth = Indi.metrics.getWidth(Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE),
                            triggerWidth = 20, inputWidth = (found.toString().length + 1) * 7 + 2;

                        inputWidth = inputWidth > 30 ? inputWidth : 30;

                        return mode == 'tooltipOffset'
                            ? labelWidth + 5 + inputWidth/2 - (labelWidth + 5 + inputWidth + triggerWidth)/2
                            : labelWidth + inputWidth + triggerWidth;
                    }
                },
                found: 1
            }
        }
    },

    // @inheritdoc
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

        listeners: {
            validitychange: function(form, valid){
                this.ctx().toggleSaveAbility(valid);
            },
            actioncomplete: function(form, action) {
                if (action.result.redirect) Indi.load(action.result.redirect);
            },
            actionfailed: function(form, action) {
                var cmp, msg;
                Object.keys(action.result.mismatch).forEach(function(i, index, mismatch){
                    if (cmp = Ext.getCmp(form.owner.ctx().bid() + '-field-' + i)) {
                        msg = action.result.mismatch[i];
                        msg = msg.replace(cmp.fieldLabel, '').replace(/""/g, '');
                        cmp.markInvalid(msg);
                    }
                });

                var idCmp = Ext.getCmp(this.ctx().panelToolbarMasterId() + '-id');
                if (idCmp) idCmp.setValue(idCmp.lastValidValue);

                var offsetCmp = Ext.getCmp(this.ctx().panelToolbarMasterId() + '-offset');
                if (offsetCmp) offsetCmp.setValue(offsetCmp.lastValidValue);

                form.fireEvent('validitychange', form, false);

                // Hide mask
                this.ctx().getMask().hide();
            }
        }
    },

    toggleSaveAbility: function(valid) {
        var me = this, cbAutosave = Ext.getCmp(me.panelToolbarMasterId() + '-autosave');
        var toggleA = ['save', 'autosave'], toggleI;
        for (var i = 0; i < toggleA.length; i++) {
            toggleI = Ext.getCmp(me.panelToolbarMasterId() + '-' + toggleA[i]);
            if (toggleI) toggleI.setDisabled(!valid);

            if (toggleA[i] == 'autosave' && toggleI) {
                if (!valid) {
                    toggleI.backupValue = toggleI.checked ? 1 : 0;
                    toggleI.setValue(0);
                } else {
                    toggleI.setValue(toggleI.hasOwnProperty('backupValue')
                        ? toggleI.backupValue
                        : (toggleI.checked ? 1 : 0));
                }
            }
        }
    },

    // @inheritdoc
    initComponent: function() {
        this.id = this.bid();
        this.row = Ext.merge({
            id: this.id + '-form',
            items: this.formItemA(),
            url: this.ti().section.href + 'save'
                + (this.ti().row.id ? '/id/' + this.ti().row.id : '')
                + (this.ti().scope.hash ? '/ph/' + this.ti().scope.hash : '') + '/'
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
     * Build and return array of form panel items
     *
     * @return {Array}
     */
    formItemA: function() {

        // Declare a number of auxiliary variables
        var me = this, itemA = [], itemI, itemX, fnItemX, item$, fnItem$;

        // Setup ids-array of a fields, that are disabled and shouldn't be shown in form,
        // and ids-array of a fields, that are disabled but should be shown in form
        var disabledA = me.ti().disabledFields.select('0', 'displayInForm').column('fieldId');
        var visibleA = me.ti().disabledFields.select('1', 'displayInForm').column('fieldId');

        // Header form item
        itemA.push(me.formItemXSpan());

        // Other form items (fields)
        for (var i = 0; i < me.ti().fields.length; i++)

            // If current field is not disabled, or disabled but visible
            if (disabledA.indexOf(me.ti().fields[i].id) == -1) {

                // Setup default config
                itemI = me.formItemDefault(me.ti().fields[i]);

                // Apply specific control element config, as fields control elements/xtypes may be different
                fnItemX = 'formItemX' + Indi.ucfirst(me.ti().fields[i].foreign('elementId').alias);
                if (typeof me[fnItemX] == 'function') {
                    itemX = me[fnItemX](itemI);
                    itemI = Ext.isObject(itemX) ? Ext.merge(itemI, itemX) : itemX;
                } else Ext.merge(itemI, {
                    fieldLabel: '!!! ' + me.ti().fields[i].foreign('elementId').alias
                });

                // Apply field custom config
                fnItem$ = 'formItem$' + Indi.ucfirst(me.ti().fields[i].alias);
                if (typeof me[fnItem$] == 'function') {
                    item$ = me[fnItem$](itemI);
                    itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
                }

                // If itemI is still not empty/null/false
                if (itemI) {

                    // Setup `disabled` property as boolean true, if current field is disabled-but-visible
                    if (visibleA.indexOf(me.ti().fields[i].id) != -1) itemI.disabled = true;

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
            id: this.bid() + '-field-' + field.alias,
            xtype: 'textfield',
            fieldLabel: field.title,
            labelWidth: '100%',
            name: field.alias,
            value: this.ti().row[field.alias],
            field: field,
            row: this.ti().row
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
            id: this.bid() + (item ? '-field-' + item.field.alias : '-header'),
            xtype: 'displayfield',
            cls: (item ? '' : 'i-field ') + 'i-field-span',
            fieldLabel: '',
            value: (item ? item.field.title : this.ti().model.title),
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
    },

    /**
     * Loads the url, given in `url` arg. This function overrides parent class `goto` function,
     * as autosave mode should be additionally taken into attention here
     *
     * @param url
     */
    goto: function(url) {
        var me = this, hidden = Ext.getCmp(me.bid() + '-redirect-url'),
            btnSave = Ext.getCmp(me.panelToolbarMasterId() + '-save'),
            formCmp = Ext.getCmp(me.bid() + '-form'),
            autosave = Ext.getCmp(me.bid() + '-form-autosave');

        // If save button is toggled
        if (btnSave && btnSave.pressed && !btnSave.disabled) {

            // Update value of the 'redirect-url' field, or, if it's not
            // yet exists - create it and assign `url` as it's value
            if (hidden) hidden.setValue(url); else formCmp.add({
                id: me.bid() + '-redirect-url',
                xtype: 'hidden',
                name: 'redirect-url',
                value: url
            });

            // Submit form
            if (formCmp.getForm().isValid()) formCmp.submit(); else me.getMask().hide();

        // Else we just load required contents
        } else Indi.load(url + (me.ti().scope.toggledSave ? '?stopAutosave=1' : ''));
    },

    /**
     * Master toolbar 'Save' item, for ability to save form data
     *
     * @return {Object}
     */
    panelToolbarMasterItem$Save: function() {
        // Here we check if 'save' action is in the list of allowed actions
        var me = this, formCmp = Ext.getCmp(me.bid() + '-form'); me.ti().disableSave = true;
        for (var i = 0; i < me.ti().actions.length; i++)
            if (me.ti().actions[i].alias == 'save')
                me.ti().disableSave = false;

        // 'Save' item config
        return {
            id: me.panelToolbarMasterId() + '-save',
            xtype: 'button',
            text: Indi.lang.I_SAVE,
            handler: function() {
                me.goto(me.panelToolbarMasterItem$Back(true));
            },
            disabled: me.ti().disableSave,
            iconCls: 'i-btn-icon-save',
            cls: 'i-action-form-topbar-button-save',
            pressed: me.ti().scope.toggledSave
        }
    },

    /**
     * Master toolbar 'Autosave' item, for ability to toggle autosave mode while navigating
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelToolbarMasterItem$Autosave: function() {
        var me = this;

        // 'Autosave' item config
        return {
            id: me.panelToolbarMasterId() + '-autosave',
            xtype: 'checkbox',
            tooltip: {
                html: Indi.lang.I_AUTOSAVE,
                staticOffset: [0, 4]
            },
            disabled: me.ti().disableSave,
            iconCls: 'i-btn-icon-save',
            cls: 'i-action-form-topbar-checkbox-autosave',
            checked: me.ti().scope.toggledSave,
            margin: '0 6 0 3',
            handler: function(cb){
                var btnSave = Ext.getCmp(me.panelToolbarMasterId() + '-save'),
                    sqNested = Ext.getCmp(me.panelToolbarMasterId() + '-nested');

                if (btnSave) btnSave.toggle();
                if (sqNested && me.ti().sections.length && !me.ti().row.id)
                    sqNested[cb.checked ? 'enable' : 'disable']();
            },
            listeners: {
                afterrender: function(){
                    var btnSave = Ext.getCmp(me.panelToolbarMasterId() + '-save');
                    this.getEl().hover(function(){
                        btnSave.getEl().addCls('x-btn-default-toolbar-small-over');
                    }, function(){
                        btnSave.getEl().removeCls('x-btn-default-toolbar-small-over');
                    });
                }
            }
        }
    },

    /**
     * Master toolbar 'Autosave' item, for ability to toggle autosave mode while navigating
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelToolbarMasterItem$Create: function() {
        var me = this;

        // 'Create' item config
        return {
            id: me.panelToolbarMasterId() + '-create',
            iconCls: 'i-btn-icon-create',
            disabled: parseInt(me.ti().section.disableAdd) || me.ti().disableSave ? true : false,
            tooltip: Indi.lang.I_NAVTO_CREATE,
            handler: function(){
                var url = me.ti().section.href + me.ti().action.alias + '/ph/' + me.ti().section.primaryHash+'/',
                    tfID = Ext.getCmp(me.panelToolbarMasterId() + '-id'),
                    btnPrev = Ext.getCmp(me.panelToolbarMasterId() + '-prev'),
                    btnNext = Ext.getCmp(me.panelToolbarMasterId() + '-next'),
                    cmbSibling = Ext.getCmp(me.panelToolbarMasterId() + '-sibling'),
                    spnOffset = Ext.getCmp(me.panelToolbarMasterId() + '-offset');

                // Show mask
                me.getMask().show();

                if (tfID) tfID.setValue('');
                if (btnPrev) btnPrev.disable();
                if (cmbSibling && typeof me.ti().row.title != 'undefined')
                    cmbSibling.setKeywordValue(''); // !!
                if (btnNext && parseInt(me.ti().scope.found)) btnNext.enable();
                if (spnOffset) spnOffset.setValue('');

                me.goto(url);
            }
        }
    }
});
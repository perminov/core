/**
 * Base class for all controller actions instances, that operate with some certain rows,
 * and use forms controls to display/modify those rows properties
 */
Ext.define('Indi.lib.controller.action.Form', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Row.Form',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Row',

    // @inheritdoc
    panel: {

        // @inheritdoc
        docked: {
            items: [{alias: 'master'}],
            elems: {
                master: [
                    {alias: 'back'}, '-',
                    {alias: 'ID'}, '-',
                    {alias: 'save'}, {alias: 'autosave'}, '-',
                    {alias: 'prev'}, {alias: 'next'}, '-',
                    {alias: 'create'}, '-',
                    {alias: 'nested'}, '->',
                    {alias: 'offset'}, {alias: 'found'}
                ]
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

        // @inheritdoc
        listeners: {
            validitychange: function(form, valid){
                this.ctx().toggleSaveAbility(valid);
            },
            actioncomplete: function(form, action) {
                if (action.result.redirect) Indi.load(action.result.redirect);
            },
            actionfailed: function(form, action) {
                var cmp, msg;

                // The the info about invalid fields from the response, and mark the as invalid
                Object.keys(action.result.mismatch).forEach(function(i, index, mismatch){
                    if (cmp = Ext.getCmp(form.owner.ctx().bid() + '-field-' + i)) {
                        msg = action.result.mismatch[i];
                        msg = msg.replace(cmp.fieldLabel, '').replace(/""/g, '');
                        cmp.markInvalid(msg);
                    }
                });

                // Reset value of the 'ID' master toolbar item to the last valid value
                var idCmp = Ext.getCmp(this.ctx().panelDockedElemBid() + 'id');
                if (idCmp) idCmp.setValue(idCmp.lastValidValue);

                // Reset value of the 'Offset' master toolbar item to the last valid value
                var offsetCmp = Ext.getCmp(this.ctx().panelDockedElemBid() + 'offset');
                if (offsetCmp) offsetCmp.setValue(offsetCmp.lastValidValue);

                // Fire the 'validitychange' event
                form.fireEvent('validitychange', form, false);

                // Hide mask
                this.ctx().getMask().hide();
            }
        }
    },

    /**
     * Disabled/enable master toolbar controls, explicitly related to form save ability.
     * Function is called each time form validity changes
     *
     * @param valid
     */
    toggleSaveAbility: function(valid) {

        // Setup auxilliary variables and the array of master toolbar items,
        // that should be primary affected each time form saving ability is changed
        var me = this, cbAutosave = Ext.getCmp(me.panelDockedElemBid() + 'autosave'),
            toggleA = ['save', 'autosave'], toggleI;

        // For each master toolbar item, that should be affected on form saving ability change
        for (var i = 0; i < toggleA.length; i++) {

            // Get item's component and if got - disable
            toggleI = Ext.getCmp(me.panelDockedElemBid() + toggleA[i]);
            if (toggleI) toggleI.setDisabled(!valid);

            // If that component is 'Autosave' - implement additional behaviour
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
        var me = this;
        me.id = me.bid();
        me.row = Ext.merge({
            id: me.id + '-form',
            items: me.formItemA(),
            url: me.ti().section.href + 'save'
                + (me.ti().row.id ? '/id/' + me.ti().row.id : '')
                + (me.ti().scope.hash ? '/ph/' + me.ti().scope.hash : '') + '/'
        }, me.row);
        me.panel.items = me.panelItemA();
        me.callParent();
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
        return item.fieldLabel == 'Auto title'
            ? null
            : (item.name == 'alias' ? {allowBlank: false} : {});
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
     * Master toolbar 'Save' item, for ability to save form data
     *
     * @return {Object}
     */
    panelDockedElem$Save: function() {

        // Here we check if 'save' action is in the list of allowed actions
        var me = this, formCmp = Ext.getCmp(me.bid() + '-form'); me.ti().disableSave = true;
        for (var i = 0; i < me.ti().actions.length; i++)
            if (me.ti().actions[i].alias == 'save')
                me.ti().disableSave = false;

        // 'Save' item config
        return {
            id: me.panelDockedElemBid() + 'save',
            xtype: 'button',
            text: Indi.lang.I_SAVE,
            handler: function() {
                me.goto(me.panelDockedElem$Back(true), true);
            },
            disabled: me.ti().disableSave,
            iconCls: 'i-btn-icon-save',
            pressed: me.ti().scope.toggledSave
        }
    },

    /**
     * Master toolbar 'Autosave' item, for ability to toggle autosave mode while navigating
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelDockedElem$Autosave: function() {
        var me = this;

        // 'Autosave' item config
        return {
            id: me.panelDockedElemBid() + 'autosave',
            xtype: 'checkbox',
            tooltip: {html: Indi.lang.I_AUTOSAVE, staticOffset: [0, 4]},
            disabled: me.ti().disableSave,
            iconCls: 'i-btn-icon-save',
            cls: 'i-cb-autosave',
            checked: me.ti().scope.toggledSave,
            margin: '0 6 0 3',
            handler: function(cb){

                // Create shortcuts for involved components
                var btnSave = Ext.getCmp(me.panelDockedElemBid() + 'save'),
                    sqNested = Ext.getCmp(me.panelDockedElemBid() + 'nested');

                // Other items adjustments
                if (btnSave) btnSave.toggle();
                if (sqNested && me.ti().sections.length && !me.ti().row.id) sqNested.setDisabled(!cb.checked);
            },
            listeners: {
                afterrender: function(){
                    var btnSave = Ext.getCmp(me.panelDockedElemBid() + 'save');
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
    panelDockedElem$Create: function() {
        var me = this;

        // 'Create' item config
        return {
            id: me.panelDockedElemBid() + 'create',
            iconCls: 'i-btn-icon-create',
            disabled: parseInt(me.ti().section.disableAdd) || me.ti().disableSave ? true : false,
            tooltip: Indi.lang.I_NAVTO_CREATE,
            handler: function(){

                // Create shortcuts for involved components
                var url = me.ti().section.href + me.ti().action.alias + '/ph/' + me.ti().section.primaryHash+'/',
                    tfID = Ext.getCmp(me.panelDockedElemBid() + 'id'),
                    btnPrev = Ext.getCmp(me.panelDockedElemBid() + 'prev'),
                    btnNext = Ext.getCmp(me.panelDockedElemBid() + 'next'),
                    cmbSibling = Ext.getCmp(me.panelDockedElemBid() + 'sibling'),
                    spnOffset = Ext.getCmp(me.panelDockedElemBid() + 'offset');

                // Show mask
                me.getMask().show();

                // Other items adjustments
                if (tfID) tfID.setValue('');
                if (btnPrev) btnPrev.disable();
                if (cmbSibling && typeof me.ti().row.title != 'undefined') cmbSibling.setKeywordValue(''); // !!
                if (btnNext && parseInt(me.ti().scope.found)) btnNext.enable();
                if (spnOffset) spnOffset.setValue('');

                // Goto the url
                me.goto(url);
            }
        }
    },

    /**
     * Master toolbar 'Nested' item, for ability to navigate to current row's nested entries lists
     *
     * @return {Object}
     */
    panelDockedElem$Nested: function() {
        var me = this, btnSave = Ext.getCmp(me.panelDockedElemBid() + 'save');

        // If there are currently no nested sections - return
        if (!me.ti().sections.length) return null;

        // 'Nested' item config
        return {
            id: me.panelDockedElemBid() + 'nested',
            xtype: 'shrinklist',
            displayField: 'title',
            disabled: !me.ti().row.id && ((btnSave && btnSave.pressed != true) || true),
            tooltip: {
                html: Indi.lang.I_NAVTO_NESTED,
                hideDelay: 0,
                showDelay: 1000,
                dismissDelay: 2000,
                staticOffset: [0, 1]
            },
            store: {
                xtype: 'store',
                fields: ['alias', 'title'],
                data : me.ti().sections
            },
            listeners: {
                itemclick: function(cmp, row) {
                    me.goto(Indi.pre + '/' + row.get('alias') + '/index/id/'+ me.ti().row.id
                        +'/ph/'+ me.ti().scope.hash + '/aix/'+ me.ti().scope.aix +'/');
                }
            }
        }
    },

    /**
     * Loads the url, given in `url` arg. This function overrides parent class `goto` function,
     * as autosave mode should be additionally taken into attention here
     *
     * @param url
     */
    goto: function(url, btnSaveClick) {

        // Create shortcuts for involved components
        var me = this, hidden = Ext.getCmp(me.bid() + '-redirect-url'),
            btnSave = Ext.getCmp(me.panelDockedElemBid() + 'save'),
            formCmp = Ext.getCmp(me.bid() + '-form');

        // If save button is toggled
        if (btnSave && !btnSave.disabled && (btnSave.pressed || btnSaveClick)) {

            // "-1" - is a special value that means that after save, this form should be displayed again
            if (btnSaveClick == -1) url += '?stopAutosave=1';

            // Update value of the 'redirect-url' field, or, if it's not
            // yet exists - create it and assign `url` as it's value
            if (hidden) hidden.setValue(url);
            else if (!btnSaveClick || btnSaveClick == -1) formCmp.add({
                id: me.bid() + '-redirect-url',
                xtype: 'hidden',
                name: 'redirect-url',
                value: url
            });

            // Submit form
            if (formCmp.getForm().isValid()) formCmp.submit(); else me.getMask().hide();

        // Else
        } else {

            // If `forceValidate` arg is given we check form validity before loading required contents
            if (btnSave && btnSave.pressed && !formCmp.getForm().isValid()) return;

            // We just load required contents
            Indi.load(url + (me.ti().scope.toggledSave ? '?stopAutosave=1' : ''));
        }
    }
});
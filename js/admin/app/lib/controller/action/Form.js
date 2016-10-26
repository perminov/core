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
            inner: {
                master: [
                    {alias: 'back'}, {alias: 'close'}, '-',
                    {alias: 'ID'},
                    {alias: 'reload'}, '-',
                    {alias: 'save'}, {alias: 'autosave'}, '-',
                    {alias: 'reset'}, '-',
                    {alias: 'prev'}, {alias: 'sibling'}, {alias: 'next'}, '-',
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
        overflowY: 'auto',

        // Fields will be arranged vertically, stretched to full width
        layout: 'anchor',
        defaults: {
            anchor: '100%',
            labelWidth: '50%'
        },

        // @inheritdoc
        listeners: {
            validitychange: function(form, valid){
                if (valid) this.ctx().toggleSaveAbility(valid);
            },
            dirtychange: function(form, dirty) {
                var resetBtn = Ext.getCmp(this.ctx().panelDockedInnerBid() + 'reset');
                if (resetBtn) resetBtn.setDisabled(!dirty);
            },
            actioncomplete: function(form, action) {
                var me = this, json = action.response.responseText.json(), gotoO, uri, cfg = {},
                    wrp = me.up('[isWrapper]'), isTab = wrp.isTab, sth = wrp.up('[isSouth]'),
                    sthItm = wrp.up('[isSouthItem]'), found;

                // If response text is not json-convertable, or does not have `redirect` property - return
                if (!Ext.isObject(json) || !(uri = json.redirect || '').length) return;

                // Parse request url
                gotoO = Indi.parseUri(uri);

                // If current wrapper is placed within a tab, and we gonna go to same-type wrapper
                if (isTab && gotoO.section == me.ti().section.alias && gotoO.action == 'form') {

                    // If tab, that we are gonna goto - is already exists
                    if (sthItm.name != gotoO.id && (found = sth.down('[isSouthItem][name="' + gotoO.id + '"]'))) {

                        // Hide mask
                        me.ctx().getMask().hide();

                        // Reset master toolbar controls
                        me.ctx().noGoto = true;
                        Ext.getCmp(me.ctx().panelDockedInnerBid() + 'id').reset();
                        Ext.getCmp(me.ctx().panelDockedInnerBid() + 'sibling').reset();
                        Ext.getCmp(me.ctx().panelDockedInnerBid() + 'offset').reset();
                        me.ctx().noGoto = false;

                        // Set found tab as active
                        sth.setActiveTab(found);

                        // Provide current wrapper to be replaced by new same-type wrapper panel
                        cfg.title = gotoO.id ? json.title : Indi.lang.I_CREATE;

                        // Setup a new uri for use in Indi.load() call, for current row to be refreshed rather redirection
                        uri = '/' + me.ti().section.alias + '/' + gotoO.action
                            + '/id/' + json.id + '/ph/' + me.ti().scope.hash
                            + '/aix/' + json.aix + '/';

                    // Else provide current wrapper to be replaced by new same-type wrapper panel
                    } else cfg.onLoad = function(ctx) {
                        this.up('[isSouthItem]').setTitle(ctx.ti().row.id ? ctx.ti().row.title : Indi.lang.I_CREATE);
                    }

                    // Provide current wrapper to be replaced by new same-type wrapper panel
                    Ext.merge(cfg, {
                        insteadOf: wrp.id,
                        into: sthItm.id
                    });
                }

                // Load required contents
                Indi.load(uri, cfg);
            },
            actionfailed: function(form, action) {
                var cmp, certainFieldMsg, wholeFormMsg = [], mismatch, errorByFieldO, trigger, msg;

                // Parse response text
                action.result = Ext.JSON.decode(action.response.responseText, true);

                // The the info about invalid fields from the response, and mark the as invalid
                if (Ext.isObject(action.result) && Ext.isObject(action.result.mismatch)) {

                    // Shortcut to action.result.mismatch
                    mismatch = action.result.mismatch;

                    // Error messages storage
                    errorByFieldO = mismatch.errors;

                    // Detect are error related to current form fields, or related to fields of some other entry,
                    // that is set up to be automatically updated (as a trigger operation, queuing after the primary one)
                    trigger = mismatch.entity.title != this.ctx().ti().model.title || mismatch.entity.entry != this.ctx().ti().row.id;

                    Object.keys(errorByFieldO).forEach(function(i){

                        // If mismatch key starts with a '#' symbol, we assume that message, assigned
                        // under such key - is not related to any certain field within form, so we
                        // collect al such messages for them to be bit later displayed within Ext.MessageBox
                        if (i.substring(0, 1) == '#' || trigger) wholeFormMsg.push(errorByFieldO[i]);

                        // Else if mismatch key doesn't start with a '#' symbol, we assume that message, assigned
                        // under such key - is related to some certain field within form, so we get that field's
                        // component and mark it as invalid
                        else if (cmp = Ext.getCmp(form.owner.ctx().bid() + '-field$' + i)) {

                            // Get the mismatch message
                            certainFieldMsg = errorByFieldO[i];

                            // If mismatch message is a string
                            if (Ext.isString(certainFieldMsg))

                                // Cut off field title mention from message
                                certainFieldMsg = certainFieldMsg.replace('"' + cmp.fieldLabel + '"', '').replace(/""/g, '');

                            // Mark field as invalid
                            cmp.markInvalid(certainFieldMsg);

                            // If field is currently hidden - we duplicate erroк message for it to be shown within
                            // Ext.MessageBox, additionally
                            if (cmp.hidden) wholeFormMsg.push(errorByFieldO[i])

                        // Else mismatch message is related to field, that currently, for some reason, is not available
                        // within the form - push that message to the wholeFormMsg array
                        } else wholeFormMsg.push(errorByFieldO[i]);
                    });

                    // If we collected at least one error message, that is related to the whole form rather than
                    // some certain field - use an Ext.MessageBox to display it
                    if (wholeFormMsg.length) {

                        msg = (wholeFormMsg.length > 1 || trigger ? '&raquo; ' : '') + wholeFormMsg.join('<br><br>&raquo; ');

                        // If this is a mismatch, caused by background php-triggers
                        if (trigger) msg = 'При выполнении вашего запроса, одна из автоматически производимых операций, в частности над записью типа "'
                            + mismatch.entity.title + '"'
                            + (parseInt(mismatch.entity.entry) ? ' [id#' + mismatch.entity.entry + ']' : '')
                            + ' - выдала следующие ошибки: <br><br>' + msg;

                        // Show message box
                        Ext.MessageBox.show({
                            title: Indi.lang.I_ERROR,
                            msg: msg,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                }

                // Reset value of the 'ID' master toolbar item to the last valid value
                var idCmp = Ext.getCmp(this.ctx().panelDockedInnerBid() + 'id');
                if (idCmp) idCmp.setValue(idCmp.lastValidValue);

                // Reset value of the 'Offset' master toolbar item to the last valid value
                var offsetCmp = Ext.getCmp(this.ctx().panelDockedInnerBid() + 'offset');
                if (offsetCmp) offsetCmp.setValue(offsetCmp.lastValidValue);

                // Fire the 'validitychange' event
                form.fireEvent('validitychange', form, false);

                // Hide mask
                this.ctx().getMask().hide();
                
                // Turn `isLoading` flag back to `false`
                form.isLoading = false;
            },

            /**
             * Provide the ability to fix scrollbar-overlap problem
             */
            resize: function(form, nw, nh) {

                // Detect is there currently scrollbar appeared
                var hasScroll = form.body.dom.scrollHeight > nh;

                // If appeared, or dissapeared
                if (form.hasScroll == undefined || hasScroll !== form.hasScroll) {

                    // Update `anchor` property for each field
                    form.query('> *').forEach(function(item){
                        item.anchor = hasScroll ? '-16' : '100%';
                        item.updateLayout();
                    });

                    // Remember `hasScroll` state
                    form.hasScroll = hasScroll;
                }
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
        var me = this, cbAutosave = Ext.getCmp(me.panelDockedInnerBid() + 'autosave'),
            toggleA = ['save', 'autosave'], toggleI;

        // If we are within readOnly mode - return
        if (me.row.readOnly) return;

        // For each master toolbar item, that should be affected on form saving ability change
        for (var i = 0; i < toggleA.length; i++) {

            // Get item's component and if got - disable
            toggleI = Ext.getCmp(me.panelDockedInnerBid() + toggleA[i]);
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

    /**
     * Detect if all inputs within the row panel should be read-only
     */
    rowReadOnly: function() {
        return this.ti().actions.r('save', 'alias') ? false : true;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Detect if all inputs within the row panel should be read-only
        me.row.readOnly = me.rowReadOnly();

        // Setup row panel
        me.row = Ext.merge({
            items: me.formItemA(),
            url: me.ti().section.href + 'save'
                + (me.ti().row.id ? '/id/' + me.ti().row.id : '')
                + (me.ti().scope.hash ? '/ph/' + me.ti().scope.hash : '')
                + (me.ti().scope.aix ? '/aix/' + me.ti().scope.aix : '') + '/'
        }, me.row);

        // Call parent
        me.callParent();
    },

    /**
     * This config is to specify the only fields that should be displayed. This can be
     * 1. A string, containing comma-separated list of ids/aliases
     * 2. An array, containing ids/aliases
     * 3. Function, returning values compatible to both upper points
     *
     * @var {Array/String/Function}
     */
    formItemOnlyA: [],

    /**
     * Build and return array of form panel items.
     * If `fieldA` argument is given, function will will use it instead of me.ti().fields for building
     * array of form fields configuration objects
     *
     * @param fieldA
     * @return {Array}
     */
    formItemA: function(fieldA) {

        // Declare a number of auxiliary variables
        var me = this, itemA = [], itemI, itemX, eItemX, item$, eItem$, formItemOnlyA, xtype, build;

        // If `fieldA` argument was given - use it rather than me.ti().fields
        fieldA = fieldA || me.ti().fields;

        // Setup ids-array of a fields, that are disabled and shouldn't be shown in form,
        // and ids-array of a fields, that are disabled but should be shown in form
        var disabledA = me.ti().disabledFields.select('0', 'displayInForm').column('fieldId');
        var visibleA = me.ti().disabledFields.select('1', 'displayInForm').column('fieldId');

        // If me.formItemOnlyA is a function - call it
        formItemOnlyA = Ext.isFunction(me.formItemOnlyA) ? me.formItemOnlyA() : me.formItemOnlyA;

        // If `formItemOnlyA` variable is an object - convert it to empty array
        if (Ext.isObject(formItemOnlyA)) formItemOnlyA = [];

        // If formItemOnlyA is a non-empty string - convert it to array by comma-splitting
        if (Ext.isString(formItemOnlyA) && formItemOnlyA.length) formItemOnlyA = formItemOnlyA.split(',');

        // Header form item
        if (!arguments.length) itemA.push(me.formItemXSpan());

        // Other form items (fields)
        for (var i = 0; i < fieldA.length; i++) {

            // Reset `build` to `false`
            build = false;

            // Detect whether or not field should be presented in form
            if (formItemOnlyA.length) {
                if (formItemOnlyA.indexOf(fieldA[i].id) != -1) {
                    build = true;
                } else if (formItemOnlyA.indexOf(fieldA[i].alias) != -1) {
                    build = true;
                }
            } else if (disabledA.indexOf(fieldA[i].id) == -1) {
                build = true;
            }

            // If current field is not disabled, or disabled but visible
            if (build) {

                // Setup default config
                itemI = me.formItemDefault(fieldA[i]);

                // Get control element name
                xtype = fieldA[i].xtype || fieldA[i].foreign('elementId').alias;

                // Apply specific control element config, as fields control elements/xtypes may be different
                eItemX = 'formItemX' + Indi.ucfirst(xtype);
                if (Ext.isFunction(me[eItemX]) || Ext.isObject(me[eItemX])) {
                    itemX = Ext.isFunction(me[eItemX]) ? me[eItemX](itemI) : me[eItemX];
                    itemI = Ext.isObject(itemX) ? Ext.merge(itemI, itemX) : itemX;
                } else Ext.merge(itemI, {
                    fieldLabel: '!!! ' + xtype
                });

                // Apply field custom config
                eItem$ = 'formItem$' + Indi.ucfirst(fieldA[i].alias);
                if (Ext.isFunction(me[eItem$]) || Ext.isObject(me[eItem$])) {
                    item$ = Ext.isFunction(me[eItem$]) ? me[eItem$](itemI) : me[eItem$];
                    itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
                } else if (me[eItem$] === false) itemI = me[eItem$];

                // If itemI is still not empty/null/false
                if (itemI) {

                    // Setup `disabled` property as boolean true, if current field is disabled-but-visible
                    if (visibleA.indexOf(fieldA[i].id) != -1) itemI.disabled = true;

                    // Prepend `cls` property with 'i-field' css class name
                    itemI.cls = 'i-field' + (itemI.cls ? ' ' + itemI.cls : '');

                    // Push item to the `itemA` array
                    itemA.push(itemI);
                }
            }
        }

        // Return form items (fields) array
        return itemA;
    },

    /**
     * Fileupload-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXUpload: function(item) {
        return {
            xtype: 'filepanel',
            allowBlank: true
        }
    },

    /**
     * Color-picker fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXColor: function(item) {
        return {
            xtype: 'colorfield',
            allowBlank: true
        }
    },

    /**
     * Provide initial/default config for all fields
     *
     * @param field
     * @return {Object}
     */
    formItemDefault: function(field) {
        var me = this;

        // Default config
        return {
            id: me.bid() + '-field$' + field.alias,
            xtype: 'textfield',
            readOnly: me.row.readOnly,
            fieldLabel: field.title,
            name: field.alias,
            satellite: field.satellite,
            value: this.ti().row[field.alias],
            allowBlank: field.mode != 'required' && (parseInt(field.relation) != 6 || field.storeRelationAbility == 'many'),
            disabled: field.mode == 'readonly',
            labelAlign: field.params && field.params.wide == 'true' ? 'top' : 'left',
            cls: field.params && field.params.wide == 'true' ? 'i-field-wide' : '',
            field: field,
            row: this.ti().row,
            listeners: {
                validitychange: function(cmp, valid){
                    if (!valid) me.toggleSaveAbility(valid); else {
                        var activeErrors = 0;
                        cmp.up('form').getForm().getFields().each(function(field, index, length){
                            if (field.hasActiveError()) activeErrors++;
                        });
                        if (!activeErrors) me.toggleSaveAbility(true);
                    }
                },
                dirtychange: function(cmp, dirty) {
                    if (cmp.el) {
                        cmp.getDirtyIcon().alignTo(cmp.el, 'tl', [0, 1]);
                        cmp.getDirtyIcon().setVisible(dirty);
                    }
                },
                resize: function(cmp) {
                    if (cmp.dirtyIcon) cmp.dirtyIcon.alignTo(cmp.el, 'tl', [0, 1]);
                },
                show: function(cmp) {
                    cmp.ownerCt.query('> *').forEach(function(sbl){
                        if (sbl.dirtyIcon) sbl.dirtyIcon.alignTo(sbl.el, 'tl', [0, 1]);
                    })
                },
                hide: function(cmp) {
                    cmp.ownerCt.query('> *').forEach(function(sbl){
                        if (sbl.dirtyIcon) sbl.dirtyIcon.alignTo(sbl.el, 'tl', [0, 1]);
                    })
                }
            },
            getDirtyIcon: function() {
                if (!this.dirtyIcon) this.dirtyIcon = this.el.createChild({cls: 'i-field-dirty-icon', html: '&nbsp;'});
                return this.dirtyIcon
            }
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
            id: this.bid() + (item ? '-field$' + item.field.alias : '-header'),
            xtype: 'displayfield',
            cls: (item ? '' : 'i-field ') + 'i-field-span' + (item ? '' : ' i-field-span-title'),
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

        // Cfg object
        var cfgO = {
            maxLength: 255
        }

        // If field's name is 'alias' - setup `allowBlank` property as `false`
        if (item.name == 'alias') cfgO.allowBlank = false;

        // Apply input mask
        if (item.field.params && item.field.params.inputMask) cfgO.inputMask = item.field.params.inputMask;

        // Apply vtype
        if (item.field.params && item.field.params.vtype) cfgO.vtype = item.field.params.vtype;

        // Return config
        return cfgO
    },

    /**
     * Calendar-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXCalendar: function(item) {

        // Prevent field from being marked as dirty in case if initial value is "0000-00-00", but actual (Date object)
        // value is null, because "0000-00-00" can't be converted into a valid Date object
        if (item.value == '0000-00-00') item.value = null;

        // Config
        return {
            xtype: 'datefield',
            ariaTitle: '',
            cls: 'i-field-date',
            startDay: 1,
            format: item.field.params.displayFormat,
            submitFormat: 'Y-m-d'
        };
    },

    /**
     * Datetime-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXDatetime: function(item) {

        // Prevent field from being marked as dirty in case if initial value is "0000-00-00 00:00:00",
        // but actual (Date object) value is null, because "0000-00-00 00:00:00" can't be converted
        // into a valid Date object
        if (item.value == '0000-00-00 00:00:00') item.value = null;

        // Config
        return {
            xtype: 'datetimefield',
            cls: 'i-field-datetime',
            startDay: 1,
            format: item.field.params.displayDateFormat,
            submitFormat: 'Y-m-d H:i:s'
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
        var tbq = item.field.params.measure.match(/^tbq:([^;]+)/);
        return {
            xtype: 'numberfield',
            cls: 'i-field-number',
            afterSubTpl: '<span class="i-field-number-after">' + (tbq ? '' : item.field.params.measure) + '</span>',
            maxLength: item.field.params.maxlength,
            minValue: 0,
            maxValue: Math.pow(2, 32) - 1,
            tbq: tbq ? tbq[1] : ''
        };
    },

    /**
     * Price-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXPrice: function(item) {
        return {
            xtype: 'numberfield',
            cls: 'i-field-number',
            afterSubTpl: '<span class="i-field-number-after">'+ (item.field.params && item.field.params.measure ? item.field.params.measure : '')+'</span>',
            maxLength: 12,
            minValue: 0,
            maxValue: Math.pow(10, 9) - 0.01,
            precisionPad: true,
            value: parseFloat(item.value) || item.value
        };
    },

    /**
     * Decimal143-fields config adjuster
     *
     * @param item
     * @return {Object}
     */
    formItemXDecimal143: function(item) {
        return {
            xtype: 'numberfield',
            cls: 'i-field-number',
            afterSubTpl: '<span class="i-field-number-after">'+ (item.field.params && item.field.params.measure ? item.field.params.measure : '')+'</span>',
            maxLength: 15,
            minValue: 0,
            maxValue: Math.pow(10, 11) - 0.01,
            decimalPrecision: 3,
            precisionPad: true,
            value: parseFloat(item.value) || item.value
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
            growMin: 30,
            rows: 2,
            value: Ext.isNumber(item.value) ? item.value + '' : (item.value || '')
        }
    },

    /**
     * Radio-fields config adjuster. If field, that radio should represent
     * is not enumset field - use combo instead radios
     *
     * @param item
     * @return {Object}
     */
    formItemXMulticheck: function(item) {
        return {
            xtype: 'multicheck',
            cls: 'i-field-multicheck',
            allowBlank: true
        }
    },

    /**
     * Radio-fields config adjuster. If field, that radio should represent
     * is not enumset field - use combo instead radios
     *
     * @param item
     * @return {Object}
     */
    formItemXRadio: function(item) {
        return item.field.relation == '6' ? {xtype: 'radios'} : this.formItemXCombo(item);
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
            store: item.row.view(item.name).store,
            multiSelect: item.field.storeRelationAbility == 'many'
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
            xtype: 'ckeditor',
            allowBlank: true
        }
    },

    /**
     * Master toolbar 'Save' item, for ability to save form data
     *
     * @return {Object}
     */
    panelDockedInner$Save: function() {

        // Here we check if 'save' action is in the list of allowed actions
        var me = this;

        // 'Save' item config
        return {
            id: me.panelDockedInnerBid() + 'save',
            xtype: 'button',
            text: Indi.lang.I_SAVE,
            handler: function() {

                // If we're within a tab, button click should lead to saving the form, and to redirect
                // back to that form, so if an existing row was saved, we should return to it, or if
                // new row was saved - we should return to that newly created row
                if (this.up('[isWrapper]').isTab) me.goto(me.panelDockedInner$Reload_uri(true), true);

                // Else follow ordinary behaviour
                else me.goto(me.panelDockedInner$Back(true), true);
            },
            disabled: me.row.readOnly,
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
    panelDockedInner$Autosave: function() {
        var me = this;

        // 'Autosave' item config
        return {
            id: me.panelDockedInnerBid() + 'autosave',
            xtype: 'checkbox',
            tooltip: {html: Indi.lang.I_AUTOSAVE, staticOffset: [0, 4]},
            disabled: me.row.readOnly,
            cls: 'i-cb-autosave',
            checked: me.ti().scope.toggledSave,
            margin: '0 6 0 3',
            handler: function(cb){

                // Create shortcuts for involved components
                var btnSave = Ext.getCmp(me.panelDockedInnerBid() + 'save'),
                    sqNested = Ext.getCmp(me.panelDockedInnerBid() + 'nested');

                // Other items adjustments
                if (btnSave) btnSave.toggle();
                if (sqNested && me.ti().sections.length && !me.ti().row.id) sqNested.setDisabled(!cb.checked);
            },
            listeners: {
                afterrender: function(){
                    var btnSave = Ext.getCmp(me.panelDockedInnerBid() + 'save');
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
     * Master toolbar 'Reset' item, for ability to reset form changes
     *
     * @return {Object}
     */
    panelDockedInner$Reset: function() {
        var me = this;

        // 'Reset' item config
        return {
            id: me.panelDockedInnerBid() + 'reset',
            iconCls: 'i-btn-icon-reset',
            disabled: true,
            tooltip: Indi.lang.I_NAVTO_RESET,
            handler: function() {

                // Reset form
                Ext.getCmp(me.row.id).getForm().reset();

                // If save ability was turned On before reset, but now it is turned Off - turn it On back
                me.toggleSaveAbility(true);
            }
        }
    },

    /**
     * Master toolbar 'Autosave' item, for ability to toggle autosave mode while navigating
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelDockedInner$Create: function() {
        var me = this;

        // 'Create' item config
        return {
            id: me.panelDockedInnerBid() + 'create',
            iconCls: 'i-btn-icon-create',
            disabled: parseInt(me.ti().section.disableAdd) == 1 || (me.row.readOnly && !me.row.createOnly && parseInt(me.ti().section.disableAdd) != 2) ? true : false,
            tooltip: Indi.lang.I_NAVTO_CREATE,
            handler: function(){

                // Create shortcuts for involved components
                var url = '/' + me.ti().section.alias + '/' + me.ti().action.alias + '/ph/' + me.ti().section.primaryHash+'/',
                    tfID = Ext.getCmp(me.panelDockedInnerBid() + 'id'),
                    btnPrev = Ext.getCmp(me.panelDockedInnerBid() + 'prev'),
                    btnNext = Ext.getCmp(me.panelDockedInnerBid() + 'next'),
                    cmbSibling = Ext.getCmp(me.panelDockedInnerBid() + 'sibling'),
                    spnOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset');

                // Show mask
                me.getMask().show();

                // Other items adjustments
                if (tfID) tfID.setValue('');
                if (btnPrev) btnPrev.disable();
                if (cmbSibling && typeof me.ti().row.title != 'undefined') cmbSibling.keywordEl.val('');
                if (btnNext && parseInt(me.ti().scope.found)) btnNext.enable();
                if (spnOffset) spnOffset.setValue('');

                // Goto the url
                me.goto(url, undefined, {
                    title: Indi.lang.I_CREATE
                });
            }
        }
    },

    /**
     * Loads the url, given in `url` arg. This function overrides parent class `goto` function,
     * as autosave mode should be additionally taken into attention here
     *
     * @param url
     */
    goto: function(url, btnSaveClick, cfg) {

        // Create shortcuts for involved components
        var me = this, hidden = Ext.getCmp(me.bid() + '-redirect-url'),
            btnSave = Ext.getCmp(me.panelDockedInnerBid() + 'save'),
            cbAutosave = Ext.getCmp(me.panelDockedInnerBid() + 'autosave'),
            formCmp = Ext.getCmp(me.bid() + '-row'), gotoO, isTab = Ext.getCmp(me.panel.id).isTab, found;

        // If `noGoto` flag is turned on, or previous save request is not yet completed - return
        if (me.noGoto || formCmp.getForm().isLoading) return;

        // If save button is toggled
        if (btnSave && !btnSave.disabled && (btnSave.pressed || btnSaveClick)) {

            // "-1" - is a special value that means that after save, this form should not be displayed again
            // The same concept is when form is in tab and autosave-checkbox is not checked
            if (btnSaveClick == -1 || (isTab && !cbAutosave.checked)) url += '?stopAutosave=1';

            // Update value of the 'redirect-url' field, or, if it's not
            // yet exists - create it and assign `url` as it's value
            if (hidden) hidden.setValue(url);

            // Else there is no hidden field (for redirect), yes but it should be created
            else if (!btnSaveClick || btnSaveClick == -1 || isTab)

                // Create it
                formCmp.add({
                    id: me.bid() + '-redirect-url',
                    xtype: 'hidden',
                    name: 'redirect-url'
                })

                // Set a value to it, so field become dirty
                .setValue(url);

            // If form is valid
            if (formCmp.getForm().isValid()) {

                // If data-row, that current form is operating with - is an existing row, or is a new row, but has
                // at least one property that had been changed using current form - submit (try to save) the form
                if (parseInt(me.ti().row.id) || formCmp.getForm().isDirty()) {

                    // Show mask if form is within tab
                    if (isTab) me.getMask().show();

                    // Prevent duplicate save request
                    formCmp.getForm().isLoading = true;

                    // Submit form
                    formCmp.submit({
                        submitEmptyText: false,
                        dirtyOnly: true
                    });

                // Else if user is trying to create a new row, but didn't setup any data for that new row - show warning
                } else Ext.MessageBox.show({
                    title: Indi.lang.I_ROWSAVE_ERROR_NOTDIRTY_TITLE,
                    msg: Indi.lang.I_ROWSAVE_ERROR_NOTDIRTY_MSG,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.WARNING,
                    fn: function() {
                        formCmp.focus();
                    }
                });

            // Else
            } else {

                // Scroll to the first invalid field
                formCmp.down('[activeError]').el.scrollIntoView(formCmp.body, false, true);

                // Hide mask
                me.getMask().hide();
            }

        // Else
        } else {

            // If `forceValidate` arg is given we check form validity before loading required contents
            if (btnSave && btnSave.pressed && !formCmp.getForm().isValid()) return;

            // Append request failure callback to the load config
            Ext.merge({
                failure: function() {
                    me.getMask().hide();
                }
            }, cfg);

            // Parse request url
            gotoO = Indi.parseUri(url);

            // Append autosave flag to the query string of the uri, if needed
            var uri = url + (me.ti().scope.toggledSave && me.ti().action.alias == 'form' ? '?stopAutosave=1' : ''),
                wrp = Ext.getCmp(me.panel.id), sth = wrp.up('[isSouth]'), sthItm = wrp.up('[isSouthItem]');

            // If current wrapper is placed within a tab, and we gonna go to same-type wrapper
            if (isTab && gotoO.section == me.ti().section.alias && gotoO.action == 'form') {

                // If tab, that we are gonna goto - is already exists
                if (sthItm.name != gotoO.id && (found = sth.down('[isSouthItem][name="' + gotoO.id + '"]'))) {

                    // Hide mask
                    me.getMask().hide();

                    // Reset master toolbar controls
                    me.noGoto = true;
                    Ext.getCmp(me.panelDockedInnerBid() + 'id').reset();
                    Ext.getCmp(me.panelDockedInnerBid() + 'sibling').reset();
                    Ext.getCmp(me.panelDockedInnerBid() + 'offset').reset();
                    me.noGoto = false;

                    // Set found tab as active
                    sth.setActiveTab(found);

                    // Force function stop, so nothing will be loaded, unless 'setActiveTab(found)' call will load something
                    return;
                }

                // Ensure current wrapper to be replaced by new same-type wrapper
                Ext.merge(cfg, {
                    insteadOf: me.panel.id,
                    into: sthItm.id
                });
            }

            // Load required contents into the main panel
            Indi.load(uri, cfg);
        }
    },

    // Key map for row body
    keyMap: function() {
        var me = this;

        // Set up focus
        Ext.getCmp(me.row.id).focus();

        // Call parent
        me.callParent();

        // Attach key map on a row panel
        if (Ext.getCmp(me.row.id).rendered) Ext.getCmp(me.row.id).getEl().addKeyMap({
            eventName: 'keydown',
            binding: [{
                key: Ext.EventObject.N,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$create'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.F4,
                shift: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$create'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.A,
                alt: true,
                fn:  function(){
                    var cb = Ext.getCmp(me.bid() + '-docked-inner$autosave'); if (cb) cb.press();
                },
                scope: me
            },{
                key: Ext.EventObject.S,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$save'); if (btn) btn.press();
                },
                scope: me
            }]
        });
    }
});
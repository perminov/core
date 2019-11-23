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
                    {alias: 'close'},
                    {alias: 'ID'},
                    {alias: 'reload'}, '-',
                    {alias: 'save'}, {alias: 'autosave'}, '-',
                    {alias: 'reset'}, '-',
                    {alias: 'prev'}, {alias: 'sibling'}, {alias: 'next'}, '-',
                    {alias: 'actions'},
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
        layout: {
            type: 'form',
            tableCls: 'x-form-layout-table i-table'
        },
        defaults: {
            labelWidth: '50%',
            width: '100%'
        },

        // @inheritdoc
        listeners: {
            boxready: function(){
                Ext.defer(function(){
                    this.body.attr('tabindex', '0');
                }, 100, this);
            },
            validitychange: function(form, valid){
                if (valid) this.ctx().toggleSaveAbility(valid);
            },
            dirtychange: function(form, dirty) {
                var resetBtn = Ext.getCmp(this.ctx().panelDockedInnerBid() + 'reset');
                if (resetBtn) resetBtn.setDisabled(!dirty);
            },
            beforeaction: function() {
                Indi.app.loader();
            },
            actioncomplete: function(form, action) {
                var me = this, json = action.response.responseText.json(), gotoO, uri, cfg = {},
                    wrp = me.up('[isWrapper]'), isTab = wrp.isTab, sth = wrp.up('[isSouth]'),
                    sthItm = wrp.up('[isSouthItem]'), found, rowsetActId, rowsetActCmp, record;

                // If response text is not json-convertable, or does not have `redirect` property - return
                if (!Ext.isObject(json) || !(uri = json.redirect || '').length) return;

                // Affect record
                me.ctx().affectRecord(action.response);

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

                // Else if we are going to go to same-section rowset -
                } else if (gotoO.section == me.ti().section.alias && gotoO.action == 'index') {

                    // Close the form's window
                    return wrp.getWindow().close();
                }

                // If form saved
                if (json.success) {

                    // Apply callbacks for redirect-request
                    Ext.merge(cfg, {

                        // If redirect was unsuccessful - make sure current action will be reloaded
                        failure: function() {
                            uri = '/' + me.ti().section.alias + '/' + me.ti().action.alias
                                + '/id/' + json.id + '/ph/' + me.ti().scope.hash
                                + '/aix/' + json.aix + '/';
                            Indi.load(uri);
                        },

                        // If entry was successfully deleted - close window
                        callback: function(options, success, response) {
                            if (success && gotoO.action == 'delete') wrp.getWindow().close();
                        }
                    });

                    // If jump is intended to goto 'create-new-entry' action within one of nested sections
                    if (gotoO.action == 'form' && gotoO.jump && gotoO.section != me.ti().section.alias) {

                        // Close
                        //wrp.getWindow().close();

                        // Spoof uri
                        var uri = '/' + me.ti().section.alias + '/' + me.ti().action.alias
                            + '/id/' + json.id + '/ph/' + me.ti().scope.hash
                            + '/aix/' + json.aix + '/';

                        // Force jump-uri to be loaded, but after 'created-new-entry'
                        // screen is overwritten by 'edit-existing' screen
                        cfg.callback = function(options, success, response) {
                            if (success) Indi.load(json.redirect);
                        }
                    }
                }
                // Load required contents
                Indi.load(uri, cfg);
            },
            actionfailed: function(form, action) {

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
        var me = this, itemA = [], itemI, itemX, eItemX, item$, eItem$, formItemOnlyA, xtype, build, se;

        // If `fieldA` argument was given - use it rather than me.ti().fields
        fieldA = fieldA || me.ti().fields;

        // If space fields are defined for current model, and duration-field is exist among them
        if (me.ti().model.space.fields) se = me.ti().model.space.fields.events;

        // Setup ids-array of a fields, that are disabled and shouldn't be shown in form,
        // and ids-array of a fields, that are disabled but should be shown in form
        var disabledA = fieldA.select('hidden', 'mode').column('id');
        var visibleA = fieldA.select('readonly', 'mode').column('id');

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

                    // If current model does have space fields, and schedule-related event handlers should be added
                    if (se && itemI.name in se.change) Ext.merge(itemI, {listeners: {

                        // Bind handlers for certain events once form item was rendered
                        afterrender: function(c) {

                            // Bind 'change' event listener
                            c.on('change', function(c){ me.refreshSpaceOptions(c); });

                            // If current field is a 'date' or 'datetime' coord-field (e.g is a calendar)
                            if (c.name == se.boundchange) {

                                // Make sure space options will be refreshed on calendar month change
                                c.on('boundchange', function(c, bounds) {
                                    me.refreshSpaceOptions(c, {
                                        since: Ext.Date.format(bounds[0], 'Y-m-d'),
                                        until: Ext.Date.format(bounds[1], 'Y-m-d')
                                    });
                                });

                                // Make sure space options will be refreshed on calendar blur,
                                // because user may not select any date despite month change
                                // so we need to refresh options considering current field's date
                                c.on('blur', function(c){ me.refreshSpaceOptions(c); });
                            }

                            // If current field is a duration-field, e.g. is a 'dayQty' or 'minuteQty'
                            // or 'timespan' coord-field - refresh space options right now for them to
                            // be already refreshed at the moment of form appeared within the UI
                            if (c.name == se.afterrender) me.refreshSpaceOptions(c);
                        }
                    }});

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
            allowBlank: true,
            allowTypes: item.field.params.allowTypes,
            minSize: item.field.params.minSize,
            maxSize: item.field.params.maxSize
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
            value: this.ti().row[field.alias],
            allowBlank: field.mode != 'required' && (parseInt(field.relation) != 6 || field.storeRelationAbility == 'many'),
            disabled: field.mode == 'readonly',
            labelAlign: field.params && field.params.wide == 'true' ? 'top' : 'left',
            cls: field.params && field.params.wide == 'true' ? 'i-field-wide' : '',
            originalValue: this.ti().row._original && field.alias in this.ti().row._original ? this.ti().row._original[field.alias] : undefined,
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
                    var w = cmp.up('actionrow').getWindow();
                    var diff = arguments[2] - arguments[4];
                    if (diff && cmp.up('actionrow').heightUsage && !cmp.up('actionrow').down('[isSouth]')) {
                        if (w.restoreSize) w.restoreSize.height += diff;
                        if (!w.maximized) w.setHeight(w.height + diff);
                    }
                },
                show: function(cmp) {
                    cmp.ownerCt.query('> *').forEach(function(sbl){
                        if (sbl.dirtyIcon) sbl.dirtyIcon.alignTo(sbl.el, 'tl', [0, 1]);
                    });

                    var w = cmp.up('actionrow').getWindow();
                    var diff = cmp.getHeight() - cmp._wasHeight;
                    if (diff && cmp.up('actionrow').heightUsage && !cmp.up('actionrow').down('[isSouth]')) {
                        if (w.restoreSize) w.restoreSize.height += diff;
                        if (!w.maximized) w.setHeight(w.height + diff);
                    }
                },
                hide: function(cmp) {
                    cmp.ownerCt.query('> *').forEach(function(sbl){
                        if (sbl.dirtyIcon) sbl.dirtyIcon.alignTo(sbl.el, 'tl', [0, 1]);
                    })

                    var w = cmp.up('actionrow').getWindow();
                    var diff = cmp.getHeight() - cmp._wasHeight;
                    if (diff && cmp.up('actionrow').heightUsage && !cmp.up('actionrow').down('[isSouth]')) {
                        if (w.restoreSize) w.restoreSize.height += diff;
                        if (!w.maximized) w.setHeight(w.height + diff);
                    }
                },
                beforeshow: function(c) {
                    if (this.el) c._wasHeight = c.getHeight();
                },
                beforehide: function(c) {
                    if (this.el) c._wasHeight = c.getHeight();
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
            value: '<span>' + (item ? item.field.title : this.ti().model.title) + '</span>',
            align: 'center',
            getInputWidthUsage: function() {
                return this.inputEl.down('span').getWidth() + this.getEl().getBorderWidth('rl');
            }
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

        // Apply vtype
        if (Indi.ini.demo && item.field.params && item.field.params.shade) {
            cfgO.value = '';
            cfgO.emptyText = Indi.lang.I_PRIVATE_DATA;
        }

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
        }
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
            getInputWidthUsage: function() {
                return this.getLabelWidthUsage();
            },
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
        return item.field.relation == '6' ? {xtype: 'radios', cls: 'i-field-radio'} : this.formItemXCombo(item);
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
            },
            getInputWidthUsage: function() {
                return this.inputEl.getWidth();
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
            cls: 'i-field i-field-combo-form',
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
        var me = this, bid = me.panelDockedInnerBid();

        // 'Autosave' item config
        return {
            id: bid + 'autosave',
            xtype: 'checkbox',
            tooltip: {html: Indi.lang.I_AUTOSAVE, staticOffset: [0, 4]},
            disabled: me.row.readOnly,
            cls: 'i-cb-autosave',
            checked: me.ti().scope.toggledSave,
            margin: '0 6 0 3',
            handler: function(cb){

                // Create shortcuts for involved components
                var btnSave = Ext.getCmp(bid + 'save');

                // Other items adjustments
                if (btnSave) btnSave.toggle();
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
            formCmp = Ext.getCmp(me.bid() + '-row'), gotoO, isTab = Ext.getCmp(me.panel.id).isTab, found,
            cfg = cfg || {};

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
            if (isTab && gotoO.section == me.ti().section.alias && (gotoO.action == 'form' || gotoO.action == 'print')) {

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
    },

    /**
     *  Refresh options, that are selectable within space-fields.
     *  This function prevent user from selecting options leading to
     *  schedule overlapping
     *
     *
     * @param srcField
     * @param calendarBounds
     */
    refreshSpaceOptions: function(srcField, calendarBounds) {
        var me = this, data = {}, name, dd, sbl;

        // Collect data for all space-fields
        for (name in me.ti().model.space.fields.events.change)
            if (sbl = srcField.sbl(name)) data[name] = sbl.getSubmitValue();

        // If calendarBounds arg is given - append to collected data
        if (calendarBounds) Ext.Object.merge(data, calendarBounds);

        // Make a special request to get the inaccessible values for each field considering their current values
        Indi.load('/' + me.ti().section.alias + '/form' + (me.ti().row.id ? '/id/' + me.ti().row.id : '') + '/consider/duration/', {
            params: data,
            success: function(response) {

                // Get info about disabled values for each field
                dd = response.responseText.json().disabled;

                // Apply those disabled values, so only non-disabled will remain accessible
                for (var i in dd) if (sbl = srcField.sbl(i)) sbl.setDisabledOptions(dd[i]);
            }
        });
    }
});
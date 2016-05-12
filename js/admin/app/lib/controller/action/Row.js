/**
 * Base class for all controller actions instances, that operate with some certain rows
 */
Ext.define('Indi.lib.controller.action.Row', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Row',

    // @inheritdoc
    extend: 'Indi.Controller.Action',

    // @inheritdoc
    mcopwso: ['row', 'panel', 'south'],

    // @inheritdoc
    panel: {

        // @inheritdoc
        xtype: 'actionrow',

        title: '',
        closable: false,

        /**
         * Here we separate docked items on items-level1 and items-level2, e.g items-level1 are
         * panels/toolbars definitions, and items-level2 are child items within these panels/toolbars.
         * The `alias` property is a special property, in some sense similar to extjs 'xtype' property.
         * `alias` property in some sense is the key that connecting panels/toolbars with their items collections.
         * In 99.99% cases, when `alias` property faced here - it means that there is exist a special function
         * that is responsible for building fully/partially configuration object for such item
         */
        docked: {
            default: {minHeight: 27},
            items: [{alias: 'master'}],
            inner: {
                master: [
                    {alias: 'close'},
                    {alias: 'ID'},
                    {alias: 'reload'}, '-',
                    {alias: 'prev'}, {alias: 'sibling'}, {alias: 'next'}, '-',
                    {alias: 'nested'}, '->',
                    {alias: 'offset'}, {alias: 'found'}
                ]
            }
        }
    },

    /**
     * Main panel's row inner panel config
     */
    row: {
        border: 0,
        height: '70%'
    },

    south: {
        xtype: 'rowactionsouth'
    },

    /**
     * Build and return array of row-panel toolbars
     *
     * @return {Array}
     */
    rowDockedA: function() {
        return this._docked('row');
    },

    /**
     * Panel master toolbar builder
     *
     * @return {Object}
     */
    panelDocked$Master: function() {
        return {
            id: this.bid() + '-docked$master',
            dock: 'top',
            minHeight: 26,
            items: this.panelDocked$MasterItemA()
        }
    },

    /**
     * Panel master toolbar id constructor
     *
     * @return {String}
     */
    panelDockedInnerBid: function() {
        return this.bid() + '-docked-inner$';
    },

    /**
     * Master toolbar 'Back' button
     *
     * @return {Object}
     */
    panelDockedInner$Back: function(urlonly) {

        // Build the url for goto
        var me = this, isTab = me.panel.xtype == 'actiontabrow', url = '/' + me.ti().section.alias + '/';
        if (me.ti(1).row) url += 'index/id/' + me.ti(1).row.id + '/' +
            (me.ti().scope.upperHash ? 'ph/'+me.ti().scope.upperHash+'/' : '') +
            (me.ti().scope.upperAix ? 'aix/'+me.ti().scope.upperAix+'/' : '');

        // Return builded url, if `geturl` arg is given, or return 'Back' button config otherwise
        return urlonly ? url : (isTab ? null : {
            id: me.panelDockedInnerBid() + 'back',
            text: '',
            iconCls: 'i-btn-icon-back',
            tooltip: Indi.lang.I_NAVTO_ROWSET,
            handler: function() {

                // Show mask
                me.getMask().show();

                // Goto url
                me.goto(url);
            }
        })
    },

    /**
     * Master toolbar 'Close' button
     *
     * @return {Object}
     */
    panelDockedInner$Close: function() {
        var me = this, isTab = me.panel.xtype == 'actiontabrow';

        if (!isTab) return null;

        // Return 'Close' button config
        return [{
            id: me.panelDockedInnerBid() + 'close',
            text: '',
            iconCls: 'i-btn-icon-close',
            tooltip: Indi.lang.I_CLOSE,
            handler: function() {
                Ext.getCmp(me.panel.id).up('[isSouth]').getActiveTab().close();
            }
        }, '-']
    },

    /**
     * Master toolbar 'Back' button right-side separator
     *
     * @return {Object}
     */
    panelDockedInner$BackSeparator: function() {
        var me = this; return me.panel.xtype == 'actiontabrow' ? null : {xtype: 'tbseparator'}
    },

    /**
     * Master toolbar 'ID' field, for ability to navigate to some row by it's id
     *
     * @return {Object}
     */
    panelDockedInner$ID: function() {

        // Setup getMaxWidth function
        var me = this, getMaxWidth = function(row, mode) {
            var labelWidth = 20, inputWidth = 30;

            if (row.id) {
                inputWidth = row.id.toString().length * 9;
                inputWidth = inputWidth > 30 ? inputWidth : 30;
            }

            return mode == 'tooltipOffset'
                ? labelWidth + inputWidth/2 - (labelWidth + inputWidth)/2
                : labelWidth + inputWidth;
        };

        // ID field config
        return {
            id: me.panelDockedInnerBid()+'id',
            fieldLabel: 'ID',
            labelWidth: 15,
            xtype: 'numberfield',
            hideTrigger: true,
            tooltip: {
                html: Indi.lang.I_NAVTO_ID,
                staticOffset: [getMaxWidth(me.ti().row, 'tooltipOffset'), 1]
            },
            value: me.ti().row.id,
            width: getMaxWidth(me.ti().row),
            lastValidValue: me.ti().row.id,
            margin: '0 3 0 3',
            disabled: parseInt(me.ti().scope.found) <= 1,
            errorMsgCls: '',
            minValue: 1,
            listeners: {

                // Change hander
                change: function(input){

                    // We provide a reload ability only after user finished typing in ID field
                    if (input.changeTimeout) clearTimeout(input.changeTimeout);
                    input.changeTimeout = setTimeout(function(input){

                        // If field's value is not empty, and value is not the same as last valid value
                        if (input.getValue() && input.getValue() != input.lastValidValue) {

                            // Show mask
                            me.getMask().show();

                            // Build the request uri and setup save button shortcut
                            var url = '/' + me.ti().section.alias + '/' + me.ti().action.alias + '/id/' +
                                input.getValue() + '/ph/'+ me.ti().section.primaryHash+'/';

                            // We should ensure that row that user wants to retrieve
                            // - is exists within a current section scope
                            Ext.Ajax.request({
                                url: Indi.pre.replace(/\/$/, '') + url + 'check/1/',
                                params: {forceOffsetDetection: true},
                                success: function(response){

                                    // Convert response to json object
                                    var json = response.responseText.json(), aix;

                                    // If offset was successfully detected,
                                    // append 'aix' param to the url, and load that url
                                    if (Ext.isObject(json) && !isNaN(aix = parseInt(json.aix)))
                                        me.goto(url += 'aix/' + aix + '/', undefined, {
                                            title: json.title
                                        });

                                    // Otherwise we build an warning message, and display Ext.MessageBox
                                    else me.onDetectionFailed(input);
                                },
                                failure: function() {
                                    me.getMask().hide();
                                }
                            });
                        }
                    }, 500, input);
                }
            }
        }
    },

    /**
     * Build the uri for making reload request
     *
     * @param autosave {Boolean}
     * @return {String}
     */
    panelDockedInner$Reload_uri: function (autosave) {
        var me = this, uri = '/' + me.ti().section.alias + '/' + me.ti().action.alias;

        // Append 'id' param to the uri
        uri += autosave ? '/id/'+ (parseInt(me.ti().row.id) ? me.ti().row.id  : '') : (parseInt(me.ti().row.id) ? '/id/' + me.ti().row.id : '');

        // Append 'ph' and 'aix' params
        uri += '/ph/'+ me.ti().scope.hash + '/' + (me.ti().scope.aix || autosave ? 'aix/'+ me.ti().scope.aix +'/' : '');

        // Return
        return uri;
    },

    /**
     * Master toolbar 'Reload' item, for ability to reload the current row
     *
     * @return {Object}
     */
    panelDockedInner$Reload: function() {
        var me = this, ats;

        // 'Reload' item config
        return {
            id: me.panelDockedInnerBid() + 'reload',
            iconCls: 'x-tbar-loading',
            tooltip: Indi.lang.I_NAVTO_RELOAD,
            handler: function(){

                // Show mask
                me.getMask().show();

                // Get the autosave-checkbox
                ats = Ext.getCmp(me.bid() + '-docked-inner$autosave');

                // Reload the current uri
                me.goto(me.panelDockedInner$Reload_uri(ats && ats.checked), undefined, {
                    title: me.ti().row.id ? me.ti().row.title : Indi.lang.I_CREATE
                });
            }
        }
    },

    /**
     * Master toolbar 'Found' field, that will be displaying found number of rows,
     * accessible within for user at the moment
     *
     * @return {Object}
     */
    panelDockedInner$Found: function() {
        var me = this;

        // 'Found' field config
        return {
            id: me.panelDockedInnerBid() + 'found',
            xtype: 'displayfield',
            disabled: parseInt(me.ti().scope.found) ? false : true,
            value: Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_OF + Indi.numberFormat(me.ti().scope.found),
            width: Indi.metrics.getWidth(Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_OF
                + Indi.numberFormat(me.ti().scope.found))
        }
    },

    /**
     * Master toolbar 'Offset' field, for ability to navigate to some row by it's offset/index
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelDockedInner$Offset: function() {

        // Setup getMaxWidth function
        var me = this, getMaxWidth = function(found, mode) {

            // Setup auxilliary variables
            var labelWidth = Indi.metrics.getWidth(Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE),
                triggerWidth = 20, inputWidth = (found.toString().length + 1) * 7 + 2;

            // Set the minimum input field width as 30px
            inputWidth = inputWidth > 30 ? inputWidth : 30;

            // Calculate and return total width, depending whether or not width will be used to determite
            // x-offset of current field's tooltip
            return mode == 'tooltipOffset'
                ? labelWidth + 5 + inputWidth/2 - (labelWidth + 5 + inputWidth + triggerWidth)/2
                : labelWidth + inputWidth + triggerWidth;

        };

        // 'Offset' field config
        return {
            id: me.panelDockedInnerBid() + 'offset',
            fieldLabel: Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE,
            labelSeparator: '',
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE) + 2,
            xtype: 'numberfield',
            tooltip: {
                html: Indi.lang.I_NAVTO_ROWINDEX,
                staticOffset: [getMaxWidth(me.ti().scope.found, 'tooltipOffset'), 0]
            },
            value: (me.ti().row.id ? me.ti().scope.aix : ''),
            width: getMaxWidth(me.ti().scope.found) + 1,
            disabled: parseInt(me.ti().scope.found) > 1 ? false : true,
            margin: '0 5 0 3',
            minValue: 1,
            maxValue: me.ti().scope.found,
            validateOnChange: false,
            lastValidValue: (me.ti().scope.aix ? me.ti().scope.aix : ''),
            listeners: {

                // Change hander
                change: function(input){

                    // We provide a reload ability only after user finished typing in ID field
                    if (input.changeTimeout) clearTimeout(input.changeTimeout);
                    input.changeTimeout = setTimeout(function(input){

                        // If field's value is not empty, and value is not the same as last valid value
                        if (input.getValue() &&
                            input.getValue() >= input.minValue &&
                            input.getValue() <= input.maxValue &&
                            input.getValue() != input.lastValidValue) {

                            // Show mask
                            me.getMask().show();

                            // Get 'Prev' and 'Next' items
                            var btnPrev = Ext.getCmp(me.panelDockedInnerBid() + 'prev'),
                                btnNext = Ext.getCmp(me.panelDockedInnerBid() + 'next');

                            // Disable/enable them if needed
                            if (btnPrev) btnPrev.setDisabled(input.getValue() == input.minValue);
                            if (btnNext) btnNext.setDisabled(input.getValue() == input.maxValue);

                            // Try to navigate to current row's sibling by it's offset
                            me.gotoOffset(input.getValue(), input);
                        }
                    }, 500, input);
                },
                afterrender: function() {
                    this.el.removeCls('x-field-default-toolbar');
                }
            }
        }
    },

    /**
     * Master toolbar 'Prev' field, for ability to navigate to previous row
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelDockedInner$Prev: function() {
        var me = this, enabled = parseInt(me.ti().scope.found) && parseInt(me.ti().scope.aix)
            && parseInt(me.ti().scope.aix) > 1;

        // 'Prev' field config
        return {
            id: me.panelDockedInnerBid() + 'prev',
            disabled: !enabled,
            tooltip: Indi.lang.I_NAVTO_PREV,
            iconCls: Ext.baseCSSPrefix + 'tbar-page-prev',
            handler: function(){
                var cmbSibling = Ext.getCmp(me.panelDockedInnerBid() + 'sibling'),
                    spnOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset'),
                    btnNext = Ext.getCmp(me.panelDockedInnerBid() + 'next'),
                    btnPrev = Ext.getCmp(me.panelDockedInnerBid() + 'prev'),
                    btnPrevEnabled = parseInt(me.ti().scope.found) && parseInt(me.ti().scope.aix)
                        && parseInt(me.ti().scope.aix) - 1 > 1;

                // Show mask
                me.getMask().show();

                // Here we should detect, whether we can use sibling combo for navigation
                // We check several conditions:
                // 1. Sibling combo exists
                // 2. It's running in 'no-keyword' mode
                // 3. Programmatical keyboard UP causes actual change
                //    of 'selectedIndex' attribute of combo's `keywordEl` element
                var canUseSibling = false, selectedIndexWas, selectedIndexNow;
                if (cmbSibling && cmbSibling.infoEl.attr('fetch-mode') == 'no-keyword') {

                    // Initiate sibling combo's picker rendering, if it wasn't yet rendered
                    cmbSibling.getPicker();

                    // Get value of 'selectedIndex' attribute before programmatic UP key press simulation
                    selectedIndexWas = cmbSibling.keywordEl.attr('selectedIndex');

                    // Do programmatic UP key press simulation
                    cmbSibling.keyDownHandler('38', true);

                    // Get value of 'selectedIndex' attribute after programmatic UP key press simulation was done
                    selectedIndexNow = cmbSibling.keywordEl.attr('selectedIndex');

                    // Compare values of `selectedIndex` attribute, and if change detected - allow use sibling
                    if (selectedIndexWas != selectedIndexNow) canUseSibling = true;
                }

                // If we have 'Sibling' master toolbar item - use it for navigation
                if (canUseSibling) cmbSibling.keyDownHandler('13', true);

                // Else if we have 'Offset' master toolbar item - use it's spinDown method
                else if (spnOffset) spnOffset.spinDown();

                // Try to navigate to current row's sibling by it's offset
                else me.gotoOffset(me.ti().scope.aix - 1);

                // Disable 'Prev' master toolbar item if needed
                btnPrev.setDisabled(!btnPrevEnabled);

                // Enable 'Next' master toolbar item, if it exists
                if (btnNext) btnNext.enable();
            }
        }
    },

    /**
     * Master toolbar 'Sibling' item, for ability to navigate through current row's siblings
     * within the currently available rows scope, using combobox
     *
     * @return {Object}
     */
    panelDockedInner$Sibling: function() {
        // Setup auxilliary variables
        var me = this, row = me.ti().row, field = row.view('sibling').field;

        // 'Sibling' item config
        return {
            id: me.panelDockedInnerBid() + 'sibling',
            name: 'sibling',
            xtype: 'combo.sibling',
            tooltip: Indi.lang.I_NAVTO_SIBLING,
            disabled: !parseInt(me.ti().scope.found) || (parseInt(me.ti().scope.found) == 1 && me.ti().row.id),
            field: field,
            value: Ext.isNumeric(row.id) ? parseInt(row.id) : row.id,
            subTplData: row.view(field.alias).subTplData,
            store: row.view(field.alias).store,
            listeners: {
                change: function(cmb, value) {

                    // If value is a non-zero integer
                    if (parseInt(value)) {

                        // Show mask
                        if (!me.noGoto) me.getMask().show();

                        // Build the request uri and setup save button shortcut
                        var url = '/' + me.ti().section.alias + '/' + me.ti().action.alias + '/id/' +
                            value + '/ph/'+ me.ti().section.primaryHash + '/';

                        // If value was selected without combo lookup usage
                        if (cmb.infoEl.attr('fetch-mode') == 'no-keyword') {

                            // Get the index
                            var index = cmb.count() < cmb.found()
                                ? (me.ti().scope.aix ? parseInt(me.ti().scope.aix) : 1)
                                - 1 + parseInt(cmb.keywordEl.attr('selectedIndex'))
                                - cmb.store.fetchedByPageUps
                                : cmb.keywordEl.attr('selectedIndex');

                            // Append index to the url
                            url += 'aix/' + index + '/';

                            // Setup shortcuts for other navigation components
                            var tfID = Ext.getCmp(me.panelDockedInnerBid() + 'id'),
                                tfOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset'),
                                btnPrev = Ext.getCmp(me.panelDockedInnerBid() + 'prev'),
                                btnNext = Ext.getCmp(me.panelDockedInnerBid() + 'next');

                            // Apply state where it can be applied
                            if (tfID) tfID.setRawValue(value);
                            if (tfOffset) tfOffset.setRawValue(index);
                            if (btnNext) btnNext.setDisabled(index == me.ti().scope.found);
                            if (btnPrev) btnPrev.setDisabled(index == 1);
                        }

                        // Goto url
                        me.goto(url, undefined, {
                            title: cmb.r(value).title
                        });
                    }
                }
            }
        }
    },

    /**
     * Master toolbar 'Next' item, for ability to navigate to next row
     * within the currently available rows scope
     *
     * @return {Object}
     */
    panelDockedInner$Next: function() {
        var me = this, enabled = parseInt(me.ti().scope.found) && ((parseInt(me.ti().scope.aix) &&
            parseInt(me.ti().scope.aix) < parseInt(me.ti().scope.found)) || !parseInt(me.ti().scope.aix));

        // 'Prev' item config
        return {
            id: me.panelDockedInnerBid() + 'next',
            disabled: !enabled,
            tooltip: Indi.lang.I_NAVTO_NEXT,
            iconCls: Ext.baseCSSPrefix + 'tbar-page-next',
            handler: function(){
                var cmbSibling = Ext.getCmp(me.panelDockedInnerBid() + 'sibling'),
                    spnOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset'),
                    btnPrev = Ext.getCmp(me.panelDockedInnerBid() + 'prev'),
                    btnNext = Ext.getCmp(me.panelDockedInnerBid() + 'next'),
                    btnNextEnabled = parseInt(me.ti().scope.found) && parseInt(me.ti().scope.aix)
                        && parseInt(me.ti().scope.aix) + 1 < parseInt(me.ti().scope.found);

                // Show mask
                me.getMask().show();

                // Here we should detect, whether we can use sibling combo for navigation
                // We check several conditions:
                // 1. Sibling combo exists
                // 2. It's running in 'no-keyword' mode
                // 3. Programmatical keyboard DOWN causes actual change
                //    of 'selectedIndex' attribute of combo's `keywordEl` element
                var canUseSibling = false, selectedIndexWas, selectedIndexNow;
                if (cmbSibling && cmbSibling.infoEl.attr('fetch-mode') == 'no-keyword') {

                    // Initiate sibling combo's picker rendering, if it wasn't yet rendered
                    cmbSibling.getPicker();

                    // Get value of 'selectedIndex' attribute before programmatic DOWN key press simulation
                    selectedIndexWas = cmbSibling.keywordEl.attr('selectedIndex');

                    // Do programmatic DOWN key press simulation
                    cmbSibling.keyDownHandler('40', true);

                    // Get value of 'selectedIndex' attribute after programmatic DOWN key press simulation was done
                    selectedIndexNow = cmbSibling.keywordEl.attr('selectedIndex');

                    // Compare values of `selectedIndex` attribute, and if change detected - allow use sibling
                    if (selectedIndexWas != selectedIndexNow) canUseSibling = true;
                }

                // If we have 'Sibling' master toolbar item - use it for navigation
                if (canUseSibling) cmbSibling.keyDownHandler('13', true);

                // Else if we have 'Offset' master toolbar item - use it's spinUp method
                else if (spnOffset) spnOffset.spinUp();

                // Try to navigate to current row's sibling by it's offset
                else me.gotoOffset((parseInt(me.ti().scope.aix) ? parseInt(me.ti().scope.aix) : 0)+ 1);

                // Disable 'Next' master toolbar item if needed
                btnNext.setDisabled(!btnNextEnabled);

                // Enable 'Prev' master toolbar item, if it exists
                if (btnPrev && !isNaN(parseInt(me.ti().scope.aix))) btnPrev.enable();
            }
        }
    },

    /**
     * Master toolbar 'Nested' item, for ability to navigate to current row's nested entries lists
     *
     * @return {Object}
     */
    panelDockedInner$Nested: function() {
        var me = this, btnSave = Ext.getCmp(me.panelDockedInnerBid() + 'save');

        // 'Nested' item config
        return {
            id: me.panelDockedInnerBid() + 'nested',
            xtype: 'shrinklist',
            displayField: 'title',
            hidden: !me.ti().sections.length,
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
                afterrender: function(cmp) {
                    var btnSave = Ext.getCmp(me.panelDockedInnerBid() + 'save');
                    cmp.setDisabled(!me.ti().row.id && btnSave && !btnSave.pressed);
                },
                itemclick: function(cmp, row) {
                    me.goto('/' + row.get('alias') + '/index/id/'+ me.ti().row.id
                        +'/ph/'+ me.ti().scope.hash + '/aix/'+ me.ti().scope.aix +'/');
                }
            }
        }
    },

    /**
     * Create row panel mask
     *
     * @return {*}
     */
    createMask: function() {
        return this.rowMask = new Ext.LoadMask(this.panel.items[0].id);
    },

    /**
     * Row panel mask getter. If mask is not yet exists - it'll be created
     *
     * @return {*}
     */
    getMask: function() {
        return this.rowMask ? this.rowMask : this.createMask();
    },

    /**
     * Loads the url, given in `url` arg. This function is useful then there is a need to wrap
     * reload process with some custom clauses/operations/etc. And such a need is already exists
     * in current class '*.Form' subsclass, e.g. Indi.Controller.Action.Row.Form
     *
     * @param url
     */
    goto: function(url, btnSaveClick, cfg) {
        if (!this.noGoto) Indi.load(url, cfg);
    },

    /**
     * Function is used when id-by-offset or offset-by-id detection failed
     *
     * @param {Ext.form.field.Text} input
     */
    onDetectionFailed: function(input) {

        // Declare `smp` variable. SMP - mean Search Params Mention
        var me = this, spm = '',
            kind = (input ? input.id.replace(me.panelDockedInnerBid(), '') : 'offset').toUpperCase();

        // If no `input` argument given, we assume it's a 'Offset' item
        if (!input) input = Ext.getCmp(me.panelDockedInnerBid() + 'offset');

        // Hide mask
        me.getMask().hide();

        // If user was using filters or keyword for browsing the scope of rows,
        // the warning message will contain an indication about that
        if (me.ti().scope.filters != '[]' || (me.ti().scope.keyword && me.ti().scope.keyword.length))
            spm = Indi.lang['I_ACTION_FORM_TOPBAR_NAVTOROW'+kind+'_NOT_FOUND_MSGBOX_MSG_SPM'];

        // Display an Ext message box
        Ext.MessageBox.show({
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.WARNING,
            title: Indi.lang['I_ACTION_FORM_TOPBAR_NAVTOROW'+kind+'_NOT_FOUND_MSGBOX_TITLE'],
            msg: Indi.lang['I_ACTION_FORM_TOPBAR_NAVTOROW'+kind+'_NOT_FOUND_MSGBOX_MSG_START'] +
                spm + Indi.lang['I_ACTION_FORM_TOPBAR_NAVTOROW'+kind+'_NOT_FOUND_MSGBOX_MSG_END'],

            // After OK button was pressed, we restore the last valid value
            fn: function(){
                if (input) input.setValue(input.lastValidValue);
            }
        });
    },

    /**
     * Function is used when user changed the value of 'Offset' master toolbar item's component,
     * or (in certain cases) pressed 'Prev' or 'Next' items buttons
     *
     * @param offset
     * @param input
     */
    gotoOffset: function(offset, input) {

        // Build the request uri
        var me = this, url = '/' + me.ti().section.alias + '/' + me.ti().action.alias + '/aix/' +
                offset + '/ph/'+ me.ti().section.primaryHash+'/',
            spnOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset');

        // Set temporary value of 'Offset' spinner
        if (spnOffset) spnOffset.setRawValue(offset);

        // We should ensure that row that user wants to retrieve
        // - is exists within a current section scope
        Ext.Ajax.request({
            url: Indi.pre.replace(/\/$/, '') + url + 'check/1/',
            success: function(response){

                // Convert response to json object
                var json = response.responseText.json(), rowId;

                // If row id was successfully detected,
                // append 'id' param to the url, and load that url
                if (Ext.isObject(json) && !isNaN(rowId = parseInt(json.id)))
                    me.goto(url.replace(/(\/aix\/[0-9]+\/)/, '/id/' + rowId+ '$1'), undefined, {
                        title: json.title
                    });

                // Otherwise we build an warning message, and display Ext.MessageBox
                else me.onDetectionFailed(input);
            },
            failure: function() {
                me.getMask().hide();
            }
        });
    },

    /**
     * Default config for south region panel items
     *
     * @param src
     * @return {Object}
     */
    southItemIDefault: function(src) {
        var me = this, scope = me.ti().scope, id = 'i-section-' + src.alias + '-action-index-parentrow-' + me.ti().row.id + '-wrapper';

        // Config
        return {
            xtype: 'panel',
            isSouthItem: true,
            id: me.id + '-tab$' + src.alias,
            title: src.title,
            name: src.alias,
            border: 0,
            bodyStyle: {
                display: 'table-cell',
                'vertical-align': 'middle',
                'background-color': 'rgb(220, 220, 220)'
            },
            layout: 'fit',
            items: [{
                xtype: 'actiontabrowset',
                id: id,
                load: '/' + src.alias + '/index/id/' + me.ti().row.id + '/ph/' + scope.hash + '/aix/' + scope.aix + '/',
                name: src.alias
            }]
        }
    },

    /**
     * Build and return the array of components configs, that will be used as inner items within south region panel
     *
     * @return {Array}
     */
    southItemA: function() {
        var me = this, srcA = me.ti().sections, itemA = [], itemI, item$, eItem$, i;

        // Foreach item within srcA
        for (i = 0; i < srcA.length; i++) {

            // Get item default config
            itemI = me.southItemIDefault(srcA[i]);

            // Apply item custom config
            eItem$ = 'southItem$' + Indi.ucfirst(srcA[i].alias);
            if (Ext.isFunction(me[eItem$]) || Ext.isObject(me[eItem$])) {
                item$ = Ext.isFunction(me[eItem$]) ? me[eItem$](itemI, srcA[i]) : me[eItem$];
                itemI = Ext.isObject(item$) ? Ext.merge(itemI, item$) : item$;
            } else if (Ext.isDefined(me[eItem$])) {
                itemI = null;
                continue;
            }

            // Add item
            if (itemI) itemA.push(itemI);
        }

        // Return
        return itemA;
    },

    /**
     * Builds and return an array of panels, that will be used to represent the major UI contents.
     * Currently is consists from this.row (form panel configuration) and from this.south
     * (if has non-zero-length `items` property)
     *
     * @return {Array}
     */
    panelItemA: function() {

        // Panels array
        var me = this, itemA = [], rowItem = me.row, southItem = me.south;

        // Append row (center region) panel
        if (rowItem) itemA.push(rowItem);

        // Append tab (south region) panel only if it's consistent
        if (me.panel.xtype != 'actiontabrow' && southItem && (southItem.items = me.southItemA()).length && me.ti().row.id) {
            if (me.ti().scope.actionrow && me.ti().scope.actionrow.south) {
                if (me.ti().scope.actionrow.south.height == 25) southItem.height = 25;
                southItem.activeTab = me.ti().sections.column('alias').indexOf(me.ti().scope.actionrow.south.activeTab);
            }

            // Push `southItem` into `itemA` array
            itemA.push(southItem);
        }

        // Return panels array
        return itemA;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this, exst = Ext.getCmp(me.panel.id);

        // Setup row panel
        me.row = Ext.merge({
            id: me.id + '-row',
            items: me.rowItemA(),
            dockedItems: me.rowDockedA()
        }, me.row);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // If such a panel is already exists within a tab - close it
        if (exst && exst.xtype == 'actiontabrow') exst.up('[isSouthItem]').close();

        // Call parent
        me.callParent();

        // Attach key map
        me.keyMap();
    },

    /**
     * Builder for row-panel items
     *
     * @return {Array}
     */
    rowItemA: function() {
        return [];
    },

    /**
     * Key map for row-panel body
     */
    keyMap: function() {
        var me = this;

        // Setup a focus on a row panel
        Ext.getCmp(me.row.id).focus();

        // Attach key map on a row panel
        if (Ext.getCmp(me.row.id).rendered) Ext.getCmp(me.row.id).getEl().addKeyMap({
            eventName: 'keydown',
            binding: [{
                key: Ext.EventObject.R,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$reload'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.F5,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$reload'); if (btn) btn.press();
                },
                scope: me
            }, {
                key: Ext.EventObject.RIGHT,
                alt: true,
                fn:  function(keyCode, e){
                    e.stopEvent();
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$next'); if (btn) btn.press();
                },
                scope: me
            }, {
                key: Ext.EventObject.LEFT,
                alt: true,
                fn:  function(keyCode, e){
                    e.stopEvent();
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$prev'); if (btn) btn.press();
                },
                scope: me
            }, {
                key: Ext.EventObject.BACKSPACE,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$back'); if (btn) btn.press();
                },
                scope: me
            }, {
                key: Ext.EventObject.F10,
                fn:  function(keyCode, e){
                    e.stopEvent();
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$back'); if (btn) btn.press();
                },
                scope: me
            }]
        });

        // Batch-attach key-map, for ability to navigate to subsections via keyboard
        me.setupSubsectionsAccessKeys(me.row.id);
    },

    /**
     * Build the uri of another action for same section and row, that can be used with `goto`
     * function, for easier development of navigation from-one-action-to-another ability
     *
     * @param action
     * @return {String}
     */
    other: function(action) {
        var me = this; return me.uri.replace(
            '/' + me.ti().section.alias + '/' + me.ti().action.alias + '/',
            '/' + me.ti().section.alias + '/' + action + '/'
        );
    },

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Setup `route` property
        if (config.route) me.route = config.route;

        // Setup main panel title as current secion title
        me.panel.title = me.ti().row.id ? me.ti().row.title : Indi.lang.I_CREATE;

        // Merge configs
        me.mergeParent(config);

        // Call parent
        me.callParent(arguments);
    }
});

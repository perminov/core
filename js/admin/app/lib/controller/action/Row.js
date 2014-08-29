/**
 * Base class for all controller actions instances, that operate with some certain rows
 */
Ext.define('Indi.lib.controller.action.Row', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Row',

    // @inheritdoc
    extend: 'Indi.Controller.Action',

    // @inheritdoc
    mcopwso: ['row', 'panel'],

    // @inheritdoc
    panel: {
        title: '',
        closable: false,
        listeners: {
            afterrender: function(me){
                Indi.trail(true).breadCrumbs();
            }
        },

        /**
         * Here we separate docked items on items-level1 and items-level2, e.g items-level1 are
         * panels/toolbars definitions, and items-level2 are child items within these panels/toolbars.
         * The `alias` property is a special property, in some sense similar to extjs 'xtype' property.
         * `alias` property in some sense is the key that connecting panels/toolbars with their items collections.
         * In 99.99% cases, when `alias` property faced here - it means that there is exist a special function
         * that is responsible for building fully/partially configuration object for such item
         */
        docked: {
            items: [{alias: 'master'}],
            inner: {
                master: [
                    {alias: 'back'}, '-',
                    {alias: 'ID'}, '-',
                    {alias: 'prev'}, {alias: 'next'}, '-',
                    {alias: 'create'}, '-',
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
        border: 0
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
     * Build panel master toolbar items array
     *
     * @return {Array}
     */
    panelDocked$MasterItemA: function() {
        return this.push(this.panel.docked.inner['master'], 'panelDockedInner', true);
    },

    /**
     * Master toolbar 'Back' button
     *
     * @return {Object}
     */
    panelDockedInner$Back: function(urlonly) {

        // Build the url for goto
        var me = this, url = me.ti().section.href;
        if (me.ti(1).row) url += 'index/id/' + me.ti(1).row.id + '/' +
            (me.ti().scope.upperHash ? 'ph/'+me.ti().scope.upperHash+'/' : '') +
            (me.ti().scope.upperAix ? 'aix/'+me.ti().scope.upperAix+'/' : '');

        // Return builded url, if `geturl` arg is given, or return 'Back' button config otherwise
        return urlonly ? url : {
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
        }
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
            id: me.panelDockedInnerBid()+'-id',
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
            disabled: parseInt(me.ti().scope.found) > 1 ? false : true,
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
                            var url = me.ti().section.href + me.ti().action.alias + '/id/' +
                                input.getValue() + '/ph/'+ me.ti().section.primaryHash+'/';

                            // We should ensure that row that user wants to retrieve
                            // - is exists within a current section scope
                            Ext.Ajax.request({
                                url: url + 'check/1/',
                                params: {forceOffsetDetection: true},
                                success: function(response){

                                    // Get the result of row offset detection from the response
                                    var aix = response.responseText.match(/^[0-9]+$/)
                                        ? parseInt(response.responseText)
                                        : false;

                                    // If offset was successfully detected,
                                    // append 'aix' param to the url, and load that url
                                    if (aix) me.goto(url += 'aix/' + aix + '/');

                                    // Otherwise we build an warning message, and display Ext.MessageBox
                                    else me.onDetectionFailed(me, input);
                                }
                            });
                        }
                    }, 500, input);
                }
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
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE) + 1,
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
            handler: function(btnPrev){
                var cmbSibling = Ext.getCmp(me.panelDockedInnerBid() + 'sibling'),
                    spnOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset'),
                    btnNext = Ext.getCmp(me.panelDockedInnerBid() + 'next'),
                    btnPrevEnabled = parseInt(me.ti().scope.found) && parseInt(me.ti().scope.aix)
                        && parseInt(me.ti().scope.aix) - 1 > 1;

                // Show mask
                me.getMask().show();

                if (false && cmbSibling && typeof me.ti().row.title != 'undefined') {
                    cmbSibling.keyDownHandler('38', true);
                    cmbSibling.keyDownHandler('13', true);

                // Else if we have 'Offset' master toolbar item - use it's spinDown method
                } else if (spnOffset) spnOffset.spinDown();

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
            handler: function(btnNext){
                var cmbSibling = Ext.getCmp(me.panelDockedInnerBid() + 'sibling'),
                    spnOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset'),
                    btnPrev = Ext.getCmp(me.panelDockedInnerBid() + 'prev'),
                    btnNextEnabled = parseInt(me.ti().scope.found) && parseInt(me.ti().scope.aix)
                        && parseInt(me.ti().scope.aix) + 1 < parseInt(me.ti().scope.found);

                // Show mask
                me.getMask().show();

                if (false && cmbSibling && typeof me.ti().row.title != 'undefined') {
                    cmbSibling.keyDownHandler('40', true);
                    cmbSibling.keyDownHandler('13', true);

                // Else if we have 'Offset' master toolbar item - use it's spinUp method
                } else if (spnOffset) spnOffset.spinUp();

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
    goto: function(url) {
        Indi.load(url);
    },

    /**
     * Function is used when id-by-offset or offset-by-id detection failed
     *
     * @param {Ext.form.field.Text} input
     */
    onDetectionFailed: function(input) {

        // Declare `smp` variable. SMP - mean Search Params Mention
        var me = this, spm,
            kind = (input ? input.id.replace(me.panelDockedInnerBid() +'-', '') : 'offset').toUpperCase();

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
        var me = this, url = me.ti().section.href + me.ti().action.alias + '/aix/' +
                offset + '/ph/'+ me.ti().section.primaryHash+'/',
            spnOffset = Ext.getCmp(me.panelDockedInnerBid() + 'offset');

        // Set temporary value of 'Offset' spinner
        if (spnOffset) spnOffset.setRawValue(offset);

        // We should ensure that row that user wants to retrieve
        // - is exists within a current section scope
        Ext.Ajax.request({
            url: url + 'check/1/',
            success: function(response){

                // Get the result of row id detection from the response
                var rowId = response.responseText.match(/^[0-9]+$/)
                    ? parseInt(response.responseText)
                    : false;

                // If row id was successfully detected,
                // append 'id' param to the url, and load that url
                if (rowId) me.goto(url.replace(/(\/aix\/[0-9]+\/)/, '/id/' + rowId+ '$1'));

                // Otherwise we build an warning message, and display Ext.MessageBox
                else me.onDetectionFailed(input);
            }
        });
    }
});

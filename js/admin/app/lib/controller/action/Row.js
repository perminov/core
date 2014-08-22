/**
 * Base class for all controller actions instances, that operate with some certain rows
 */
Ext.define('Indi.lib.controller.action.Row', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Row',

    // @inheritdoc
    extend: 'Indi.Controller.Action',

    // @inheritdoc
    mcopwso: ['row'],

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
         * Master toolbar config
         */
        toolbarMaster: {

            /**
             * Possible value of properties, or of properties's `mode` property, in case if properties are objects
             *
             * 1 - item only
             * 2 - item with tbseparator
             * 3 - item with tbfill
             * 4 - item with tbspacer
             */
            items: {
                back: 2,
                ID: {
                    mode: 2,
                    width: function(row, mode) {
                        var labelWidth = 20, inputWidth = 30;

                        if (row.id) {
                            inputWidth = row.id.toString().length * 9;
                            inputWidth = inputWidth > 30 ? inputWidth : 30;
                        }

                        return mode == 'tooltipOffset'
                            ? labelWidth + inputWidth/2 - (labelWidth + inputWidth)/2
                            : labelWidth + inputWidth;
                    }
                }
                /*offset: {
                    mode: 1,
                    width: function(found, mode) {
                        var labelWidth = Indi.metrics.getWidth(Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE),
                            triggerWidth = 20, inputWidth = (found.toString().length + 1) * 7 + 2;

                        inputWidth = inputWidth > 30 ? inputWidth : 30;

                        return mode == 'tooltipOffset'
                            ? labelWidth + 5 + inputWidth/2 - (labelWidth + 5 + inputWidth + triggerWidth)/2
                            : labelWidth + inputWidth + triggerWidth;
                    }
                }*/
            },

            /**
             * Function is used when id-by-offset or offset-by-id detection failed
             *
             * @param {Ext.form.field.Text} input
             */
            onDetectionFailed: function(input) {

                // Declare `smp` variable. SMP - mean Search Params Mention
                var me = this, spm,
                    kind = (input ? input.id.replace(me.panelToolbarMasterId() +'-', '') : 'offset').toUpperCase();

                // If no `input` argument given, we assume it's a 'Offset' item
                if (!input) input = Ext.getCmp(me.panelToolbarMasterId() + '-offset');

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
                    spnOffset = Ext.getCmp(me.panelToolbarMasterId() + '-offset');

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
                        else me.panel.toolbarMaster.onDetectionFailed.call(me, input);
                    }
                });
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
     * Panel toolbars array builder
     *
     * @return {Array}
     */
    panelToolbarA: function() {

        // Toolbars array
        var toolbarA = [], toolbarMaster = this.panelToolbarMaster();

        // Append master toolbar
        if (toolbarMaster) toolbarA.push(toolbarMaster);

        // Return toolbars array
        return toolbarA;
    },

    /**
     * Panel master toolbar builder
     *
     * @return {Object}
     */
    panelToolbarMaster: function() {
        return {
            id: this.panelToolbarMasterId(),
            xtype: 'toolbar',
            dock: 'top',
            style: {
                paddingRight: '3px'
            },
            items: this.panelToolbarMasterItemA()
        }
    },

    /**
     * Panel master toolbar id constructor
     *
     * @return {String}
     */
    panelToolbarMasterId: function() {
        return this.bid() + '-toolbar-master';
    },

    /**
     * Build panel master toolbar items array
     *
     * @return {Array}
     */
    panelToolbarMasterItemA: function() {

        // Setup auxilliary variables
        var me = this, itemA = [], kind, itemI, fnItemI, kindO = {2: '-', 3: '->', 4: ' '},
            itemO = me.panel.toolbarMaster.items;

        // Build itemA array
        for (var i in itemO) {
            kind = parseInt(Ext.isObject(itemO[i]) ? itemO[i].mode : itemO[i]);
            if (kind) {
                fnItemI = 'panelToolbarMasterItem$' + Indi.ucfirst(i);
                if (typeof me[fnItemI] == 'function') {
                    itemI = me[fnItemI]();
                    if (itemI) {
                        itemA.push(itemI);
                        if (kind >= 2) itemA.push(kindO[kind]);
                    }
                }
            }
        }

        // Return master toolbar items array
        return itemA;
    },

    /**
     * Master toolbar 'Back' button
     *
     * @return {Object}
     */
    panelToolbarMasterItem$Back: function(urlonly) {

        // Build the url for goto
        var me = this, url = me.ti().section.href;
        if (me.ti(1).row) url += 'index/id/' + me.ti(1).row.id + '/' +
            (me.ti().scope.upperHash ? 'ph/'+me.ti().scope.upperHash+'/' : '') +
            (me.ti().scope.upperAix ? 'aix/'+me.ti().scope.upperAix+'/' : '');

        // Return builded url, if `geturl` arg is given, or return 'Back' button config otherwise
        return urlonly ? url : {
            id: me.panelToolbarMasterId() + '-back',
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
    panelToolbarMasterItem$ID: function() {
        var me = this;

        // ID field config
        return {
            id: me.panelToolbarMasterId()+'-id',
            fieldLabel: 'ID',
            labelWidth: 15,
            xtype: 'numberfield',
            hideTrigger: true,
            tooltip: {
                html: Indi.lang.I_NAVTO_ID,
                staticOffset: [me.panel.toolbarMaster.items.ID.width(me.ti().row, 'tooltipOffset'), 1]
            },
            value: me.ti().row.id,
            width: me.panel.toolbarMaster.items.ID.width(me.ti().row),
            lastValidValue: me.ti().row.id,
            margin: '0 3 0 3',
            disabled: parseInt(me.ti().scope.found) ? false : true,
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
                                    else me.panel.toolbarMaster.onDetectionFailed.call(me, input);
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
    panelToolbarMasterItem$Found: function() {
        var me = this;

        // 'Found' field config
        return {
            id: me.panelToolbarMasterId() + '-found',
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
    panelToolbarMasterItem$Offset: function() {
        var me = this;

        // 'Offset' field config
        return {
            id: me.panelToolbarMasterId() + '-offset',
            fieldLabel: Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE,
            labelSeparator: '',
            labelWidth: Indi.metrics.getWidth(Indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWOFFSET_TITLE) + 1,
            xtype: 'numberfield',
            tooltip: {
                html: Indi.lang.I_NAVTO_ROWINDEX,
                staticOffset: [me.panel.toolbarMaster.items.offset.width(me.ti().scope.found, 'tooltipOffset'), 0]
            },
            value: (me.ti().row.id ? me.ti().scope.aix : ''),
            width: me.panel.toolbarMaster.items.offset.width(me.ti().scope.found) + 1,
            disabled: parseInt(me.ti().scope.found) ? false : true,
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
                            var btnPrev = Ext.getCmp(me.panelToolbarMasterId() + '-prev'),
                                btnNext = Ext.getCmp(me.panelToolbarMasterId() + '-next');

                            // Disable/enable them if needed
                            if (btnPrev) btnPrev.setDisabled(input.getValue() == input.minValue);
                            if (btnNext) btnNext.setDisabled(input.getValue() == input.maxValue);

                            // Try to navigate to current row's sibling by it's offset
                            me.panel.toolbarMaster.gotoOffset.call(me, input.getValue(), input);
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
    panelToolbarMasterItem$Prev: function() {
        var me = this, enabled = parseInt(me.ti().scope.found) && parseInt(me.ti().scope.aix)
            && parseInt(me.ti().scope.aix) > 1;

        // 'Prev' field config
        return {
            id: me.panelToolbarMasterId() + '-prev',
            disabled: !enabled,
            tooltip: Indi.lang.I_NAVTO_PREV,
            iconCls: Ext.baseCSSPrefix + 'tbar-page-prev',
            handler: function(btnPrev){
                var cmbSibling = Ext.getCmp(me.panelToolbarMasterId() + '-sibling'),
                    spnOffset = Ext.getCmp(me.panelToolbarMasterId() + '-offset'),
                    btnNext = Ext.getCmp(me.panelToolbarMasterId() + '-next'),
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
                else me.panel.toolbarMaster.gotoOffset.call(me, me.ti().scope.aix - 1);

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
    panelToolbarMasterItem$Next: function() {
        var me = this, enabled = parseInt(me.ti().scope.found) && ((parseInt(me.ti().scope.aix) &&
            parseInt(me.ti().scope.aix) < parseInt(me.ti().scope.found)) || !parseInt(me.ti().scope.aix));

        // 'Prev' item config
        return {
            id: me.panelToolbarMasterId() + '-next',
            disabled: !enabled,
            tooltip: Indi.lang.I_NAVTO_NEXT,
            iconCls: Ext.baseCSSPrefix + 'tbar-page-next',
            handler: function(btnNext){
                var cmbSibling = Ext.getCmp(me.panelToolbarMasterId() + '-sibling'),
                    spnOffset = Ext.getCmp(me.panelToolbarMasterId() + '-offset'),
                    btnPrev = Ext.getCmp(me.panelToolbarMasterId() + '-prev'),
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
                else me.panel.toolbarMaster.gotoOffset.call(me, (parseInt(me.ti().scope.aix) ? parseInt(me.ti().scope.aix) : 0)+ 1);

                // Disable 'Next' master toolbar item if needed
                btnNext.setDisabled(!btnNextEnabled);

                // Enable 'Prev' master toolbar item, if it exists
                if (btnPrev && !isNaN(parseInt(me.ti().scope.aix))) btnPrev.enable();
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
    }
});

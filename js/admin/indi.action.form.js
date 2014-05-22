var Indi = (function (indi) {
    "use strict";
    var process = function () {
        /**
         * Setup empty indi.proto.action object
         *
         * @type {Object}
         */
        indi.proto.action = {};

        /**
         * Setup `filter` property of indi.proto.combo object
         */
        indi.proto.action.form = function(){

            /**
             * This is for context stabilization
             *
             * @type {*}
             */
            var instance = this;

            /**
             * This will be used at the stage of request uri constructing while within remoteFetch()
             * and also, is used to get a proper stack of callbacks that should be called in run()
             *
             * @type {String}
             */
            this.componentName = 'action.form';

            /**
             * Configuration
             *
             * @type {Object}
             */
            this.options = {
                hideTopbar: false
            }

            this.getPanel = function() {
                return top.window.Ext.getCmp('i-center-center-wrapper');
            }

            /**
             * Get the <iframe> tag
             *
             * @return {*}
             */
            this.getIframe = function() {
                return top.window.$('iframe[name="form-frame"]');
            }

            /**
             * Get the <iframe> dom element
             *
             * @return {*}
             */
            this.getIframeContext = function() {
                return top.frames['form-frame'];
            }

            /**
             * Object, that contains a functions for calculating widhts of some topbar elements
             *
             * @type {Object}
             */
            this.widths = {
                topbar : {
                    // Navigate-by-ID field
                    ID: function(row) {
                        var labelWidth = 20, inputWidth = 30;
                        if (row.id) {
                            inputWidth = row.id.toString().length * 9;
                            inputWidth = inputWidth > 30 ? inputWidth : 30;
                        }
                        return labelWidth + inputWidth;
                    },
                    // Navigate-by-row-number field
                    RN: function(row) {
                        var labelWidth = indi.metrics.getWidth(indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_TITLE),
                            triggerWidth = 20, inputWidth;
                        inputWidth = (indi.trail.item().scope.found.toString().length + 1) * 7 + 2;
                        inputWidth = inputWidth > 30 ? inputWidth : 30;
                        return labelWidth + inputWidth + triggerWidth;
                    }
                }
            }

            /**
             * Build the top toolbar for form action
             */
            this.applyTopToolbar = function(config) {

                // If there is a some custom implementation, return
                if (!instance.getPanel()) return;

                // Remove old toolbarr
                var formPanelTopbar = instance.getPanel().getDockedComponent('i-action-form-topbar');
                if (formPanelTopbar) instance.getPanel().removeDocked(formPanelTopbar);

                // Declare an array for docked items
                var dockedItems = [];

                // Adding button for returning to the grid
                dockedItems.push({
                    text: '',
                    handler: function(){
                        top.window.Ext.getCmp('iframe-mask').show();
                        top.window.Indi.load(
                            Indi.pre +
                                '/' + indi.trail.item().section.alias +
                                '/' + (indi.trail.item(1).row
                                      ?
                                      'index/id/' + indi.trail.item(1).row.id + '/' +
                                      (indi.trail.item().scope.upperHash ? 'ph/'+indi.trail.item().scope.upperHash+'/' : '') +
                                      (indi.trail.item().scope.upperAix ? 'aix/'+indi.trail.item().scope.upperAix+'/' : '')
                                      :
                                      '')
                        )
                    },
                    iconCls: 'i-btn-icon-back',
                    id: 'i-action-form-topbar-button-back'
                });

                // Add a separator
                dockedItems.push('-');

                // Add an input for navigate-to-id feature
                dockedItems.push({

                    // Configuration
                    fieldLabel: 'ID',
                    labelWidth: 15,
                    xtype: 'numberfield',
                    hideTrigger: true,
                    value: (indi.trail.item().row ? indi.trail.item().row.id : ''),
                    width: instance.widths.topbar.ID(indi.trail.item().row),
                    lastValidValue: (indi.trail.item().row ? indi.trail.item().row.id : ''),
                    margin: '0 3 0 3',
                    disabled: parseInt(indi.trail.item().scope.found) ? false : true,
                    cls: 'i-form-text',
                    errorMsgCls: '',
                    minValue: 1,
                    id: 'i-action-form-topbar-nav-to-row-id',
                    listeners: {

                        // Change hander
                        change: function(input){

                            // We provide a reload ability only after user finished typing in ID field
                            if (input.changeTimeout) clearTimeout(input.changeTimeout);
                            input.changeTimeout = setTimeout(function(input){

                                // If field's value is not empty, and value is not the same as last valid value
                                if (input.getValue() && input.getValue() != input.lastValidValue) {

                                    top.window.Ext.getCmp('iframe-mask').show();

                                    var existingIframeQueryString = '?' + instance.getIframe().attr('src').split('?')[1], url;

                                    // Build the request uri
                                    var url = indi.pre+'/' + indi.trail.item().section.alias + '/' + indi.trail.item().action.alias + '/id/' +
                                        input.getValue() + '/ph/'+ indi.trail.item().section.primaryHash+'/';

                                    var data = {
                                        forceOffsetDetection: true
                                    }

                                    // We should ensure that row that user wants to retrieve - is exists within a current
                                    // section scope
                                    $.post(url + 'check/1/', data, function(response){

                                        var aix = response.match(/^[0-9]+$/) ? parseInt(response) : false;

                                        // If exists, we replace the iframe's src attribute with new one
                                        if (aix) {

                                            // If save button is toggled
                                            if (top.window.Ext.getCmp('i-action-form-topbar-button-save').pressed)

                                                // We save current row but remeber the redirect url
                                                $('form[name='+indi.trail.item().model.tableName+']')
                                                    .append('<input type="hidden" name="redirect-url" value="'+url + 'aix/' + aix + '/' + existingIframeQueryString+'"/>')
                                                    .submit();

                                            // Else we just update iframe's src
                                            else instance.getIframe().attr('src', url + 'aix/' + aix + '/' + existingIframeQueryString);

                                        // Otherwise we build an warning message, and display Ext.MessageBox
                                        } else {

                                            top.window.Ext.getCmp('iframe-mask').hide();

                                            // Declare `smp` variable. SMP - mean Search Params Mention
                                            var spm = '';

                                            // If user was using filters or keyword for browsing the scope of rows,
                                            // the warning message will contain an indication about that
                                            if (indi.trail.item().scope.filters != '[]' || (indi.trail.item().scope.keyword && indi.trail.item().scope.keyword.length))
                                                spm = indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_SPM;

                                            // Display an Ext message box
                                            //top.window.Ext.getCmp('iframe-mask').hide();
                                            Ext.MessageBox.show({
                                                title: indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_TITLE,
                                                msg: indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_START +
                                                    spm + indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWID_NOT_FOUND_MSGBOX_MSG_END,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.WARNING,

                                                // After OK button was pressed, we restore the last valid value
                                                fn: function(){
                                                    input.setValue(input.lastValidValue);
                                                }
                                            });
                                        }
                                    });
                                }
                            }, 500, input);
                        }
                    }
                })

                // Add a separator
                dockedItems.push('-');

                // Here we check if 'save' action is in the list of allowed actions
                indi.trail.item().disableSave = true;
                for (var i = 0; i < indi.trail.item().actions.length; i++)
                    if (indi.trail.item().actions[i].alias == 'save')
                        indi.trail.item().disableSave = false;

                // 'Save' button
                dockedItems.push({
                    xtype: 'splitbutton',
                    text: indi.lang.I_SAVE,
                    handler: function(){

                        top.window.Ext.getCmp('iframe-mask').show();

                        var url = Indi.pre +
                            '/' + indi.trail.item().section.alias +
                            '/' + (indi.trail.item(1).row
                            ?
                            'index/id/' + indi.trail.item(1).row.id + '/' +
                                (indi.trail.item().scope.upperHash ? 'ph/'+indi.trail.item().scope.upperHash+'/' : '') +
                                (indi.trail.item().scope.upperAix ? 'aix/'+indi.trail.item().scope.upperAix+'/' : '')
                            :
                            '');

                        // We save current row but remeber the redirect url
                        $('form[name='+indi.trail.item().model.tableName+']')
                            .append('<input type="hidden" name="redirect-url" value="'+url+'"/>')
                            .submit();

                    },
                    disabled: indi.trail.item().disableSave,
                    iconCls: 'i-btn-icon-save',
                    id: 'i-action-form-topbar-button-save',
                    pressed: indi.trail.item().scope.toggledSave,
                    arrowHandler: function(button, event){
                        button.toggle();
                        if (indi.trail.item().sections.length && !indi.trail.item().row.id) {
                            if (button.pressed) {
                                top.window.Ext.getCmp('i-action-form-topbar-nav-to-subsection').enable();
                            } else {
                                top.window.Ext.getCmp('i-action-form-topbar-nav-to-subsection').disable();
                            }
                        }
                    }
                });

                // Add a separator
                dockedItems.push('-');

                // 'Prev' button
                dockedItems.push({
                    text: '&nbsp;&nbsp;',
                    id: 'i-action-form-topbar-nav-to-sibling-prev',
                    disabled: parseInt(indi.trail.item().scope.found) && parseInt(indi.trail.item().scope.aix) && parseInt(indi.trail.item().scope.aix) > 1 ? false : true,
                    handler: function(btn){
                        top.window.Ext.getCmp('iframe-mask').show();
                        if (typeof indi.trail.item().row.title != 'undefined') {
                            top.window.Indi.combo.sibling.keyDownHandler('i-action-form-topbar-nav-to-sibling-id', '38', true);
                            top.window.Indi.combo.sibling.keyDownHandler('i-action-form-topbar-nav-to-sibling-id', '13', true);
                        } else {
                            top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').spinDown();
                        }

                        if(parseInt(indi.trail.item().scope.found) && parseInt(indi.trail.item().scope.aix) && parseInt(indi.trail.item().scope.aix) - 1 > 1) {
                            btn.enable();
                        } else {
                            btn.disable();
                        }
                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').enable();
                    },
                    listeners: {
                        render: function(btn){
                            $(btn.el.dom).find('span.x-btn-inner').addClass('x-tbar-page-prev');
                        }
                    }
                });

                // Combo for search and navigate to any sibling. We append this combo if row have a 'title' property
                if (typeof indi.trail.item().row.title != 'undefined')
                dockedItems.push({
                    xtype: 'component',
                    id: 'i-action-form-topbar-nav-to-sibling',
                    contentEl: jQuery('#i-action-form-topbar-nav-to-sibling-combo-wrapper')[0],
                    setKeywordValue: function(value) {
                        top.window.Indi.combo.sibling.clearCombo('i-action-form-topbar-nav-to-sibling-id');
                    },
                    listeners: {
                        afterrender: function(){
                            top.window.Indi.combo.sibling.run();
                            top.window.Indi.combo.sibling.rebuildComboData('i-action-form-topbar-nav-to-sibling-id');
                            top.window.Indi.combo.sibling.store['i-action-form-topbar-nav-to-sibling-id'].fetchedByPageUps = 0;
                        },
                        change: function(selected){
                            if (parseInt(selected.value)) {
                                top.window.Ext.getCmp('iframe-mask').show();

                                var existingIframeQueryString = '?' + instance.getIframe().attr('src').split('?')[1], url;

                                if (selected.mode == 'no-keyword') {

                                    // Build the request uri
                                    url = indi.pre+'/' + indi.trail.item().section.alias + '/' + indi.trail.item().action.alias+
                                        '/id/' + selected.value +
                                        '/aix/' + selected.index +
                                        '/ph/'+ indi.trail.item().section.primaryHash+'/' +
                                        existingIframeQueryString;

                                    top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').lastValidValue = selected.index;
                                    top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').setValue(selected.index);

                                    top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-id').lastValidValue = selected.value;
                                    top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-id').setValue(selected.value);

                                    if (selected.index == indi.trail.item().scope.found) {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').disable();
                                    } else {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').enable();
                                    }

                                    if (selected.index == 1) {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').disable();
                                    } else {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').enable();
                                    }

                                } else {
                                    // Build the request uri
                                    url = indi.pre+'/' + indi.trail.item().section.alias + '/' + indi.trail.item().action.alias+
                                        ''+
                                        '/id/' + selected.value +
                                        '/ph/'+ indi.trail.item().section.primaryHash+'/' +
                                        existingIframeQueryString;
                                }

                                top.window.$('#i-action-form-topbar-nav-to-sibling-id-suggestions').remove();

                                // If save button is toggled
                                if (top.window.Ext.getCmp('i-action-form-topbar-button-save').pressed)

                                    // We save current row but remeber the redirect url
                                    $('form[name='+indi.trail.item().model.tableName+']')
                                        .append('<input type="hidden" name="redirect-url" value="'+url+'"/>')
                                        .submit();

                                // Else we just update iframe's src
                                else instance.getIframe().attr('src', url);
                            }

                        }
                    }
                });

                // 'Next' button
                dockedItems.push({
                    text: '&nbsp;&nbsp;',
                    id: 'i-action-form-topbar-nav-to-sibling-next',
                    disabled: parseInt(indi.trail.item().scope.found) && ((parseInt(indi.trail.item().scope.aix) && parseInt(indi.trail.item().scope.aix) < parseInt(indi.trail.item().scope.found)) || !parseInt(indi.trail.item().scope.aix)) ? false : true,
                    handler: function(btn){
                        top.window.Ext.getCmp('iframe-mask').show();
                        if(parseInt(indi.trail.item().scope.found) && parseInt(indi.trail.item().scope.aix) && parseInt(indi.trail.item().scope.aix) + 1 < parseInt(indi.trail.item().scope.found)) {
                            btn.enable();
                        } else {
                            btn.disable();
                        }
                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').enable();

                        if (typeof indi.trail.item().row.title != 'undefined') {
                            top.window.Indi.combo.sibling.keyDownHandler('i-action-form-topbar-nav-to-sibling-id', '40', true);
                            top.window.Indi.combo.sibling.keyDownHandler('i-action-form-topbar-nav-to-sibling-id', '13', true);
                        } else {
                            top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').spinUp();
                        }
                    },
                    listeners: {
                        render: function(btn){
                            $(btn.el.dom).find('span.x-btn-inner').addClass('x-tbar-page-next');
                        }
                    }
                });

                // Add a separator
                dockedItems.push('-');

                // Add a separator
                dockedItems.push({
                    iconCls: 'i-btn-icon-create',
                    disabled: parseInt(indi.trail.item().section.disableAdd) || indi.trail.item().disableSave ? true : false,
                    id: 'i-action-form-topbar-button-add',
                    handler: function(){

                        top.window.Ext.getCmp('iframe-mask').show();

                        // Build the request uri
                        var url = indi.pre+'/' + indi.trail.item().section.alias + '/' + indi.trail.item().action.alias + '/' + '/ph/'+
                            indi.trail.item().section.primaryHash+'/';

                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-id').setValue('');
                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').disable();
                        
						if (typeof indi.trail.item().row.title != 'undefined') 
							top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling').setKeywordValue('');
							
                        if (parseInt(indi.trail.item().scope.found)) top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').enable();
                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').setValue('');

                        // If save button is toggled
                        if (top.window.Ext.getCmp('i-action-form-topbar-button-save').pressed)

                        // We save current row but remeber the redirect url
                            $('form[name='+indi.trail.item().model.tableName+']')
                                .append('<input type="hidden" name="redirect-url" value="'+url+'"/>')
                                .submit();

                        // Else we just update iframe's src
                        else instance.getIframe().attr('src', url);
                    }
                });

                // Add a separator
                dockedItems.push('-');

                // Add a separator
                dockedItems.push({
                    fieldLabel: indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_TITLE,
                    labelSeparator: '',
                    labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_TITLE),
                    xtype: 'numberfield',
                    value: (indi.trail.item().row.id ? indi.trail.item().scope.aix : ''),
                    width: instance.widths.topbar.RN(),
                    disabled: parseInt(indi.trail.item().scope.found) ? false : true,
                    margin: '0 5 0 3',
                    cls: 'i-form-text',
                    minValue: 1,
                    maxValue: indi.trail.item().scope.found,
                    validateOnChange: false,
                    lastValidValue: (indi.trail.item().scope.aix ? indi.trail.item().scope.aix : ''),
                    id: 'i-action-form-topbar-nav-to-row-number',
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

                                    top.window.Ext.getCmp('iframe-mask').show();

                                    if (input.getValue() == input.maxValue) {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').disable();
                                    } else {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').enable();
                                    }

                                    if (input.getValue() == input.minValue) {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').disable();
                                    } else {
                                        top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').enable();
                                    }

                                    var existingIframeQueryString = '?' + instance.getIframe().attr('src').split('?')[1];

                                    // Build the request uri
                                    var url = indi.pre+'/' + indi.trail.item().section.alias + '/' + indi.trail.item().action.alias + '/aix/' +
                                        input.getValue() + '/ph/'+ indi.trail.item().section.primaryHash+'/';

                                    // We should ensure that row that user wants to retrieve - is exists within a current
                                    // section scope.
                                    $.post(url + 'check/1/', function(response){

                                        var rowId = response.match(/^[0-9]+$/) ? parseInt(response) : false;

                                        // If exists, we replace the iframe's src attribute with new one
                                        if (rowId) {
                                            url = url.replace(/(\/aix\/[0-9]+\/)/, '/id/' + rowId+ '$1');
                                            url += existingIframeQueryString;

                                            // If save button is toggled
                                            if (top.window.Ext.getCmp('i-action-form-topbar-button-save').pressed)

                                            // We save current row but remeber the redirect url
                                                $('form[name='+indi.trail.item().model.tableName+']')
                                                    .append('<input type="hidden" name="redirect-url" value="'+url+'"/>')
                                                    .submit();

                                            // Else we just update iframe's src
                                            else instance.getIframe().attr('src', url);

                                        // Otherwise we build an warning message, and display Ext.MessageBox
                                        } else {

                                            // Declare `smp` variable. SMP - mean Search Params Mention
                                            var spm = '';

                                            // If user was using filters or keyword for browsing the scope of rows,
                                            // the warning message will contain an indication about that
                                            if (indi.trail.item().scope.filters != '[]' || (indi.trail.item().scope.keyword && indi.trail.item().scope.keyword.length))
                                                spm = indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_SPM;

                                            // Display an Ext message box
                                            Ext.MessageBox.show({
                                                title: indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_TITLE,
                                                msg: indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_START +
                                                    spm + indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_NOT_FOUND_MSGBOX_MSG_END,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.WARNING,

                                                // After OK button was pressed, we restore the last valid value
                                                fn: function(){
                                                    input.setValue(input.lastValidValue);
                                                }
                                            });
                                        }
                                    });
                                }
                            }, 500, input);
                        }
                    }
                }, {
                    xtype: 'textfield',
                    disabled: parseInt(indi.trail.item().scope.found) ? false : true,
                    fieldLabel: indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_OF + indi.numberFormat(indi.trail.item().scope.found),
                    width: indi.metrics.getWidth(
                            indi.lang.I_ACTION_FORM_TOPBAR_NAVTOROWNUMBER_OF + indi.numberFormat(indi.trail.item().scope.found)
                    ),
                    labelSeparator: '',
                    inputType: 'hidden',
                    cls: 'i-action-form-topbar-nav-to-row-number-of',
                    margin: '0 5 0 0',
                    style: {
                        paddingTop: '0px'
                    }
                });

                // Add a separator
                dockedItems.push('-');

                // Add a right-side shifter
                dockedItems.push('->');

                // Add a subsections combo
                dockedItems.push(top.window.Ext.create('Ext.form.ComboBox', {
                    store: top.window.Ext.create('Ext.data.Store',{
                        fields: ['alias', 'title'],
                        data: indi.trail.item().sections
                    }),
                    fieldLabel: indi.lang.I_ACTION_INDEX_SUBSECTIONS_LABEL,
                    labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_SUBSECTIONS_LABEL),
                    labelSeparator: '',
                    valueField: 'alias',
                    hiddenName: 'alias',
                    displayField: 'title',
                    typeAhead: false,
                    width: function (){
                        var triggerWidth = 17, maxTitleWidth = 0, maxTitle='', labelWidth =
                                indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_SUBSECTIONS_LABEL),
                            paddingsWidth = 6, labelPad = 5, maxTitleWidth = indi.metrics.getWidth(
                                indi.lang['I_ACTION_INDEX_SUBSECTIONS_' + (indi.trail.item().sections.length ? 'VALUE' : 'NO')]
                            )
                        for (var i = 0; i < indi.trail.item().sections.length; i++) {
                            var titleWidth = indi.metrics.getWidth(indi.trail.item().sections[i].title);
                            if (titleWidth > maxTitleWidth) {
                                maxTitleWidth = titleWidth;
                                maxTitle = indi.trail.item().sections[i].title;
                            }
                        }
                        return labelWidth + labelPad + maxTitleWidth + paddingsWidth + triggerWidth;
                    }(),
                    style: 'font-size: 10px',
                    disabled: indi.trail.item().sections.length ? false : true,
                    cls: 'i-form-combo',
                    id: 'i-action-form-topbar-nav-to-subsection',
                    editable: false,
                    margin: '0 3 2 0',
                    value: indi.trail.item().sections.length ? indi.lang.I_ACTION_FORM_TOPBAR_NAVTOSUBSECTION_SELECT : indi.lang.I_ACTION_FORM_TOPBAR_NAVTOSUBSECTION_NO_SUBSECTIONS,
                    listeners: {
                        change: function(combo){

                            var url = indi.pre + '/' + combo.getValue() + '/index/id/'+ indi.trail.item().row.id
                                +'/ph/'+indi.trail.item().scope.hash
                                +'/aix/'+top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').getValue()+'/';

                            // If save button is toggled
                            if (top.window.Ext.getCmp('i-action-form-topbar-button-save').pressed) {
                                // We save current row but remeber the redirect url
                                $('form[name='+indi.trail.item().model.tableName+']')
                                    .append('<input type="hidden" name="redirect-url" value="'+url+'"/>')
                                    .submit();

                            // Else we just update iframe's src
                            } else top.window.Indi.load(url);

                        },
                        render: function(combo){
                            if (!indi.trail.item().row.id &&
                                top.window.Ext.getCmp('i-action-form-topbar-button-save').pressed != true) {
									top.window.Ext.getCmp('i-action-form-topbar-nav-to-subsection').disable();
								}
                        }
                    }
                }));

                // Add a docked panel to main panel, with all needed items
                instance.getPanel().addDocked({
                    xtype: 'toolbar',
                    id: 'i-action-form-topbar',
                    cls: 'i-action-form-topbar',
                    items: dockedItems,
                    hidden: instance.options.hideTopbar
                });

            }

            /**
             * The enter point.
             */
            this.run = function() {

                // Provide an ability for javascript to be executed after form load, if specified
                if (indi.trail.item().section.javascriptForm) eval(indi.trail.item().section.javascriptForm);

                // Call the callbacks
                if (indi.callbacks && indi.callbacks[instance.componentName] && indi.callbacks[instance.componentName].length) {
                    for (var i = 0; i < indi.callbacks[instance.componentName].length; i++) {
                        indi.callbacks[instance.componentName][i]();
                    }
                }

                instance.applyTopToolbar();

                indi.trail.breadCrumbs();

                $(document).ready(function(){
                    $('.i-tr-disabled input').attr('disabled', 'disabled');
                    $('.i-tr-disabled .i-combo').addClass('x-item-disabled i-combo-disabled');
                    if (top.window.Ext.getCmp('iframe-mask'))
                        top.window.Ext.getCmp('iframe-mask').hide();
                });
            }
        }

        // Enter point
        if ($('.i-action-form').length) {
            indi.action = indi.action || {};
            indi.action.form = new indi.proto.action.form();
            indi.action.form.run();
        }
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof indi.proto !== 'undefined' &&
                typeof indi.proto.combo !== 'undefined' &&
                typeof top.window.Indi.proto.combo.sibling !== 'undefined') {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));
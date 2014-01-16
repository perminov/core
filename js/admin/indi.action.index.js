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
        indi.proto.action.index = function(){

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
            this.componentName = 'action.index';

            /**
             * Configuration
             *
             * @type {Object}
             */
            this.options = {
            }

            this.getPanel = function() {
                return top.window.Ext.getCmp('i-center-center-wrapper');
            }

            this.storeProxyUrl = function (){

                // Setup keyword
                var keyword = '';

                var keywordCmpId = 'i-section-' + indi.trail.item().section.alias + '-action-index-keyword';

                // If we have keyword as a field value (mean that Ext component is already initialised)
                if (Ext.getCmp(keywordCmpId)) {
                    keyword =  Ext.getCmp(keywordCmpId).getValue();

                // Else we get keyword from scope, if it is not null|empty there
                } else if (indi.scope.keyword) {
                    keyword = indi.urldecode(indi.scope.keyword);
                }

                // Build the store request url
                return indi.pre + '/' +
                    indi.trail.item().section.alias + '/' +
                    indi.trail.item().action.alias + '/' +
                   (indi.trail.item(1).row ? 'id/' + indi.trail.item(1).row.id + '/' : '') +
                    'json/1/' +
                    (keyword ? 'keyword/' + keyword + '/' : '');
            }

            /**
             * Handler for any filter change
             *
             * @param obj
             * @param newv
             * @param oldv
             */
            this.filterChange = function(cmp){

                // Declare and fulfil an array with filters component ids
                var filterAliases = [];
                var filterCmpIdPrefix = 'i-section-' + indi.trail.item().section.alias + '-action-index-filter-';
                for (var i = 0; i < indi.trail.item().filters.length; i++) {
                    if (['number', 'calendar', 'datetime']
                        .indexOf(indi.trail.item().filters[i].foreign.fieldId.foreign.elementId.alias) != -1) {
                        filterAliases.push(filterCmpIdPrefix+indi.trail.item().filters[i].foreign.fieldId.alias + '-gte');
                        filterAliases.push(filterCmpIdPrefix+indi.trail.item().filters[i].foreign.fieldId.alias + '-lte');
                    } else {
                        filterAliases.push(filterCmpIdPrefix+indi.trail.item().filters[i].foreign.fieldId.alias);
                    }
                }

                // Declare and fulfil an array with filters component ids
                var gridColumnsAliases = [];
                for (var i = 0; i < indi.trail.item().gridFields.length; i++) {
                    gridColumnsAliases.push(indi.trail.item().gridFields[i].alias);
                }

                var params = [];
                var usedFilterAliasesThatHasGridColumnRepresentedBy = [];
                for (var i in filterAliases) {
                    var filterAlias = filterAliases[i].replace(filterCmpIdPrefix, '');
                    var filterValue = Ext.getCmp(filterAliases[i]).getValue();
                    if (filterValue != '%' && filterValue != '' && filterValue !== null) {
                        var param = {};
                        if (Ext.getCmp(filterAliases[i]).xtype == 'datefield') {
                            if(Ext.getCmp(filterAliases[i]).format != 'Y-m-d') {
                                param[filterAlias] = Ext.Date.format(Ext.Date.parse(Ext.getCmp(filterAliases[i]).getRawValue(), Ext.getCmp(filterAliases[i]).format), 'Y-m-d');
                            } else {
                                param[filterAlias] = Ext.getCmp(filterAliases[i]).getRawValue();
                            }
                        } else {
                            param[filterAlias] = Ext.getCmp(filterAliases[i]).getValue();
                        }
                        params.push(param);
                        for (var j =0; j < gridColumnsAliases.length; j++) {
                            if (gridColumnsAliases[j] == filterAliases[i]) {
                                usedFilterAliasesThatHasGridColumnRepresentedBy.push(filterAliases[i]);
                            }
                        }
                    }
                }
                instance.store.getProxy().extraParams = {search: JSON.stringify(params)};

                var keywordCmpId = 'i-section-' + indi.trail.item().section.alias + '-action-index-keyword';
                var keyword = Ext.getCmp(keywordCmpId).disabled == false && Ext.getCmp(keywordCmpId).getValue() ?
                    Ext.getCmp(keywordCmpId).getValue() : '';

                instance.store.getProxy().url = indi.pre + '/' + indi.trail.item().section.alias + '/index/' +
                    (indi.trail.item(1).row ? 'id/' + indi.trail.item(1).row.id + '/' : '') + 'json/1/' +
                    (keyword ? 'keyword/' + keyword + '/' : '');

                Ext.getCmp(keywordCmpId).setDisabled(usedFilterAliasesThatHasGridColumnRepresentedBy.length == gridColumnsAliases.length);

                if (!cmp.noReload) {
                    instance.store.currentPage = 1;
                    instance.store.lastOptions.page = 1;
                    instance.store.lastOptions.start = 0;
                    if (cmp.xtype == 'combobox') {
                        instance.store.reload();
                    } else if (cmp.xtype == 'datefield' && (/^([0-9]{4}-[0-9]{2}-[0-9]{2}|[0-9]{2}\.[0-9]{2}\.[0-9]{4})$/.test(cmp.getRawValue()) || !cmp.getRawValue().length)) {
                        clearTimeout(instance.timeout);
                        instance.timeout = setTimeout(function(){
                            instance.store.reload();
                        }, 500);
                    } else if (cmp.xtype != 'datefield') {
                        clearTimeout(instance.timeout);
                        instance.timeout = setTimeout(function(){
                            instance.store.reload();
                        }, 500);
                    }
                }
            }

            this.storeFields = function (){
                // Id field
                var fieldA = [{name: 'id', type: 'int'}];

                // Other fields
                for (var i = 0; i < indi.trail.item().gridFields.length; i++)
                    fieldA.push({
                        name: indi.trail.item().gridFields[i].alias,
                        type: [3,5].indexOf(indi.trail.item().gridFields[i].columnTypeId) != -1 ? 'int' : 'string'
                    });

                // Return array
                return fieldA;
            }

            this.gridColumns = function (){
                // Id field
                var columnA = [{header: 'id', dataIndex: 'id', width: 30, sortable: true, align: 'right', hidden: true}];

                // Other fields
                for (var i = 0; i < indi.trail.item().gridFields.length; i++)
                    columnA.push({
                        id: 'i-section-' + indi.trail.item().section.alias + '-action-index-grid-column-' + indi.trail.item().gridFields[i].alias,
                        header: indi.trail.item().gridFields[i].title,
                        dataIndex: indi.trail.item().gridFields[i].alias,
                        sortable: true,
                        align: [3,5].indexOf(indi.trail.item().gridFields[i].columnTypeId) != -1 ? 'right' : 'left',
                        hidden: indi.trail.item().gridFields[i].alias == 'move' ? true : false
                    });

                // Setup flex for first non-hidden column
                columnA[1].flex = 1;

                // Return array
                return columnA;
            }

            /**
             * Create and append toolbar panels main content wrapper panel
             *
             * @return {Array}
             */
            this.toolbars = function () {

                // Toolbars array
                var toolbars = [];

                // If there is at least one filter was setup for current section
                if (indi.trail.item().filters.length) {

                    // Prepare Opera fieldset margin bottom fix
                    var fieldsetMarginBottom = (navigator.userAgent.match(/Opera/)) ? 2 : 1;

                    // Append filters toolbar to the toolbars stack
                    toolbars.push({
                        xtype: 'toolbar',
                        dock: 'top',
                        padding: '1 3 5 5',
                        id: 'i-section-' + indi.trail.item().section.alias + '-action-index-toolbar-filter',
                        items: [{
                            xtype:'fieldset',
                            padding: '0 4 1 5',
                            margin: '0 2 ' + fieldsetMarginBottom + ' 0',
                            title: indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_TITLE,
                            width: '100%',
                            columnWidth: '100%',
                            layout: 'column',
                            defaults: {
                                padding: '0 5 4 0',
                                margin: '-1 0 0 0'
                            },
                            items: instance.filterToolbarItems()
                        }]
                    });
                }

                // Append keyword toolbar to the toolbars stack
                toolbars.push({
                    xtype: 'toolbar',
                    id: 'i-section-' + indi.trail.item().section.alias + '-action-index-toolbar-keyword',
                    dock: 'top',
                    height: 27,
                    padding: '0 3 0 2',
                    items: instance.keywordToolbarItems()
                });

                return toolbars;
            };

            this.timeout;

            /**
             * Prepare and return an array containing components for each filter, defined for a section
             *
             * @return {Array}
             */
            this.filterToolbarItems = function() {

                // Items array
                var items = [];

                // For each filter
                for (var i = 0; i < indi.trail.item().filters.length; i++) {

                    // Prepare the id for current filter component
                    var filterCmpId = 'i-section-' + indi.trail.item().section.alias + '-action-index-filter-' +
                        indi.trail.item().filters[i].foreign.fieldId.alias;

                    // If current filter is defined for 'string', 'textarea' or 'html' field
                    if (['string', 'textarea', 'html']
                        .indexOf(indi.trail.item().filters[i].foreign.fieldId.foreign.elementId.alias) != -1) {

                        // Get the label
                        var fieldLabel = indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign.fieldId.title;

                        // Append the extjs textfield component data object to filters stack
                        items.push({
                            xtype: 'textfield',
                            id: filterCmpId,
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            width: 80 + indi.metrics.getWidth(fieldLabel),
                            margin: 0,
                            listeners: {
                                change: instance.filterChange
                            }
                        });

                    // Else if current filter is defined for 'number' field
                    } else if (indi.trail.item().filters[i].foreign.fieldId.foreign.elementId.alias == 'number') {

                        // Get the label
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign.fieldId.title) + ' ' +
                            indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM;

                        // Append the extjs numberfield component data object to filters stack, for minimum value
                        items.push({
                            xtype: 'numberfield',
                            id: filterCmpId + '-gte',
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            width: 50 + indi.metrics.getWidth(fieldLabel),
                            margin: 0,
                            minValue: 0,
                            listeners: {
                                change: instance.filterChange
                            }
                        });

                        // Append the extjs numberfield component data object to filters stack, for maximum value
                        items.push({
                            xtype: 'numberfield',
                            id: filterCmpId + '-lte',
                            fieldLabel: indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO,
                            labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
                            labelSeparator: '',
                            width: 50 + indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO),
                            margin: 0,
                            minValue: 0,
                            listeners: {
                                change: instance.filterChange
                            }
                        });

                    // Else if current filter is defined for 'calendar' or 'datetime' field
                    } else if (['calendar', 'datetime']
                        .indexOf(indi.trail.item().filters[i].foreign.fieldId.foreign.elementId.alias) != -1) {
                        /*
                         <?if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'calendar') {?>
                         <?if ($params['displayFormat']){?>
                         format: '<?=$params['displayFormat']?>',
                         ariaTitleDateFormat: '<?=$params['displayFormat']?>',
                         longDayFormat: '<?=$params['displayFormat']?>',
                         <?}?>
                         <?} else if ($filter->foreign['fieldId']->foreign['elementId']['alias'] == 'datetime') {?>
                         <?if ($params['displayDateFormat']){?>
                         format: '<?=$params['displayDateFormat']?>',
                         ariaTitleDateFormat: '<?=$params['displayDateFormat']?>',
                         longDayFormat: '<?=$params['displayDateFormat']?>',
                         <?}?>
                         <?}?>

                         */

                        // Get the label
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign.fieldId.title) + ' ' +
                            indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM;

                        // Append the extjs datefield component data object to filters stack, for minimum value
                        items.push({
                            xtype: 'datefield',
                            id: filterCmpId + '-gte',
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            width: 85 + indi.metrics.getWidth(fieldLabel),
                            startDay: 1,
                            margin: 0,
                            validateOnChange: false,
                            listeners: {
                                change: instance.filterChange
                            }
                        });

                        // Append the extjs datefield component data object to filters stack, for maximum value
                        items.push({
                            xtype: 'datefield',
                            id: filterCmpId + '-lte',
                            fieldLabel: indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO,
                            labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
                            labelSeparator: '',
                            width: 85 + indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO),
                            startDay: 1,
                            margin: 0,
                            validateOnChange: false,
                            listeners: {
                                change: instance.filterChange
                            }
                        });

                    // Else if current filter is defined for 'color' field
                    } else if (indi.trail.item().filters[i].foreign.fieldId.foreign.elementId.alias == 'color') {

                        // Get the label
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign.fieldId.title);

                        // Append the extjs multislider component data object to filters stack, as multislider will
                        // be the approriate way to represent color hue range (0 to 360)
                        items.push({
                            xtype: 'multislider',
                            id: filterCmpId,
                            fieldLabel: fieldLabel,
                            labelWidth: indi.metrics.getWidth(fieldLabel),
                            labelSeparator: '',
                            labelClsExtra: 'i-multislider-color-label',
                            values: [0, 360],
                            increment: 1,
                            minValue: 0,
                            maxValue: 360,
                            constrainThumbs: false,
                            width: 197,
                            margin: '1 0 0 0',
                            cls: 'i-multislider-color',
                            listeners: {
                                changecomplete: instance.filterChange
                            }
                        });

                    // Else if current filter is defined for 'combo' or 'check' field
                    } else if (parseInt(indi.trail.item().filters[i].foreign.fieldId.relation) || ['check', 'combo']
                        .indexOf(indi.trail.item().filters[i].foreign.fieldId.foreign.elementId.alias) != -1) {

                        // Get the label
                        var fieldLabel = (indi.trail.item().filters[i].alt ?
                            indi.trail.item().filters[i].alt :
                            indi.trail.item().filters[i].foreign.fieldId.title);

                        // Push the special extjs component data object to represent needed filter. Component consists of
                        // two hboxed components. First is extjs label component, and second - is setup to pick up
                        // a custom DOM node as it's contentEl property. This DOM node is already prepared by non-extjs
                        // solution, implemented in IndiEngine
                        items.push({
                            padding: 0,
                            id: filterCmpId + '-item',
                            layout: 'hbox',
                            margin: '0 5 4 0',
                            cls: 'i-filter-combo',
                            border: 0,
                            items: [{
                                xtype: 'label',
                                id: filterCmpId + '-label',
                                text: fieldLabel,
                                forId: indi.trail.item().filters[i].foreign.fieldId.alias + '-keyword',
                                margin: '0 5 0 0',
                                padding: '1 0 1 0',
                                cls: 'i-filter-combo-label',
                                border: 0
                            }, {
                                id: filterCmpId,
                                contentEl: filterCmpId + '-combo',
                                border: 0,
                                cls: 'i-filter-combo-component',
                                getValue: function(){
                                    var me = this;
                                    var hidden = $(me.el.dom).find('input[type="hidden"]');
                                    if (hidden.parent().hasClass('i-combo-single')) {
                                        return hidden.val() == '0' && hidden.attr('boolean') != 'true' ? '' : hidden.val();
                                    } else if (hidden.parent().hasClass('i-combo-multiple')) {
                                        return hidden.val().split(',');
                                    }
                                },
                                setValue: function(){
                                },
                                listeners: {
                                    render: function(){
                                        // Here we provide an ability for width autoadjusting, in case if current combo has
                                        // a satellite, and satelitte value was changed, so maximum option width may change,
                                        // because options list was refreshed
                                        var me = this;
                                        var width = parseInt($('#'+me.contentEl).css('width'));
                                        var diff = width + Ext.getCmp(me.id+'-label').getWidth() - Ext.getCmp(me.id + '-item').getWidth()
                                        me.setWidth(width);
                                        Ext.getCmp(me.id + '-item').setWidth(
                                            Ext.getCmp(me.id + '-item').getWidth() + diff + 5
                                        );
                                    }
                                }
                            }]
                        });
                    }
                }

                return items;
            }

            /**
             * Prepare the array, containing data objects for keyword toolbar components
             *
             * @return {Array}
             */
            this.keywordToolbarItems = function(){

                // Items array
                var items = [];

                // Check if 'save' and 'form' actions are allowed
                var canSave = false, canForm = false, canAdd = indi.trail.item().section.disableAdd == '0';
                for (var i = 0; i < indi.trail.item().actions.length; i++) {
                    if (indi.trail.item().actions[i].alias == 'save') canSave = true;
                    if (indi.trail.item().actions[i].alias == 'form') canForm = true;
                }

                // 'Create' button will be added only if it was not switched off
                // in section config and if 'save' and 'form' actions are allowed
                if (canForm && canSave && canAdd) {

                    items.push({
                        id: 'i-section-' +indi.trail.item().section.alias + '-action-index-button-add',
                        text: indi.lang.ACTION_CREATE,
                        iconCls: 'add',
                        actionAlias: 'form',
                        handler: function(){
                            indi.load(
                                Ext.getCmp('i-center-center-wrapper').getComponent(0).indi.href +
                                    this.actionAlias + '/ph/' + Indi.trail.item().section.primaryHash + '/'
                            );
                        }
                    });
                }

                // Fulfil items array with other actions
                var iconA = ['form', 'delete', 'save', 'toggle', 'up', 'down'];

                for (var i = 0; i < indi.trail.item().actions.length; i++) {
                    if (indi.trail.item().actions[i].display == 1) {

                        // Basic action object
                        var item = {
                            id: 'i-section-' +indi.trail.item().section.alias + '-action-index-button-' +
                                indi.trail.item().actions[i].alias,
                            text: indi.trail.item().actions[i].title,
                            actionAlias: indi.trail.item().actions[i].alias,
                            rowRequired: indi.trail.item().actions[i].rowRequired,
                            javascript: indi.trail.item().actions[i].javascript,
                            handler: function(){

                                // Get selection
                                var selection = Ext.getCmp('i-center-center-wrapper')
                                    .getComponent(0)
                                    .getSelectionModel()
                                    .getSelection();

                                // Get first selected row and it's index, if selected
                                if (selection.length) {
                                    var row = selection[0].data;
                                    var aix = selection[0].index + 1;
                                }

                                // If there is no rows selected, but at elast one should
                                if (this.rowRequired == 'y' && !selection.length) {

                                    // Display a message box with approriate warning
                                    Ext.MessageBox.show({
                                        title: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                                        msg: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.WARNING
                                    });

                                // Run the handler
                                } else {
                                    eval(this.javascript);
                                }
                            }
                        };

                        // Setup iconCls property, if need
                        if(iconA.indexOf(indi.trail.item().actions[i].alias) != -1)
                            item.iconCls = indi.trail.item().actions[i].alias;

                        // Pustto the actions stack
                        items.push(item);
                    }
                }

                // We figure that other items should be right-aligned
                items.push('->');

                // Append fast search keyword field component to the items stack
                items.push({
                    xtype: 'textfield',
                    fieldLabel: indi.lang.I_ACTION_INDEX_KEYWORD_LABEL,
                    labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_KEYWORD_LABEL),
                    labelClsExtra: 'i-action-index-keyword-toolbar-keyword-label',
                    labelSeparator: '',
                    value: indi.scope.keyword ? indi.urldecode(indi.scope.keyword) : '',
                    width: 100 + indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_KEYWORD_LABEL),
                    height: 19,
                    cls: 'i-form-text',
                    margin: '0 0 0 5',
                    id: 'i-section-' + indi.trail.item().section.alias + '-action-index-keyword',
                    listeners: {
                        change: function(){
                            clearTimeout(instance.timeout);
                            instance.timeout = setTimeout(function(){
                                instance.filterChange({});
                            }, 500);
                        }
                    }
                });

                // Add a subsections combo
                if (indi.trail.item().sections.length) items.push({
                    xtype: 'combo',
                    store: Ext.create('Ext.data.Store',{
                        fields: ['alias', 'title'],
                        data: indi.trail.item().sections
                    }),
                    fieldLabel: indi.lang.I_ACTION_INDEX_SUBSECTIONS_LABEL,
                    labelWidth: indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_SUBSECTIONS_LABEL),
                    labelSeparator: '',
                    valueField: 'alias',
                    displayField: 'title',
                    typeAhead: false,
                    width: function (combo){
                        var triggerWidth = 17, maxTitleWidth = 0, maxTitle='', labelWidth =
                            indi.metrics.getWidth(indi.lang.I_ACTION_INDEX_SUBSECTIONS_LABEL),
                            paddingsWidth = 6, labelPad = 5;
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
                    margin: '0 0 2 5',
                    value: function(){
                        if (indi.trail.item().sections.length) {
                            return indi.lang.I_ACTION_INDEX_SUBSECTIONS_VALUE
                        } else {
                            return indi.lang.I_ACTION_INDEX_SUBSECTIONS_NO
                        }
                    }(),
                    listeners: {
                        change: function(combo){
                            var selection = Ext.getCmp('i-center-center-wrapper').getComponent(0).getSelectionModel().getSelection();
                            if (selection.length) {
                                if (this.getValue()) {
                                    indi.load(
                                        indi.pre + '/' +
                                        combo.getValue() + '/index/id/' +
                                        selection[0].data.id + '/' +
                                        'ph/'+Indi.scope.hash+'/aix/' +
                                        (selection[0].index + 1)+'/'
                                    );
                                }
                            } else {
                                combo.reset();
                                Ext.MessageBox.show({
                                    title: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_TITLE,
                                    msg: indi.lang.I_ACTION_INDEX_SUBSECTIONS_WARNING_MSG,
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.WARNING
                                });
                            }
                        }
                    }
                });

                return items;
            };

            this.storeSorters = function(){
                if (indi.trail.item().section.defaultSortField) {
                    return [{
                        property : indi.trail.item().section.defaultSortFieldAlias,
                        direction: indi.trail.item().section.defaultSortDirection
                    }]
                }
                return [];
            }

            instance.storeCurrentPage = function(){
                if (indi.scope.page)
                    return indi.scope.page;

                return 1;
            }

            this.gridFirstColumnWidthFraction = function(){
                return 0.4;
            }

            this.store = Ext.create('Ext.data.Store', {
                fields: instance.storeFields(),
                sorters: instance.storeSorters(),
                method: 'POST',
                pageSize: indi.trail.item().section.rowsOnPage,
                remoteSort: true,
                currentPage: (indi.scope.page ? indi.scope.page : 1),
                proxy:  new Ext.data.HttpProxy({
                    url: instance.storeProxyUrl(),
                    method: 'POST',
                    reader: {
                        type: 'json',
                        root: 'blocks',
                        totalProperty: 'totalCount',
                        idProperty: 'id'
                    }
                }),
                listeners: {
                    load: function (){
                        var grid = Ext.getCmp('i-center-center-wrapper').getComponent(0);
                        var columnWidths = {};
                        var totalColumnsWidth = 0;
                        for(var i in grid.columns) {
                            if (grid.columns[i].hidden == false) {
                                columnWidths[i] = indi.metrics.getWidth(grid.columns[i].text) + 10;
                                for (var j in grid.store.data.items) {
                                    var cellWidth = indi.metrics.getWidth(grid.store.data.items[j].data[grid.columns[i].dataIndex]) + 7;
                                    if (cellWidth > columnWidths[i]) columnWidths[i] = cellWidth;
                                }
                                totalColumnsWidth += columnWidths[i];
                            }
                        }
                        var totalGridWidth = grid.getWidth();
                        if (totalColumnsWidth < totalGridWidth) {
                            var first = true;
                            for(i in columnWidths) {
                                if (first) {
                                    first = false;
                                } else {
                                    grid.columns[i].width = columnWidths[i];
                                }
                            }
                        } else {
                            var smallColumnsWidth = 0;
                            var first = true;
                            for(var i in columnWidths) {
                                if (first) {
                                    first = false;
                                } else if (columnWidths[i] <= 100) {
                                    smallColumnsWidth += columnWidths[i];
                                }
                            }
                            var firstColumnWidth = Math.ceil(totalGridWidth*instance.gridFirstColumnWidthFraction());
                            var percent = (totalGridWidth-firstColumnWidth-smallColumnsWidth)/(totalColumnsWidth-columnWidths[1]-smallColumnsWidth);
                            var first = true;
                            for(i in columnWidths) {
                                if (first) {
                                    grid.columns[i].width = firstColumnWidth;
                                    first = false;
                                } else if (columnWidths[i] > 100) {
                                    grid.columns[i].width = columnWidths[i] * percent;
                                } else {
                                    grid.columns[i].width = columnWidths[i];
                                }
                            }
                        }

                        // Set up full request string (but without paging params)
                        var url = grid.store.getProxy().url;
                        var get = [];
                        if (grid.store.getProxy().extraParams.search) get.push('search=' + encodeURIComponent(grid.store.getProxy().extraParams.search));
                        if (grid.store.getSorters().length) get.push('sort=' + encodeURIComponent(JSON.stringify(grid.store.getSorters())));
                            grid.indi.request = url + (get.length ? '?' + get.join('&') : '');

                    }
                }
            });
            instance.store.load();

            /**
             * Build the grid
             */
            this.buildGrid = function() {

                return Ext.create('Ext.grid.Panel', {
                    id: 'i-section-' + indi.trail.item().section.alias + '-action-index-grid',
                    border: 0,
                    multiSelect: false,
                    store: instance.store,
                    columns: instance.gridColumns(),
                    indi: {
                        href : indi.pre + '/' + indi.trail.item().section.alias + '/',
                        msgbox: {
                            confirm: {
                                title: indi.lang.MSGBOX_CONFIRM_TITLE,
                                message: indi.lang.MSGBOX_CONFIRM_MESSAGE
                            }
                        }
                    },
                    viewConfig: {
                        getRowClass: function (row) {
                            if (row.raw._system && row.raw._system.disabled)
                                return 'i-grid-row-disabled';
                        },
                        loadingText: Ext.LoadMask.prototype.msg
                    },
                    listeners: {
                        beforeselect: function (selectionModel, row) {
                            if (row.raw._system && row.raw._system.disabled)
                                return false;
                        },
                        selectionchange: function (selectionModel, selectedRows) {
                            if (selectedRows.length > 0)
                                Ext.Array.each(selectedRows, function (row) {
                                    if (row.raw._system && row.raw._system.disabled)
                                        selectionModel.deselect(row, true);
                                });
                        },
                        itemdblclick: function(){
                            if (Ext.getCmp('i-section-' + indi.trail.item().section.alias + '-action-index-button-form'))
                                Ext.getCmp('i-section-' + indi.trail.item().section.alias + '-action-index-button-form')
                                    .handler();
                        }
                    }
                });
            }

            /**
             * Build the wrapper panel with the listing pagel inside it, depending on indi.trail.item().type
             */
            this.build = function(){

                // Type of the section
                var type = indi.trail.item().section.type;

                // Create the main panel
                Ext.create('Ext.Panel', {
                    id: 'i-center-center-wrapper',
                    renderTo: 'i-center-center-body',
                    border: 0,
                    height: '100%',
                    closable: true,
                    layout: 'fit',
                    title: indi.trail.item().section.title,
                    items: [instance['build'+type.charAt(0).toUpperCase() + type.slice(1)]()],
                    bbar: new Ext.PagingToolbar({
                        store: instance.store,
                        displayInfo: true,
                        items:['-']
                    }),
                    dockedItems: instance.toolbars()
                });
            }

            /**
             * The enter point.
             */
            this.run = function() {

                // Provide an ability for javascript to be executed, if specified
                if (indi.trail.item().section.javascriptForm) eval(indi.trail.item().section.javascript);

                // Call the callbacks
                if (indi.callbacks && indi.callbacks[instance.componentName] && indi.callbacks[instance.componentName].length) {
                    for (var i = 0; i < indi.callbacks[instance.componentName].length; i++) {
                        indi.callbacks[instance.componentName][i]();
                    }
                }

                // View type. Later also 'tile' and 'calendar' will be available
                indi.trail.item().section.type = 'grid';

                // Build
                instance.build();
            }
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
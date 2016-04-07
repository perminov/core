/**
 * Base class for all controller actions instances, that operate with rowsets,
 * and use Ext.panel.Grid view to display/modify those rowsets
 */
Ext.define('Indi.lib.controller.action.Grid', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset.Grid',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Rowset',

    /**
     * Config of panel, that will be used for representing the rowset
     */
    rowset: {
        xtype: 'grid',
        multiSelect: false,
        firstColumnWidthFraction: 0.4,
        smallColumnWidth: 100,
        border: 0,
        layout: 'fit',

        /**
         * Features
         */
        features: [{
            ftype: 'summary',
            remoteRoot: 'summary'
        }],

        /**
         * Plugins
         */
        $plugins: [{alias: 'cellediting'}],

        /**
         * Docked items special config
         */
        docked: {
            items: [{alias: 'paging'}],
            inner: {
                paging: ['-', {alias: 'excel'}, {alias: 'pdf'}]
            }
        },

        /**
         * View config
         */
        viewConfig: {
            getRowClass: function (row) {
                if (row.raw._system && row.raw._system.disabled)
                    return 'i-grid-row-disabled';
            },
            loadingText: Ext.LoadMask.prototype.msg,
            listeners: {
                beforeitemkeydown: function(view, r, d, i, e) {
                    if (e.altKey) return false;
                }
            }
        },
        listeners: {
            beforeselect: function (selectionModel, row, index) {
                var grid = this, keyCode = Ext.EventObject.getKey(), sm = grid.getSelectionModel(),
                    firstNonDisabledIndex, prevNonDisabledIndex = -1;
                if (row.raw._system && row.raw._system.disabled) {
                    if (keyCode == Ext.EventObject.DOWN) {
                        if ((firstNonDisabledIndex = grid.getStore().findBy(function(r){
                            if (!r.raw._system || !r.raw._system.disabled) return true;
                        }, null, index + 1)) != -1) Ext.defer(function(){sm.select(firstNonDisabledIndex)}, 10);
                        else return false;
                    } else return false;
                }
            },
            selectionchange: function (selectionModel, selectedRows) {

                // Refresh summary row
                if (this.multiSelect && selectionModel.view.getFeature('summary')) {
                    selectionModel.views.forEach(function(view){
                        view.getFeature('summary').refresh();
                    });
                }

                if (selectedRows.length > 0)
                    Ext.Array.each(selectedRows, function (row) {
                        if (row.raw._system && row.raw._system.disabled)
                            selectionModel.deselect(row, true);
                    });
            },
            itemdblclick: function() {
                var btn = Ext.getCmp(this.ctx().bid() + '-docked-inner$form'); if (btn) btn.press();
            },

            itemclick: function() {
                if (Ext.EventObject.ctrlKey) {
                    var btn = Ext.getCmp(this.ctx().bid() + '-docked-inner$form'); if (btn) btn.press();
                }
            },

            cellclick: function(gridview, tdDom, cellIndex, record, trDom, rowIndex, e) {
                var me = gridview.ctx(), col = gridview.headerCt.getGridColumns()[cellIndex],
                    dataIndex = col['dataIndex'], field = me.ti().fields.r(dataIndex, 'alias'),
                    enumset = field._nested && field._nested.enumset, value, valueItem, valueItemIndex, oldValue, s,
                    canSave = me.ti().actions.r('save', 'alias');

                // If 'Save' action is accessible, and column is linked to 'enumset' field
                // and that field is not in the list of disabled fields - provide some kind
                // of cell-editor functionality, so enumset values can be switched from one to another
                if (canSave && enumset && !me.ti().disabledFields.r(field.id, 'fieldId') && field.storeRelationAbility == 'one') {
                    s = me.getStore();
                    value = record.key(dataIndex);
                    valueItem = enumset.r(value, 'alias');
                    enumset.forEach(function(item, i){
                        if (item.alias == value) valueItemIndex = i;
                    });
                    valueItemIndex ++;
                    valueItemIndex = valueItemIndex > enumset.length - 1 ? 0 : valueItemIndex;
                    valueItem = enumset[valueItemIndex];
                    value = valueItem.alias;
                    record.key(dataIndex, value);
                    record.set(dataIndex, valueItem.title.replace(/(<\/span>).*$/, '\1'));
                    me.recordRemoteSave(record, s.indexOfTotal(record) + 1, me.ti());
                }
            }
        }
    },

    /**
     * Builds and returns config for grid Id column
     *
     * @return {Object}
     */
    gridColumn$Id: function() {
        return {header: 'ID', dataIndex: 'id', width: 30, sortable: true, align: 'right', hidden: true}
    },

    /**
     * Builds and returns default/initial config for all grid columns (except 'Id' columns)
     *
     * @return {Object}
     */
    gridColumnDefault: function(field, column) {
        var me = this, tooltip = column.tooltip || (field && field.tooltip);

        // Default column config
        return {
            id: me.bid() + '-rowset-grid-column-' + field.alias,
            header: column.alterTitle || field.title,
            dataIndex: field.alias,
            tooltip: tooltip ? {html: tooltip, constrainParent: false, constrainPosition: false} : '',
            cls: tooltip ? 'i-tooltip' : undefined,
            sortable: true,
            align: function(){
                return (field.storeRelationAbility == 'none' &&
                    [3,5,14].indexOf(parseInt(field.columnTypeId)) != -1) ? 'right' : 'left';
            }()
        }
    },

    /**
     * Build and return an array, containing column definitions for grid panel
     *
     * @return {Array}
     */
    gridColumnA: function() {
        var me = this, columnA = [], column$Id = me.gridColumn$Id();

        // Append Id column
        if (column$Id) columnA.push(column$Id);

        // Recursively build the columns
        columnA = columnA.concat(me.gridColumnADeep(me.ti().grid));

        if (columnA[1] && !columnA[1].columns && !columnA[1].locked) columnA[1].flex = 1;

        return columnA;
    },

    ////////////////////////////////
    // Grid column cell renderers //
    ////////////////////////////////

    /**
     * Default renderer for text columns
     *
     * @param value
     * @return {*}
     */
    gridColumnRenderer_Text: function (value) {
        if (String(value).match(/<\?/)) return Ext.util.Format.htmlEncode(value);
        if (String(value).match(/ class="i-color-box"/))
            return String(value).match(/ class="i-color-box" style="background:\surl\(/)
                ? '<div class="i-bgimg-box-wrap">'+value+'</div>'
                : '<div class="i-color-box-wrap">'+value+'</div>';
        return value;
    },

    /**
     * Default renderer for numeric columns
     *
     * @param v
     * @param m
     * @param r
     * @param i
     * @param c
     * @param s
     * @return {*}
     */
    gridColumnRenderer_Numeric: function(v, m, r, i, c, s) {
        var column = this.xtype == 'gridcolumn' ? this : this.headerCt.getGridColumns()[c];
        if (column.displayZeroes === false && parseFloat(v) == 0) return '';
        return Indi.numberFormat(v, column.decimalPrecision, column.decimalSeparator, column.thousandSeparator);
    },

    /**
     * Renderer fn for string-columns
     *
     * @return {*}
     */
    gridColumnXString_Renderer: function() {
        return this.ctx().gridColumnRenderer_Text.apply(this, arguments);
    },

    /**
     * Renderer fn for combo-columns
     *
     * @return {*}
     */
    gridColumnXCombo_Renderer: function() {
        return this.ctx().gridColumnRenderer_Text.apply(this, arguments);
    },

    /**
     * Renderer fn for textarea-columns
     *
     * @return {*}
     */
    gridColumnXTextarea_Renderer: function() {
        return this.ctx().gridColumnRenderer_Text.apply(this, arguments);
    },

    /**
     * Renderer fn for radio-columns
     *
     * @return {*}
     */
    gridColumnXRadio_Renderer: function() {
        return this.ctx().gridColumnRenderer_Text.apply(this, arguments);
    },

    /**
     * Renderer fn for number-columns
     *
     * @return {*}
     */
    gridColumnXNumber_Renderer: function(v) {
        return this.ctx() ? this.ctx().gridColumnRenderer_Numeric.apply(this, arguments) : v;
    },

    /**
     * Renderer fn for price-columns
     *
     * @return {*}
     */
    gridColumnXPrice_Renderer: function(v) {
        return this.ctx() ? this.ctx().gridColumnRenderer_Numeric.apply(this, arguments) : v;
    },

    /**
     * Renderer fn for decimal143-columns
     *
     * @return {*}
     */
    gridColumnXDecimal143_Renderer: function(v) {
        return this.ctx() ? this.ctx().gridColumnRenderer_Numeric.apply(this, arguments) : v;
    },

    /**
     * Default editor config for string-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXString_Editor: function(column, field) {
        return {
            xtype: 'textfield',
            allowBlank: false,
            margin: '0 2 0 3',
            height: 18
        }
    },

    /**
     * Default editor config for number-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXNumber_Editor: function(column, field) {
        return {
            xtype: 'numberfield',
            hideTrigger: true,
            height: 18
        }
    },

    /**
     * Default editor config for date-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXCalendar_Editor: function(column, field) {
        return {
            xtype: 'datefield',
            hideTrigger: true,
            height: 18,
            format: field.params.displayFormat
        }
    },

    /**
     * Default editor config for datetime-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXDatetime_Editor: function(column, field) {
        return {
            xtype: 'datetimefield',
            hideTrigger: true,
            height: 18,
            format: field.params.displayDateFormat
        }
    },

    /**
     * Default editor config for number-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXPrice_Editor: function(column, field) {
        return {
            xtype: 'numberfield',
            hideTrigger: true,
            decimalPrecision: 2,
            precisionPad: true,
            height: 18
        }
    },

    /**
     * Default editor config for number-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXDecimal143_Editor: function(column, field) {
        return {
            xtype: 'numberfield',
            hideTrigger: true,
            decimalPrecision: 3,
            precisionPad: true,
            height: 18
        }
    },

    /**
     * Default config for number-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXNumber: function(column, field) {
        return {
            thousandSeparator: ' ',
            decimalSeparator: '.',
            decimalPrecision: 0,
            displayZeroes: true
        }
    },

    /**
     * Default config for price-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXPrice: function(column, field) {
        return Ext.merge(this.gridColumnXNumber(column, field), {
            displayZeroes: true,
            decimalPrecision: 2
        });
    },

    /**
     * Default config for decimal143-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXDecimal143: function(column, field) {
        return Ext.merge(this.gridColumnXNumber(column, field), {
            displayZeroes: true,
            decimalPrecision: 3
        });
    },

    /**
     * Default config for move-columns
     */
    gridColumnXMove: {hidden: true},

    /**
     * Default config for move-columns
     */
    gridColumn$Toggle: function(){
        return {
            cls: 'i-column-header-icon',
            header: '<img src="/i/admin/btn-icon-toggle.png">',
            tooltip: arguments[0].tooltip || arguments[0].header
        }
    },

    /**
     * Default config for date-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXCalendar: function(column, field) {
        return {
            xtype: 'datecolumn',
            format: field.params.displayFormat
        }
    },

    /**
     * Default config for datetime-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnXDatetime: function(column, field) {
        return {
            xtype: 'datecolumn',
            format: field.params.displayDateFormat + ' ' + field.params.displayTimeFormat
        }
    },

    /**
     * Build an array, representing grid columns for the given column level,
     * so columns groupings will be taken into consideration
     *
     * @param colA
     * @return {Array}
     */
    gridColumnADeep: function(colA) {
        var me = this, i, c, colI, field, columnA = [], columnI, columnX, eColumnX, column$, eColumn$, eColumnSummaryX,
            eColumnXRenderer, eColumnXEditor;

        // Other columns
        for (i = 0; i < colA.length; i++) {

            // Get current col
            colI = colA[i];

            // Setup a shortcut for a grid field
            field = me.ti().fields.r(colI.fieldId);

            // If current col - is a group col
            if (colI._nested && colI._nested.grid && colI._nested.grid.length) {

                // Base cfg. Note that here we set up whole column group to be hidden, initialy,
                // and if at least one of the sub-columns is not hidden - we will set `hidden` prop as `false`
                columnI = {
                    text: colI.title,
                    hidden: true,
                    columns: me.gridColumnADeep(colI._nested.grid)
                }

                // Check if current column group has at least one non-hidden sub-column
                // and if so, set `hidden` prop of whole group as `false`
                for (c = 0; c < columnI.columns.length; c++)
                    if (!columnI.columns[c].hidden)
                        if (Ext.merge(columnI, {hidden: false}))
                            break;

                // If `alias` prop of `colI` is not empty
                if (colI.alias) {

                    // Use it to build an explicit id
                    columnI.id = me.bid() + '-rowset-grid-column-' + colI.alias;

                    // Apply column custom config
                    eColumn$ = 'gridColumn$' + Indi.ucfirst(colI.alias);
                    if (Ext.isFunction(me[eColumn$]) || Ext.isObject(me[eColumn$])) {
                        column$ = Ext.isFunction(me[eColumn$]) ? me[eColumn$](columnI, field) : me[eColumn$];
                        columnI = Ext.isObject(column$) ? Ext.merge(columnI, column$) : column$;
                    } else if (me[eColumn$] === false) columnI = me[eColumn$];
                }

                // Add column
                if (columnI) columnA.push(columnI);

            // Else
            } else {

                // Get default column config
                columnI = me.gridColumnDefault(field, colI);

                // Apply specific control element config, as columns control elements/xtypes may be different
                eColumnX = 'gridColumnX' + Indi.ucfirst(field.foreign('elementId').alias);
                if (Ext.isFunction(me[eColumnX]) || Ext.isObject(me[eColumnX])) {
                    columnX = Ext.isFunction(me[eColumnX]) ? me[eColumnX](columnI, field) : me[eColumnX];
                    columnI = Ext.isObject(columnX) ? Ext.merge(columnI, columnX) : columnX;
                } else if (me[eColumnX] === false) columnI = me[eColumnX];

                // Apply column custom config
                eColumn$ = 'gridColumn$' + Indi.ucfirst(field.alias);
                if (Ext.isFunction(me[eColumn$]) || Ext.isObject(me[eColumn$])) {
                    column$ = Ext.isFunction(me[eColumn$]) ? me[eColumn$](columnI, field) : me[eColumn$];
                    columnI = Ext.isObject(column$) ? Ext.merge(columnI, column$) : column$;
                } else if (me[eColumn$] === false) columnI = me[eColumn$];

                // Apply renderer
                if (Ext.isObject(columnI) && columnI.renderer === undefined) {
                    eColumnXRenderer = 'gridColumnX' + Indi.ucfirst(field.foreign('elementId').alias) + '_Renderer';
                    if (Ext.isFunction(me[eColumnXRenderer])) columnI.renderer = me[eColumnXRenderer];
                }

                // Apply string-summary, if column's non-empty `summaryText` property detected
                if (Ext.isObject(columnI) && columnI.summaryText) {
                    columnI.summaryRenderer = function(value, summaryData, dataIndex) {
                        return this.grid.headerCt.getGridColumns().r(dataIndex, 'dataIndex').summaryText;
                    }
                }

                // Apply column 'sum' summary renderer config
                eColumnSummaryX = 'gridColumnSummaryX' + Indi.ucfirst(field.foreign('elementId').alias);
                if (Ext.isObject(columnI) && columnI.summaryType && typeof me[eColumnSummaryX] == 'function') {
                    column$ = me[eColumnSummaryX](columnI, field);
                    columnI = Ext.isObject(column$) ? Ext.merge(columnI, column$) : column$;
                }

                // Apply editor
                if (Ext.isObject(columnI) && columnI.editor) {
                    eColumnXEditor = 'gridColumnX' + Indi.ucfirst(field.foreign('elementId').alias) + '_Editor';
                    if (Ext.isFunction(me[eColumnXEditor]) || Ext.isObject(me[eColumnXEditor])) {
                        columnI.editor = Ext.isFunction(me[eColumnXEditor]) ? me[eColumnXEditor](columnI, field, Ext.isObject(columnI.editor) ? columnI.editor : {}) : me[eColumnXEditor];
                    } else if (!Ext.isObject(columnI.editor)) {
                        columnI.editor = false;
                    }
                }

                // Add column
                if (columnI) columnA.push(columnI);
            }
        }

        // Return columns array
        return columnA;
    },

    /**
     * Summary renderer for number-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnSummaryXNumber: function(column, field) {
        return {
            summaryRenderer: function(value, summaryData, dataIndex) {
                var me = this, grid = me.grid, selectedRows = grid.getSelectionModel().selected,
                    column = grid.headerCt.getGridColumns().r(dataIndex, 'dataIndex'), columnData = [],
                    type = column.summaryType, tr, td;

                // If there is currently selected more than 1 row in the grid,
                // force summary to be calculated for selected rows only
                if (selectedRows.getCount() > 1 && !column.summaryText) {

                    // Apply summary cell style
                    Ext.defer(function(){
                        // Get tr
                        tr = grid.view.el.down('tr.x-grid-row-summary');
                        td = tr.down('td.x-grid-cell-' + grid.ctx().rowset.id + '-column-' + dataIndex);
                        td.addCls('x-grid-cell-selected');
                    }, 1);

                    // Get column data
                    selectedRows.each(function(r){columnData.push(r.get(dataIndex));});

                    // If summary type is 'sum'
                    if (['sum', 'min', 'max'].indexOf(type) != -1) value = Ext.Array[type](columnData);
                }

                // Return
                return column.renderer(value);
            }
        }
    },

    /**
     * Summary renderer for number-columns
     *
     * @param column
     * @param field
     * @return {Object}
     */
    gridColumnSummaryXPrice: function(column, field) {
        return this.gridColumnSummaryXNumber(column, field);
    },

    /**
     * Adjust grid columns widths, for widths to match column contents
     */
    gridColumnAFit: function(grid, locked) {

        // Setup auxiliary variables
        var me = this, grid = grid || Ext.getCmp(me.rowset.id), view = grid.getView(), columnA = [],
            widthA = [], px = {ellipsis: 18, sort: 18}, store = grid.getStore(), total = 0, i, j, longestWidth, cell,
            visible, scw = me.rowset.smallColumnWidth, fcwf = me.rowset.firstColumnWidthFraction, sctw = 0, fcw,
            hctw = 0, busy = 0, free, longest, summaryData, summaryFeature;

        // If view not consists from normalView and lockedView
        if (view.headerCt) {

            // Suspend layouts
            Ext.suspendLayouts();

            // Get columns
            columnA = view.headerCt.getGridColumns();

            // Get visible area
            visible = grid.getWidth() - (view.hasScrollY() ? 16 : 0);

            // Get sumary feature
            summaryFeature = view.getFeature('summary');

        // Else
        } else {

            // Pass exection directly to locked and non-locked part of grid
            me.gridColumnAFit(view.lockedGrid, true);
            me.gridColumnAFit(view.normalGrid);

            // Return
            return;
        }

        // Get summary data
        if (summaryFeature) summaryData = summaryFeature.generateSummaryData();

        // For each column, mapped to a store field
        for (i = 0; i < columnA.length; i++) {

            // Get initial column width, based on a column title metrics
            widthA[i] = Indi.metrics.getWidth(columnA[i].text);// + px.ellipsis;

            // Reset length
            longest = '';

            // If columns does not have a dataIndex - skip this iteration
            if (columnA[i].dataIndex) {

                // Increase the width of a column, that store is sorted by, to provide an additional amount
                // of width for sort icon, that is displayed next after column title, within the same column
                if (columnA[i].dataIndex == me.ti().section.defaultSortFieldAlias) widthA[i] += px.sort;

                // Get the longest (within current column) cell contents
                store.each(function(r){
                    cell = typeof columnA[i].renderer == 'function'
                        ? columnA[i].renderer(r.get(columnA[i].dataIndex))
                        : r.get(columnA[i].dataIndex);
                    if (cell && cell.length > longest.length) longest = cell;
                });

                // Don't forgot about summaries
                if (columnA[i].summaryType && Ext.isObject(summaryData)) {
                    cell = typeof columnA[i].renderer == 'function'
                        ? columnA[i].renderer(summaryData[columnA[i].id])
                        : summaryData[columnA[i].id];
                    if (cell.length > longest.length) longest = cell;
                } else if (columnA[i].summaryText) {
                    cell = columnA[i].summaryText;
                    if (cell.length > longest.length) longest = cell;
                }

            // Else if column does not have `dataIndex` prop
            } else {

                // If column's xtype is 'rownumberer'
                if (columnA[i].xtype == 'rownumberer') longest = store.getTotalCount().toString();
            }

            // Get width of the longest cell
            longestWidth = Indi.metrics.getWidth(longest);

            // Update widthA[i] if need
            if (longestWidth > widthA[i]) widthA[i] = longestWidth;

            // Append ellipsis space
            widthA[i] += px.ellipsis;

            // Limit the maximum column width, if such a config was set
            if (columnA[i].maxWidth && widthA[i] > columnA[i].maxWidth) widthA[i] = columnA[i].maxWidth;

            // Increase the total width
            total += widthA[i];

            // If column is hidden - sum it's width into `hctw` variable
            if (columnA[i].hidden) hctw += widthA[i];
        }

        // Exclude first non-hidden column width from total width
        total -= widthA[1];

        // Detect the first column's width, using it's fraction
        widthA[1] = fcw = Math.ceil(visible * fcwf) || widthA[1];

        // Include first non-hidden column width (regarding `firstColumnWidthFraction` cfg) to total width
        total += widthA[1];

        // If total width of all columns is less that available/visible width
        // - set column widths without any additional calculations
        if (total - hctw < visible) {

            // For each column (except first non-hidden)
            for (i = 2; i < widthA.length; i++) {

                // Set width
                columnA[i].setWidth(widthA[i]);

                // Sum widths
                busy += columnA[i].width;
            }
        }

        // Else if total width of all columns is greater than available/visible width - calculate
        // the percent of column widths shrink, and apply it
        else {

            var over = total - hctw - visible, shrinkedWidth;

            // Calc the total width for all small columns
            for (i = 2; i < widthA.length; i++) if (widthA[i] > scw) sctw += widthA[i];

            busy = 0;

            // For each column (except first non-hidden)
            for (i = 2; i < widthA.length; i++) {

                // If current column's width is greater than `smallColumnWidth` - calc shrinked width,
                // and apply it if it, hovewer, still not less than `smallColumnWidth`
                if (widthA[i] > scw) widthA[i] = (shrinkedWidth = widthA[i] - Math.ceil(widthA[i]/sctw * over)) < scw
                    ? scw
                    : shrinkedWidth;

                // Set width
                columnA[i].setWidth(widthA[i]);

                // Sum widths
                busy += columnA[i].width;
            }
        }

        // Increase first non-hidden column's width, if free space is available
        if (locked) {
            columnA[0].setWidth(widthA[0]);
            if (columnA[1]) columnA[1].setWidth(widthA[1]);
        } else {
            columnA[1].setWidth((free = visible - busy) > fcw ? free : fcw);
        }

        // If current grid view is not consists from locked and non-locked parts - resume layouts
        if (view.headerCt) Ext.resumeLayouts(true);
    },

    /**
     * Callback for store load, will be fired if current section type = 'grid'
     */
    storeLoadCallbackDefault: function() {
        var me = this;

        // Call parent
        me.callParent();

        // Get the grid panel object
        var grid = Ext.getCmp(me.bid() + '-rowset-grid');

        // Set the focus on grid, to automatically provide an ability to use keyboard
        // cursor to navigate through rows, but only if it's not prevented
        if (me.preventViewFocus) me.preventViewFocus = false; else {
            grid.getView().focus ? grid.getView().focus() : grid.getView().normalView.focus();
        }

        // Setup last row autoselection, if need
        /*if (me.ti().scope.aix) {

            // Calculate row index value, relative to current page
            var index = parseInt(me.ti().scope.aix) - 1 - (parseInt(me.ti().scope.page) - 1) *
                parseInt(me.ti().section.rowsOnPage);

            // If such row (row at that index) exists in grid - selectit
            if (grid.getStore().getAt(index)) grid.selModel.select(index, true);
        }*/

        if (Ext.isArray(me.ti().scope.lastIds)) {
            me.ti().scope.lastIds.forEach(function(id){
                if (grid.getStore().getById(parseInt(id))) {
                    grid.selModel.select(grid.getStore().getById(parseInt(id)), true);
                }
            });
        }

        // Adjust grid column widths
        me.gridColumnAFit();

        // Bind Indi.load(...) for all DOM nodes (within grid), that have 'load' attibute
        me.bindLoads(grid);

        // Bind Indi.load(...) for all DOM nodes (within grid), that have 'jump' attibute
        me.bindJumps(grid);
    },

    /**
     * Bind Indi.load(...) call on click on all DOM nodes (within `root`), that have 'load' attibute
     *
     * @param root
     */
    bindLoads: function(root) {
        root.getEl().select('[load]').each(function(el){
            el.on('click', function(e, dom){
                Indi.load(Ext.get(dom).attr('load'));
            });
        });
    },

    /**
     * Bind Indi.load(...) call on click on all DOM nodes (within `root`), that have 'jump' attibute
     *
     * @param root
     */
    bindJumps: function(root) {
        root.getEl().select('[jump]').each(function(el){
            el.on('click', function(e, dom){
                Indi.load(Ext.get(dom).attr('jump') + 'jump/1/');
            });
        });
    },

    /**
     * Key map for gridpanel body
     */
    keyMap: function() {
        var me = this;

        // Add keyboard event handelers
        if (Ext.getCmp(me.rowset.id)) Ext.getCmp(me.rowset.id).getEl().addKeyMap({
            eventName: 'keydown',
            binding: [{
                key: Ext.EventObject.F4,
                shift: false,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$form'); if (btn) btn.press();
                },
                scope: me
            }, {
                key: Ext.EventObject.DELETE,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$delete'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.E,
                alt: true,
                fn:  function(keyCode, e){
                    e.stopEvent();
                    var btn = Ext.getCmp(me.bid() + '-rowset-docked-inner$excel'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.N,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$create'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.T,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$toggle'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.F4,
                shift: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$create'); if (btn) btn.press();
                },
                scope: me
            }]
        });

        // Add keyboard event handelers
        if (Ext.getCmp(me.rowset.id)) Ext.getCmp(me.rowset.id).getEl().addKeyMap({
            eventName: 'keyup',
            binding: [{
                key: Ext.EventObject.ENTER,
                fn:  function(a, b){
                    if (Ext.getCmp(me.rowset.id).preventEnter) Ext.getCmp(me.rowset.id).preventEnter = false; else {
                        var btn = Ext.getCmp(me.bid() + '-docked-inner$form'); if (btn) btn.press();
                    }
                },
                scope: me
            },{
                key: Ext.EventObject.UP,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$up'); if (btn) btn.press();
                },
                scope: me
            },{
                key: Ext.EventObject.DOWN,
                alt: true,
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$down'); if (btn) btn.press();
                },
                scope: me
            }, {
                key: Ext.EventObject.DOWN,
                alt: false,
                fn:  function(){
                    var grid = Ext.getCmp(me.rowset.id), sm = grid.getSelectionModel(), firstNonDisabledIndex;
                    if (!sm.hasSelection() && (firstNonDisabledIndex = grid.getStore().findBy(function(r){
                        if (!r.raw._system || !r.raw._system.disabled) return true;
                    })) != -1) sm.select(firstNonDisabledIndex);
                },
                scope: me
            }]
        });

        // Batch-attach key-map, for ability to navigate to subsections via keyboard
        me.setupSubsectionsAccessKeys(me.rowset.id);
    },

    /**
     * Rowset panel toolbars array builder
     *
     * @return {Array}
     */
    rowsetDockedA: function() {
        return this._docked('rowset');
    },

    /**
     * Rowset panel paging toolbar builder
     *
     * @return {Object}
     */
    rowsetDocked$Paging: function() {
        var me = this;

        // If scope contains tab info return, as both paging toolbar and tabs toolbar looks bad one under another
        try {if (me.ti().scope.actionrowset.south.tabs.length) return false;} catch(e) {}

        // Paging toolbar cfg
        return {
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: this.getStore(),
            displayInfo: true,
            items: this.push(this.rowset.docked.inner.paging, 'rowsetInner', true)
        }
    },

    /**
     * Rowset panel paging toolbar 'Excel' button-item, for ability to make an advanced Excel-export
     * within the currently available rows scope
     *
     * @return {Object}
     */
    rowsetInner$Excel: function() {
        var me = this;

        // 'Excel-export' item cfg
        return {
            id: me.bid() + '-rowset-docked-inner$excel',
            iconCls: 'i-btn-icon-xls',
            tooltip: Indi.lang.I_EXPORT_EXCEL,
            handler: function(){
                window.location = me.rowsetExportQuery('excel');
            }
        }
    },

    /**
     * Rowset panel paging toolbar 'PDF' button-item, for ability to make an advanced PDF-export
     * within the currently available rows scope
     *
     * @return {Object}
     */
    rowsetInner$Pdf: function() {
        var me = this;

        // 'Pdf-export' item cfg
        return {
            id: me.bid() + '-rowset-docked-inner$pdf',
            iconCls: 'i-btn-icon-pdf',
            tooltip: Indi.lang.I_EXPORT_PDF,
            hidden: true,
            handler: function(){
                window.location = me.rowsetExportQuery('pdf');
            }
        }
    },

    rowsetExport$PdfColumnA: function() {
        return this.rowsetExportColumnA();
    },

    /**
     * Detect grid's columns that should be exported
     *
     * @return {Array}
     */
    rowsetExportColumnA: function() {
        var me = this, grid = Ext.getCmp(me.rowset.id), view = grid.getView(), columnA = [];

        // Apply a workaround for cases when grid has locked columns
        if (view.headerCt) columnA = view.headerCt.getGridColumns(); else {
            if (view.lockedView) columnA = columnA.concat(view.lockedView.headerCt.getGridColumns());
            if (view.normalView) columnA = columnA.concat(view.normalView.headerCt.getGridColumns());
        }

        return columnA.select(false, 'hidden');
    },

    rowsetExport$ExcelColumnA: function() {
        return this.rowsetExportColumnA();
    },

    /**
     * Build and return array of objects, representing each column that should be presented in the pdf-export
     *
     * @return {Array}
     */
    _rowsetExport$PdfColumnA: function() {
        var me = this, pdfWidth = 720, i, gridColumnA = me.rowsetExport$PdfColumnA(), excelColumnA = [],
            totalColumnWidthExceptFirstColumn = 0, firstColumnWidth = 0, pdfFirstColumnWidth, width;

        // Collect needed data about columns
        for (i = 0; i < gridColumnA.length; i++) if (gridColumnA[i].hidden == false) {
            if (firstColumnWidth == 0) firstColumnWidth = gridColumnA[i].getWidth();
            else totalColumnWidthExceptFirstColumn += gridColumnA[i].getWidth();
        }

        // Get width of first pdf's column
        pdfFirstColumnWidth = pdfWidth - totalColumnWidthExceptFirstColumn;

        // Collect needed data about columns
        for (i = 0; i < gridColumnA.length; i++) {
            if (gridColumnA[i].hidden == false) {

                // Width
                width = excelColumnA.length ? 1 : pdfFirstColumnWidth;

                // Prepare the data object for excel column
                var exportColumnI = {
                    title: gridColumnA[i].text,
                    dataIndex: gridColumnA[i].dataIndex,
                    align: gridColumnA[i].align,
                    width: excelColumnA.length ? gridColumnA[i].getWidth() : pdfFirstColumnWidth
                };

                // If current grid column - is a number (int, float) column, get it's `displayZeroes` prop
                if (gridColumnA[i].align == 'right')
                    Ext.merge(exportColumnI, {
                        displayZeroes: gridColumnA[i].displayZeroes
                    });

                // If current grid column - is column, currently used for sorting,
                // we pick sorting direction, and column title width
                if (gridColumnA[i].sortState)
                    Ext.merge(exportColumnI, {
                        sortState: gridColumnA[i].sortState.toLowerCase(),
                        titleWidth: Indi.metrics.getWidth(gridColumnA[i].text)
                    })

                // Push the data object to array
                excelColumnA.push(exportColumnI);
            }
        }

        // Return
        return excelColumnA;
    },

    /**
     * Build and return array of objects, representing each column that should be presented in the excel-export
     *
     * @return {Array}
     */
    _rowsetExport$ExcelColumnA: function() {
        var me = this, gridColumnA = me.rowsetExport$ExcelColumnA(), exportColumnA = [],
            multiplier = screen.availWidth/Ext.getCmp(me.rowset.id).getWidth();

        // Collect needed data about columns
        for (var i = 0; i < gridColumnA.length; i++) {
            if (gridColumnA[i].hidden == false) {

                // Prepare the data object for excel column
                var exportColumnI = {
                    title: gridColumnA[i].text,
                    dataIndex: gridColumnA[i].dataIndex,
                    align: gridColumnA[i].align,
                    width: Math.ceil(gridColumnA[i].getWidth() * multiplier)
                };

                if (Ext.isString(gridColumnA[i].cls) && gridColumnA[i].cls.match(/i-grid-column-multiline/))
                    exportColumnI.height = gridColumnA[i].getHeight();

                // If current grid column - is a number (int, float) column, get it's `displayZeroes` prop
                if (gridColumnA[i].align == 'right')
                    Ext.merge(exportColumnI, {
                        displayZeroes: gridColumnA[i].displayZeroes
                    });

                // If current grid column - is column, currently used for sorting,
                // we pick sorting direction, and column title width
                if (gridColumnA[i].sortState)
                    Ext.merge(exportColumnI, {
                        sortState: gridColumnA[i].sortState.toLowerCase(),
                        titleWidth: Indi.metrics.getWidth(gridColumnA[i].text)
                    });

                // Push the data object to array
                exportColumnA.push(exportColumnI);
            }
        }

        // Return
        return exportColumnA;
    },

    /**
     * Builds full request string (uri + query string) for retrieving current rowset in a format,
     * identified by `format` argument. Currently 'excel' and 'pdf' values of that argument are supported
     *
     * @param format
     * @return {String}
     */
    rowsetExportQuery: function(format) {
        var me = this, i, request = me.storeLastRequest().replace('format/json/', 'format/' + format + '/'),
            columns = 'columns=' + encodeURIComponent(JSON.stringify(me['_rowsetExport$' + Indi.ucfirst(format) + 'ColumnA']()));

        // Check if there is color-filters within used filters, and if so, we append a _xlsLabelWidth
        // property for each object, that is representing a color-filter in request
        for (i = 0; i < me.ti().filters.length; i++) {
            if (me.ti().filters[i].foreign('fieldId').foreign('elementId').alias == 'color') {
                var reg = new RegExp('(%7B%22' + me.ti().filters[i].foreign('fieldId').alias + '%22%3A%5B[0-9]{1,3}%2C[0-9]{1,3}%5D)');
                request = request.replace(reg, '$1' + encodeURIComponent(',"_xlsLabelWidth":"' + Indi.metrics.getWidth(me.ti().filters[i].foreign('fieldId').title + '&nbsp;-&raquo;&nbsp;') + '"'));
            }
        }

        // Return request string
        return request + '&' + columns;
    },

    // @inheritdoc
    rowsetSummary: function(grid) {
        var me = this, grid = grid || Ext.getCmp(me.rowset.id), summary = {}, view = grid.getView(), columnA = [];

        if (view.headerCt) {
            columnA = view.headerCt.getGridColumns();
        } else {
            if (view.lockedView) columnA = columnA.concat(view.lockedView.headerCt.getGridColumns());
            if (view.normalView) columnA = columnA.concat(view.normalView.headerCt.getGridColumns());
        }

        // Pick summary definition from grid columns's summaries types definitions, if used
        columnA.forEach(function(r, i){
            if (r.summaryType && !r.summaryText)
                summary[r.summaryType] = Ext.isArray(summary[r.summaryType])
                    ? summary[r.summaryType].concat([r.dataIndex])
                    : [r.dataIndex];

        });

        // Return
        return summary;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-grid',
            columns: me.gridColumnA(),
            store: me.getStore(),
            dockedItems: me.rowsetDockedA(),
            plugins: me.rowsetPluginA(),
            listeners: {
                boxready: function() {
                    me.gridColumnAFit();
                }
            }
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        me.callParent();

        // Attach key map
        me.keyMap();
    },

    /**
     * Config for 'cellediting' grid plugin
     */
    rowsetPlugin$Cellediting: {
        ptype: 'cellediting',
        triggerEvent: 'cellsecondclick',
        listeners: {
            edit: function(editor, e) {
                var grid = editor.grid, ctx = grid.ctx();

                // Make sure pressing ENTER will not cause call of it's ordinary handler
                grid.preventEnter = true;

                // Try to save
                ctx.recordRemoteSave(e.record, e.rowIdx + 1);
            }
        }
    },

    /**
     * 'checkchange' listener, for use with 'xtype: checkcolumn'
     *
     * @param checkcolumn
     * @param rowIndex
     * @param checked
     * @param eOpts
     */
    gridColumnCheckChange: function(checkcolumn, rowIndex, checked, eOpts) {
        var me = this.ctx(), s = me.getStore(), r = s.getAt(rowIndex), aix = s.indexOfTotal(r) + 1;

        // Try to save
        me.recordRemoteSave(r, aix);
    }
});

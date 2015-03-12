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
         * Docked items special config
         */
        docked: {
            items: [{alias: 'paging'}],
            inner: {
                paging: ['-', {alias: 'excel'}]
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
                if (this.multiSelect && selectionModel.view.getFeature(0).ftype == 'summary')
                    selectionModel.view.getFeature(0).refresh();

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
    gridColumnDefault: function(field) {

        // Default column config
        return {
            id: this.bid() + '-rowset-grid-column-' + field.alias,
            header: field.title,
            dataIndex: field.alias,
            sortable: true,
            align: function(){
                return (field.storeRelationAbility == 'none' &&
                    [3,5].indexOf(parseInt(field.columnTypeId)) != -1) ? 'right' : 'left';
            }(),
            renderer: function (value) {
                if (String(value).match(/<\?/)) return Ext.util.Format.htmlEncode(value);
                if (String(value).match(/ class="i-color-box"/)) return '<div class="i-color-box-wrap">'+value+'</div>';
                return value;
            }
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

        if (!columnA[1].columns) columnA[1].flex = 1;

        return columnA;
    },

    gridColumnXNumber: function(column, field) {
        return {
            thousandSeparator: ' ',
            decimalSeparator: '.',
            decimalPrecision: 0,
            displayZeroes: true,
            renderer: function(v, m, r, i, c, s) {
                var column = this.xtype == 'gridcolumn' ? this : this.headerCt.getGridColumns()[c];
                if (column.displayZeroes === false && parseFloat(v) == 0) return '';
                return Indi.numberFormat(v, column.decimalPrecision, column.decimalSeparator, column.thousandSeparator);
            }
        }
    },

    gridColumnXPrice: function(column, field) {
        return Ext.merge(this.gridColumnXNumber(column, field), {
            displayZeroes: true,
            decimalPrecision: 2
        });
    },

    gridColumnXMove: function(column, field) {
        return {
            hidden: true
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
        var me = this, colI, field, columnA = [], columnI, columnX, eColumnX, column$, eColumn$, eColumnSummaryX;

        // Other columns
        for (var i = 0; i < colA.length; i++) {

            // Get current col
            colI = colA[i];

            // If current col - is a group col
            if (colI._nested && colI._nested.grid && colI._nested.grid.length) {

                columnI = {
                    text: colI.title,
                    columns: me.gridColumnADeep(colI._nested.grid)
                }

                // Add column
                columnA.push(columnI);

            // Else
            } else {

                // Setup a shortcut for a grid field
                field = me.ti().fields.r(colI.fieldId);

                // Get default column config
                columnI = me.gridColumnDefault(field);

                // Apply specific control element config, as columns control elements/xtypes may be different
                eColumnX = 'gridColumnX' + Indi.ucfirst(field.foreign('elementId').alias);
                if (Ext.isFunction(me[eColumnX]) || Ext.isObject(me[eColumnX])) {
                    columnX = Ext.isFunction(me[eColumnX]) ? me[eColumnX](columnI, field) : me[eColumnX];
                    columnI = Ext.isObject(columnX) ? Ext.merge(columnI, columnX) : columnX;
                }

                // Apply column custom config
                eColumn$ = 'gridColumn$' + Indi.ucfirst(field.alias);
                if (Ext.isFunction(me[eColumn$]) || Ext.isObject(me[eColumn$])) {
                    column$ = Ext.isFunction(me[eColumn$]) ? me[eColumn$](columnI, field) : me[eColumn$];
                    columnI = Ext.isObject(column$) ? Ext.merge(columnI, column$) : column$;
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

                    // Get tr
                    tr = grid.view.el.down('tr.x-grid-row-summary');

                    // Apply summary cell style
                    Ext.defer(function(){
                        td = tr.down('td.x-grid-cell-' + grid.id + '-column-' + dataIndex);
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
    gridColumnAFit: function(grid) {

        // Suspend layouts
        Ext.suspendLayouts();

        // Setup auxiliary variables
        var me = this, grid = grid || Ext.getCmp(me.rowset.id), columnA = grid.getView().headerCt.getGridColumns(),
            widthA = [], px = {ellipsis: 18, sort: 18}, store = grid.getStore(), total = 0, i, j, cellWidth,
            visible = grid.getWidth() - (grid.getView().hasScrollY() ? 16 : 0), scw = me.rowset.smallColumnWidth,
            fcwf = me.rowset.firstColumnWidthFraction, sctw = 0, fcw, hctw = 0, busy = 0, free;

        // For each column, mapped to a store field
        for (i = 0; i < columnA.length; i++) {

            // Get initial column width, based on a column title metrics
            widthA[i] = Indi.metrics.getWidth(columnA[i].text) + px.ellipsis;

            // Increase the width of a column, that store is sorted by, to provide an additional amount
            // of width for sort icon, that is displayed next after column title, within the same column
            if (columnA[i].dataIndex == me.ti().section.defaultSortFieldAlias) widthA[i] += px.sort;

            // Increase the width, to fit data, rendered within any cell under current column
            store.each(function(r){
                cellWidth = Indi.metrics.getWidth(typeof columnA[i].renderer == 'function'
                    ? columnA[i].renderer(r.get(columnA[i].dataIndex))
                    : r.get(columnA[i].dataIndex)) + px.ellipsis;
                if (cellWidth > widthA[i]) widthA[i] = cellWidth;
            });

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
        columnA[1].setWidth((free = visible - busy) > fcw ? free : fcw);

        // Resume layouts
        Ext.resumeLayouts(true);
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
        if (me.preventViewFocus) me.preventViewFocus = false; else grid.getView().focus();

        // Setup last row autoselection, if need
        if (me.ti().scope.aix) {

            // Calculate row index value, relative to current page
            var index = parseInt(me.ti().scope.aix) - 1 - (parseInt(me.ti().scope.page) - 1) *
                parseInt(me.ti().section.rowsOnPage);

            // If such row (row at that index) exists in grid - selectit
            if (grid.getStore().getAt(index)) grid.selModel.select(index, true);
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
                fn:  function(){
                    var btn = Ext.getCmp(me.bid() + '-docked-inner$form'); if (btn) btn.press();
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

        // 'Excel' item cfg
        return {
            id: me.bid() + '-rowset-docked-inner$excel',
            iconCls: 'i-btn-icon-xls',
            tooltip: Indi.lang.I_EXPORT_EXCEL,
            handler: function(){

                // Start preparing request string
                var request = me.ctx().storeLastRequest().replace('json/1/', 'excel/1/');

                // Get grid component id
                var gridCmpId = me.ctx().bid() + '-rowset-grid';

                // Get grid columns
                var gridColumnA = Ext.getCmp(gridCmpId).columns;

                // Define and array for storing column info, required for excel columns building
                var excelColumnA = [];

                // Setup a multiplier, for proper column width calculation
                var multiplier = screen.availWidth/Ext.getCmp(gridCmpId).getWidth();

                // Collect needed data about columns
                for (var i = 0; i < gridColumnA.length; i++) {
                    if (gridColumnA[i].hidden == false) {

                        // Prepare the data object for excel column
                        var excelColumnI = {
                            title: gridColumnA[i].text,
                            dataIndex: gridColumnA[i].dataIndex,
                            align: gridColumnA[i].align,
                            width: Math.ceil(gridColumnA[i].getWidth() * multiplier)
                        };

                        // If current grid column - is column, currently used for sorting,
                        // we pick sorting direction, and column title width
                        if (gridColumnA[i].sortState) {
                            excelColumnI = $.extend(excelColumnI, {
                                sortState: gridColumnA[i].sortState.toLowerCase(),
                                titleWidth: Indi.metrics.getWidth(gridColumnA[i].text)
                            })
                        }

                        // Push the data object to array
                        excelColumnA.push(excelColumnI);
                    }
                }

                // Set column info as a request variable
                var columns = 'columns=' + encodeURIComponent(JSON.stringify(excelColumnA));

                // Check if there is color-filters within used filters, and if so, we append a _xlsLabelWidth
                // property for each object, that is representing a color-filter in request
                for (var i = 0; i < me.ti().filters.length; i++) {
                    if (me.ti().filters[i].foreign('fieldId').foreign('elementId').alias == 'color') {
                        var reg = new RegExp('(%7B%22' + me.ti().filters[i].foreign('fieldId').alias + '%22%3A%5B[0-9]{1,3}%2C[0-9]{1,3}%5D)');
                        request = request.replace(reg, '$1' + encodeURIComponent(',"_xlsLabelWidth":"' + Indi.metrics.getWidth(me.ti().filters[i].foreign('fieldId').title + '&nbsp;-&raquo;&nbsp;') + '"'));
                    }
                }

                // Do request
                window.location = request + '&' + columns;
            }
        }
    },

    // @inheritdoc
    rowsetSummary: function() {
        var me = this, grid = Ext.getCmp(me.rowset.id), summary = {};

        // Pick summary definition from grid columns's summaries types definitions, if used
        grid.headerCt.getGridColumns().forEach(function(r, i){
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
    }
});

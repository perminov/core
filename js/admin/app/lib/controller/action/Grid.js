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
        border: 0,

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
            itemdblclick: function() {
                var btn = Ext.getCmp(this.ctx().bid() + '-toolbar-master-button-form'); if (btn) btn.handler();
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
    gridColumnDefault: function(column) {

        // Default column config
        return {
            id: this.bid() + '-rowset-grid-column-' + column.alias,
            header: column.title,
            dataIndex: column.alias,
            cls: 'i-grid-column-filtered',
            sortable: true,
            align: function(){
                return (column.storeRelationAbility == 'none' &&
                    [3,5].indexOf(parseInt(column.columnTypeId)) != -1) ? 'right' : 'left';
            }(),
            hidden: !!(column.alias == 'move')
        }
    },

    /**
     * Build and return an array, containing column definitions for grid panel
     *
     * @return {Array}
     */
    gridColumnA: function (){

        // Id column
        var columnA = [], column$Id = this.gridColumn$Id(), columnI, columnICustom;

        // Append Id column
        if (column$Id) columnA.push(column$Id);

        // Other columns
        for (var i = 0; i < this.ti().gridFields.length; i++) {
            columnI = this.gridColumnDefault(this.ti().gridFields[i]);
            columnICustom = 'gridColumn$'+Indi.ucfirst(this.ti().gridFields[i].alias);
            if (typeof this[columnICustom] == 'function') columnI = this[columnICustom](columnI);
            if (columnI) columnA.push(columnI);
        }

        // Setup flex for first non-hidden column
        columnA[1].flex = 1;

        // Return array
        return columnA;
    },

    /**
     * Adjust grid columns widths, for widths to match column contents
     */
    gridColumnAFit: function() {
        var grid = Ext.getCmp(this.bid() + '-rowset-grid');
        var columnWidths = {};
        var totalColumnsWidth = 0;
        for(var i in grid.columns) {
            if (grid.columns[i].hidden == false) {
                columnWidths[i] = Indi.metrics.getWidth(grid.columns[i].text) + 12;
                if (grid.columns[i].dataIndex == this.ti().section.defaultSortFieldAlias) {
                    columnWidths[i] += 12;
                }
                for (var j = 0; j < grid.getStore().data.items.length; j++) {
                    var cellWidth = Indi.metrics.getWidth(grid.getStore().data.items[j].data[grid.columns[i].dataIndex]) + 12;
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
                    grid.columns[i].setWidth(columnWidths[i]);
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
            var firstColumnWidth = Math.ceil(totalGridWidth*this.rowset.firstColumnWidthFraction);
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
    },

    /**
     * Callback for store load, will be fired if current section type = 'grid'
     */
    storeLoadCallbackDefault: function() {

        // Call parent
        this.callParent();

        // Get the grid panel object
        var grid = Ext.getCmp(this.bid() + '-rowset-grid');

        // Set the focus on grid, to automatically provide an ability to use keyboard
        // cursor to navigate through rows
        grid.getView().focus();

        // Setup last row autoselection, if need
        if (this.ti().scope.aix) {

            // Calculate row index value, relative to current page
            var index = parseInt(this.ti().scope.aix) - 1 - (parseInt(this.ti().scope.page) - 1) *
                parseInt(this.ti().section.rowsOnPage);

            // If such row (row at that index) exists in grid - selectit
            if (grid.getStore().getAt(index)) grid.selModel.select(index, true);
        }

        // Add keyboard event handelers
        grid.body.addKeyMap({
            eventName: "keyup",
            binding: [{
                key: Ext.EventObject.ENTER,
                fn:  function(){
                    var btn = Ext.getCmp(this.ctx().bid() + '-button-form'); if (btn) btn.handler();
                },
                scope: this
            }]
        });

        // Adjust grid column widths
        this.gridColumnAFit();
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

        // 'Excel' item cfg
        return {
            text: '',
            iconCls: 'i-btn-icon-xls',
            tooltip: Indi.lang.I_EXPORT_EXCEL,
            handler: function(){

                // Start preparing request string
                var request = this.ctx().storeLastRequest().replace('json/1/', 'excel/1/');

                // Get grid component id
                var gridCmpId = this.ctx().bid() + '-rowset-grid';

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
                for (var i = 0; i < this.ctx().ti().filters.length; i++) {
                    if (this.ctx().ti().filters[i].foreign('fieldId').foreign('elementId').alias == 'color') {
                        var reg = new RegExp('(%7B%22' + this.ctx().ti().filters[i].foreign('fieldId').alias + '%22%3A%5B[0-9]{1,3}%2C[0-9]{1,3}%5D)');
                        request = request.replace(reg, '$1' + encodeURIComponent(',"_xlsLabelWidth":"' + Indi.metrics.getWidth(this.ctx().ti().filters[i].foreign('fieldId').title + '&nbsp;-&raquo;&nbsp;') + '"'));
                    }
                }

                // Do request
                window.location = request + '&' + columns;
            }
        }
    },

    /**
     * Builds and return an array of panels, that will be used to represent the major UI contents.
     * Currently is consists only from this.rowset form panel configuration
     *
     * @return {Array}
     */
    panelItemA: function() {

        // Panels array
        var itemA = [], rowsetItem = this.rowsetPanel();

        // Append rowset panel
        if (rowsetItem) itemA.push(rowsetItem);

        // Return panels array
        return itemA;
    },

    /**
     * Build an return main panel's rowset panel config object
     *
     * @return {*}
     */
    rowsetPanel: function() {
        return this.rowset;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup id
        me.id = me.bid();

        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-grid',
            columns: me.gridColumnA(),
            store: me.getStore(),
            dockedItems: me.rowsetDockedA()
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        this.callParent();
    }
});

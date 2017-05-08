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
            ftype: 'grouping',
            groupHeaderTpl: '{name}'
        }, {
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
                var cls = [];

                // Append 'i-grid-row-disabled' css class if need
                if (row.raw._system && row.raw._system.disabled) cls.push('i-grid-row-disabled');

                // Append 'i-grid-row-m4d-(1|2)' css class if need
                if (row.raw.$keys && row.raw.$keys.hasOwnProperty('m4d')) cls.push('i-grid-row-m4d-' + row.raw.$keys.m4d);

                // Return whitespace-separated list of css clases
                return cls.join(' ');
            },
            loadMask: {
                shadow: false,
                msg: Ext.LoadMask.prototype.msg,
                autoRender: true,
                setZIndex: function(index) {
                    var me = this, owner = me.activeOwner, w;

                    if (owner) {
                        // it seems silly to add 1 to have it subtracted in the call below,
                        // but this allows the x-mask el to have the correct z-index (same as the component)
                        // so instead of directly changing the zIndexStack just get the z-index of the owner comp
                        index = parseInt(owner.el.getStyle('zIndex'), 10) + 1;

                    } else if (w = me.container.up('.x-window[id^=desktopwindow-]')) {
                        index = w.zindex + 9;
                    }

                    me.getMaskEl().setStyle('zIndex', index - 1);
                    return me.mixins.floating.setZIndex.apply(me, arguments);
                }
            },
            cellOverflow: true,
            listeners: {
                beforeitemkeydown: function(view, r, d, i, e) {
                    if (e.altKey) return false;
                },
                itemkeydown: function(view, row, item, index, e) {

                    // Load previous page on Page Up, if need
                    if (e.keyCode == Ext.EventObject.PAGE_UP
                        &&  index == 0 && view.store.indexOfTotal(row) > 0)
                        view.store.previousPage({callback: function(records){
                            view.getSelectionModel().select(view.store.getCount() - 1);
                        }});

                    // Load previous page on Page Down, if need
                    if (e.keyCode == Ext.EventObject.PAGE_DOWN
                        && index == view.store.getCount() - 1 && view.store.indexOfTotal(row) < view.store.getTotalCount() - 1)
                        view.store.nextPage({callback: function(records){
                            view.getSelectionModel().select(0);
                        }});
                },
                cellmouseover: function(view, td, tdIdx, record, tr, trIdx, e, eOpts) {
                    if (view.cellOverflow) {
                        if (Indi.metrics.getWidth(Ext.get(td).getHTML()) > Ext.get(td).getWidth())
                            Ext.get(td).addCls('i-overflow').selectable();
                    }
                },
                cellmouseout: function(view, td, tdIdx, record, tr, trIdx, e, eOpts) {
                    if (view.cellOverflow) {
                        Ext.get(td).removeCls('i-overflow');
                    }
                },
                cellclick: function(gridview, tdDom, cellIndex, record, trDom, rowIndex, e) {
                    var me = gridview.ctx(), col = gridview.headerCt.getGridColumns()[cellIndex],
                        dataIndex = col['dataIndex'], field = me.ti().fields.r(dataIndex, 'alias'),
                        enumset = field._nested && field._nested.enumset, value, valueItem, valueItemIndex, oldValue, s,
                        canSave = me.ti().actions.r('save', 'alias'), cb;

                    // If 'Save' action is accessible, and column is linked to 'enumset' field
                    // and that field is not in the list of disabled fields - provide some kind
                    // of cell-editor functionality, so enumset values can be switched from one to another
                    if (!col.initialConfig.editor && canSave && enumset && !me.ti().disabledFields.r(field.id, 'fieldId')
                        && field.storeRelationAbility == 'one'
                        && (col.allowCycle !== false || enumset.length <= 2)) {

                        s = me.getStore();
                        value = record.key(dataIndex);
                        valueItem = enumset.r(value, 'alias');
                        enumset.forEach(function(item, i){
                            if (item.alias == value) valueItemIndex = i;
                            if (item.title.match(/i-color-box/)) cb = true;
                        });
                        if (!cb) return;
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
            itemdblclick: function(gridview) {
                var btn, press = true, cls, dataIndex, col, me = gridview.ctx(), bid = me.bid(), field, canSave;

                // Prevent collisions in cases when item was clicked in cell, having inline-editor,
                // that have 'celldblclick' it's trigger event
                if (me.ti().actions.r('save', 'alias') && me.rowsetPlugin$Cellediting.triggerEvent == 'celldblclick') {

                    // Get td's css classes list
                    cls = Ext.EventObject.getTarget('.x-grid-cell', 10, true).attr('class');

                    // Find a css class, containing bid, and pick column's dataIndex from it
                    Ext.String.trim(cls).split(' ').forEach(function(i){
                        if (i.match(new RegExp(bid))) dataIndex = i.split('-').pop();
                    });

                    // Get column by it's dataIndex
                    if (col = gridview.headerCt.down('[dataIndex="' + dataIndex + '"]')) {

                        // Get field by alias
                        field = me.ti().fields.r(dataIndex, 'alias');

                        // If field is not in the list of disabled fields
                        if (!me.ti().disabledFields.r(field.id, 'fieldId') && col.initialConfig.editor) press = false;
                    }
                }

                // Press 'Details' btn if ok
                btn = Ext.getCmp(this.ctx().bid() + '-docked-inner$form'); if (btn && press) {

                    // Set dblclick flag to prevent cell-editing
                    gridview.dblclick = true;

                    // Press form-action button
                    btn.press();

                    // Reset dblclick flag, with some delay
                    Ext.defer(function() { if (gridview) gridview.dblclick = false; }, 506);
                }
            },

            itemclick: function() {
                if (Ext.EventObject.ctrlKey && !this.multiSelect) {
                    var btn = Ext.getCmp(this.ctx().bid() + '-docked-inner$form'); if (btn) btn.press();
                }
            },
            resize: function(grid, nw, nh, ow, oh) {
                if (!ow || !grid.ctx()) return;
                grid.ctx().gridColumnAFit(null, true);
            }
        }
    },

    /**
     * Builds and returns config for grid Id column
     *
     * @return {Object}
     */
    gridColumn$Id: {header: 'ID', dataIndex: 'id', minWidth: 29, sortable: true, align: 'right', hidden: true, resizable: false},

    /**
     * Builds and returns default/initial config for all grid columns (except 'Id' columns)
     *
     * @return {Object}
     */
    gridColumnDefault: function(field, column) {
        var me = this, tooltip = column.tooltip || (field && field.tooltip), tdClsA = [], cfg;

        // Setup align
        tdClsA.push('i-grid-column-align-' + ((field.storeRelationAbility == 'none' &&
            [3,5,14].indexOf(parseInt(field.columnTypeId)) != -1) ? 'right' : 'left'));

        // Setup presence of .i-grid-column-enumset
        if (parseInt(field.relation) == 6) tdClsA.push('i-grid-column-enumset');

        // Default column config
        cfg = {
            id: me.bid() + '-rowset-grid-column-' + field.alias,
            header: column.alterTitle || field.title,
            dataIndex: field.alias,
            tooltip: tooltip ? {html: tooltip, constrainParent: false, constrainPosition: false} : '',
            cls: tooltip ? 'i-tooltip' : undefined,
            $ctx: me,
            tdCls: tdClsA.join(' '),
            sortable: true,
            editor: column.editor,
            resizable: [1, 4, 5, 6, 7, 13, 23].indexOf(field.elementId) != -1
        };

        // If current column's field is a grouping field - hide it
        if (me.ti().section.groupBy == field.id) cfg.hidden = true;

        // Return
        return cfg;
    },

    /**
     * Build and return an array, containing column definitions for grid panel
     *
     * @return {Array}
     */
    gridColumnA: function() {
        var me = this, columnA = [], column$Id = Ext.isFunction(me.gridColumn$Id) ? me.gridColumn$Id() : me.gridColumn$Id;

        // Append Id column
        if (column$Id) columnA.push(column$Id);

        // Recursively build the columns
        columnA = columnA.concat(me.gridColumnADeep(me.ti().grid));

        // Return array of column config objects
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
        var me = this, ctx = me.ctx() || me.$ctx;
        return ctx ? ctx.gridColumnRenderer_Text.apply(this, arguments) : arguments[0];
    },

    /**
     * Renderer fn for combo-columns
     *
     * @return {*}
     */
    gridColumnXCombo_Renderer: function() {
        var me = this, ctx = me.ctx() || me.$ctx;
        return ctx ? ctx.gridColumnRenderer_Text.apply(this, arguments) : arguments[0];
    },

    /**
     * Renderer fn for textarea-columns
     *
     * @return {*}
     */
    gridColumnXTextarea_Renderer: function() {
        var me = this, ctx = me.ctx() || me.$ctx;
        return ctx ? ctx.gridColumnRenderer_Text.apply(this, arguments) : arguments[0];
    },

    /**
     * Renderer fn for radio-columns
     *
     * @return {*}
     */
    gridColumnXRadio_Renderer: function() {
        var me = this, ctx = me.ctx() || me.$ctx;
        return ctx ? ctx.gridColumnRenderer_Text.apply(this, arguments) : arguments[0];
    },

    /**
     * Renderer fn for number-columns
     *
     * @return {*}
     */
    gridColumnXNumber_Renderer: function(v) {
        var me = this, ctx = me.ctx() || me.$ctx;
        return ctx ? ctx.gridColumnRenderer_Numeric.apply(this, arguments) : arguments[0];
    },

    /**
     * Renderer fn for price-columns
     *
     * @return {*}
     */
    gridColumnXPrice_Renderer: function(v) {
        var me = this, ctx = me.ctx() || me.$ctx;
        return ctx ? ctx.gridColumnRenderer_Numeric.apply(this, arguments) : arguments[0];
    },

    /**
     * Renderer fn for decimal143-columns
     *
     * @return {*}
     */
    gridColumnXDecimal143_Renderer: function(v) {
        var me = this, ctx = me.ctx() || me.$ctx;
        return ctx ? ctx.gridColumnRenderer_Numeric.apply(this, arguments) : arguments[0];
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
            allowBlank: true,
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

    gridColumnEditor_Combo: function(c) {
        var me = this, f = me.ti().fields.r(c.dataIndex, 'alias'), r = me.ti().row;
        if (parseInt(f.relation) == 6 && f.storeRelationAbility == 'one' && !c.editor) return null;
        return {
            xtype: 'combo.cell',
            store: {data: [], ids: [], found: '0', enumset: parseInt(f.relation) == 6, js: '', optionHeight: "14", page: 1},
            field: f
        }
    },

    /**
     *
     * @param c
     * @param f
     * @return {Object}
     */
    gridColumnXRadio_Editor: function(c) {
        return this.gridColumnEditor_Combo(c);
    },

    /**
     *
     * @param c
     * @param f
     * @return {Object}
     */
    gridColumnXCombo_Editor: function(c) {
        return this.gridColumnEditor_Combo(c);
    },

    /**
     *
     * @param c
     * @param f
     * @return {Object}
     */
    gridColumnXMulticheck_Editor: function(c) {
        return this.gridColumnEditor_Combo(c);
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
    gridColumn$Toggle: {
        icon: '/i/admin/btn-icon-toggle.png'
    },

    /**
     * Hide m4d-columns, as 'i-grid-row-m4d-1' css-class added within getRowClass() fn,
     * for each row having '1' as value of `m4d` prop. That provide visual distinction
     * between regular rows and rows, marked for deletion, and this way is more user-friendly
     * than keeping visibility for cell-values who just saying 'Yes' or 'No' within that column
     */
    gridColumn$M4d: {hidden: true},

    /**
     * Default config for fileupload-columns
     */
    gridColumnXUpload: {sortable: false},

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
            eColumnXRenderer, eColumnXEditor, canSave = me.ti().actions.r('save', 'alias');

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

                // Set initial width for locked columns, having minWidth, because Ext.grid.Panel
                // for some reason does not preserve minWidth for locked columns
                if (columnI && columnI.locked && columnI.minWidth) columnI.width = columnI.minWidth;

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
                    if (!canSave || me.ti().disabledFields.r(field.id, 'fieldId')) {
                        columnI.editor = false;
                    } else {
                        eColumnXEditor = 'gridColumnX' + Indi.ucfirst(field.foreign('elementId').alias) + '_Editor';
                        if (Ext.isFunction(me[eColumnXEditor]) || Ext.isObject(me[eColumnXEditor])) {
                            columnI.editor = Ext.isFunction(me[eColumnXEditor]) ? me[eColumnXEditor](columnI, field, Ext.isObject(columnI.editor) ? columnI.editor : {}) : me[eColumnXEditor];
                        } else if (!Ext.isObject(columnI.editor)) {
                            columnI.editor = false;
                        }
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
     * Adjust grid columns widths, for widths to match column contents if possible
     */
    gridColumnAFit: function(grid, noUsageRecalc) {
        var me = this, columnA, i, available, flex = false, ignoreA = [], w = {
            float: {minWidth: 100, reqWidth: 0, avgWidth: 0, qty: 0},
            total: {minWidth:   0, reqWidth: 0, fixWidth: 0}
        };

        // If `grid` arg is not given - use current grid
        if (!grid) grid = Ext.getCmp(me.rowset.id);

        // Suspend layouts
        Ext.suspendLayouts();

        // Get columns
        columnA = noUsageRecalc ? grid.getGridColumns() : grid.getGridColumnsWidthUsage();

        // Foreach column
        for (i in columnA) {

            // Collect total width, required for non-hidden columns contents to be displayed without clipping
            if (!columnA[i].hidden) w.total.reqWidth += columnA[i].widthUsage;

            // If icon is used as a column heading - set column to be not resizable
            if (columnA[i].icon) columnA[i].resizable = false;

            // If column is not resizable
            if (columnA[i].resizable === false) {

                // Collect total width, used by non-hidden columns, that should not be resized in any circumstances
                if (!columnA[i].hidden) w.total.fixWidth += columnA[i].widthUsage;

                // Set column width so contents won't be clipped
                columnA[i].setWidth(columnA[i].widthUsage);

            // Else if column may be resized, manually or automatically
            } else {

                // If column is not hidden
                if (!columnA[i].hidden) {

                    // Collect total width, required for non-hidden floating-width columns
                    w.float.reqWidth += columnA[i].widthUsage;

                    // Increase such columns counter
                    w.float.qty ++;
                }
            }
        }

        // Calc available width as
        available =

            // Main area width (minus it's border width at both left and right sides)
            Ext.getCmp('i-center-center').getWidth() - 2

            // Minus left-scrollbar width, if left-scrollbar exists
            - ((grid.normalGrid ? grid.normalGrid.view.hasScrollY() : grid.view.hasScrollY()) ? 16 : 0);

        // Get count of visible columns
        var nonHidden = 0; for (i in columnA) if (!columnA[i].hidden) nonHidden ++;

        // If available width is sufficient for all columns contents to be displayed without clipping
        if (available >= w.total.reqWidth) {

            // Foreach non-hidden column
            for (i in columnA) if (!columnA[i].hidden) {

                // If column is locked or `flex` flag is non-false
                // Set columns width to be as per actual usage
                if (columnA[i].locked || flex) columnA[i].setWidth(columnA[i].widthUsage);

                // Else set column's `flex` prop and `flex` flag to be 1 (e.g. non false)
                else if (columnA[i].resizable || nonHidden == 1) columnA[i].flex = flex = 1;
            }

        // Else if available width is insufficient
        } else {

            // Calculate average width
            w.float.avgWidth = (available - w.total.fixWidth) / w.float.qty;

            // Foreach column
            columnA.forEach(function(columnI, index) {

                // Ignore hidden and non-resizable columns, as their widths is not a question
                if (columnI.hidden || !columnI.resizable) return;

                // If column's required width that width is smaller than average width
                if (columnI.widthUsage <= w.float.avgWidth) {

                    // Push columns's index into 'ignoreA' array to remember
                    // that there is no need for that column's width to differ from it's `widthUsage` prop
                    ignoreA.push(index);

                    // Increase 'constant' variable by the value of 'itemTitleRequiredWidth' variable,
                    // as widths of items, that are smaller than average width - should not be involved
                    // in the process of width adjustment amount calculation
                    w.total.fixWidth += columnI.widthUsage;
                }
            });

            // Recalculate average width
            w.float.avgWidth = Math.floor((available - w.total.fixWidth)/(w.float.qty - ignoreA.length));

            // Get lost value. We need this because there may be undistributed number of pixels, that
            // we need to redistribute to columns by a one-lost-pixel-to-one-column logic. This will provide
            // a visual impression that all columns have dimensions that allow them to have exact fit within
            // the grid panel, despite on mathematically it's impossible
            var lost = (available - w.total.fixWidth) - w.float.avgWidth * (w.float.qty - ignoreA.length);

            // For each column
            columnA.forEach(function(item, index) {

                // If column's width shouldn't be adjusted
                if (ignoreA.indexOf(index) != -1) {

                    // Set width to be as column contents require
                    item.setWidth(item.widthUsage);

                // Else if item's width should be adjusted
                } else if (item.resizable && !item.hidden) {

                    // Set width to be average or minimum
                    item.setWidth(Math.max(w.float.avgWidth + (lost > 0 ? 1 : 0), Math.min(w.float.minWidth, item.widthUsage)));

                    // Decrease lost
                    lost--;
                }
            });
        }

        // Resume layouts
        Ext.resumeLayouts(true);
    },


    /**
     * Adjust grid columns widths, for widths to match column contents
     */
    gridColumnAFit_backup: function(grid, locked) {

        // Setup auxiliary variables
        var me = this, grid = grid || Ext.getCmp(me.rowset.id), view = grid.getView(), columnA = [],
            widthA = [], widthO = {}, px = {ellipsis: {usual: 18, rownumberer: 12, icon: 12}, sort: 18},
            store = grid.getStore(), total = 0, i, j, k, longestWidth, cell, fnhci = -1,
            visible, scw = me.rowset.smallColumnWidth, fcwf = me.rowset.firstColumnWidthFraction, sctw = 0, fcw,
            hctw = 0, busy = 0, free, longest = [], summaryData, summaryFeature,
            isTree = !!me.ti().fields.r(me.ti().model.tableName + 'Id', 'alias'), level, _longestWidth;

        //console.log('fit', new Error().stack);

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
            me.gridColumnAFit(view.lockedGrid);
            me.gridColumnAFit(view.normalGrid);

            // Return
            return;
        }

        // Get summary data
        if (summaryFeature) summaryData = summaryFeature.generateSummaryData();

        // For each column, mapped to a store field
        for (i = 0; i < columnA.length; i++) {

            // Get initial column width, based on a column title metrics
            widthA[i] = columnA[i].icon ? 16 : Indi.metrics.getWidth(columnA[i].text);

            // Reset level
            level = 0; longest = [];

            // Reset length
            longest[level] = columnA[i].text;

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

                    level = 0;
                    if (Ext.isString(cell) && isTree && columnA[i].dataIndex == 'title') {
                        cell = cell.replace(/&nbsp;/g, ' ');
                        level = cell.match(/^ */)[0].length;
                        if (level) cell = cell.substr(level - 1);
                        if (!longest[level]) longest[level] = cell;
                        else if (cell.length > longest[level].length) longest[level] = cell;
                    }

                    if (cell && cell.length > longest[level].length &&
                        (!cell.match(/class="i-color-box"/) || (cell = Indi.stripTags(cell)).length > longest[level].length))
                        longest[level] = cell;
                });

                // Don't forgot about summaries
                if (columnA[i].summaryType && Ext.isObject(summaryData)) {
                    cell = typeof columnA[i].renderer == 'function'
                        ? columnA[i].renderer(summaryData[columnA[i].id])
                        : summaryData[columnA[i].id];
                    if (cell.length > longest[0].length) longest[0] = cell;
                } else if (columnA[i].summaryText) {
                    cell = columnA[i].summaryText;
                    if (cell.length > longest[0].length) longest[0] = cell;
                }

            // Else if column does not have `dataIndex` prop
            } else {

                // If column's xtype is 'rownumberer'
                if (columnA[i].xtype == 'rownumberer') longest[0] = (store.last() ? store.indexOfTotal(store.last()) + 1 : 1) + '';
            }

            // Get width of the longest cell
            longestWidth = Indi.metrics.getWidth(longest[0].toString().replace(/ /g, '&nbsp;'));

            // Mind indents
            if (isTree && columnA[i].dataIndex == 'title') {
                for (k in longest) if (k) {
                    for (var l = 0; l < k; l++) longest[k] = '&nbsp;' + longest[k];
                    if ((_longestWidth = Indi.metrics.getWidth(longest[k])) > longestWidth)
                       longestWidth = _longestWidth;
                }
            }

            // Update widthA[i] if need
            if (longestWidth > widthA[i]) widthA[i] = longestWidth;

            // Append ellipsis space
            if (columnA[i].xtype == 'rownumberer') widthA[i] += px.ellipsis.rownumberer;
            else if (columnA[i].icon) widthA[i] += px.ellipsis.icon;
            else widthA[i] += px.ellipsis.usual;

            // Limit the maximum column width, if such a config was set
            if (columnA[i].maxWidth && widthA[i] > columnA[i].maxWidth) widthA[i] = columnA[i].maxWidth;

            // Increase the total width
            total += widthA[i];
            widthO[columnA[i].dataIndex || columnA[i].xtype] = widthA[i];

            // If column is hidden - sum it's width into `hctw` variable
            if (columnA[i].hidden) hctw += widthA[i]; // else if (fnhci == -1) fnhci = parseInt(i);
        }

        // Remember width, best suitable for grid if it was no width limitations
        grid.widthUsage = parseInt(total - hctw);

        console.log(locked ? 'locked' : 'normal', widthO, grid.widthUsage, visible);

        // If visible width completely match required width
        //if (visible == grid.widthUsage) {

            // For each column, set width
            for (i = 0; i < widthA.length; i++) columnA[i].setWidth(widthA[i]);

        // Else
        /*} else {

            if (fnhci != -1) {

                // Exclude first non-hidden column width from total width
                total -= widthA[fnhci];

                // Detect the first column's width, using it's fraction
                widthA[fnhci] = fcw = Math.ceil(visible * fcwf) || widthA[fnhci];

                // Include first non-hidden column width (regarding `firstColumnWidthFraction` cfg) to total width
                total += widthA[fnhci];
            }

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
            columnA[fnhci].setWidth((free = visible - busy) > fcw ? free : fcw);
        }

        // Increase first non-hidden column's width, if free space is available
        if (locked) {
            columnA[0].setWidth(widthA[0]);
            if (columnA[1]) columnA[1].setWidth(widthA[1]);
        } else {
            columnA[1].setWidth((free = visible - busy) > fcw ? free : fcw);
        }*/

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
        //console.log('1st fit', me.getStore().getCount());
        me.gridColumnAFit();

        // Bind Indi.load(...) for all DOM nodes (within grid), that have 'load' attibute
        me.bindLoads(grid);

        // Bind Indi.load(...) for all DOM nodes (within grid), that have 'jump' attibute
        Ext.defer(function(){
            me.bindJumps(grid);
        }, 100);
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
    gridKeyMap: function() {
        var me = this;

        // Add keyboard event handelers
        if (Ext.getCmp(me.rowset.id) && Ext.getCmp(me.rowset.id).getEl()) Ext.getCmp(me.rowset.id).getEl().addKeyMap({
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
        if (Ext.getCmp(me.rowset.id) && Ext.getCmp(me.rowset.id).getEl()) Ext.getCmp(me.rowset.id).getEl().addKeyMap({
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
            enableOverflow: {
                menuTrigger: {
                    margin: '2 0 0 0'
                },
                menu: {

                }
            },
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
            onBoxReady: function() {

                // If store raw data is available right now
                if (me.ti().scope.pageData) {

                    // Prevent separate autoload
                    Ext.getCmp(me.panel.id).autoLoadStore = false;

                    // Load raw data straight into the store
                    me.getStore().loadRawData(me.ti().scope.pageData);

                    // Ensure column widths will be recalculated each time grid width was changed
                    this.on('resize', function(grid, w, h, ow, oh) {
                        if (w != ow) {
                            //console.log('3rd fit', w, ow);
                            //me.gridColumnAFit();
                        }
                    });

                // Else fit grid as it's required because window height should be calculated
                } else me.gridColumnAFit();
            },
            listeners: {
                boxready: function() {
                    //console.log('2nd fit');
                    //me.gridColumnAFit();
                }
            }
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        me.callParent();

        // Attach key map
        me.gridKeyMap();
    },

    /**
     * Config for 'cellediting' grid plugin
     */
    rowsetPlugin$Cellediting: {
        ptype: 'cellediting',
        triggerEvent: 'cellsecondclick',
        listeners: {
            edit: function(editor, e, eOpts) {
                var grid = editor.grid, ctx = grid.ctx();

                // Make sure pressing ENTER will not cause call of it's ordinary handler
                grid.preventEnter = true;

                // Try to save
                ctx.recordRemoteSave(e.record, e.rowIdx + 1, null, function(json){
                    var cell = editor.view.getCellByPosition({
                        column: e.colIdx,
                        row: e.rowIdx
                    });
                    Ext.fly(cell).addCls('i-grid-cell-editor-focus');

                    // Call additional callback, defined as one of listeners, and pass json-decoded response
                    if (Ext.isFunction(eOpts.remotesave))
                        eOpts.remotesave.call(editor, e, json);
                });
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

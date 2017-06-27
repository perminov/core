/**
 * Here we provide an ability for grid cell to become editable each time some cell
 * got two subsequent clicks, that, however, can't be recognized as dblclick-event.
 */
Ext.override(Ext.grid.plugin.Editing, {

    // private. Used if we are triggered by a cellclick event
    onCellSecondClick: function(view, cell, colIdx, record, row, rowIdx, e) {
        var thisClick = colIdx + ':' + rowIdx, lastCell, me = this;
        if (view.lastClickCell) {
            lastCell = view.getCellByPosition({
                column: parseInt(view.lastClickCell.split(':')[0]),
                row: parseInt(view.lastClickCell.split(':')[1])
            });
            if (Ext.fly(lastCell)) Ext.fly(lastCell).removeCls('i-grid-cell-editor-focus');
        }
        if (Ext.fly(view.getHeaderAtIndex(colIdx).getEl()).hasCls('i-grid-column-editable'))
            Ext.fly(cell).addCls('i-grid-cell-editor-focus');
        if (thisClick != view.lastClickCell) {
            view.lastClickCell = colIdx + ':' + rowIdx;
            view.lastClickTime = new Date().getTime();
            return;
        } else if (view.lastClickTime && new Date().getTime() - view.lastClickTime < 506) {
            view.lastClickTime = new Date().getTime();
            return;
        }

        // cancel editing if the element that was clicked was a tree expander
        if(!view.expanderSelector || !e.getTarget(view.expanderSelector)) {
            Ext.defer(function(){
                if (!view.dblclick) {
                    if (Ext.fly(cell)) Ext.fly(cell).removeCls('i-grid-cell-editor-focus');
                    me.startEdit(record, view.getHeaderAtIndex(colIdx));
                }
            }, 506);
        }
    },

    // private
    initEditTriggers: function() {
        var me = this,
            view = me.view;

        // Listen for the edit trigger event.
        if (me.triggerEvent == 'cellfocus') {
            me.mon(view, 'cellfocus', me.onCellFocus, me);
        } else if (me.triggerEvent == 'rowfocus') {
            me.mon(view, 'rowfocus', me.onRowFocus, me);
        } else if (me.triggerEvent == 'cellsecondclick') {
            me.mon(view, 'cellclick', me.onCellSecondClick, me);
        } else {

            // Prevent the View from processing when the SelectionModel focuses.
            // This is because the SelectionModel processes the mousedown event, and
            // focusing causes a scroll which means that the subsequent mouseup might
            // take place at a different document XY position, and will therefore
            // not trigger a click.
            // This Editor must call the View's focusCell method directly when we recieve a request to edit
            if (view.selModel.isCellModel) {
                view.onCellFocus = Ext.Function.bind(me.beforeViewCellFocus, me);
            }

            // Listen for whichever click event we are configured to use
            me.mon(view, me.triggerEvent || ('cell' + (me.clicksToEdit === 1 ? 'click' : 'dblclick')), me.onCellClick, me);
        }

        // add/remove header event listeners need to be added immediately because
        // columns can be added/removed before render
        me.initAddRemoveHeaderEvents()
        // wait until render to initialize keynav events since they are attached to an element
        view.on('render', me.initKeyNavHeaderEvents, me, {single: true});
    },

    // private
    onEnterKey: function(e) {
        var me = this,
            grid = me.grid,
            selModel = grid.getSelectionModel(),
            record,
            pos,
            columnHeader = grid.headerCt.getHeaderAtIndex(0);

        // Calculate editing start position from SelectionModel
        // CellSelectionModel
        if (selModel.getCurrentPosition) {
            pos = selModel.getCurrentPosition();
            if (pos) {
                record = grid.store.getAt(pos.row);
                columnHeader = grid.headerCt.getHeaderAtIndex(pos.column);
            }
        }
        // RowSelectionModel
        else {
            record = selModel.getLastSelected();
        }

        // If there was a selection to provide a starting context...
        if (record && columnHeader && me.triggerEvent != 'cellsecondclick') {
            me.startEdit(record, columnHeader);
        }
    }
});
Ext.override(Ext.grid.plugin.CellEditing, {
    /**
     * Starts editing the specified record, using the specified Column definition to define which field is being edited.
     * @param {Ext.data.Model} record The Store data record which backs the row to be edited.
     * @param {Ext.grid.column.Column} columnHeader The Column object defining the column to be edited.
     * @return `true` if editing was started, `false` otherwise.
     */
    startEdit: function(record, columnHeader) {
        var me = this,
            context = me.getEditingContext(record, columnHeader),
            value, ed;

        // Complete the edit now, before getting the editor's target
        // cell DOM element. Completing the edit causes a row refresh.
        // Also allows any post-edit events to take effect before continuing
        me.completeEdit();

        // Cancel editing if EditingContext could not be found (possibly because record has been deleted by an intervening listener), or if the grid view is not currently visible
        if (!context || !me.grid.view.isVisible(true)) {
            return false;
        }

        record = context.record;
        columnHeader = context.column;

        // See if the field is editable for the requested record
        if (columnHeader && !columnHeader.getEditor(record)) {
            return false;
        }

        value = record.key(columnHeader.dataIndex) === null ? record.get(columnHeader.dataIndex) : record.key(columnHeader.dataIndex);

        context.originalValue = context.value = value;
        if (me.beforeEdit(context) === false || me.fireEvent('beforeedit', me, context) === false || context.cancel) {
            return false;
        }

        ed = me.getEditor(record, columnHeader);

        // Whether we are going to edit or not, ensure the edit cell is scrolled into view
        me.grid.view.cancelFocus();
        me.view.focusCell({
            row: context.rowIdx,
            column: context.colIdx
        });
        if (ed) {
            if (ed.field.field && (!ed.field.store.enumset || columnHeader.initialConfig.editor)) {
                var ctx = me.view.ctx(), ti = ctx.ti(), section = ti.section, scope = ti.scope,
                    url = '/' + section.alias + '/form/id/' + record.get('id') + '/ph/' + scope.hash
                        + '/aix/' + context.rowIdx + '/';

                // Show loader
                Indi.app.loader();

                // Make odata-request
                Ext.Ajax.request({
                    url: Indi.pre.replace(/\/$/, '') + url + 'odata/' + ed.field.name + '/',
                    success: function(response) {

                        // Convert response.responseText to JSON object
                        var json = JSON.parse(response.responseText);

                        // Refresh store
                        ed.field.resetInfo(value, json);
                        ed.field[ed.field.store.ids.length ? 'enable' : 'disable']();
                        ed.field.fetchUrl = url;

                        // Show editor
                        me.editTask.delay(0, me.showEditor, me, [ed, context, value]);
                    }
                });
            } else {
                me.editTask.delay(15, me.showEditor, me, [ed, context, value]);
            }
            return true;
        }

        return false;
    },

    onEditComplete : function(ed, value, startValue) {
        var me = this,
            grid = me.grid,
            activeColumn = me.getActiveColumn(),
            sm = grid.getSelectionModel(),
            record, editorCmp = ed.field, indiField = editorCmp.field, cellVal;

        if (activeColumn) {
            record = me.context.record;

            me.setActiveEditor(null);
            me.setActiveColumn(null);
            me.setActiveRecord(null);

            if (!me.validateEdit()) {
                return;
            }

            // Only update the record if the new value is different than the
            // startValue. When the view refreshes its el will gain focus
            if (!record.isEqual(value, startValue)) {
                if (indiField) {
                    if (String(record.key(activeColumn.dataIndex)) != value) {
                        if (indiField.storeRelationAbility == 'one') {
                            cellVal = parseInt(value) || indiField.relation == '6' ? editorCmp.r(value).raw : '';
                            cellVal = cellVal.replace(/(class="i-color-box" style="background:\s*[^u][^>]+><\/span>).*$/, '$1');
                            record.set(activeColumn.dataIndex, cellVal);
                            record.key(activeColumn.dataIndex, value);
                        } else if (indiField.storeRelationAbility == 'many') {
                            value = value.length ? value.split(',') : [];
                            var titleA = [], title;
                            value.forEach(function(item){
                                titleA.push(editorCmp.r(item).raw);
                            });
                            record.set(activeColumn.dataIndex, titleA.join(', '));
                            record.key(activeColumn.dataIndex, value.join(','));
                        }
                    }
                } else {
                    record.set(activeColumn.dataIndex, value);
                }
            }

            // Restore focus back to the view's element.
            if (sm.setCurrentPosition) {
                sm.setCurrentPosition(sm.getCurrentPosition());
            }
            grid.getView().getEl(activeColumn).focus();

            me.context.value = value;
            me.fireEvent('edit', me, me.context);
        }
    },

    showEditor: function(ed, context, value) {
        var me = this,
            record = context.record,
            columnHeader = context.column,
            sm = me.grid.getSelectionModel(),
            selection = sm.getCurrentPosition();

        me.context = context;
        me.setActiveEditor(ed);
        me.setActiveRecord(record);
        me.setActiveColumn(columnHeader);

        // Select cell on edit only if it's not the currently selected cell
        if (sm.selectByPosition && (!selection || selection.column !== context.colIdx || selection.row !== context.rowIdx)) {
            sm.selectByPosition({
                row: context.rowIdx,
                column: context.colIdx
            });
        }

        ed.startEdit(me.getCell(record, columnHeader), value);

        if (typeof ed.field.onTriggerClick == 'function') {
            if (Ext.isFunction(ed.field.expand)) ed.field.expand();
            ed.field.focus(false, true);
        } //+

        me.editing = true;
        me.scroll = me.view.el.getScroll();
    }
});

/**
 * Here we provide an ability for grid cell to become editable each time some cell
 * got two subsequent clicks, that, however, can't be recognized as dblclick-event.
 */
Ext.override(Ext.grid.plugin.Editing, {

    // private. Used if we are triggered by a cellclick event
    onCellSecondClick: function(view, cell, colIdx, record, row, rowIdx, e) {
        var thisClick = colIdx + ':' + rowIdx, lastCell;
        if (view.lastClickCell) {
            lastCell = view.getCellByPosition({
                column: parseInt(view.lastClickCell.split(':')[0]),
                row: parseInt(view.lastClickCell.split(':')[1])
            });
            Ext.fly(lastCell).removeCls('i-grid-cell-editor-focus');
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
            Ext.fly(cell).removeCls('i-grid-cell-editor-focus');
            this.startEdit(record, view.getHeaderAtIndex(colIdx));
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

/**
 * Grid view adjustment
 */
Ext.override(Ext.grid.View, {
    hasScrollY: function() {
        var me = this, gridTable = me.getEl().select('.x-grid-table').first();
        return gridTable && (gridTable.getHeight() > me.getHeight());
    }
});

/**
 * Grid panel adjustments
 */
Ext.override(Ext.grid.Panel, {

    /**
     * This function's code is picked from Ext.grid.PagingScroller
     */
    getFirstVisibleRowIndex: function(returnRecord) {
        var me = this,
            view = me.view,
            scrollTop = view.el.dom.scrollTop,
            rows,
            count,
            i,
            rowBottom;

        if (me.variableRowHeight) {
            rows = view.getNodes();
            count = rows.length;
            if (!count) {
                return;
            }
            rowBottom = Ext.fly(rows[0]).getOffsetsTo(view.el)[1];
            for (i = 0; i < count; i++) {
                rowBottom += rows[i].offsetHeight;

                // Searching for the first visible row, and off the bottom of the clientArea, then there's no visible first row!
                if (rowBottom > view.el.dom.clientHeight) {
                    return;
                }

                // Return the index *within the total dataset* of the first visible row
                // We cannot use the loop index to offset from the table's start index because of possible intervening group headers.
                if (rowBottom > 0) {
                    console.log(view.getRecord(rows[i]));
                    return view.getRecord(rows[i]).index;
                }
            }
        } else {
            return Math.floor(scrollTop / me.rowHeight);
        }
    },

    /**
     * This function's code is picked from Ext.grid.PagingScroller
     */
    getLastVisibleRowIndex: function() {
        var me = this,
            store = me.store,
            view = me.view,
            clientHeight = view.el.dom.clientHeight,
            rows,
            count,
            i,
            rowTop;

        if (me.variableRowHeight) {
            rows = view.getNodes();
            if (!rows.length) {
                return;
            }
            count = store.getCount() - 1;
            rowTop = Ext.fly(rows[count]).getOffsetsTo(view.el)[1] + rows[count].offsetHeight;
            for (i = count; i >= 0; i--) {
                rowTop -= rows[i].offsetHeight;

                // Searching for the last visible row, and off the top of the clientArea, then there's no visible last row!
                if (rowTop < 0) {
                    return;
                }

                // Return the index *within the total dataset* of the last visible row.
                // We cannot use the loop index to offset from the table's start index because of possible intervening group headers.
                if (rowTop < clientHeight) {
                    return view.getRecord(rows[i]).index;
                }
            }
        } else {
            return me.getFirstVisibleRowIndex() + Math.ceil(clientHeight / me.rowHeight) + 1;
        }
    }
});

/**
 * Here we override:
 * 1. Ext.view.Table.initFeatures() method, for it to create ftype-named keys (rather than undefined-keys)
 *    for items within featuresMC MixedCollection, for the possibility to get the features using the
 *    grid.getView().getFeature('summary') call instead of grid.getView().getFeature(0) call, where
 *    'summary' - is the value of 'ftype' prop of a certain feature, and 0 - is just an index, what makes
 *    us worry about whether or not, for example, summary-feature is at 0-index, or at some other index, in cases
 *    when multiple different features are used as same time within a view
 */
Ext.override(Ext.view.Table, {

    /**
     * Initializes each feature and bind it to this view.
     * @private
     */
    initFeatures: function(grid) {
        var me = this,
            i,
            features,
            feature,
            len;

        me.featuresMC = new Ext.util.MixedCollection();
        features = me.features = me.constructFeatures();
        len = features ? features.length : 0;
        for (i = 0; i < len; i++) {
            feature = features[i];

            // inject a reference to view and grid - Features need both
            feature.view = me;
            feature.grid = grid;
            me.featuresMC.add(feature.ftype, feature);
            feature.init();
        }
    },

    processItemEvent: function(record, row, rowIndex, e) {
        var me = this,
            cell = e.getTarget(me.cellSelector, row),
            cellIndex = cell ? cell.cellIndex : -1,
            map = me.statics().EventMap,
            selModel = me.getSelectionModel(),
            type = e.type,
            result;

        if (type == 'keydown' && !cell && selModel.getCurrentPosition) {
            // CellModel, otherwise we can't tell which cell to invoke
            cell = me.getCellByPosition(selModel.getCurrentPosition());
            if (cell) {
                cell = cell.dom;
                cellIndex = cell.cellIndex;
            }
        }

        result = me.fireEvent('uievent', type, me, cell, rowIndex, cellIndex, e, record, row);

        if (result === false /*|| me.callParent(arguments) === false*/) {
            return false;
        }

        // Don't handle cellmouseenter and cellmouseleave events for now
        if (type == 'mouseover' || type == 'mouseout') {
            return !((me.fireEvent('cell' + type, me, cell, cellIndex, record, row, rowIndex, e) === false)); // ++
            //return true; // --
        }

        if(!cell) {
            // if the element whose event is being processed is not an actual cell (for example if using a rowbody
            // feature and the rowbody element's event is being processed) then do not fire any "cell" events
            return true;
        }

        return !(
            // We are adding cell and feature events
            (me['onBeforeCell' + map[type]](cell, cellIndex, record, row, rowIndex, e) === false) ||
                (me.fireEvent('beforecell' + type, me, cell, cellIndex, record, row, rowIndex, e) === false) ||
                (me['onCell' + map[type]](cell, cellIndex, record, row, rowIndex, e) === false) ||
                (me.fireEvent('cell' + type, me, cell, cellIndex, record, row, rowIndex, e) === false)
            );
    }
});

/**
 * Here we override:
 * 1. onKeyUp() and onKeyDown() methods. The only change is that second argument `true` replaced with `false`
 *    in me.doDeselect(me.lastFocused, true) calls. We do it to force `selectionchange` event to be fired in case
 *    of deselection made by keyboard UP and DOWN keys, so this way we use to fix this bug, officially known
 *    by ExtJS, see https://www.sencha.com/forum/showthread.php?208702-Problem-with-selectionchange-event-of-selection-model
 *    Despite the mentioned forum thread relates to ExtJS 4.2 (discovered by comparing the code), this bug
 *    is also actual for ExtJS 4.1.1
 */
Ext.override(Ext.selection.RowModel, {
    onKeyUp: function(e) {
        var me = this,
            idx  = me.store.indexOf(me.lastFocused),
            record;

        if (idx > 0) {
            // needs to be the filtered count as thats what
            // will be visible.
            record = me.store.getAt(idx - 1);
            if (e.shiftKey && me.lastFocused) {
                if (me.isSelected(me.lastFocused) && me.isSelected(record)) {
                    // me.doDeselect(me.lastFocused, true);
                    me.doDeselect(me.lastFocused, false);
                    me.setLastFocused(record);
                } else if (!me.isSelected(me.lastFocused)) {
                    me.doSelect(me.lastFocused, true);
                    me.doSelect(record, true);
                } else {
                    me.doSelect(record, true);
                }
            } else if (e.ctrlKey) {
                me.setLastFocused(record);
            } else {
                me.doSelect(record);
                //view.focusRow(idx - 1);
            }
        }
        // There was no lastFocused record, and the user has pressed up
        // Ignore??
        //else if (this.selected.getCount() == 0) {
        //
        //    this.doSelect(record);
        //    //view.focusRow(idx - 1);
        //}
    },

    // Navigate one record down. This could be a selection or
    // could be simply focusing a record for discontiguous
    // selection. Provides bounds checking.
    onKeyDown: function(e) {
        var me = this,
            idx  = me.store.indexOf(me.lastFocused),
            record;

        // needs to be the filtered count as thats what
        // will be visible.
        if (idx + 1 < me.store.getCount()) {
            record = me.store.getAt(idx + 1);
            if (me.selected.getCount() === 0) {
                if (!e.ctrlKey) {
                    me.doSelect(record);
                } else {
                    me.setLastFocused(record);
                }
                //view.focusRow(idx + 1);
            } else if (e.shiftKey && me.lastFocused) {
                if (me.isSelected(me.lastFocused) && me.isSelected(record)) {
                    //me.doDeselect(me.lastFocused, true);
                    me.doDeselect(me.lastFocused, false);
                    me.setLastFocused(record);
                } else if (!me.isSelected(me.lastFocused)) {
                    me.doSelect(me.lastFocused, true);
                    me.doSelect(record, true);
                } else {
                    me.doSelect(record, true);
                }
            } else if (e.ctrlKey) {
                me.setLastFocused(record);
            } else {
                me.doSelect(record);
                //view.focusRow(idx + 1);
            }
        }
    }
});
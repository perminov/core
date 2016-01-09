/**
 * Here we override Ext.Component component, to provide an ability for 'tooltip' config properties to be used for
 * creating Ext.tip.ToolTip objects instead of standart Ext.tip.QuickTip objects
 */
Ext.override(Ext.data.Model, {
    key: function(key, val) {
        var me = this;

        // If model has no `$keys` property within it's `raw` object, or it's not an object - return
        if (!Ext.isObject(me.raw.$keys)) return null;

        // If `$keys` object within model's `raw` property has no own property, named as `key` argument - return
        if (!me.raw.$keys.hasOwnProperty(key)) return null;

        // Return original value for a given key name
        return arguments.length > 1 ? (me.raw.$keys[key] = val) : me.raw.$keys[key];
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
            me.editTask.delay(15, me.showEditor, me, [ed, context, value]);
            return true;
        }

        return false;
    },

    onEditComplete : function(ed, value, startValue) {
        var me = this,
            grid = me.grid,
            activeColumn = me.getActiveColumn(),
            sm = grid.getSelectionModel(),
            record;

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
                console.log(record.key(activeColumn.dataIndex));
                record.set(activeColumn.dataIndex, value);
            }

            // Restore focus back to the view's element.
            if (sm.setCurrentPosition) {
                sm.setCurrentPosition(sm.getCurrentPosition());
            }
            grid.getView().getEl(activeColumn).focus();

            me.context.value = value;
            me.fireEvent('edit', me, me.context);
        }
    }
});

Ext.override(Ext.Editor, {
    /**
     * Starts the editing process and shows the editor.
     * @param {String/HTMLElement/Ext.Element} el The element to edit
     * @param {String} value (optional) A value to initialize the editor with. If a value is not provided, it defaults
     * to the innerHTML of el.
     */
    startEdit : function(el, value) {
        var me = this,
            field = me.field;

        me.completeEdit();

        me.boundEl = Ext.get(el);
        value = Ext.isDefined(value) ? value : Ext.String.trim(me.boundEl.dom.innerText || me.boundEl.dom.innerHTML);

        if (!me.rendered) {
            me.render(me.parentEl || document.body);
        }

        if (me.fireEvent('beforestartedit', me, me.boundEl, value) !== false) {
            me.startValue = value;

            me.show();
            // temporarily suspend events on field to prevent the "change" event from firing when reset() and setValue() are called
            field.suspendEvents();
            field.reset();
            field.setValue(value);
            field.resumeEvents();
            me.realign(true);
            field.focus(false, 10);
            if (field.autoSize) {
                field.autoSize();
            }
            me.editing = true;
            if (typeof field.onTriggerClick == 'function') field.onTriggerClick();
        }
    }
});
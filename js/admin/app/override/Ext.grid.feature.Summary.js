Ext.override(Ext.XTemplate, {
    recurse : function(values, reference) {
        this.recursiveCall = true;
        var returnValue = this.apply(reference ? values[reference] : values);
        this.recursiveCall = false;
        return returnValue;
    }
});

Ext.override(Ext.grid.feature.Grouping, {

    // Create an associated DOM id for the group's header element given the group name
    getGroupHeaderId: function(groupName) {
        return this.view.id + '-hd-' + Indi.stripTags(groupName);
    },

    // Create an associated DOM id for the group's body element given the group name
    getGroupBodyId: function(groupName) {
        return this.view.id + '-bd-' + Indi.stripTags(groupName);
    },

    // Here we replaced "!changedFields ||" with "Ext.isArray(changedFields) &&",
    // as view.refresh() was called after record.commit() call (`type` arg is 'commit')
    // and this forced grid contents scroll to top, unneeded
    onUpdate: function(store, record, type, changedFields){
        var view = this.view;
        if (view.rendered && Ext.isArray(changedFields) && Ext.Array.contains(changedFields, this.getGroupField())) {
            view.refresh();
        }
    }
});

Ext.override(Ext.grid.feature.Summary, {

    /**
     * This override is a part of solution, that fixes the bug, related to summary and grouping features together usage
     *
     * @return {String}
     */
    closeRows: function() {
        return '</tpl>{[this.recursiveCall ? "" : this.printSummaryRow()]}';
    },

    /**
     * Refresh summary row
     */
    refresh: function() {
        if (this.view.rendered) {
            var tpl = Ext.create('Ext.XTemplate',
                '{[this.printSummaryRow()]}',
                this.getFragmentTpl()
            );
            tpl.overwrite(this.getRowEl(), {});
        }
    },

    /**
     * Get summary row element
     *
     * @return {Ext.dom.Element}
     */
    getRowEl: function() {
        return this.view.el.down('tr.x-grid-row-summary');
    },

    /**
     * This function is redeclared for an ability to pick summary data from server-response,
     * in case if response contains such a data
     *
     * @return {Object}
     */
    generateSummaryData: function() {
        var columns, comp, data, fieldData, groups, i, key, length, me, reader, remote, remoteData, root, store;

        me = this;
        data = {};
        store = me.view.store;
        columns = me.view.headerCt.getColumnsForTpl();
        i = 0;
        length = columns.length;
        fieldData = void 0;
        key = void 0;
        comp = void 0;
        i = 0;
        length = columns.length;
        reader = store.proxy.reader;
        remoteData = {};
        groups = me.summaryGroups;
        remote = void 0;
        if (me.remoteRoot && reader.rawData) {
            root = reader.root;
            reader.root = me.remoteRoot;
            reader.buildExtractors(true);
            Ext.Array.each(reader.getRoot(reader.rawData), function(value) {
                return remoteData = value;
            });
            reader.root = root;
            reader.buildExtractors(true);
        }
        while (i < length) {
            comp = Ext.getCmp(columns[i].id);
            data[comp.id] = me.getSummary(store, comp.summaryType, comp.dataIndex, false);
            ++i;
            for (key in remoteData) {
                if (remoteData.hasOwnProperty(key)) {
                    remote = remoteData[key];
                    if (remote !== void 0 && comp.dataIndex === key) {
                        data[comp.id] = remote;
                    }
                }
            }
        }
        return data;
    }
});
Ext.override(Ext.grid.feature.Summary, {

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
Ext.override(Ext.grid.header.Container, {

    /**
     * Get header width usage. Here we assume that at the moment of this function call,
     * gridColumnAFit was already called, so grid has `widthUsage` prop, containing best width,
     * that was calculated regarding a lot of params
     *
     * @return {*}
     */
    getWidthUsage: function() {
        return this.ownerCt.widthUsage;
    },
    prepareData: function(data, rowIdx, record, view, panel) {

        var me        = this,
            obj       = {},
            headers   = me.gridDataColumns || me.getGridColumns(),
            headersLn = headers.length,
            colIdx    = 0,
            header,
            headerId,
            renderer,
            value,
            metaData,
            store = panel.store;

        for (; colIdx < headersLn; colIdx++) {
            metaData = {
                tdCls: '',
                style: ''
            };
            header = headers[colIdx];
            headerId = header.id;
            renderer = header.renderer;
            value = record.raw._render && (header.dataIndex in record.raw._render) // +
                ? record.raw._render[header.dataIndex]             // +
                : data[header.dataIndex];                          // +

            if (typeof renderer == "function") {
                value = renderer.call(
                    header.scope || me.ownerCt,
                    value,
                    // metadata per cell passing an obj by reference so that
                    // it can be manipulated inside the renderer
                    metaData,
                    record,
                    rowIdx,
                    colIdx,
                    store,
                    view
                );
            }

            // <debug>
            if (metaData.css) {
                // This warning attribute is used by the compat layer
                obj.cssWarning = true;
                metaData.tdCls = metaData.css;
                delete metaData.css;
            }
            // </debug>
            if (me.markDirty) {
                obj[headerId + '-modified'] = record.isModified(header.dataIndex) ? Ext.baseCSSPrefix + 'grid-dirty-cell' : '';
            }
            obj[headerId+'-tdCls'] = metaData.tdCls;
            obj[headerId+'-tdAttr'] = metaData.tdAttr;
            obj[headerId+'-style'] = metaData.style;
            if (typeof value === 'undefined' || value === null || value === '') {
                value = header.emptyCellText;
            }
            obj[headerId] = value;
        }
        return obj;
    }
});

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
    }
});

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
    }
});
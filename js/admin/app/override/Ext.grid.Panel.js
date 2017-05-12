Ext.override(Ext.grid.Panel, {

    /**
     * Default width usage
     */
    widthUsage: 0,

    /**
     * This value will be involved in the process of real width
     * usage detection in case if there is currently no rows in grid
     */
    emptyTableHeightUsage: 300,

    /**
     * Get inner items summary width usage. Here we assume that at the moment of this function call,
     * gridColumnAFit was already called, so grid has `widthUsage` prop, containing best width,
     * that was calculated regarding a lot of params
     *
     * @return {Number}
     */
    getInnerItemsWidthUsage: function() {
        return this.widthUsage;
    },

    /**
     * Get inner items summary height usage
     *
     * @return {Number}
     */
    getInnerItemsHeightUsage: function() {
        var me = this, el = me.items.getAt(0).getEl(), table = el.down('.x-grid-table');

        // Return
        return table ? table.getHeight() + 3 : me.emptyTableHeightUsage;
    },

    /**
     * Get all columns, even if grid is divided into 'locked' and 'normal' sub-grids
     *
     * @return {Array}
     */
    getGridColumns: function() {
        var me = this, hct = me.getView().headerCt, columnA = [];

        // If grid is not divided into locked and normal grids, get grid columns, else
        if (hct) columnA = hct.getGridColumns(); else {

            // Get all: locked columns and normal columns
            columnA = columnA.concat(me.lockedGrid.headerCt.getGridColumns());
            columnA = columnA.concat(me.normalGrid.headerCt.getGridColumns());
        }

        // Return all columns
        return columnA;
    },

    /**
     * Get summary data, even if grid is divided into 'locked' and 'normal' sub-grids
     *
     * @return {Object}
     */
    getSummaryData: function() {
        var me = this, view = me.getView(), hct = view.headerCt, sf, sd = {};

        // If grid is not divided into locked and normal grids, get grid columns, else
        if (hct) {

            // If grid's view has summary feature - get it's summary data
            if (sf = view.getFeature('summary')) Ext.merge(sd, sf.generateSummaryData());

        // Else
        } else {

            // Get all: locked columns and normal columns
            if (sf = me.lockedGrid.view.getFeature('summary')) Ext.merge(sd, sf.generateSummaryData());
            if (sf = me.normalGrid.view.getFeature('summary')) Ext.merge(sd, sf.generateSummaryData());
        }

        // Return summary data
        return sd;
    },

    /**
     * Get width required for column contents to be displayed unclipped, for each column
     * Note: this function sets calculated width as `widthUsage` prop, for each column
     * and set `widthUsage` prop to grid itself, that will contain total width, required
     * for all non-hidden columns contents
     *
     * @param isTree
     * @return {Array}
     */
    getGridColumnsWidthUsage: function(isTree) {
        var me = this, columnA = me.getGridColumns(), s = me.getStore(), sortA = s.sorters.keys, sd = me.getSummaryData(),
            i, widthA = [], level, longest, px = {ellipsis: {usual: 18, rownumberer: 12, icon: 12}, sort: 18, id: 15}, cell,
            longestWidth, _longestWidth, k, total = 0, cb = false, lineA, longestLine;

        // For each column, mapped to a store field
        for (i = 0; i < columnA.length; i++) {

            // Get initial column width, based on a column title metrics
            if (columnA[i].icon) widthA[i] = 16; else {

                // Here we check if column header's inner text is multiline, and if so
                // 1. Detect the longest line
                longestLine = '';
                (lineA = columnA[i].text.toString().split('<br>')).forEach(function(line){
                    if (line.length > longestLine.length) longestLine = line;
                });

                // 2. Add .i-grid-column-multiline css class to the header DOM
                if (lineA.length > 1) columnA[i].addCls('i-grid-column-multiline');

                // Get width of longest line within column's header text
                widthA[i] = Indi.metrics.getWidth(longestLine);
            }

            // Reset level
            level = 0; longest = [];

            // Reset length
            longest[level] = columnA[i].text || '';

            // If columns does not have a dataIndex - skip this iteration
            if (columnA[i].dataIndex) {

                // Increase the width of a column, that store is sorted by, to provide an additional amount
                // of width for sort icon, that is displayed next after column title, within the same column
                if (sortA.indexOf(columnA[i].dataIndex) != -1) widthA[i] += px.sort;

                // Get the longest (within current column) cell contents
                me.getStore().each(function(r){
                    cell = typeof columnA[i].renderer == 'function'
                        ? columnA[i].renderer(r.get(columnA[i].dataIndex))
                        : r.get(columnA[i].dataIndex).toString();

                    level = 0;
                    if (Ext.isString(cell) && isTree && columnA[i].dataIndex == 'title') {
                        cell = cell.replace(/&nbsp;/g, ' ');
                        level = cell.match(/^ */)[0].length;
                        if (level) cell = cell.substr(level - 1);
                        if (!longest[level]) longest[level] = cell;
                        else if (cell.length > longest[level].length) longest[level] = cell;
                    }

                    // If cell is empty - return
                    if (!cell) return;

                    // If cell's html have a color-box inside, append '---' to mind color-box width,
                    // as '---' addition will provide a same-width alternative
                    if (Ext.isString(cell) && (cb = cell.match(/class="i-color-box"/))) cell += '---';

                    // Strip tags from cell's inner text/html and re-check longest cell contents
                    if (cell.length > longest[level].length &&
                        (!cb || (cell = Indi.stripTags(cell)).length > longest[level].length))
                        longest[level] = cell;
                });

                // Don't forgot about summaries
                if (columnA[i].summaryType) {
                    cell = typeof columnA[i].renderer == 'function'
                        ? columnA[i].renderer(sd[columnA[i].id])
                        : sd[columnA[i].id];
                    if (cell.length > longest[0].length) longest[0] = cell;
                } else if (columnA[i].summaryText) {
                    cell = columnA[i].summaryText;
                    if (cell.length > longest[0].length) longest[0] = cell;
                }

            // Else if column does not have `dataIndex` prop
            } else {

                // If column's xtype is 'rownumberer'
                if (columnA[i].xtype == 'rownumberer') longest[0] = (s.last() ? s.indexOfTotal(s.last()) + 1 : 1) + '';
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
            else if (columnA[i].dataIndex == 'id') widthA[i] += px.id;
            else widthA[i] += px.ellipsis.usual;

            // Limit the maximum column width, if such a config was set
            if (columnA[i].maxWidth && widthA[i] > columnA[i].maxWidth) widthA[i] = columnA[i].maxWidth;

            // Set width usage for current column
            columnA[i].widthUsage = widthA[i];

            // If column is not hidden - increase grid's `widthUsage` prop
            if (!columnA[i].hidden) me.widthUsage += widthA[i];
        }

        // Return columns, that now having `widthUsage` prop
        return columnA;
    }
});
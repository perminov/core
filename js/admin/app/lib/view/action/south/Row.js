Ext.define('Indi.lib.view.action.south.Row', {

    // @inheritdoc
    extend: 'Indi.lib.view.action.south.South',

    // @inheritdoc
    alias: 'widget.rowactionsouth',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.South.Row',

    /**
     * Set up initial height for this component. The key feature is that this function will try to detect if
     * height of this component can be greater than the default. As long as this component represents the south
     * region panel within the main action panel, function takes a look at the contents of center region panel
     * (within main action panel), and if height, used by that contents is less than center region panel actually
     * have, function will increase south region panel (south region panel - is `this` component) for it to
     * additionally use that blank/unused height.
     */
    initHeight: function() {
        var me = this, wPanel = me.up('[isWrapper]'), cPanel = wPanel.down('[region="center"]'), cUsedHeight = 0,
            pixelsPer1Percent, wPanelDockedItemsHeight = 0, unusedSpace, fitSpaceHeight;

        // Consider center panel body top and bottom padding
        cUsedHeight = cPanel.bodyPadding * 2;

        // Consider height of each component within center panel
        cPanel.query('> *').forEach(function(r){cUsedHeight += r.getHeight();});

        // Calculate wrapper panel's docked items total height
        wPanel.getDockedItems().forEach(function(r){wPanelDockedItemsHeight += r.minHeight;});

        // Calculate the number of pixels that is equal to one percent of height
        pixelsPer1Percent = (wPanel.getHeight() - wPanelDockedItemsHeight) / 100;

        // Calculate the unused space in percents
        unusedSpace = parseInt((pixelsPer1Percent * parseInt(cPanel.height) - cUsedHeight - 1) / pixelsPer1Percent);

        // If unused space detected in center region panel
        if (unusedSpace > 0) {

            // Calculate new (increased) height for south region panel
            me.heightPercent = (100 - parseInt(cPanel.height) + unusedSpace) + '%';

            // If south region panel is not minified, force it's height to fit all available space
            if (me.height != me.minHeight) me.height = me.heightPercent;

        // Else if there is no unused space and south region panel is not minimized
        // - set up `heightPercent` prop same as `height`
        } else if (me.height != me.minHeight) me.heightPercent = me.height;

        // Else set `heightPercent` to default south panel height
        else me.heightPercent = me.self.prototype.height;

        // If we decided to hide center panel, set south panel's height as '100%'
        if (cPanel.hidden) me.height = me.heightPercent = '100%';
    }
});
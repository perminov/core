/**
 * Base class for all controller actions instances
 */
Ext.define('Indi.lib.controller.action.Action', {

    // @inheritdoc
    extend: 'Ext.Component',

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action',

    // @inheritdoc
    mcopwso: ['panel'],

    /**
     * Main panel config
     */
    panel: {
        id: 'i-center-center-wrapper',
        renderTo: 'i-center-center-body',
        border: 0,
        height: '100%',
        closable: true,
        layout: 'fit',
        docked: {
            default: {
                xtype: 'toolbar',
                style: {paddingRight: '3px'},
                padding: '0 3 0 2'
            },
            items: [],
            inner: {}
        }
    },

    /**
     * Get the current trail item, or upper trail item - if `up` argument is given
     *
     * @param up
     * @return {Indi.lib.Trail.Item}
     */
    ti: function(up) {
        return Indi.trail(this.trailLevel - (Indi.trail(true).store.length - 1) + (up ? up : 0));
    },

    /**
     * Get the base id for all components, created while controller's action execution
     * If `up` argument is given, function will return base id of upper-level controller's action
     *
     * @param up
     * @return {String}
     */
    bid: function(up) {
        return this.ti(up).bid();
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Append tools and toolbars to the main panel
        Ext.merge(me.panel, {
            dockedItems: me.panelDockedA(),
            tools: me.panelToolA()
        });

        // Setup main panel title, contents and trailLevel property
        Ext.create('Ext.Panel', Ext.merge({
            title: me.ti().section.title,
            items: me.panel.items,
            trailLevel: me.trailLevel
        }, me.panel));

        // Call parent
        me.callParent();
    },

    /**
     * Builder for items arrays. Used for toolbars, items within each toolbar, etc
     *
     * @param itemCfgA
     * @param fnItemPrefix
     * @param allowNaO
     * @param adjust
     * @return {Array}
     */
    push: function(itemCfgA, fnItemPrefix, allowNaO, adjust) {

        // Setup auxilliary variables
        var me = this, itemA = [], itemI, fnItemI;

        // Build itemA array
        for (var i = 0; i < itemCfgA.length; i++) {

            // If only object-items are allowed for pushing, but itemCfgA[i] is not an object, try itemCfg[i+1]
            if (!allowNaO && !Ext.isObject(itemCfgA[i])) continue;

            // Reset itemI
            itemI = null;

            // Else if itemCfgA[i] is an object, and have `alias` property
            if (itemCfgA[i].hasOwnProperty('alias')) {

                // Get the item's creator function name
                fnItemI = fnItemPrefix + '$' + Indi.ucfirst(itemCfgA[i].alias);

                // If such function exists and return value of that function call
                if (typeof me[fnItemI] == 'function') itemI = me[fnItemI]();

                // If config is an object, merge itemI with it
                if (Ext.isObject(itemCfgA[i])) Ext.merge(itemI = itemI ? itemI : {}, itemCfgA[i]);

            // Else use as is
            } else itemI = itemCfgA[i];

            // Adjust item
            if (typeof adjust == 'function') itemI = adjust(itemI);

            // If itemI become consistent - push it to items array
            if (itemI && (Ext.isObject(itemI) || allowNaO)) itemA.push(itemI);
        }

        // Return items array
        return itemA;
    },

    /**
     * Panel tools array builder. This method is for use in subclasses of Indi.Controller.Action
     *
     * @return {Array}
     */
    panelToolA: function() {
        return []
    },

    /**
     * Build and return array of panel toolbars
     *
     * @return {Array}
     */
    panelDockedA: function() {
        var me = this;

        // Setup docked items
        return me.push(me.panel.docked.items, 'panelDocked', false, function(itemI){

            // Setup default toolbar config
            if (itemI) itemI = Ext.merge({}, me.panel.docked.default, itemI);

            // Setup toolbar items, if not yet
            if (!itemI.items && itemI.alias && me.panel.docked.inner && me.panel.docked.inner[itemI.alias])
                itemI.items = me.push(me.panel.docked.inner[itemI.alias], 'panelDockedInner', true);

            // Return
            return itemI.items ? itemI : null;
        });

    }
});

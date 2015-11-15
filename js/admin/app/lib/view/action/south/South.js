Ext.define('Indi.lib.view.action.south.South', {

    // @inheritdoc
    extend: 'Ext.tab.Panel',

    // @inheritdoc
    alias: 'widget.actionsouth',

    // @inheritdoc
    alternateClassName: 'Indi.View.Action.South',

    // @inheritdoc
    layout: 'fit',

    // @inheritdoc
    border: 0,

    // @inheritdoc
    minHeight: 25,

    // @inheritdoc
    region: 'south',

    // @inheritdoc
    height: '60%',

    /**
     * Special property for easier lookup
     */
    isSouth: true,

    /**
     * This property is for storing the actual height of this component, but in percents, not in pixels.
     * If actual height of this component will be equal to `minHeight` (this will mean that south panel
     * is visually minimized so the only tabs titles are shown), this property will store the percent value
     * of height that this component had before minimize, bor being able to apply that height back once user
     * un-minimize panel back by clicking on it's tab bar
     */
    heightPercent: 0,

    // @inheritdoc
    resizable: {
        handles: 'n',
        transparent: true
    },

    // @inheritdoc
    tabBar: {
        listeners: {
            click: function(c, e) {
                c.up('tabpanel').onTabBarClick(e);
            }
        }
    },

    // @inheritdoc
    initComponent: function() {
        var me = this, tab;

        // Bind `tabchange` event listener
        me.on('tabchange', function(){
            if (me.rendered && (tab = me.getActiveTab().down('[isTab]'))) tab.doLoad();
        });

        // Call parent
        me.callParent();
    },

    // @inheritdoc
    onRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Init height
        me.initHeight();
    },

    /**
     * This function is a handler for tab bar click, and it's purpose is to determine whether south region panel's
     * height should be minimized or restored back to normal, or do nothing with that height, depending on current
     * south panel height and the dom element, that click was made on
     *
     * @param e Event
     */
    onTabBarClick: function(e) {
        var me = this, h = me.getHeight();

        // If click was made on tab close icon, or on a scroller - return
        if (e.getTarget('.x-tab-close-btn') || e.getTarget('.x-box-scroller')) return;

        // If click was made on tab, but south panel is not minimized - return
        if (h != me.minHeight && e.getTarget('.x-tab')) return;

        // If we reach this line, and south panel is not minimized - minimize it, else
        if (h != me.minHeight) me.setHeight(me.minHeight); else {

            // Set `height` to be the same as `heightPercent`, so `height` will be in percents rather than in pixels
            me.height = me.heightPercent;

            // Apply height, stored in `height` prop, as we do not pass an argument while setHeight() call
            me.setHeight();
        }
    },

    /**
     * Catch up cases when new size is set up as a result of Ext.resizer.Resizer usage, and, for such cases -
     * force `height` property to be expressed in percents rather than in pixels
     *
     * @return {*}
     */
    setSize: function() {
        var me = this, tab;

        // Call parent
        me.callParent(arguments);

        // Force `height` property to be expressed in percents rather than in pixels,
        // if new size was applied using Ext.resizer.Resizer
        if (Ext.EventObject.getTarget() && Ext.EventObject.getTarget('.x-resizable-proxy'))
            me.height = me.heightPercent = Math.ceil(arguments[1]/me.up('[isWrapper]').body.getHeight() * 100) + '%';

        // Try to load the contents of tab. Try will be successful only in case if
        // a numder of conditions are in place. NOTE: The check if they are in place or not
        // is performed within doLoad() call
        Ext.defer(function(){
            if (me.rendered && !me.hidden && (tab = me.getActiveTab().down('[isTab]'))) tab.doLoad();
        }, 10);

        // Return
        return me;
    },

    /**
     * Empty function. To be overridden in child classes
     */
    initHeight: Ext.emptyFn
});
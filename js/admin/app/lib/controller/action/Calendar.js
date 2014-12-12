/**
 * Base class for all controller actions instances, that operate with rowsets,
 * and use Ext.panel.Grid view to display/modify those rowsets
 */
Ext.define('Indi.lib.controller.action.Calendar', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Rowset.Calendar',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Rowset',

    /**
     * Config of panel, that will be used for representing the rowset
     */
    rowset: {
        xtype: 'calendarpanel',
        border: 0,

        /**
         * View config
         */
        viewConfig: {
            loadingText: Ext.LoadMask.prototype.msg,
        }
    },


    store: {
        beforeload: function(){
            //this.ctx().filterChange({noReload: true});
        },
        load: function(){
            //this.ctx().storeLoadCallbackDefault();
            //this.ctx().storeLoadCallback();
        }
    },

    /**
     * Rowset panel toolbars array builder
     *
     * @return {Array}
     */
    rowsetDockedA: function() {
        return this._docked('rowset');
    },

    /**
     * Builds and return an array of panels, that will be used to represent the major UI contents.
     * Currently is consists only from this.rowset form panel configuration
     *
     * @return {Array}
     */
    panelItemA: function() {

        // Panels array
        var itemA = [], rowsetItem = this.rowsetPanel();

        // Append rowset panel
        if (rowsetItem) itemA.push(rowsetItem);

        // Return panels array
        return itemA;
    },

    /**
     * Build an return main panel's rowset panel config object
     *
     * @return {*}
     */
    rowsetPanel: function() {
        return this.rowset;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup id
        me.id = me.bid();

        // Setup rowset panel config
        me.rowset = Ext.merge({
            id: me.id + '-rowset-calendar',
            store: me.getStore(),
            dayViewCfg: {
                store: me.getStore()
            },
            weekViewCfg: {
                store: me.getStore()
            },
            monthViewCfg: {
                store: me.getStore()
            },
            dockedItems: me.rowsetDockedA()
        }, me.rowset);

        // Setup main panel items
        me.panel.items = me.panelItemA();

        // Call parent
        me.callParent();
    }
});

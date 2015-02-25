/**
 * Main left menu for Indi Engine interface
 */
Ext.define('Indi.view.Menu', {
    extend: 'Ext.tree.Panel',
    alternateClassName: 'Indi.Menu',
    id: 'i-menu',
    rootVisible: false,
    title: Indi.lang.I_MENU,
    useArrows: true,
    border: 1,
    region: 'west',
    width: 200,
    collapsible: true,
    padding: '50 0 0 0',
    weight: 300,

    /**
     * Convert `data` argument to array, suitable for usage with Ext.tree.Panel
     *
     * @param data
     * @return {Array}
     */
    data2items: function(data){

        // Menu items array
        var itemA = [];

        // Walk through raw data
        for (var i = 0; i < data.length; i++) {

            // If current item is a root item, we add it to items array
            if (!parseInt(data[i].sectionId)) {

                // Prepare item data
                var itemI = {
                    text: data[i].title,
                    expanded: false,
                    cls: 'i-menu-root-item',
                    children: []
                }

                // Detect and append item's children (nested menu items)
                do {
                    itemI.children.push({
                        text: data[i+1].title,
                        iconCls: 'i-menu-leaf-item-icon',
                        leaf: true,
                        cls: 'i-menu-leaf-item',
                        value: Indi.pre + '/' + data[i+1].alias + '/'
                    });
                    i++;
                } while (data[i+1] && parseInt(data[i+1].sectionId));

                // Add item to array
                itemA.push(itemI);
            }
        }

        // Return items array
        return itemA;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup store
        me.store = Ext.create('Ext.data.TreeStore', {
            root: {
                expanded: true,
                children: me.data2items(Indi.menu)
            }
        });

        // Call parent
        me.callParent();
    },

    // @inheritdoc
    listeners: {
        afterrender: function(c) {
            c.expandAll();
        },
        itemclick: function(view, rec, item, index, eventObj) {
            if (rec.get('leaf') == false) {
                if (rec.data.expanded) rec.collapse();
                else rec.expand();
            } else {
                Indi.load(rec.raw.value);
            }
        },
        beforecollapse: function(){
            Ext.getCmp('i-logo').hide();
        },
        collapse: function(){
            Indi.viewport.doComponentLayout();
        },
        expand: function(){
            Ext.getCmp('i-logo').show();
            Indi.viewport.doComponentLayout();
        }
    }
});
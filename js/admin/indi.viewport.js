Ext.define('Indi.Menu', {
    extend: 'Ext.tree.Panel',
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
    data2items: function(data){

        // Menu items array
        var items = [];

        // Walk through raw data
        for (var i = 0; i < data.length; i++) {

            // If current item is a root item, we add it to items array
            if (!parseInt(data[i].sectionId)) {

                // Prepare item data
                var item = {
                    text: data[i].title,
                    expanded: false,
                    cls: 'i-menu-root-item',
                    children: []
                }

                // Detect and append item's children (nested menu items)
                do {
                    item.children.push({
                        text: data[i+1].title,
                        iconCls: 'i-menu-leaf-item-icon',
                        leaf: true,
                        cls: 'i-menu-leaf-item',
                        value: Indi.pre + '/' + data[i+1].alias + '/'
                    });
                    i++;
                } while (data[i+1] && parseInt(data[i+1].sectionId));

                // Add item to array
                items.push(item);
            }
        }

        // Return items array
        return items;
    },
    initComponent: function() {
        var me = this;
        this.store = Ext.create('Ext.data.TreeStore', {
            root: {
                expanded: true,
                children: me.data2items(Indi.menu)
            }
        });
        this.callParent();
    },
    listeners: {
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
    // Setup listeners for itemclick, beforecollapse, collapse and expand events
});

/**
 * Setup a viewport
 */
Ext.define('Indi.Viewport', {
    extend: 'Ext.Viewport',
    layout: {type: 'border', padding: 5},
    defaults: {split: true},

    statics: {

        /**
         * Format of date, that is displayed at the top right corner
         *
         * @type {String}
         */
        dateUpdaterFormat: '<b>l</b>, d.m.Y [H:i] \\G\\M\\TP',

        /**
         * Date updater, updates the top right date
         */
        dateUpdater: function() {
            Ext.get('i-center-north-date').setHTML(
                Ext.Date.format(
                    new Date(Indi.time * 1000),
                    Indi.Viewport.dateUpdaterFormat
                )
            );
        }
    },

    /**
     * Logo small panel
     *
     * @type {Object}
     */
    logo: {
        id: 'i-logo',
        width: 200,
        height: 45,
        border: 0,
        tpl: new Ext.XTemplate('<img src="{std}/i/admin/logo.png"/>'),
        afterRender: function() {
            this.tpl.overwrite(this.el, {std: Indi.std});
            this.superclass.afterRender.apply(this, arguments);
        }
    },

    /**
     * Menu tree panel
     *
     * @type {Object}
     */
    menu: {
    },

    center: {
        region: 'center',
        defaults: {split: true},
        border: 1,
        layout: {type: 'border', padding: '0 0 0 0'},
        id: 'i-center',
        items: [{
            id: 'i-center-north',
            region: 'north',
            tpl:
                '<div>' +
                    '<div id="i-center-north-date">{date}</div>' +
                    '<div id="i-center-north-admin">{admin} <a href="{pre}/logout/">{logout}</a></div>' +
                    '<div id="i-center-north-trail"></div>' +
                '</div>',
            minHeight: 36,
            border: 0,
            afterRender: function() {
                setInterval(Indi.Viewport.dateUpdater, 1000);
                this.tpl.overwrite(this.el, {
                    date: Ext.Date.format(new Date(Indi.time * 1000), Indi.Viewport.dateUpdaterFormat),
                    admin: Indi.user,
                    pre: Indi.pre,
                    logout: Indi.lang.I_LOGOUT
                });
                this.superclass.afterRender.apply(this, arguments);
            }
        }, {
            region: 'center',
            id: 'i-center-center',
            border: 1,
            contentEl: 'i-section-index-action-index-content'
        }]
    },
    initComponent: function() {
        this.menu = Ext.create('Indi.Menu', this.menu);
        this.items = [this.logo, this.menu, this.center];
        this.callParent();
    },
    listeners: {
        afterrender: function (){
            Indi.metrics = new Ext.util.TextMetrics();
        },
        afterlayout: function(){
            if (Ext.getCmp('i-center-center-wrapper')) {
                Ext.getCmp('i-center-center-wrapper').doComponentLayout();
            }
        }
    }
});

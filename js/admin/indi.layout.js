var Indi = (function (indi) {
    "use strict";
    var process = function () {

        /**
         * Setup layout prototype
         */
        indi.proto.layout = function(){

            /**
             * This is for context stabilization
             *
             * @type {*}
             */
            var instance = this;

            /**
             * Component name
             *
             * @type {String}
             */
            this.componentName = 'layout';

            /**
             * Will contain menu raw data array, and extjs panel component object
             *
             * @type {Object}
             */
            this.menu = {};

            /**
             * Logo small panel
             *
             * @type {Object}
             */
            this.logo = {
                id: 'i-logo',
                width: 200,
                height: 36,
                border: 0,
                html: '<img src="' + indi.std + '/i/admin/logo.png"/>'
            };

            /**
             * Reads the instance.menu.data array and builds the items array, approriate for use as a extjs treepanel
             * store data
             *
             * @return {Array}
             */
            this.menu.items = function(){

                // Menu items array
                var items = [];

                // Walk through raw data
                for (var i = 0; i < instance.menu.data.length; i++) {

                    // If current item is a root item, we add it to items array
                    if (!parseInt(instance.menu.data[i].sectionId)) {

                        // Prepare item data
                        var item = {
                            text: instance.menu.data[i].title,
                            expanded: false,
                            cls: 'i-menu-root-item',
                            children: []
                        }

                        // Detect and append item's children (nested menu items)
                        do {
                            item.children.push({
                                text: instance.menu.data[i+1].title,
                                iconCls: 'i-menu-leaf-item-icon',
                                leaf: true,
                                cls: 'i-menu-leaf-item',
                                value: indi.pre + '/' + instance.menu.data[i+1].alias
                            });
                            i++;
                        } while (instance.menu.data[i+1] && parseInt(instance.menu.data[i+1].sectionId));

                        // Add item to array
                        items.push(item);
                    }
                }

                // Return items array
                return items;
            };

            /**
             * Format of date, that is displayed at the top right corner
             *
             * @type {String}
             */
            this.dateUpdaterFormat = '<b>l</b>, d.m.Y [H:i]';

            /**
             * Date updater, updates the top right date
             */
            this.dateUpdater = function() {
                $('#i-center-north-date').html(Ext.Date.format(new Date(indi.time * 1000), instance.dateUpdaterFormat));
            };

            /**
             * Build the viewport
             */
            this.build = function(){

                // Create the extjs treepanel, for menu
                instance.menu.panel = Ext.create('Ext.tree.Panel', {
                    id: 'i-menu',
                    rootVisible: false,
                    title: indi.lang.I_MENU,
                    useArrows: true,
                    border: 1,
                    region: 'west',
                    width: 200,
                    collapsible: true,
                    padding: '41 0 0 0',
                    weight: 300,

                    // Setup menu items store
                    store: Ext.create('Ext.data.TreeStore', {
                        root: {
                            expanded: true,
                            children: instance.menu.items()
                        }
                    }),

                    // Setup listeners for itemclick, beforecollapse, collapse and expand events
                    listeners: {
                        itemclick: function(view, rec, item, index, eventObj) {
                            if (rec.get('leaf') == false) {
                                if (rec.data.expanded) rec.collapse();
                                else rec.expand();
                            } else {
                                indi.load(rec.raw.value);
                            }
                        },
                        beforecollapse: function(){
                            Ext.getCmp('i-logo').hide();
                        },
                        collapse: function(){
                            indi.layout.viewport.doComponentLayout();
                        },
                        expand: function(){
                            Ext.getCmp('i-logo').show();
                            indi.layout.viewport.doComponentLayout();
                        }
                    }
                });

                // Setup data for center region
                instance.center = {
                    region: 'center',
                    defaults: {split: true},
                    border: 1,
                    layout: {type: 'border', padding: '0 0 0 0'},
                    id: 'i-center',
                    items: [{
                        id: 'i-center-north',
                        region: 'north',
                        html:
                            '<div>' +
                                '<div id="i-center-north-date">' +
                                    Ext.Date.format(new Date(indi.time * 1000), instance.dateUpdaterFormat) +
                                '</div>' +
                                '<div id="i-center-north-admin">' +
                                    instance.adminInfo +
                                    ' <a href="' + indi.pre + '/logout/">' + indi.lang.I_LOGOUT + '</a>' +
                                '</div>' +
                                '<div id="i-center-north-trail"></div>'+
                            '</div>',
                        height: 36,
                        border: 0,
                        listeners: {
                            afterrender: function(){
                                setInterval(instance.dateUpdater, 1000);
                            }
                        }
                    }, {
                        region: 'center',
                        id: 'i-center-center',
                        border: 1
                    }]
                };

                // Setup viewport
                instance.viewport = Ext.create('Ext.Viewport', {
                    layout: {type: 'border', padding: 5},
                    defaults: {split: true},
                    items: [instance.logo, instance.menu.panel, instance.center],
                    listeners: {
                        afterlayout: function(){
                            if (Ext.getCmp('i-center-center-wrapper')) {
                                Ext.getCmp('i-center-center-wrapper').doComponentLayout();
                            }
                        }
                    }
                });
            }

            /**
             * The enter point.
             */
            this.run = function() {

                // Call the callbacks
                if (indi.callbacks && indi.callbacks[instance.componentName] && indi.callbacks[instance.componentName].length) {
                    for (var i = 0; i < indi.callbacks[instance.componentName].length; i++) {
                        indi.callbacks[instance.componentName][i]();
                    }
                }

                // Build the interface
                instance.build();
            }
        };

        (indi.layout = new indi.proto.layout()).run();
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof indi.proto !== 'undefined') {
                Ext.onReady(function(){
                    clearInterval(checkRequirementsId);
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));
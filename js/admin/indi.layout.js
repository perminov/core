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
                height: 45,
                border: 0,
                html: '<img src="' + indi.std + '/i/admin/logo.png"/>'
            };

            this.options = {
                loginPanelTitle: 'Indi Engine'
            }

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
                                value: indi.pre + '/' + instance.menu.data[i+1].alias + '/'
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
             * Build the signin box
             */
            this.buildLoginBox = function() {

                // Create the panel
                Ext.create('Ext.Panel', {
                    id: 'i-login-panel',
                    title: instance.options.loginPanelTitle,
                    renderTo: 'i-login-box',
                    titleAlign: 'center',
                    height: 125,
                    width: 300,
                    bodyPadding: 10,
                    items: [
                        {
                            xtype: 'textfield',
                            id: 'i-login-box-username',
                            fieldLabel: indi.lang.I_LOGIN_BOX_USERNAME,
                            labelWidth: 90,
                            value: Ext.util.Cookies.get('i-username'),
                            width: 275
                        },{
                            xtype: 'textfield',
                            id: 'i-login-box-password',
                            inputType: 'password',
                            fieldLabel: indi.lang.I_LOGIN_BOX_PASSWORD,
                            value: Ext.util.Cookies.get('i-password'),
                            labelWidth: 90,
                            width: 246,
                            cls: 'i-inline-block'
                        },{
                            xtype: 'checkboxfield',
                            id: 'i-login-box-remember',
                            checked: Ext.util.Cookies.get('i-remember') !== null,
                            margin: '0 0 2 8',
                            cls: 'i-inline-block'
                        },{
                            xtype: 'button',
                            id: 'i-login-box-submit',
                            text: indi.lang.I_LOGIN_BOX_ENTER,
                            margin: '4 0 0 20',
                            width: 113,
                            handler: function(){

                                // Prepare the request data
                                var data = {
                                    username: Ext.getCmp('i-login-box-username').getValue(),
                                    password: Ext.getCmp('i-login-box-password').getValue(),
                                    remember: Ext.getCmp('i-login-box-remember').getValue(),
                                    enter: true
                                }

                                // Make an authentication request
                                $.post(indi.pre + '/', data, function(response){

                                    // If request returned an error, display a Ext.MessageBox
                                    if (response.error) {
                                        Ext.MessageBox.show({
                                            title: indi.lang.I_LOGIN_ERROR_MSGBOX_TITLE,
                                            msg: response.error,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });

                                    // Else signin was ok, we check if 'remember' checkbox was checked,
                                    // and if so - set the cookies. After that we do a page reload
                                    } else if (response.ok) {

                                        // Delete 'enter' property from 'data' object for it to be ready to
                                        // set or remove cookies for it's all remaining properties
                                        delete data.enter;

                                        // For each remaining property in 'data' object
                                        for (var i in data)

                                            // If 'remember' checkbox was checked, we create cookie
                                            if (data.remember)
                                                Ext.util.Cookies.set(
                                                    'i-' + i,
                                                    data[i],

                                                    // We set cookie expire date as 1 month
                                                    Ext.Date.add(new Date(), Ext.Date.MONTH, 1),
                                                    indi.pre
                                                );

                                            // Else we delete cookie
                                            else Ext.util.Cookies.clear(i, indi.pre);

                                        // Reload window contents
                                        window.location.replace(indi.pre + '/');
                                    }
                                }, 'json');
                            }
                        },{
                            xtype: 'button',
                            id: 'i-login-box-reset',
                            text: indi.lang.I_LOGIN_BOX_RESET,
                            margin: '4 0 0 10',
                            width: 113,
                            handler: function(){
                                Ext.getCmp('i-login-box-username').setValue();
                                Ext.getCmp('i-login-box-password').setValue();
                                Ext.getCmp('i-login-box-remember').setValue(false);
                            }
                        }
                    ],
                    listeners: {
                        afterRender: function(){
                            this.keyNav = Ext.create('Ext.util.KeyNav', this.el, {
                                enter: function(){
                                    Ext.getCmp('i-login-box-submit').handler();
                                }
                            });
                        }
                    }
                });
            }

            /**
             * Build the viewport
             */
            this.build = function(){

                // If user is not signed in, we build signin box
                if ($('#i-login-box').length) {
                    this.buildLoginBox();

                    // If user was thrown out, display a message box with a reason
                    if (Indi.throwOutMsg) {
                        Ext.MessageBox.show({
                            title: indi.lang.I_LOGIN_ERROR_MSGBOX_TITLE,
                            msg: Indi.throwOutMsg,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                    return;
                }

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
                    padding: '50 0 0 0',
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
                        minHeight: 36,
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
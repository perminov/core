/**
 * Setup a viewport for Indi Engine interface
 */
Ext.define('Indi.view.Viewport', {
    extend: 'Ext.container.Viewport',
    layout: {type: 'border', padding: '3 5 5 5'},
    defaults: {split: true},
    alternateClassName: 'Indi.Viewport',

    /**
     * Logo small panel
     *
     * @type {Object}
     */
    logo: {
        id: 'i-logo',
        margin: '2 0 0 0',
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
        id: 'i-menu',
        title: Indi.lang.I_MENU,
        collapsible: true,
        padding: '52 0 0 0'
    },

    /**
     * Center panel cfg
     */
    center: {
        region: 'center',
        defaults: {split: true},
        border: 1,
        layout: {type: 'border', padding: '0 0 0 0'},
        id: 'i-center',
        items: [{
            id: 'i-center-north',
            region: 'north',
            items: [{
                border: 0,
                dockedItems: [{
                    dock: 'top',
                    xtype: 'toolbar',
                    style: 'background: transparent',
                    padding: 0,
                    margin: '0 0 3 0',
                    height: 18,
                    border: 0,
                    items: [{
                        xtype: 'panel',
                        height: 16,
                        border: 0,
                        html: '<div id="i-center-north-admin">{admin}</div>',
                        listeners: {
                            boxready: function() {
                                var div = this.body.down('div');
                                var tpl = new Ext.Template(div.dom.outerHTML);
                                div.update(tpl.apply({
                                    admin: Indi.user.title,
                                    pre: Indi.pre,
                                    logout: Indi.lang.I_LOGOUT
                                }));
                                this.setWidth();
                            }
                        }
                    }, {
                        margin: '0 2 0 0',
                        padding: 0,
                        xtype: 'button',
                        height: 16,
                        id: 'i-mobile-menu-trigger',
                        arrowCls: '',
                        text: '[{role}]',
                        menu: {
                            floating: true,
                            items: [{
                                xtype: 'mainmenu',
                                id: 'i-mobile-menu'
                            }],
                            listeners: {
                                afterrender: function(c) {
                                    c.down('mainmenu').maxHeight = Ext.getCmp('i-center-center').getHeight();
                                }
                            }
                        },
                        listeners: {
                            boxready: function() {
                                this.btnInnerEl.css('padding', '0');
                                var div = this.btnInnerEl;
                                var tpl = new Ext.Template(div.dom.innerHTML);
                                div.update(tpl.apply({
                                    role: Indi.user.role
                                }));
                                this.setWidth();
                            }
                        }
                    }, {
                        xtype: 'panel',
                        height: 16,
                        border: 0,
                        html: '<a href="{pre}/logout/" style="font-size: 11px; display: inline-block; line-height: 16px;">{logout}</a>',
                        listeners: {
                            boxready: function() {
                                var div = this.body.down('a');
                                var tpl = new Ext.Template(div.dom.outerHTML);
                                div.update(tpl.apply({
                                    pre: Indi.pre,
                                    logout: Indi.lang.I_LOGOUT
                                }));
                                this.setWidth();
                            }
                         }
                    }]
                }],
                bodyStyle: 'border-top: 0;',
                html: '<div id="i-center-north-trail"></div>'
            }],
            minHeight: 36,
            border: 0
        }, {
            region: 'center',
            id: 'i-center-center',
            border: 1
            //contentEl: 'i-section-index-action-index-content'
        }]
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;
        me.menu = Ext.create('Indi.Menu', me.menu);
        me.menu.on({
            collapse: function(){
                me.down('#i-mobile-menu-trigger').enable();
            },
            expand: function() {
                me.down('#i-mobile-menu-trigger').disable();
            }
        });
        me.items = [me.logo, me.menu, me.center];
        me.callParent();
    },

    // @inheritdoc
    listeners: {
        afterrender: function (){
            Indi.metrics = new Ext.util.TextMetrics();
        },
        afterlayout: function(){
            if (Ext.getCmp(Indi.centerId)) Ext.getCmp(Indi.centerId).doComponentLayout();
        },
        boxready: function(c, w, h) {
            if (w > 600) c.down('#i-mobile-menu-trigger').setDisabled(true);
        },
        resize: function(c, w, h) {
            var m = c.down('#i-menu'), t = c.down('#i-mobile-menu-trigger');
            if (w <= 600) {
                if (!m.hidden || m.collapsed) m.hide();
            } else {
                if (m.hidden) m.show();
            }
            t.setDisabled(w > 600 && !m.collapsed);
            if (c.down('#i-mobile-menu')) {
                c.down('#i-mobile-menu').maxHeight = c.down('#i-center-center').getHeight();
                if (!c.down('#i-mobile-menu').up('menu').hidden) c.down('#i-mobile-menu').setHeight();
            }
        }
    }
});

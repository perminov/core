/**
 * Setup a viewport for Indi Engine interface
 */
Ext.define('Indi.view.Viewport', {
    extend: 'Ext.container.Viewport',
    layout: {type: 'border', padding: '0 5 5 5'},
    defaults: {split: true},
    alternateClassName: 'Indi.Viewport',

    /**
     * Logo small panel
     *
     * @type {Object}
     */
    logo: {
        id: 'i-logo',
        width: 200,
        height: 50,
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

    /**
     * Center panel cfg
     */
    center: {
        region: 'center',
        defaults: {split: true},
        border: 1,
        layout: {type: 'border', padding: '0 0 0 0'},
        id: 'i-center',
        padding: 0,
        items: [{
            id: 'i-center-north',
            region: 'north',
            xtype: 'taskbar',
            /*tpl:
                '<div>' +
                    '<div id="i-center-north-date">{date}</div>' +
                    '<div id="i-center-north-admin">{admin} <a href="{pre}/logout/">{logout}</a></div>' +
                    '<div id="i-center-north-trail"></div>' +
                    '</div>',
            minHeight: 36,
            border: 0,
            afterRender: function() {
                setInterval(Indi.view.Viewport.dateUpdater, 1000);
                this.tpl.overwrite(this.el, {
                    date: Ext.Date.format(new Date(Indi.time * 1000), Indi.view.Viewport.dateUpdaterFormat),
                    admin: Indi.user.title,
                    pre: Indi.pre,
                    logout: Indi.lang.I_LOGOUT
                });
                this.superclass.afterRender.apply(this, arguments);
            }*/
        }, {
            id: 'i-center-north-trail-panel',
            region: 'north',
            minHeight: 17,
            border: 1,
            margin: '-1 0 0 0'
        }, {
            region: 'center',
            id: 'i-center-center',
            border: 1,
            //contentEl: 'i-section-index-action-index-content'
        }]
    },

    // @inheritdoc
    initComponent: function() {
        this.menu = Ext.create('Indi.Menu', this.menu);
        this.items = [this.logo, this.menu, this.center];
        this.callParent();
    },

    // @inheritdoc
    listeners: {
        afterrender: function (){
            Indi.metrics = new Ext.util.TextMetrics();
        },
        afterlayout: function(){
            if (Ext.getCmp(Indi.centerId)) Ext.getCmp(Indi.centerId).doComponentLayout();
        }
    }
});

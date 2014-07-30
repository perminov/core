Ext.define('Indi.lib.controller.action.Action', {
    extend: 'Ext.Component',
    alternateClassName: 'Indi.Controller.Action',
    mcopwso: ['panel'],
    panel: {
        id: 'i-center-center-wrapper',
        renderTo: 'i-center-center-body',
        border: 0,
        height: '100%',
        closable: true,
        layout: 'fit'
    },
    trail: function(up) {
        return Indi.trail(this.trailLevel - (Indi.trail(true).store.length - 1) + (up ? up : 0));
    },
    bid: function(up) {
        var ti = this.trail();
        return 'i-section-'+ti.section.alias+'-action-'+ti.action.alias;
    },
    initComponent: function() {
        Ext.create('Ext.Panel', Ext.merge({
            title: this.trail().section.title,
            items: this.panel.items,
            trailLevel: this.trailLevel
        }, this.panel));
        this.callParent();
    }
});

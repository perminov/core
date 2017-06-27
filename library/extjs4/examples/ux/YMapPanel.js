/**
 * @class Ext.ux.YMapPanel
 * @extends Ext.Panel
 */
Ext.define('Ext.ux.YMapPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.ymappanel',
    requires: ['Ext.window.MessageBox'],

    // @inheritdoc
    initComponent : function(){
        Ext.applyIf(this, {
            border: false
        });
        this.callParent();
    },

    // @inheritdoc
    afterFirstLayout : function(){
        var me = this;

        // Call parent
        me.callParent();

        // Try set center
        if (me.center) {
            if (me.center.geoCodeAddr) me.lookupCode(me.center.geoCodeAddr, me.center.marker);
            else me.createMap(me.center);
        } else Ext.Error.raise('center is required');
    },
    
    createMap: function(center, marker) {
        var me = this, options = Ext.apply({}, me.mapOptions);

        options = Ext.applyIf(options, {
            zoom: 14,
            center: center
        });

        me.ymap = new ymaps.Map(this.body.dom, options);

        if (marker) {
            me.ymap.geoObjects.add(new ymaps.GeoObject(
                Ext.merge(marker[0], {geometry: {coordinates: center}}),
                marker[1])
            );
        }
    },
    
    lookupCode : function(addr, marker) {
        var me = this, myGeocoder = ymaps.geocode(addr);
        myGeocoder.then(function(response){
            me.onLookupComplete(response, marker);
        }, function(response){
            me.onLookupFailure(response);
        });
    },
    
    onLookupComplete: function(response, marker) {
        this.createMap(response.geoObjects.get(0).geometry._coordinates, marker);
    },

    onLookupFailure: function() {
        console.log('geocode failed', arguments);
    },
    
    afterComponentLayout : function(w, h){
        this.callParent(arguments);
        this.redraw();
    },
    
    redraw: function(){
        var me = this; if (me.ymap) me.ymap.container.fitToViewport();
    }
});

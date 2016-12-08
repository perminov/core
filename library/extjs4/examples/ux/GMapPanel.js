/**
 * @class Ext.ux.GMapPanel
 * @extends Ext.Panel
 * @author Shea Frederick
 */
Ext.define('Ext.ux.GMapPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.gmappanel',
    requires: ['Ext.window.MessageBox'],

    // @inheritdoc
    initComponent : function(){
        Ext.applyIf(this,{
            plain: true,
            gmapType: 'map',
            border: false
        });
        this.markers = [];
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
        var options = Ext.apply({}, this.mapOptions);
        options = Ext.applyIf(options, {
            zoom: 14,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        this.gmap = new google.maps.Map(this.body.dom, options);
        if (marker) {
            this.markers.push(this.addMarker(Ext.applyIf(marker, {
                position: center
            })));
        }
        
        Ext.each(this.markers, this.addMarker, this);
    },
    
    addMarker: function(marker) {
        marker = Ext.apply({
            map: this.gmap
        }, marker);
        
        if (!marker.position) {
            marker.position = new google.maps.LatLng(marker.lat, marker.lng);
        }
        var o =  new google.maps.Marker(marker);
        Ext.Object.each(marker.listeners, function(name, fn){
            google.maps.event.addListener(o, name, fn);    
        });
        return o;
    },
    
    lookupCode : function(addr, marker) {
        this.geocoder = new google.maps.Geocoder();
        this.geocoder.geocode({
            address: addr
        }, Ext.Function.bind(this.onLookupComplete, this, [marker], true));
    },
    
    onLookupComplete: function(data, response, marker) {
        if (response != 'OK') {
            Ext.MessageBox.alert('Error', 'An error occured: "' + response + '"');
            return;
        }
        this.createMap(data[0].geometry.location, marker);
    },
    
    afterComponentLayout : function(w, h){
        this.callParent(arguments);
        this.redraw();
    },
    
    redraw: function(){
        var me = this;
        if (me.gmap) google.maps.event.trigger(me.gmap, 'resize');
    }
});

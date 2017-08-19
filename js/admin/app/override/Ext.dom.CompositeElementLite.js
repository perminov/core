/**
 * Append some jQuery-style methods, and collection methods to Ext.dom.CompositeElementLite component
 */
Ext.override(Ext.dom.CompositeElementLite, {

    attr: function(name, value) {
        if (arguments.length == 1) {
            if (typeof name == 'object') {
                this.each(function(el){
                    el.set(name);
                });
            }
            return this;
        } else if (arguments.length == 2) {
            var attrO = {}; attrO[name] = value;
            return this.each(function(el){
                el.set(attrO);
            });
        }
    },

    css: function(name, value) {
        if (arguments.length == 1) {
            if (typeof name == 'object') {
                this.each(function(el){
                    el.setStyle(name);
                });
            }
            return this;
        } else if (arguments.length == 2) {
            var styleO = {}; styleO[name] = value;
            return this.each(function(el){
                el.setStyle(styleO);
            });
        }
    },

    click: function() {
        return this.each(function(el){
            el.dom.click();
        })
    },

    removeCls: function(cls) {
        cls = (cls || '').split(' ');
        for (var i = 0; i < cls.length; i++) {
            this.each(function(el){
                el.removeCls(cls[i]);
            });
        }
        return this;
    },

    on: function(eventName, fn, scope) {
        this.each(function(el){
            elScope = scope ? scope : Ext.get(el);
            el.on(eventName, fn, elScope);
        });
    }
});

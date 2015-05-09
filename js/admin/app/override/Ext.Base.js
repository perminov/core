Ext.Base.mixin = function(name, mixinClass) {
    var mixin = mixinClass.prototype,
        prototype = this.prototype,
        key;

    if (typeof mixin.onClassMixedIn != 'undefined') {
        mixin.onClassMixedIn.call(mixinClass, this);
    }

    if (!prototype.hasOwnProperty('mixins')) {
        if ('mixins' in prototype) {
            prototype.mixins = Ext.Object.chain(prototype.mixins);
        }
        else {
            prototype.mixins = {};
        }
    }

    for (key in mixin) {
        if (key === 'mixins') {
            Ext.merge(prototype.mixins, mixin[key]);
        }
        else if (typeof prototype[key] == 'undefined' && key != 'mixinId' && key != 'config') {
            prototype[key] = mixin[key];
        } else if (Ext.isArray(this.prototype.mcopwso) && this.prototype.mcopwso.indexOf(key) != -1) {
            prototype[key] = Ext.merge(mixin[key], prototype[key]);
        }
    }
    //<feature classSystem.config>
    if ('config' in mixin) {
        this.addConfig(mixin.config, false);
    }
    //</feature>

    prototype.mixins[name] = mixin;
}
Ext.ns('Indi.lib.dbtable.Row');
Indi.lib.dbtable.Row.prototype = function (data) {
    this.foreign = function(key) {
        if (key == 'fieldId') {
            for (var i = 0; i < Indi.trail(true).store.length; i++) {
                if (Indi.trail(i).fields)
                    for (var j = 0; j < Indi.trail(i).fields.length; j++) {
                        if (parseInt(Indi.trail(i).fields[j].id) == parseInt(this.fieldId)) {
                            return Indi.trail(i).fields[j];
                        }
                    }
            }
        } else if (this._foreign && this._foreign[key]) {
            return this._foreign[key];
        }
    };

    this.nested = function(key) {
        return this._nested[key];
    };

    this.view = function(key) {
        return this._view[key];
    };
    Ext.merge(this, data);
};

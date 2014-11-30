Ext.define('Indi.controller.enumset', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumnDefault: function() {
                return Ext.merge(this.callParent(arguments), {
                    renderer: function (value) {
                        return value;
                    }
                });
            }
        }
    }
});
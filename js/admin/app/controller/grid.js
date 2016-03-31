Ext.define('Indi.controller.grid', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$Alias: {
                allowBlank: true,
                considerOn: [{
                    name: 'fieldId'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(!d.fieldId);
                    }
                }
            }
        }
    }
});
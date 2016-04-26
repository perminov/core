Ext.define('Indi.controller.disabledFields', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$ProfileIds: {
                allowBlank: true,
                considerOn: [{
                    name: 'impact'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.impact != 'all');
                    }
                }
            }
        }
    }
});
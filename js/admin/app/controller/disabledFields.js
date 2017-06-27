Ext.define('Indi.controller.disabledFields', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            rowset: {
                multiSelect: true
            }
        },
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
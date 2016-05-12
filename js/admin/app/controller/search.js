Ext.define('Indi.controller.search', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$ProfileIds: {
                allowBlank: true,
                considerOn: [{
                    name: 'access'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.access != 'all');
                    }
                }
            }
        }
    }
});
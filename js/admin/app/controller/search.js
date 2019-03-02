Ext.define('Indi.controller.search', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            rowset: {
                multiSelect: true
            }
        },
        form: {
            formItem$FieldId: {
                jump: '/fields/form/id/{id}/'
            },
            formItem$ProfileIds: {
                allowBlank: true,
                considerOn: [{
                    name: 'access'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.access != 'all');
                    }
                }
            }
        }
    }
});
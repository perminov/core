Ext.define('Indi.controller.grid', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            rowset: {multiSelect: true},
            gridColumn$Editor: {
                icon: '/i/admin/btn-icon-editor.png'
            }
        },
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
            },
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
            },
            formItem$FieldId: {
                jump: '/fields/form/id/{id}/'
            }
        }
    }
});
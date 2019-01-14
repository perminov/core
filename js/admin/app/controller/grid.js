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
            },
            formItem$SummaryText: {
                considerOn: [{
                    name: 'summaryType'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.summaryType == 'text');
                    }
                }
            }
        }
    }
});
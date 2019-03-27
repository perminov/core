Ext.define('Indi.controller.grid', {
    extend: 'Indi.lib.controller.Controller',
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
                    considerchange: function(c, d) {
                        c.setVisible(!d.fieldId);
                    }
                }
            },
            formItem$ProfileIds: {
                considerOn: [{
                    name: 'access'
                }],
                listeners: {
                    considerchange: function(c, d) {
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
                    considerchange: function(c, d) {
                        c.setVisible(d.summaryType == 'text');
                    }
                }
            }
        }
    }
});
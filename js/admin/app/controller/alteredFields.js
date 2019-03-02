Ext.define('Indi.controller.alteredFields', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            rowset: {
                multiSelect: true
            },
            gridColumn$Mode: {
                icon: '/i/admin/field/readonly.png',
                allowCycle: true
            }
        },
        form: {
            formItem$FieldId: {
                jump: '/fields/form/id/{id}/'
            },
            formItem$ProfileIds: {
                considerOn: [{
                    name: 'impact'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.impact != 'all');
                    }
                }
            }
        }
    }
});
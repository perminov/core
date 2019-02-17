Ext.define('Indi.controller.fieldsAll', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        index: {
            gridColumn$Mode: {
                icon: '/i/admin/field/readonly.png',
                allowCycle: true
            }
        },
        form: {
            formItem$EntityId: {
                jump: '/entities/form/id/{id}/',
            },
            formItem$Relation: {
                jump: '/entities/form/id/{id}/',
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none');
                    }
                }
            }
        }
    }
});
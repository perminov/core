Ext.define('Indi.controller.fieldsAll', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        index: {
            gridColumn$Mode: {
                icon: '/i/admin/field/readonly.png',
                allowCycle: true
            },
            gridColumn$StoreRelationAbility: {
                icon: '/i/admin/btn-icon-multikey.png'
            }
        },
        form: {
            formItem$EntityId: {
                jump: '/entities/form/id/{id}/'
            },
            formItem$Relation: {
                jump: '/entities/form/id/{id}/',
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none');
                    }
                }
            }
        }
    }
});
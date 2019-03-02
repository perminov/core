Ext.define('Indi.controller.fields', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumn$Title: {editor: true},
            gridColumn$Alias: {editor: true},
            gridColumn$Mode: {
                icon: '/i/admin/field/readonly.png',
                allowCycle: true
            },
            rowset: {multiSelect: true}
        },
        form: {
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
            },
            formItem$Filter: {
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none');
                    }
                }
            },
            formItem$L10n: {
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.storeRelationAbility == 'none');
                    }
                }
            }
        }
    }
});
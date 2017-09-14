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
            formItem$StoreRelationAbility: {nojs: true},
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
            },
            formItem$Filter: {
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none');
                    }
                }
            },
            formItem$SatelliteAlias: {
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none');
                    }
                }
            },
            formItem$Span: {
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none');
                    }
                }
            },
            formItem$Satellite: {
                considerOn: [{
                    name: 'storeRelationAbility'
                }, {
                    name: 'dependency'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none' && d.dependency != 'u');
                    }
                }
            },
            formItem$Dependency: {
                nojs: true,
                considerOn: [{
                    name: 'storeRelationAbility'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none');
                    }
                }
            },
            formItem$Alternative: {
                considerOn: [{
                    name: 'storeRelationAbility'
                }, {
                    name: 'dependency'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.storeRelationAbility != 'none' && d.dependency != 'u' && d.dependency != 'e');
                    }
                }
            }
        }
    }
});
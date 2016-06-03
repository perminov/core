Ext.define('Indi.controller.fields', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumn$Title: {editor: true},
            gridColumn$Alias: {editor: true},
            gridColumn$Mode: function() {
                return {
                    cls: 'i-column-header-icon',
                    header: '<img src="' + Indi.std + '/i/admin/field/readonly.png" style="left: -1px;">',
                    tooltip: arguments[0].tooltip || arguments[0].header,
                    allowCycle: true
                }
            }
        },
        form: {
            formItem$StoreRelationAbility: {nojs: true},
            formItem$Relation: {
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
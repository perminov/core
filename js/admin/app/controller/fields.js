Ext.define('Indi.controller.fields', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumn$Title: {
                editor: {
                    xtype: 'textfield',
                    allowBlank: false,
                    margin: '0 2 0 3',
                    height: 18
                }
            },
            gridColumn$Alias: {
                editor: {
                    xtype: 'textfield',
                    allowBlank: false,
                    margin: '0 2 0 3',
                    height: 18
                }
            },
            gridColumn$Required: function() {
                return {
                    cls: 'i-column-header-icon',
                    header: '<img src="' + Indi.std + '/i/admin/btn-icon-required.png">',
                    tooltip: arguments[0].tooltip || arguments[0].header
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
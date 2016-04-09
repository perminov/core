Ext.define('Indi.controller.resize', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$Proportions: {nojs: true},
            formItem$MasterDimensionAlias: {
                nojs: true,
                considerOn: [{
                    name: 'proportions'
                }],
                listeners: {
                    enablebysatellite: function(c, d){
                        c.setVisible(d.proportions == 'p');
                    }
                }
            },
            formItem$MasterDimensionValue: {
                considerOn: [{
                    name: 'proportions',
                    clear: false
                }, {
                    name: 'masterDimensionAlias',
                    clear: false
                }, {
                    name: 'slaveDimensionLimitation',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d){
                        c.setVisible(d.proportions == 'c' || (d.proportions == 'p' && (d.masterDimensionAlias == 'width' || d.slaveDimensionLimitation)));
                    }
                }
            },
            formItem$SlaveDimensionValue: {
                considerOn: [{
                    name: 'proportions',
                    clear: false
                }, {
                    name: 'masterDimensionAlias',
                    clear: false
                }, {
                    name: 'slaveDimensionLimitation',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d){
                        c.setVisible(d.proportions == 'c' || (d.proportions == 'p' && (d.masterDimensionAlias == 'height' || d.slaveDimensionLimitation)));
                    }
                }
            },
            formItem$SlaveDimensionLimitation: {
                considerOn: [{
                    name: 'masterDimensionAlias'
                }, {
                    name: 'proportions'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.labelEl.update('Ограничить пропорциональную ' + (d.masterDimensionAlias == 'width' ? 'высоту' : 'ширину'));
                        c.setVisible(d.proportions == 'p');
                    }
                }
            },
            formItem$ChangeColor: {nojs: true},
            formItem$Color: function() {
                return {
                    considerOn: [{
                        name: 'changeColor'
                    }],
                    listeners: {
                        enablebysatellite: function(c, d) {
                            c.setVisible(d.changeColor == 'y');
                        }
                    }
                }
            }
        }
    }
});
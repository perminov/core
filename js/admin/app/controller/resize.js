Ext.define('Indi.controller.resize', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        form: {
            formItem$MasterDimensionAlias: {
                considerOn: [{
                    name: 'proportions'
                }],
                listeners: {
                    considerchange: function(c, d){
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
                    considerchange: function(c, d){
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
                    considerchange: function(c, d){
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
                    considerchange: function(c, d) {
                        c.labelEl.update('Ограничить пропорциональную ' + (d.masterDimensionAlias == 'width' ? 'высоту' : 'ширину'));
                        c.setVisible(d.proportions == 'p');
                    }
                }
            },
            formItem$Color: function() {
                return {
                    considerOn: [{
                        name: 'changeColor'
                    }],
                    listeners: {
                        considerchange: function(c, d) {
                            c.setVisible(d.changeColor == 'y');
                        }
                    }
                }
            }
        }
    }
});
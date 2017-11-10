Ext.define('Indi.controller.noticeGetters', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        index: {

        },
        form: {
            formItem$Criteria: {
                considerOn: [{
                    name: 'criteriaMode'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.criteriaMode == 'shared');
                    }
                }
            },
            formItem$CriteriaUp: {
                considerOn: [{
                    name: 'criteriaMode'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.criteriaMode == 'separate');
                    }
                }
            },
            formItem$CriteriaDown: {
                considerOn: [{
                    name: 'criteriaMode'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.criteriaMode == 'separate');
                    }
                }
            }
        }
    }
});
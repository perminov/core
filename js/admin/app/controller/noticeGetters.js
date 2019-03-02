Ext.define('Indi.controller.noticeGetters', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        index: {

        },
        form: {
            formItem$CriteriaEvt: {
                considerOn: [{
                    name: 'criteriaRelyOn'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.criteriaRelyOn == 'event');
                    }
                }
            },
            formItem$CriteriaInc: {
                considerOn: [{
                    name: 'criteriaRelyOn'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.criteriaRelyOn == 'getter');
                    }
                }
            },
            formItem$CriteriaDec: {
                considerOn: [{
                    name: 'criteriaRelyOn'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.criteriaRelyOn == 'getter');
                    }
                }
            }
        }
    }
});
Ext.define('Indi.controller.notices', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        form: {
            formItem$TplUpHeader: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.tplFor == 'up');
                    }
                }
            },
            formItem$TplUpBody: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.tplFor == 'up');
                    }
                }
            },
            formItem$TplDownHeader: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.tplFor == 'down');
                    }
                }
            },
            formItem$TplDownBody: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.tplFor == 'down');
                    }
                }
            },
            formItem$TplDiffHeader: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.tplFor == 'diff');
                    }
                }
            },
            formItem$TplDiffBody: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.tplFor == 'diff');
                    }
                }
            }
        }
    }
});
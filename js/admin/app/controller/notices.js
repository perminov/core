Ext.define('Indi.controller.notices', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        form: {
            formItem$TplIncSubj: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.tplFor == 'inc');
                    }
                }
            },
            formItem$TplIncBody: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.tplFor == 'inc');
                    }
                }
            },
            formItem$TplDecSubj: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.tplFor == 'dec');
                    }
                }
            },
            formItem$TplDecBody: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.tplFor == 'dec');
                    }
                }
            },
            formItem$TplEvtSubj: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.tplFor == 'evt');
                    }
                }
            },
            formItem$TplEvtBody: {
                considerOn: [{
                    name: 'tplFor',
                    clear: false
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.tplFor == 'evt');
                    }
                }
            }
        }
    }
});
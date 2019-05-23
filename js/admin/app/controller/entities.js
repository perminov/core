Ext.define('Indi.controller.entities', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        index: {
            gridColumn$Title: {editor: true},
            gridColumn$Table: {editor: true},
            panelDockedInner$Actions$Php_InnerHandler: function(action, row, aix, btn) {
                this.panelDockedInner$Actions_DefaultInnerHandlerReload.call(this, action, row, aix, btn);
            }
        },
        form: {
            formItem$Title: {allowBlank: false},
            formItem$Table: {allowBlank: false},
            formItem$System: {nojs: true},
            formItem$UseCache: {
                considerOn: [{
                    name: 'system'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(d.system == 'y');
                    }
                }
            }
        }
    }
});
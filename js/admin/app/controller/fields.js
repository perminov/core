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
            }
        }
    }
});
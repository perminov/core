Ext.define('Indi.controller.enumset', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumnDefault: function() {
                return Ext.merge(this.callParent(arguments), {
                    renderer: function (value) {
                        return value;
                    }
                });
            },
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
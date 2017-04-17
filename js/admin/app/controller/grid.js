Ext.define('Indi.controller.grid', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumn$Editor: function(){
                return {
                    cls: 'i-column-header-icon',
                    header: '<img src="' + Indi.std + '/i/admin/btn-icon-editor.png">',
                    tooltip: arguments[0].tooltip || arguments[0].header
                }
            }
        },
        form: {
            formItem$Alias: {
                allowBlank: true,
                considerOn: [{
                    name: 'fieldId'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(!d.fieldId);
                    }
                }
            },
            formItem$ProfileIds: {
                allowBlank: true,
                considerOn: [{
                    name: 'access'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.access != 'all');
                    }
                }
            }
        }
    }
});
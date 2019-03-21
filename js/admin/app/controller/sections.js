Ext.define('Indi.controller.sections', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumn$RowsOnPage: {editor: true},
            gridColumn$Alias: {editor: true},
            gridColumn$Title: {editor: true},
            rowset: {
                multiSelect: true
            }
        },
        form: {
            formItem$SectionId: {
                jump: '/sections/form/id/{id}/'
            },
            formItem$EntityId: {
                jump: '/entities/form/id/{id}/'
            },
            formItem$Expand: {
                considerOn: [{
                    name: 'sectionId'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(!d.sectionId);
                    }
                }
            },
            formItem$ExpandRoles: {
                considerOn: [{
                    name: 'expand'
                },{
                    name: 'sectionId'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(!d.sectionId && !d.expand.match(/^(all|none)$/));
                    }
                }
            }
        }
    }
});
Ext.define('Indi.controller.sectionActions', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        index: {
            rowset: {multiSelect: true},
            gridColumn$ProfileIds: {editor: true},
            gridColumn$Rename: {editor: true},
            gridColumn$FitWindow: {icon: '/i/admin/btn-icon-toggle-lime-gray.png'}
        },
        form: {
            formItem$ActionId: {
                jump: '/actions/form/id/{id}/'
            }
        }
    }
});
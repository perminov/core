Ext.define('Indi.controller.changeLog', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumn$FieldId: {
                groupable: false,
                sortable: false,
                menuDisabled: true,
                header: 'Свойство'
            },
            gridColumn$Key: {header: 'Тип объекта &raquo; Объект'},
        }
    }
});
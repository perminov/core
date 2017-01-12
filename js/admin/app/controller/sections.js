Ext.define('Indi.controller.sections', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            gridColumn$RowsOnPage: {editor: true},
            gridColumn$Alias: {editor: true},
            gridColumn$Title: {editor: true}
        },
        form: {
            formItem$SectionId: {
                jump: '/sections/form/id/{id}/'
            }
        }
    }
});
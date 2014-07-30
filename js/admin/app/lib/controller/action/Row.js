Ext.define('Indi.lib.controller.action.Row', {
    alternateClassName: 'Indi.Controller.Action.Row',
    extend: 'Indi.Controller.Action',
    mcopwso: ['row'],
    panel: {
        title: '',
        closable: false,
        listeners: {
            afterrender: function(me){
                Indi.trail(true).breadCrumbs();
            }
        }
    },
    row: {
        border: 0
    }
});

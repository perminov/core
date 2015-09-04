Ext.define('Indi.lib.controller.Foto', {

    // @inheritdoc
    extend: 'Indi.Controller',

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Foto',

    // @inheritdoc
    actionsConfig: {
        form: {
            formItem$Title: {allowBlank: true}
        }
    }
});
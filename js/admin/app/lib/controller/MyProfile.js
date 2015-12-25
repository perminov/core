Ext.define('Indi.lib.controller.MyProfile', {

    // @inheritdoc
    extend: 'Indi.Controller',

    // @inheritdoc
    alternateClassName: 'Indi.Controller.MyProfile',

    // @inheritdoc
    actionsConfig: {

        // @inheritdoc
        form: {
            panel: {
                docked: {
                    items: [{alias: 'master'}],
                    inner: {
                        master: [
                            {alias: 'ID'},
                            {alias: 'reload'}, '-',
                            {alias: 'save'}, '-',
                            {alias: 'reset'}, '-',
                            {alias: 'nested'}, '->'
                        ]
                    }
                }
            }
        }
    }
});
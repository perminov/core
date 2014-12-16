Ext.define('Indi.controller.entities', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$GridId: function(item) {
                return {
                    listeners: {
                        change: function() {
                            console.log(arguments);
                        }
                    }
                }
            }
        }
    }
});
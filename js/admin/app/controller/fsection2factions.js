Ext.define('Indi.controller.fsection2factions', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            /**
             * Setup `allowBlank` config to boolean false
             *
             * @param item
             * @return {*}
             */
            formItem$Alias: function(item) {
                item.allowBlank = true;
                return item;
            }
        }
    }
});
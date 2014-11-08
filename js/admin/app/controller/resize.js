Ext.define('Indi.controller.resize', {
    extend: 'Indi.Controller',
    actionsConfig: {
        form: {
            formItem$Proportions: function(item) {
                var data = item.row.view(item.name).store.data;
                data[0].system.js = "show('tr-masterDimensionAlias,tr-masterDimensionValue,tr-slaveDimensionLimitation,tr-slaveDimensionValue');";
                data[1].system.js = "show('tr-masterDimensionValue,tr-slaveDimensionValue'); hide('tr-masterDimensionAlias,tr-slaveDimensionLimitation');";
                data[2].system.js = "hide('tr-masterDimensionAlias,tr-masterDimensionValue,tr-slaveDimensionLimitation,tr-slaveDimensionValue');";
                return item;
            },
            formItem$MasterDimensionAlias: function(item) {
                var data = item.row.view(item.name).store.data;
                data[0].system.js = "if (me.sbl('slaveDimensionLimitation').labelEl) me.sbl('slaveDimensionLimitation').labelEl.update('Ограничить пропорциональную высоту');";
                data[1].system.js = "if (me.sbl('slaveDimensionLimitation').labelEl) me.sbl('slaveDimensionLimitation').labelEl.update('Ограничить пропорциональную ширину');";
                return item;
            },
            formItem$SlaveDimensionLimitation: function(item) {
                return {
                    fieldLabel: 'Ограничить пропорциональную ' + (this.row.masterDimensionAlias == 'width' ? 'высоту' : 'ширину')
                }
            }
        }
    }
});
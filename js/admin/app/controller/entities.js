Ext.define('Indi.controller.entities', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            panel: {
                //dockedItems: [],
            },
            /*panelToolbarMasterItemAction$Form: function(actionItem){
                actionItem.tooltip = 'Hello, world';
                actionItem.javascript = "console.log('delete123');"
                return actionItem;
            },*/
            /*panelToolbarMasterItemA: function() {
                var original = Object.getPrototypeOf(this).panelToolbarMasterItemA.call(this, arguments);
                return original.concat([{
                    text: 'asd',
                    tooltip: 'asdcc'
                }]);
                //var original = Object.getPrototypeOf(this).panelToolbarMasterItemA();
                //return [{text: 'asd', id: this.bid() + '-keyword'}];
            },*/
            /*rowsetToolbarA: function() {
                return [this.rowsetToolbarPaging(), {
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [{
                        //xtype: 'button',
                        text: 'Hello'
                    }]
                }];
            },*/
            rowset: {
            },
            /*rowsetToolbarPagingItemA: function() {
                return Object.getPrototypeOf(this).rowsetToolbarPagingItemA().concat(
                    [{
                        text: 'xls-next',
                        tooltip: 'asd'
                    }]
                )
            },
            store: {
            }*/
            storeLoadCallback: function() {
                //console.log(this, 'entities');
            },
            panelToolbarFilterItemI$System: function(filter) {
                //console.log(filter);
                //filter.items[0].text = '';
                /*filter.itemslisteners.click = function() {
                   console.log('asd');
                };*/
                return filter;
            },
            panelToolbarFilterItemA1: function() {
                var itemA = Object.getPrototypeOf(this).panelToolbarFilterItemA.call(this, arguments);

                itemA.push({
                    xtype: 'datefield',
                    name: 'customDate',
                    fieldLabel: 'Какая-то дата',
                    labelWidth: Indi.metrics.getWidth('Какая-то дата'),
                    labelSeparator: '',
                    width: 85 + Indi.metrics.getWidth('Какая-то дата'),
                    startDay: 1,
                    margin: 0,
                    value: new Date(),
                    validateOnChange: false,
                    listeners: {
                        change: function(cmp){
                            if (!cmp.noReload) this.ctx().filterChange(cmp);
                        }
                    }
                });
                return itemA;
            }
        }
    }
});
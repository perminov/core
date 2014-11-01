/**
 * Special action class, for 'Print' actions
 */
Ext.define('Indi.lib.controller.action.Print', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Row.Print',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Row.Form',

    // @inheritdoc
    panel: {

        // @inheritdoc
        docked: {
            items: [{alias: 'master'}],
            inner: {
                master: [
                    {alias: 'back'}, '-',
                    {alias: 'ID'},
                    {alias: 'reload'}, '-',
                    {alias: 'print'}, '-',
                    {alias: 'reset'}, '-',
                    {alias: 'prev'}, {alias: 'sibling'}, {alias: 'next'}, '-',
                    {alias: 'nested'}, '->',
                    {alias: 'offset'}, {alias: 'found'}
                ]
            }
        }
    },

    /**
     * Master toolbar 'Print' item, for ability to print the built document
     *
     * @return {Object}
     */
    panelDockedInner$Print: function() {

        // Here we check if 'save' action is in the list of allowed actions
        var me = this;

        // 'Save' item config
        return {
            id: me.panelDockedInnerBid() + 'print',
            xtype: 'button',
            text: 'Распечатать',
            handler: function() {
                Ext.getCmp(me.row.id).query('[name="document"]')[0].print()
            },
            //iconCls: 'i-btn-icon-print'
        }
    },

    // @inheritdoc
    formItemA: function() {
        var me = this;
        return [me.formItemXSpan(), {
            xtype: 'ckeditor',
            name: 'document',
            cls: 'i-field',
            value: me.ti().row.view('print'),
            field: {}
        }];
    }
});
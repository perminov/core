/**
 * Special action class, for 'Print' actions
 */
Ext.define('Indi.lib.controller.action.Call', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Row.Call',

    // @inheritdoc
    extend: 'Indi.lib.controller.action.Row',

    // @inheritdoc
    panel: {

        // @inheritdoc
        docked: {
            items: [{alias: 'master'}],
            inner: {
                master: [
                    {alias: 'close'},
                    //{alias: 'ID'},
                    //{alias: 'reload'}, '-',
                    //{alias: 'print'}, '-',
                    //{alias: 'reset'}, '-',
                    //{alias: 'prev'}, {alias: 'sibling'}, {alias: 'next'}, '-',
                    {alias: 'actions'},
                    //{alias: 'nested'},
                    '->'
                    //{alias: 'offset'}, {alias: 'found'}
                ]
            }
        }
    },

    /**
     * row-panel config
     */
    row: {
        xtype: 'dialer',
        layout: {
            type: 'table',
            columns: 3,
            tdAttrs: {
                align: 'center'
            }
        }
    },

    /**
     * Omit south panel
     */
    south: false,

    /**
     * Master toolbar 'Form' item, for ability to go back to form
     *
     * @return {Object}
     */
    panelDockedInner$Form: function() {

        // Here we check if 'form' action is in the list of allowed actions
        var me = this, action$Form = me.ti().actions.r('form', 'alias');

        // 'Form' item config
        return action$Form ? {
            id: me.panelDockedInnerBid() + 'form',
            tooltip: action$Form.title,
            xtype: 'button',
            handler: function() {
                me.goto(me.other('form'));
            },
            iconCls: 'i-btn-icon-form'
        } : null;
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup phone number and SIPNET account balance
        me.row.phone = Indi.ini.demo ? me.ti().data.sipnet.demophone : me.ti().row.clientPhone;
        me.row.balance = me.ti().data.sipnet.balance;
        me.row.pricing = me.ti().data.sipnet.pricing;

        // Call parent
        return me.callParent();
    }
});
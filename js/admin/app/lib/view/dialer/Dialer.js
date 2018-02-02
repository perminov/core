Ext.define('Indi.lib.view.dialer.Dialer', {
    extend: 'Ext.form.Panel',
    alias: 'widget.dialer',
    border: 0,
    bodyPadding: 10,
    width: 234,
    height: 265,
    cls: 'i-dialer',
    getWidthUsage: function() {
        return this.width;
    },
    getHeightUsage: function() {
        return 265;
    },
    layout: {
        type: 'table',
        columns: 3,
        tdAttrs: {
            align: 'center'
        }
    },
    defaults: {
        xtype: 'dialerbutton',
        labelStyle: 'color: white;',
        style: 'color: white'
    },
    phone: '',
    balance: {},
    limit: {},
    items: [],
    initComponent: function() {
        var me = this;

        // Setup call limit
        if (me.pricing.Result) {
            me.pricing.string = [Indi.numberFormat(me.pricing.maxprice, 2), me.pricing.currency].join(' ');
            me.limit.decimal = parseFloat(me.balance.balance) / parseFloat(me.pricing.maxprice);
            me.limit.minutes = Math.floor(me.limit.decimal);
            me.limit.seconds = Math.floor((me.limit.decimal - me.limit.minutes) * 60);
            me.limit.string = Math.floor((me.limit.decimal - me.limit.minutes) * 60);
        } else {
            me.pricing.string = '--.--';
            me.limit.minutes = me.limit.seconds = '--';
        }

        // Setup inner items
        me.items = [{
            xtype: 'textfield',
            value: me.phone,
            colspan: 3,
            width: 215,
            height: 30,
            name: 'phone',
            margin: '0 0 5 0',
            getErrors: function() {
                var errorA = [];
                if (me.pricing.ErrorStr) errorA.push(me.pricing.ErrorStr);
                return errorA;
            },
            listeners: {
                validitychange: function(c, valid) {
                    if (valid) {
                        c.sbl('status').val('готов к звонку');
                        c.sbl('status').inputEl.css('color', '');
                        c.sbl('pricing').inputEl.css('color', '');
                        c.sbl('limit').inputEl.css('color', '');
                    } else {
                        c.sbl('status').val(me.pricing.ErrorStr);
                        c.sbl('status').inputEl.css('color', '#bd292d');
                        c.sbl('pricing').inputEl.css('color', '#bd292d');
                        c.sbl('limit').inputEl.css('color', '#bd292d');
                    }
                },
                boxready: function() {
                    if (me.pricing.Result) return;
                    this.validate();
                }
            }
        }/*,{
            xtype: 'displayfield',
            fieldLabel: 'Статус',
            value: 'готов к звонку',
            labelWidth: Indi.metrics.getWidth('Статус') + 5,
            id: me.id + '-status',
            margin: 0,
            colspan: 3,
            width: 215,
            name: 'status'
        }*/,{
            xtype: 'displayfield',
            fieldLabel: 'Баланс',
            labelWidth: Indi.metrics.getWidth('Баланс') + 5,
            value: [me.balance.balance, me.balance.currency].join(' '),
            margin: 0,
            colspan: 2,
            width: 143,
            name: 'balance'
        },{
            xtype: 'panel',
            rowspan: 3,
            colspan: 1,
            border: 0,
            bodyStyle: 'font-size: 20px; color: white;',
            height: 46,
            html: ''
        },{
            xtype: 'displayfield',
            fieldLabel: 'Цена минуты',
            labelWidth: Indi.metrics.getWidth('Цена минуты') + 6,
            value: me.pricing.string,
            margin: 0,
            colspan: 2,
            width: 143,
            name: 'pricing'
        },{
            xtype: 'displayfield',
            fieldLabel: 'Доступно минут',
            labelWidth: Indi.metrics.getWidth('Доступно минут') + 6,
            value: [me.limit.minutes, me.limit.seconds].join(':'),
            margin: '0 0 8 0',
            colspan: 2,
            width: 143,
            name: 'limit'
        },{
            text: '1'
        },{
            text: '2'
        },{
            text: '3'
        },{
            text: '4'
        },{
            text: '5'
        },{
            text: '6'
        },{
            html: '7'
        },{
            text: '8'
        },{
            text: '9'
        },{
            text: '*'
        },{
            text: '0'
        },{
            text: '#'
        }, {
            colspan: 3,
            xtype: 'panel',
            border: 0,
            bodyStyle: 'background: none;',
            margin: '5 0 0 0',
            defaults: {
                border: 0,
                xtype: 'button'
            },
            items: [{
                cls: 'i-dialer-remove',
                width: 50,
                height: 31,
                handler: function() {
                    var phone = me.down('[name="phone"]');
                    phone.val(phone.val().replace(/.$/, ''));
                }
            }, {
                cls: 'i-dialer-call js-start_client_call',
                width: 84,
                height: 49,
                name: 'call',
                handler: function(btn) {
                    var phone = me.down('[name=phone]').val().toString().replace(/[^0-9]/, '');
                    console.log(phone);
                    if (!btn.lastCid4 || btn.lastCid4 != phone) {
                        btn.el.addCls('js-end_client_call disabled');
                        Indi.load(me.ctx().uri.split('?').shift() + '?make', {
                            params: {phone: me.down('[name=phone]').val()},
                            success: function(response) {
                                var json = response.responseText.json();
                                btn.el.attr({'data-token': json.cid, 'data-lang': Indi.lang.name});
                                btn.lastCid4 = phone;
                                btn.el.removeCls('js-end_client_call disabled');
                                btn.el.click();
                            },
                            failure: function() {
                                btn.el.removeCls('js-end_client_call disabled');
                            }
                        });
                    } else btn.el.click();
                }
            }, {
                cls: 'i-dialer-end',
                width: 50,
                height: 31,
                handler: function(btn) {
                    if (btn.sbl('call').el.hasCls('js-end_client_call')) btn.sbl('call').el.click();
                }
            }]
        }];

        // Call parent
        return me.callParent();
    }
});
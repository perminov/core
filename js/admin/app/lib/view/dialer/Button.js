Ext.define('Indi.lib.view.dialer.Button', {
    extend: 'Ext.button.Button',
    alias: 'widget.dialerbutton',
    width: 65,
    margin: '0 0 5 0',
    listeners: {
        click: function() {
            var me = this, phone = me.sbl('phone');
            phone.val(phone.val() + me.text);
        }
    }
});
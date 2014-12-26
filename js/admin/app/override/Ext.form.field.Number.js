/**
 * 'zeroPad' config support added, for prepending the value, if nesessary, while converting value to rawValue
 */
Ext.override(Ext.form.field.Number, {

    /**
     * The number of '0' (zero) characters, that should be appended to value while converting value to rawValue
     */
    zeroPad: 0,

    /**
     * Whether or not pad the value with an X zeros, where X is a value of `decimalPrecision` config
     */
    precisionPad: false,

    /**
     * The only difference with the native method is that 'zeroPad' config support is added
     *
     * @param value
     * @return {String}
     */
    valueToRaw: function(value) {
        var me = this, decimalSeparator = me.decimalSeparator, integer, fraction;
        value = me.parseValue(value);
        value = me.fixPrecision(value);
        value = Ext.isNumber(value) ? value : parseFloat(String(value).replace(decimalSeparator, '.'));
        value = isNaN(value) ? '' : String(value).replace('.', decimalSeparator);
        if (me.precisionPad) value = Indi.numberFormat(value, me.decimalPrecision, decimalSeparator, '');
        if (me.zeroPad && value.length)
            value = Ext.String.leftPad(value, me.zeroPad + (me.precisionPad ? me.decimalPrecision + 1 : 0), '0');

        return value;
    }
});
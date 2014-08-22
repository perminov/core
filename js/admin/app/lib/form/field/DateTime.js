/**
 * Ext.form.field.Date extension, but with ability to handle date+time instead of only date
 */
Ext.define('Ext.lib.form.field.DateTime', {
    extend: 'Ext.form.field.Date',
    alias: 'widget.datetimefield',
    requires: ['Ext.lib.picker.DateTime'],

    /**
     * Append 'H:i:s' to me.format
     */
    initComponent: function() {
        this.format = this.format + ' ' + 'H:i:s';
        this.callParent();
    },

    /**
     * Method does the same as native, uses Ext.picker.DateTime component as a picker, instead of Ext.picker.Date
     *
     * @return {Ext.picker.DateTime}
     */
    createPicker: function() {
        var me = this, format = Ext.String.format;

        return Ext.create('Ext.picker.DateTime', {
            pickerField: me,
            ownerCt: me.ownerCt,
            renderTo: document.body,
            floating: true,
            hidden: true,
            focusOnShow: true,
            minDate: me.minValue,
            maxDate: me.maxValue,
            disabledDatesRE: me.disabledDatesRE,
            disabledDatesText: me.disabledDatesText,
            disabledDays: me.disabledDays,
            disabledDaysText: me.disabledDaysText,
            format: me.format,
            showToday: me.showToday,
            startDay: me.startDay,
            minText: format(me.minText, me.formatDate(me.minValue)),
            maxText: format(me.maxText, me.formatDate(me.maxValue)),
            listeners: {
                scope: me,
                select: me.onSelect
            },
            keyNavConfig: {
                esc: function() {
                    me.collapse();
                }
            }
        });
    }
});
/**
 */
Ext.override(Ext.form.field.Date, {
    initComponent: function() {
        var me = this;
        me.callParent();
        me.addEvents('boundchange');
    },

    /**
     * Return and array, containing current calendar bound dates
     *
     * @return {Array}
     */
    getBounds: function() {
        var me = this, date = me.picker && me.picker.activeDate ? me.picker.activeDate : new Date(), eDate = Ext.Date, i,
            days = eDate.getDaysInMonth(date), firstOfMonth = eDate.getFirstDateOfMonth(date),
            startingPos = firstOfMonth.getDay() - me.startDay, previousMonth = eDate.add(date, eDate.MONTH, -1),
            prevStart, prevMonthVisibleFrom, nextMonthVisibleTill = 0;

        // This block of code was got from native Ext's picker source
        if (startingPos < 0) startingPos += 7;
        days += startingPos;
        prevStart = eDate.getDaysInMonth(previousMonth) - startingPos;
        for (i = 0; i < Ext.picker.Date.prototype.numDays; ++i)
            if (i < startingPos) {
                if (!prevMonthVisibleFrom) prevMonthVisibleFrom = prevStart+1;
            } else if (i >= days) ++nextMonthVisibleTill;

        // Return bounds as Date objects
        return [
            prevMonthVisibleFrom
                ? new Date(previousMonth.setDate(prevMonthVisibleFrom))
                : firstOfMonth,
            nextMonthVisibleTill
                ? new Date(eDate.add(date, eDate.MONTH, 1).setDate(nextMonthVisibleTill))
                : eDate.getLastDateOfMonth(date)
        ];
    },

    /**
     * clearValue() call removed
     */
    enableBySatellites: function(cfg) {
        var me = this, data = {};

        // Enable field
        if (!cfg.hasOwnProperty('enable') || cfg.enable) me.enable();

        // Fire 'enablebysatellite' event (kept temporarily, for backwards compatibility)
        me.fireEvent('enablebysatellite', me, me.considerOnData());

        // Fire 'considerchange' event
        me.fireEvent('considerchange', me, me.considerOnData());
    },

    /**
     * isValid() call added after disabled dates refresh
     */
    setDisabledDates : function(dd){
        var me = this, i;

        // If disabled dates array is not empty - convert format
        for (i = 0; i < dd.length; i++) dd[i] = Ext.Date.format(new Date(dd[i]), me.format);

        // Call parent
        me.callParent([dd.length ? dd : ['0001-01-01']]);

        // Check if current value is valid
        if (!me.hasZeroValue()) me.isValid();
    },

    /**
     * Alias for setDisabledDates()
     * This method is declared to have the ability to call same-named methods
     * on {xtype: combo.(form|filter|etc)}, {xtype: date} and {xtype: datetime} components
     *
     * @param dd
     */
    setDisabledOptions: function(dd) {
        this.setDisabledDates(dd);
    },

    /**
     * Get this field's input actual width usage
     *
     * @return {Number}
     */
    getInputWidthUsage: function() {
        var me = this, picker = me.getPicker(), pickerWidth;

        // Set picker visibility as 'hidden'
        picker.getEl().setStyle('visibility', 'hidden');

        // Get picker width
        pickerWidth = picker.show().getWidth();

        // Restore picker visibility
        picker.hide().getEl().setStyle('visibility', null);

        // Return either pickerWidth or triggerWrap width, depends on what's greater
        return Math.max(pickerWidth, me.triggerWrap.getWidth());
    },

    /**
     * This function is overridden to return zeroValue instead of empty string
     *
     * @return {*}
     */
    getSubmitValue: function() {
        var format = this.submitFormat || this.format,
            value = this.getValue();

        return value ? Ext.Date.format(value, format) : this.zeroValue;
    }
});
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
    enableBySatellites: function() {
        var me = this, data = {};

        // Enable field
        me.enable();

        // Fire 'enablebysatellite' event
        me.fireEvent('enablebysatellite', me, me.considerOnData());
    },

    /**
     * isValid() call added after disabled dates refresh
     */
    setDisabledDates : function(dd){
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Check if current value is valid
        if (!me.hasZeroValue()) me.isValid();
    }
});
/**
 * Tooltips adjustments
 */
Ext.override(Ext.picker.Date, {

    /**
     * Set up empty ariaTitle config by default
     */
    ariaTitle: '',

    // @inheritdoc
    onRender: function(container, position) {
        var me = this;

        // Setup tooltip type for todayBtn
        me.todayBtn.tooltipType = 'qtip';

        // Call parent
        me.callParent(arguments);

        // Setup tooltips for prev and next month buttons
        me.prevEl.createTooltip({staticOffset: [0, 2]});
        me.nextEl.createTooltip({staticOffset: [0, 2]});
    },

    /**
     * Update the contents of the picker
     * @private
     * @param {Date} date The new date
     * @param {Boolean} forceRefresh True to force a full refresh
     */
    update : function(date, forceRefresh){
        var me = this, active = me.activeDate || new Date(), navMonthChange = false, boundsWas = me.getBounds(), boundsNow;

        // If `forceRefresh` is `true`, we update me.value property, because such a value of `forceUpdate` arg means
        // that current function call is performed by setDisabledDays(), setDisabledDates(), setMinDate() or setMaxDate()
        // calls, and these functions pass this.value as a `date` argument to current function, that leads to picker is
        // showing back the month, that current value is from. This is a problem when we combine navigating through
        // months and disabling the dates within calendar separately to each month, so here is a little trickto avoid this
        if (forceRefresh) date = active;

        // Check if picker date bounds are going to change (active date's month/year changed)
        if (Ext.Date.format(date, 'm-Y') != Ext.Date.format(active, 'm-Y')) navMonthChange = true;

        // Call parent
        me.callParent([date, arguments[1]]);

        // If bound change was detected, and picker has a field, that it's connected to
        if (navMonthChange && me.pickerField) {

            // Get new bounds
            boundsNow = me.getBounds();

            // Fire `boundchange` event on a picker field
            me.pickerField.fireEvent('boundchange', me.pickerField, boundsNow, boundsWas);
        }

        // Return
        return me;
    },

    /**
     * Return and array, containing current calendar bound dates
     *
     * @return {Array}
     */
    getBounds: function() {
        var me = this, date = me.activeDate || new Date(), eDate = Ext.Date, i, days = eDate.getDaysInMonth(date),
            firstOfMonth = eDate.getFirstDateOfMonth(date), startingPos = firstOfMonth.getDay() - me.startDay,
            previousMonth = eDate.add(date, eDate.MONTH, -1), prevStart, prevMonthVisibleFrom, nextMonthVisibleTill = 0;

        // This block of code was got from native Ext's picker source
        if (startingPos < 0) startingPos += 7;
        days += startingPos;
        prevStart = eDate.getDaysInMonth(previousMonth) - startingPos;
        for (i = 0; i < me.numDays; ++i)
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
    }
});
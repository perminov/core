/**
 * Ext.picker.Date extension, but with ability to handle date+time instead of only date
 */
Ext.define('Ext.lib.picker.DateTime', {
    extend: 'Ext.picker.Date',
    alternateClassName: 'Ext.picker.DateTime',
    alias: 'widget.datetimepicker',
    todayText: 'Сейчас',
    requires: ['Ext.lib.form.field.Time'],
    mcopwso: ['timefieldCfg'],
    ariaTitle: '',
    timefieldCfg: {
        hideSeconds: false,
        margin: 5,
        style: 'float: left;',
        spinnerCfg: {
            width: 35,
            readOnlyWidth: 18,
            cls: 'i-field-time',
            style: 'float: left;',
            tooltip: '',
            zeroPad: 2,
            listeners: {
                specialkey: function(spinner, e) {
                    if (e.getKey() == e.ENTER) {
                        var picker = spinner.timefield.datetimepicker;
                        picker.handleDateClick(e, 'activeDate');
                    }
                },
                focus: function(spinner) {
                    spinner.timefield.datetimepicker.stopUpdater = true;
                }
            }
        }
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // If component already have a value, prevent updater function from performing setValue calls,
        // so the only updating todayBtn tooltip contents will remain unstopped
        if (me.value) me.dontEvenStartUpdater = true;

        // Call parent
        this.callParent();

        // Create timefield
        me.timefield = Ext.create('Ext.lib.form.field.Time', Ext.apply({datetimepicker: me}, me.timefieldCfg));
    },

    /**
     * Updater function for todayBtn component tooltip contents.
     * Provides live datetime updating.
     */
    todayBtnToolTipUpdater: function() {
        var me = this;

        // Update tooltip contents with the formatted NOW-date
        if (me.todayBtn.getToolTip && me.todayBtn.getToolTip())
            me.todayBtn.getToolTip().update(Ext.Date.format(new Date(), me.format));

        // If stopUpdate flag is still not turned to boolean 'true', update picker value with formatted NOW-date, also
        if (!me.stopUpdater) me.setValue(new Date(), true);

        // Provide 1-second interval for todayBtnToolTipUpdater function execution
        Ext.defer(me.todayBtnToolTipUpdater, 1000, me);
    },

    // @inheritdoc
    onRender: function(container, position) {
        var me = this;

        // Start tooltip updater
        if (!me.dontEvenStartUpdater) me.todayBtnToolTipUpdater();

        // Call parent
        me.callParent(arguments);

        // Render timefield to picker footer
        me.timefield.render(me.footerEl);
    },

    /**
     * Setup hours, minutes as seconds, got from this.timefield.rawValue, on 'date' argument and return it
     *
     * @param value
     * @return {*}
     */
    appendTime: function(date) {

        // Get timefield's rawValue
        var rawTime = this.timefield.rawValue;

        // Setup hours, minutes as seconds on 'date' Date object
        date.setHours(rawTime.h);
        date.setMinutes(rawTime.m);
        date.setSeconds(rawTime.s);

        // Return date object
        return date;
    },

    // @inheritdoc
    setValue: function(value, interval) {
        var me = this;

        // Set up me.stopUpdater flag to boolean 'true', if current call was not made by updater function
        if (!interval && me.rendered && Ext.Date.format(value, me.format) != Ext.Date.format(new Date(), me.format))
            me.stopUpdater = true;

        me.value = value;
        me.timefield.setValue(value);
        return me.update(Ext.Date.clearTime(me.value, true));
    },

    /**
     * Method does the same as native, but preliminary stops updater function, because the call of this menthod
     * will mean that user started doing it's own interaction with picker, and if updater will still be runnung
     * it will reset the results of user's interation, that we can't allow to happen
     *
     * @return {Ext.picker.Month}
     */
    createMonthPicker: function() {
        this.stopUpdater = true;
        return this.callParent();
    },

    /**
     * Show the next month.
     * @param {Object} e
     * @return {Ext.picker.Date} this
     */
    showNextMonth : function(e){
        return this.setValue(Ext.Date.add(this.value, Ext.Date.MONTH, 1));
    },

    /**
     * Show the previous month.
     * @param {Object} e
     * @return {Ext.picker.Date} this
     */
    showPrevMonth : function(e){
        return this.setValue(Ext.Date.add(this.value, Ext.Date.MONTH, -1));
    },

    /**
     * Two differences from same native Ext's method are:
     *
     * 1. Here we use 'me.setValue(me.appendTime(new Date(t.dateValue)));' instead
     *    of 'me.setValue(new Date(t.dateValue))'
     * 2. Method accepts 'activeDate' keyword as a value of 't' argument. In case of this value
     *    method do a programmatical click on the date, that is already highlighted as already selected.
     *    This feature makes the possibility for user, who has just made time adjustments, just to press ENTER
     *    for hiding the picker and updating the value of datetimefield field, that current picker is used by
     *
     * @param e
     * @param t {HTMLElement|String}
     */
    handleDateClick: function(e, t) {
        var me = this,
            handler = me.handler;

        e.stopEvent();
        if(!me.disabled && (t === 'activeDate' || t.dateValue && !Ext.fly(t.parentNode).hasCls(me.disabledCellCls))) {
            me.doCancelFocus = me.focusOnSelect === false;
            me.setValue(me.appendTime(t === 'activeDate' ? me.activeDate : new Date(t.dateValue)));
            delete me.doCancelFocus;
            me.fireEvent('select', me, me.value);
            if(handler) {
                handler.call(me.scope || me, me, me.value);
            }
            me.onSelect();
        }
    },

    /**
     * The only difference from same native Ext's method is that here
     * we use 'me.setValue(new Date())' instead of 'me.setValue(Ext.Date.clearTime(new Date()))'
     *
     * @return {*}
     */
    selectToday: function() {
        var me = this,btn = me.todayBtn, handler = me.handler;

        if (btn && !btn.disabled) {
            me.setValue(new Date());
            me.fireEvent('select', me, me.value);
            if (handler) {
                handler.call(me.scope || me, me, me.value);
            }
            me.onSelect();
        }
        return me;
    },

    /**
     * The only difference from same native Ext's method is that here
     * we use set up me.stopUpdater flag to true if user at least once did some interaction with picker,
     * that leaded to setting another value to picker or changing picker's active date
     */
    initEvents: function(){
        var me = this,
            eDate = Ext.Date,
            day = eDate.DAY;

        me.superclass.superclass.initEvents.call(me);

        me.prevRepeater = new Ext.util.ClickRepeater(me.prevEl, {
            handler: me.showPrevMonth,
            scope: me,
            preventDefault: true,
            stopDefault: true
        });

        me.nextRepeater = new Ext.util.ClickRepeater(me.nextEl, {
            handler: me.showNextMonth,
            scope: me,
            preventDefault:true,
            stopDefault:true
        });

        me.keyNav = new Ext.util.KeyNav(me.eventEl, Ext.apply({
            scope: me,
            left : function(e){
                if(e.ctrlKey){
                    me.showPrevMonth();
                }else{
                    me.stopUpdater = true;
                    me.update(eDate.add(me.activeDate, day, -1));
                }
            },

            right : function(e){
                if(e.ctrlKey){
                    me.showNextMonth();
                }else{
                    me.stopUpdater = true;
                    me.update(eDate.add(me.activeDate, day, 1));
                }
            },

            up : function(e){
                me.stopUpdater = true;
                if(e.ctrlKey){
                    me.showNextYear();
                }else{
                    me.update(eDate.add(me.activeDate, day, -7));
                }
            },

            down : function(e){
                me.stopUpdater = true;
                if(e.ctrlKey){
                    me.showPrevYear();
                }else{
                    me.update(eDate.add(me.activeDate, day, 7));
                }
            },
            pageUp : me.showNextMonth,
            pageDown : me.showPrevMonth,
            enter : function(e){
                e.stopPropagation();
                return true;
            }
        }, me.keyNavConfig));

        if (me.showToday) {
            me.todayKeyListener = me.eventEl.addKeyListener(Ext.EventObject.SPACE, me.selectToday,  me);
        }
        me.update(me.value);
    }
});
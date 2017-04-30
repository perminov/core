Ext.define('Indi.view.desktop.WindowButton', {
    extend: 'Indi.lib.trail.Button',
    alias: 'widget.windowbutton',

    toggleGroup: 'all',
    textAlign: 'left',
    enableToggle: true,
    pressed: true,
    margin: 0,
    padding: 0,
    border: 1,

    requires: [
        'Ext.util.KeyNav'
    ],

    baseCls: Ext.baseCSSPrefix + 'btn',

    /**
     * @cfg {String} closableCls
     * The CSS class which is added to the tab when it is closable
     */
    closableCls: 'closable',

    /**
     * @cfg {Boolean} closable
     * True to make the Tab start closable (the close icon will be visible).
     */
    closable: true,

    //<locale>
    /**
     * @cfg {String} closeText
     * The accessible text label for the close button link; only used when {@link #cfg-closable} = true.
     */
    closeText: Indi.lang.I_CLOSE,
    //</locale>

    /**
     * @cfg {Indi.view.desktop.Window} window
     * Window, associated with this button
     */
    window: null,

    /**
     * Child els
     */
    childEls: ['closeEl'],

    /**
     * Position
     */
    position: 'top',

    // @inheritdoc
    initComponent: function() {
        var me = this;

        me.addEvents(
            /**
             * @event beforeclose
             * Fires if the user clicks on the Tab's close button, but before the {@link #close} event is fired. Return
             * false from any listener to stop the close event being fired
             * @param {Ext.tab.Tab} tab The Tab object
             */
            'beforeclose',

            /**
             * @event close
             * Fires to indicate that the tab is to be closed, usually because the user has clicked the close button.
             * @param {Ext.tab.Tab} tab The Tab object
             */
            'close'
        );

        // Call parent
        me.callParent(arguments);

        // Set window
        if (me.window) me.setWindow(me.window);
    },

    // @inheritdoc
    getTemplateArgs: function() {
        var me = this, result = me.callParent();

        // Assign `closable` and `closeText` props
        result.closable = me.closable;
        result.closeText = me.closeText;

        // Return
        return result;
    },

    // @inheritdoc
    beforeRender: function() {
        var me = this, windowBar = me.up('windowbar');

        // Call parent
        me.callParent();

        // Add cls
        me.addClsWithUI(me.position);

        // Set all the state classNames, as they need to include the UI
        //me.disabledCls = me.getClsWithUIs('disabled');

        // Sync closable UI
        me.syncClosableUI();

        // Propagate minBtnWidth settings from the owning windowBar
        if (!me.minWidth) {

            // Set up `minWidth` prop
            me.minWidth = windowBar ? windowBar.minBtnWidth : me.minWidth;

            // Adjust `minWidth` prop regarding to `iconCls` prop
            if (me.minWidth && me.iconCls) me.minWidth += 25;
        }

        // Propagate maxBtnWidth settings from the owning windowBar
        if (!me.maxWidth) me.maxWidth = windowBar ? windowBar.maxBtnWidth : me.maxWidth;
    },

    // @inheritdoc
    onRender: function() {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Bind click handler for closeEl
        me.closeEl.on('click', me.onCloseClick, me);
    },

    // @inheritdoc
    enable : function(silent) {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Remove disabled class
        me.removeClsWithUI(me.position + '-disabled');

        // Return
        return me;
    },

    // @inheritdoc
    disable : function(silent) {
        var me = this;

        // Call parent
        me.callParent(arguments);

        // Add disabled class
        me.addClsWithUI(me.position + '-disabled');

        // Return
        return me;
    },

    /**
     * Sets the button as either closable or not.
     * @param {Boolean} closable Pass false to make the button not closable. Otherwise the tab will be made closable (eg a
     * close-button will appear on the button)
     */
    setClosable: function(closable) {
        var me = this;

        // Closable must be true if no args
        closable = (!arguments.length || !!closable);

        // If `closable` arg differs from me.closable
        if (me.closable != closable) {

            // Set up new value for me.closable
            me.closable = closable;

            // Sync UI
            me.syncClosableUI();

            // If me is rendered
            if (me.rendered) {

                // Sync closable elements
                me.syncClosableElements();

                // Tab will change width to accommodate close icon
                me.updateLayout();
            }
        }
    },

    /**
     * This method ensures that the closeBtn element exists or not based on 'closable'.
     * @private
     */
    syncClosableElements: function () {
        var me = this, closeEl = me.closeEl;

        // If button is closable
        if (me.closable) {

            // If closeEl is not yet created
            if (!closeEl) {

                // Create it
                me.closeEl = me.btnWrap.insertSibling({
                    tag: 'a',
                    cls: me.baseCls + '-close-btn',
                    href: '#',
                    title: me.closeText
                }, 'after');

                // Bind close
                me.closeEl.on('click', me.onCloseClick, me);
            }


        // Else if button is not closable, but closeEl was previously created
        } else if (closeEl) {

            // Remove it
            closeEl.remove();
            delete me.closeEl;
        }
    },

    /**
     * This method ensures that the UI classes are added or removed based on 'closable'.
     * @private
     */
    syncClosableUI: function () {
        var me = this, classes = [me.closableCls, me.closableCls + '-' + me.position];

        // Adjust classes
        if (me.closable) me.addClsWithUI(classes); else me.removeClsWithUI(classes);
    },

    /**
     * Sets this tab's attached card. Usually this is handled automatically by the {@link Ext.tab.Panel} that this Tab
     * belongs to and would not need to be done by the developer
     * @param {Indi.view.desktop.Window} windiw The desktop window to set
     */
    setWindow: function(window) {
        var me = this;

        me.window = window;
        me.setText(me.title || window.title);
    },

    /**
     * @private
     * Listener attached to click events on the Tab's close button
     */
    onCloseClick: function(event) {
        var me = this;

        // Close window
        if (me.fireEvent('beforeclose', me) !== false) me.window.close();

        // Fire 'close' event
        me.fireClose();

        // Prevent default
        event.preventDefault();
    },

    /**
     * Fires the close event on the tab.
     * @private
     */
    fireClose: function(){
        this.fireEvent('close', this);
    }
});
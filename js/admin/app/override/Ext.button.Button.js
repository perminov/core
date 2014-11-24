/**
 * Provide programmatical click ability, keeping css 'clicked-style' in force
 */
Ext.override(Ext.button.Button, {
    press: function() {
        var me = this;
        if (!me.disabled) {
            me.onMouseDown({button: 0});
            Ext.defer(function(){try{me.onMouseUp({button: 0});} catch(e){}}, 300);
            me.handler();
        }
    }
});
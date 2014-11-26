/**
 * Provide programmatical click ability, keeping css 'clicked-style' in force
 */
Ext.override(Ext.button.Button, {
    press: function() {
        var me = this, tDom = Ext.EventObject.getTarget(), tEl = Ext.get(tDom);
        if (!me.disabled) {
            if (tEl && tEl.is && tEl.is('input[type="text"]') || tEl.is('textarea')) return;
            me.onMouseDown({button: 0});
            Ext.defer(function(){try{me.onMouseUp({button: 0});} catch(e){}}, 300);
            me.handler();
        }
    }
});
/**
 * Override Ext.tip.ToolTip, for append static 'lastFade' property, to be able to skip fadings,
 * if user move mouse from one tooltipped component to another enough fast
 */
Ext.override(Ext.tip.ToolTip, {
    statics: {
        lastFade: null,
        lastFadeTimeout: null,
        lastTip: null,
        isFast: 350
    }
});

/**
 * Special version of Indi.form.Combo, created for grid/tile/etc store filtering purposes
 */
Ext.define('Indi.lib.form.field.AutoCombo', {

    // @inheritdoc
    extend: 'Indi.lib.form.field.SiblingCombo',

    // @inheritdoc
    alias: 'widget.combo.auto',

    // @inheritdoc
    value: '',

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Pick field from context
        config.field = config.$ctx.ti().fields.r(config.name, 'alias') || config.$ctx.ti().pseudoFields.r(config.name, 'alias');

        // Call parent
        me.callParent(arguments);
    },

    // @inheritdoc
    initComponent: function() {
        var me = this;

        // Setup empty `store` and `subTplData` props
        me.store = {data: [], ids: [], found: '0', enumset: parseInt(me.field.relation) == 6, js: '', optionHeight: "14", page: 1};
        me.subTplData = {attrs: null, pageUpDisabled: "true", selected: {title: null, value: 0}};

        // Call parent
        me.callParent(arguments);
    },

    /**
     * Load store by Ajax-request
     */
    loadStore: function() {
        var me = this, ctx = me.$ctx, ti = ctx.ti(), section = ti.section, scope = ti.scope,
            url = '/' + section.alias + '/form/ph/' + scope.hash + '/aix/' + 0 + '/', f, he, params = {};

        // Show loader
        Indi.app.loader();

        // Load
        Ext.Ajax.request({
            url: Indi.pre.replace(/\/$/, '') + url + 'odata/' + me.field.alias + '/',
            params: Ext.merge(params, {consider: Ext.JSON.encode(me.considerOnData())}),
            success: function(response) {

                // Convert response.responseText to JSON object
                var json = JSON.parse(response.responseText);

                if (!me.isDestroyed) {
                    // Refresh store
                    me.resetInfo(me.value, json);
                    me[me.store.ids.length ? 'enable' : 'disable']();
                    me.fetchUrl = url;

                    if (params.consider) me.fireEvent('refreshchildren', me, parseInt(json.found));
                }
            }
        });
    },

    /**
     * Replace combo's value and store
     *
     * @param value
     * @param store
     */
    resetInfo: function(value, store) {
        var me = this;
        if (me.infoEl) {
            me.infoEl.attr('page-top', 0);
            me.infoEl.attr('page-btm', 0);
            me.infoEl.attr('page-top-reached', value ? 'false' : 'true');
            me.infoEl.attr('page-btm-reached', 'false');
            me.keywordEl.attr('selectedIndex', 1);
            me.fetchedByPageUps = 0;
        }
        me.subTplData.pageUpDisabled = value ? 'false' : 'true';
        if (me.picker) {
            me.picker.destroy();
            delete me.picker;
        }
        me.optionContentsMaxWidth = 0;
        me.store = store;
        if (me.comboInner) {
            me.width = 0;
            me.fitWidth();
        }
        me.setValue(value);
    }
});
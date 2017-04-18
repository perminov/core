/**
 * Here we provide that editableCls cs class will be
 * added to the component in case if it have configured editor
 */
Ext.override(Ext.grid.column.Column, {

    /**
     * Class for higlighting column as editable
     */
    editableCls: 'i-grid-column-editable',

    // @inheritdoc
    constructor: function(config) {
        var me = this;

        // Use icon as a column header contents
        if (config.icon) {
            config.cls = 'i-column-header-icon';
            config.tooltip = config.tooltip || config.header;
            config.header = '<img src="' + Indi.std + config.icon + '">';
        }

        // Call parent
        me.callParent(arguments);

        // If column has an editor - add special css class indicating that
        if (me.editor) me.addCls(me.editableCls);
    }
});

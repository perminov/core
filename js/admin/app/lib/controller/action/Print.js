/**
 * Special action class, for 'Print' actions
 */
Ext.define('Indi.lib.controller.action.Print', {

    // @inheritdoc
    alternateClassName: 'Indi.Controller.Action.Row.Print',

    // @inheritdoc
    extend: 'Indi.Controller.Action.Row.Form',

    // @inheritdoc
    panel: {

        // @inheritdoc
        docked: {
            items: [{alias: 'master'}],
            inner: {
                master: [
                    {alias: 'close'},
                    {alias: 'ID'},
                    {alias: 'reload'}, '-',
                    {alias: 'print'}, '-',
                    {alias: 'reset'}, '-',
                    {alias: 'prev'}, {alias: 'sibling'}, {alias: 'next'}, '-',
                    {alias: 'actions'},
                    {alias: 'nested'}, '->',
                    {alias: 'offset'}, {alias: 'found'}
                ]
            }
        }
    },

    /**
     * row-panel config
     */
    row: {
        layout: 'fit'
    },

    /**
     * Omit south panel
     */
    south: false,

    /**
     * Master toolbar 'Print' item, for ability to print the built document
     *
     * @return {Object}
     */
    panelDockedInner$Print: function() {

        // Here we check if 'save' action is in the list of allowed actions
        var me = this;

        // 'Save' item config
        return {
            id: me.panelDockedInnerBid() + 'doprint',
            xtype: 'button',
            text: 'Распечатать',
            handler: function() {
                var r = Ext.getCmp(me.row.id),
                    editor = r.query('[name="editor"]')[0],
                    frame = r.getEl().down('iframe');

                // Print
                if (editor) editor.print(); else if (frame) window.frames[frame.attr('name')].print();
            }
        }
    },

    /**
     * Master toolbar 'Form' item, for ability to go back to form
     *
     * @return {Object}
     */
    panelDockedInner$Form: function() {

        // Here we check if 'form' action is in the list of allowed actions
        var me = this, action$Form = me.ti().actions.r('form', 'alias');

        // 'Form' item config
        return action$Form ? {
            id: me.panelDockedInnerBid() + 'form',
            tooltip: action$Form.title,
            xtype: 'button',
            handler: function() {
                me.goto(me.other('form'));
            },
            iconCls: 'i-btn-icon-form'
        } : null;
    },

    // @inheritdoc
    formItemA: function() {
        var me = this, fieldA = [
            {xtype: 'editor', alias: 'editor'},
            {xtype: 'frame', alias: 'frame'}
        ];

        // Return
        return me.callParent([fieldA]);
    },

    /**
     * Special form item, representing printable contents area
     *
     * @return {Object}
     */
    formItemXEditor: function() {
        var me = this;
        return {
            xtype: 'ckeditor',
            cls: 'i-field',
            value: me.ti().row.view('#print'),
            field: {
                params: {
                    wide: 'true'
                }
            },
            height: 450,
            editorCfg: {
                height: 450,
                width: 710
            }
        }
    },

    /**
     * Iframe base config. Will only work
     *
     * @param dcfg
     * @return {*}
     */
    formItemXFrame: function(dcfg) {
        var me = this, eItem$, item$, itemI, src, xcfg = {xtype: 'component', cls: 'i-print-iframe', border: 0};

        // Get custom config
        eItem$ = 'formItem$' + Indi.ucfirst(dcfg.field.alias);
        if (Ext.isFunction(me[eItem$]) || Ext.isObject(me[eItem$])) {
            item$ = Ext.isFunction(me[eItem$]) ? me[eItem$](dcfg) : me[eItem$];
        } else if (Ext.isString(me[eItem$])) item$ = me[eItem$];

        // Get iframe src, either from item$'s `field` prop, if item$ is an object
        if (Ext.isObject(item$) && item$.field) src = me.ti().row[item$.field];

        // Or from item$ itself, if it is a string
        else if (Ext.isString(item$)) src = item$;

        // Else
        else {

            // Set src to be blank
            src = 'about:blank';

            // Make sure view's rendered html will be displayed within an iframe
            Ext.merge(xcfg, {listeners: {
                boxready: function() {
                    window.frames[dcfg.id].document.body.innerHTML = me.ti().row._view['#' + me.ti().action.alias];
                }
            }})
        }

        // If src is not a string, or is an empty string - return
        if (!Ext.isString(src) || !src.length) return false;

        // Else prepend src with Indi.std
        else if (src != 'about:blank') src = Indi.std + src;

        // If we gonna embed a pdf-file
        if (src.match(/\.pdf$/) || src == 'about:blank') me.row.bodyPadding = 0;

        // Build iframe markup
        xcfg.html = '<iframe name="'+ dcfg.id + '" src="' + src + '" frameborder="no" width="100%" height="100%"></iframe>';

        // Define dummy size usage calculation functions
        Ext.merge(xcfg, {
            getWidthUsage: function() {
                return 400;
            },
            getHeightUsage: function() {
                return 400;
            }
        });

        // Return
        return xcfg;
    },

    /**
     * Can be {field: 'myFileFieldAlias'}, or 'http://mysite.com'
     */
    formItem$Frame: false
});
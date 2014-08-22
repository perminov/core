var Indi = (function (indi) {
    "use strict";
    var process = function () {

        [].push({
            xtype: 'component',
            id: 'i-action-form-topbar-nav-to-sibling',
            contentEl: jQuery('#i-action-form-topbar-nav-to-sibling-combo-wrapper')[0],
            setKeywordValue: function(value) {
                top.window.Indi.combo.sibling.clearCombo('i-action-form-topbar-nav-to-sibling-id');
            },
            listeners: {
                afterrender: function(){
                    top.window.Indi.combo.sibling.run();
                    top.window.Indi.combo.sibling.rebuildComboData('i-action-form-topbar-nav-to-sibling-id');
                    top.window.Indi.combo.sibling.store['i-action-form-topbar-nav-to-sibling-id'].fetchedByPageUps = 0;
                },
                change: function(selected){
                    if (parseInt(selected.value)) {
                        top.window.Ext.getCmp('iframe-mask').show();

                        var existingIframeQueryString = '?' + instance.getIframe().attr('src').split('?')[1], url;

                        if (selected.mode == 'no-keyword') {

                            // Build the request uri
                            url = indi.pre+'/' + indi.trail.item().section.alias + '/' + indi.trail.item().action.alias+
                                '/id/' + selected.value +
                                '/aix/' + selected.index +
                                '/ph/'+ indi.trail.item().section.primaryHash+'/' +
                                existingIframeQueryString;

                            top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').lastValidValue = selected.index;
                            top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-number').setValue(selected.index);

                            top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-id').lastValidValue = selected.value;
                            top.window.Ext.getCmp('i-action-form-topbar-nav-to-row-id').setValue(selected.value);

                            if (selected.index == indi.trail.item().scope.found) {
                                top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').disable();
                            } else {
                                top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-next').enable();
                            }

                            if (selected.index == 1) {
                                top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').disable();
                            } else {
                                top.window.Ext.getCmp('i-action-form-topbar-nav-to-sibling-prev').enable();
                            }

                        } else {
                            // Build the request uri
                            url = indi.pre+'/' + indi.trail.item().section.alias + '/' + indi.trail.item().action.alias+
                                ''+
                                '/id/' + selected.value +
                                '/ph/'+ indi.trail.item().section.primaryHash+'/' +
                                existingIframeQueryString;
                        }

                        top.window.$('#i-action-form-topbar-nav-to-sibling-id-suggestions').remove();

                        // If save button is toggled
                        if (top.window.Ext.getCmp('i-action-form-topbar-button-save').pressed)

                            // We save current row but remeber the redirect url
                            $('form[name='+indi.trail.item().model.tableName+']')
                                .append('<input type="hidden" name="redirect-url" value="'+url+'"/>')
                                .submit();

                        // Else we just update iframe's src
                        else instance.getIframe().attr('src', url);
                    }

                }
            }
        });
    };

    return indi;

}(Indi || {}));
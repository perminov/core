var Indi = (function (indi) {
    "use strict";
    var process = function () {

        /**
         * Setup `filter` property of indi.proto.combo object
         */
        indi.proto.combo.sibling = function(){

            /**
             * Setup inheritance from indi.proto.combo.form
             */
            indi.proto.combo.form.call(this);

            /**
             * This is for context stabilization
             *
             * @type {*}
             */
            var instance = this;

            /**
             * This will be used at the stage of request uri constructing while within remoteFetch()
             * and also, is used to get a proper stack of callbacks that should be called in run()
             *
             * @type {String}
             */
            this.componentName = 'combo.sibling';

            /**
             * Within filters, we need to setup 'body' element as element, there combo data will be appended to,
             * because it will not be visible otherwise
             *
             * @param name
             * @return {*}
             */
            this.getComboDataAppendToEl = function() {
                return top.window.$('body');
            }

            /**
             * Builds a path to make a fetch request to
             *
             * @return string
             */
            this.fetchRelativePath = function() {
                return indi.pre + '/' + indi.trail.item().section.alias + '/form/'+
                    (indi.trail.item().row.id ? 'id/' + indi.trail.item().row.id + '/' : '') +
                    (indi.scope.aix ? 'aix/' + indi.scope.aix + '/' : '') +
                    'ph/'+ indi.scope.hash;
            }

            /**
             * Filter combos have options position calculation logic, that is different from indi.profo.combo.form
             *
             * @param name
             */
            this.adjustComboOptionsDivPosition = function(name) {
                $('#'+name+'-suggestions').css({
                    top: ($('#'+name+'-keyword').parent().offset().top + $('#'+name+'-keyword').parents('.i-combo').height() - 4) + 'px',
                    left: $('#'+name+'-keyword').parents('.i-combo').offset().left + 'px'
                });
            }

            this.changeHandler = function() {
                var valueFieldId = $(this).attr('id');
                var extComponentId = valueFieldId.replace('-id', '');

                var index;
                if (instance.particularList(valueFieldId)) {
                    index = (indi.scope.aix ? parseInt(indi.scope.aix) : 1)
                        - 1
                        + parseInt($('input[lookup='+valueFieldId+']').attr('selectedIndex'))
                        - instance.store[valueFieldId].fetchedByPageUps;

                } else {
                    index = $('input[lookup='+valueFieldId+']').attr('selectedIndex');
                }
                var opts = {
                    value: $('#'+valueFieldId).val(),
                    index: index,
                    mode: $('#'+valueFieldId+'-info').attr('fetch-mode')
                }

                Ext.getCmp(extComponentId).fireEvent('change', opts);
            }

            this.increaseWidthBy = function(name, pixels) {
                Ext.getCmp('i-action-form-topbar-nav-to-sibling').setWidth(
                    Ext.getCmp('i-action-form-topbar-nav-to-sibling').getWidth() + pixels
                );

                $('#i-action-form-topbar-nav-to-sibling-id-combo').css('width',
                    (parseInt($('#i-action-form-topbar-nav-to-sibling-id-combo').css('width')) + pixels) + 'px'
                );

                $('#i-action-form-topbar-nav-to-sibling-id-suggestions').css('width',
                    (parseInt($('#i-action-form-topbar-nav-to-sibling-id-suggestions').css('width')) + pixels) + 'px'
                );

                return parseInt($('#i-action-form-topbar-nav-to-sibling-id-info').css('margin-left'));
            }

            this.particularList = function(name) {
                return parseInt($('#'+name+'-count').text().replace(',', '')) < parseInt(instance.store[name].found);
            }

            /**
             * Set ExtJs styles for keyword field and trigger button
             */
            this.bindTrigger = function(){

                // Get the keyword selector
                var s = instance.keywordSelector();

                // Bind css class modifications on trigger mouseover event
                $(instance.componentNameClass() + ' .i-combo-trigger').mouseover(function(){

                    // Setup shortcut for combo element
                    var c = $(this).parents('.i-combo');

                    // If combo is disabled or have no lookup results - return
                    if (c.hasClass('i-combo-disabled') || c.find(s).hasClass('i-combo-keyword-no-results')) return;

                    // Add hover class
                    $(this).addClass('x-form-trigger-over');
                });

                // Bind css class modifications on trigger mouseover event
                $(instance.componentNameClass() + ' .i-combo-trigger').mouseout(function(){

                    // Setup shortcut for combo element
                    var c = $(this).parents('.i-combo');

                    // If combo is disabled or have no lookup results - return
                    if (c.hasClass('i-combo-disabled') || c.find(s).hasClass('i-combo-keyword-no-results')) return;

                    // Remove hover class
                    $(this).removeClass('x-form-trigger-over');
                });

                // Bind css class modifications on trigger mousedown event
                $(instance.componentNameClass() + ' .i-combo-trigger').mousedown(function(){

                    // Setup shortcut for combo element
                    var c = $(this).parents('.i-combo');

                    // If combo is disabled or have no lookup results - return
                    if (c.hasClass('i-combo-disabled') || c.find(s).hasClass('i-combo-keyword-no-results')) return;

                    // Setup clicked style
                    $(this).addClass('x-form-trigger-click');
                });

                // Bind css class modifications on trigger mouseup event
                $(instance.componentNameClass() + ' .i-combo-trigger').mouseup(function(){

                    // Setup shortcut for combo element
                    var c = $(this).parents('.i-combo');

                    // If combo is disabled or have no lookup results - return
                    if (c.hasClass('i-combo-disabled') || c.find(s).hasClass('i-combo-keyword-no-results')) return;

                    // Remove clicked style
                    $(this).removeClass('x-form-trigger-click');

                    // Show the options list
                    c.addClass('i-combo-focus').find('.i-combo-keyword').click().focus();
                });

                // Bind css class modifications on keyword input blur event
                $(instance.componentNameClass() + ' .i-combo-keyword').blur(function(){

                    // Setup shortcut for combo element
                    var c = $(this).parents('.i-combo');

                    // If combo is disabled or have no lookup results - return
                    if (c.hasClass('i-combo-disabled') || c.find(s).hasClass('i-combo-keyword-no-results')) return;

                    // Remove focus class
                    c.removeClass('i-combo-focus');
                });

                // Bind css class modifications on keyword input focus event
                $(instance.componentNameClass() + ' .i-combo-keyword').focus(function(){

                    // Setup shortcut for combo element
                    var c = $(this).parents('.i-combo');

                    // If combo is disabled or have no lookup results - return
                    if (c.hasClass('i-combo-disabled') || c.find(s).hasClass('i-combo-keyword-no-results')) return;

                    // Remove focus class
                    c.addClass('i-combo-focus');
                });
            }
        }
        indi.combo.sibling = new indi.proto.combo.sibling();
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof indi.proto !== 'undefined' &&
                typeof indi.proto.combo !== 'undefined' &&
                typeof indi.proto.combo.form !== 'undefined') {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));
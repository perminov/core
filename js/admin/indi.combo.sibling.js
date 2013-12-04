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
                    top: ($('#'+name+'-keyword').parent().offset().top + $('#'+name+'-keyword').parents('.i-combo').height() - 2) + 'px',
                    left: $('#'+name+'-keyword').parent().offset().left + 'px'
                });
            }

            /**
             * Ajdust left margin for '.i-combo-trigger' elements
             *
             * @param name
             */
            this.adjustComboTriggerLeftMargin = function(name) {
                var input = $('.i-combo-keyword[lookup="'+name+'"]');
                input.parents('.i-combo').find('.i-combo-trigger').css('margin-left', (input.parents('.i-combo').width() - 17) + 'px');
                input.parents('.i-combo').find('.i-combo-trigger').css('visibility', 'visible');
            }


            this.changeHandler = function() {
                var valueFieldId = $(this).attr('id');
                var extComponentId = valueFieldId.replace('-id', '');

                var index;
                if (instance.particularList(valueFieldId)) {

                    index = parseInt(indi.scope.aix)
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

                $('#i-action-form-topbar-nav-to-sibling-id-trigger').css('margin-left',
                    (parseInt($('#i-action-form-topbar-nav-to-sibling-id-trigger').css('margin-left')) + pixels) + 'px'
                );

                $('#i-action-form-topbar-nav-to-sibling-id-info').css('margin-left',
                    (parseInt($('#i-action-form-topbar-nav-to-sibling-id-info').css('margin-left')) + pixels) + 'px'
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
                $(instance.componentNameClass() + ' .i-combo-keyword').focus(function(){
                    $(this).addClass('x-form-focus').addClass('x-field-form-focus').addClass('x-field-default-form-focus');
                    $(this).parents('table').find('.i-combo-trigger').addClass('x-form-trigger-over').addClass('x-form-arrow-trigger-over');
                });
                $(instance.componentNameClass() + ' .i-combo-keyword').blur(function(){
                    $(this).removeClass('x-form-focus').removeClass('x-field-form-focus').removeClass('x-field-default-form-focus');
                    $(this).parents('table').find('.i-combo-trigger').removeClass('x-form-trigger-over').removeClass('x-form-arrow-trigger-over');
                });
                $(instance.componentNameClass() + ' .i-combo-trigger').hover(function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find('.i-combo-keyword').hasClass('i-combo-keyword-no-results') ==  false)
                            if ($(this).parents('table').find('.i-combo-keyword').hasClass('x-form-focus')) {
                                $(this).parents('table').addClass('x-form-trigger-wrap-focus');
                            } else {
                                $(this).addClass('x-form-trigger-over').addClass('x-form-arrow-trigger-over');
                            }
                }, function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find('.i-combo-keyword').hasClass('i-combo-keyword-no-results') ==  false)
                            if ($(this).parents('table').find('.i-combo-keyword').hasClass('x-form-focus')) {
                                $(this).parents('table').removeClass('x-form-trigger-wrap-focus');
                            } else {
                                $(this).removeClass('x-form-trigger-over').removeClass('x-form-arrow-trigger-over');
                            }
                });
                $(instance.componentNameClass() + ' .i-combo-trigger').mousedown(function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find('.i-combo-keyword').hasClass('i-combo-keyword-no-results') ==  false)
                        $(this).addClass('x-form-arrow-trigger-click').addClass('x-form-trigger-click');
                });
                $(instance.componentNameClass() + ' .i-combo-trigger').mouseup(function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find('.i-combo-keyword').hasClass('i-combo-keyword-no-results') ==  false) {
                        $(this).parents('.i-combo').find('.i-combo-keyword').click();
                        $(this).parents('.i-combo').find('.i-combo-keyword').addClass('x-form-focus').addClass('x-field-form-focus').addClass('x-field-default-form-focus');
                        $(this).removeClass('x-form-arrow-trigger-click').removeClass('x-form-trigger-click');
                    }
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
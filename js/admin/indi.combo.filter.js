var Indi = (function (indi) {
    "use strict";
    var process = function () {

        /**
         * Setup `filter` property of indi.proto.combo object
         */
        indi.proto.combo.filter = function(){

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
            this.componentName = 'combo.filter';

            /**
             * We need this to be able to separate options div visibility after keyword was erased
             * At form combos options wont be hidden, but here same param is set to true
             *
             * @type {Boolean}
             */
            this.hideOptionsAfterKeywordErased = true;

            /**
             * Here will be stored a value created by setTimeout() call, if combo is multiple and it's value
             * was changed. At such case it'll be better to wait a bit after change handler firing
             */
            this.multipleComboFilterDelay;


            /**
             * Within filters, we need to setup 'body' element as element, there combo data will be appended to,
             * because it will not be visible otherwise
             *
             * @param name
             * @return {*}
             */
            this.getComboDataAppendToEl = function() {
                return $('body');
            }


            /**
             * Instead just of making keyword fields (related to enumset combos) readonly, we also make an ability
             * to erase selected values
             *
             * @param name
             */
            this.setReadonlyIfNeeded = function(name) {
                if (this.store[name].enumset) {
                    $('#'+name+'-keyword').attr('no-lookup', 'true');
                }
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

            /**
             * Here we do not exec options-specific javascript (as it would be done at indi.proto.combo.form) but here we do
             * things, that are especially related to filters
             */
            this.changeHandler = function() {

                // Get name of the combo
                var name = $(this).attr('id');

                // Remove attributes from hidden field, if it's value became 0. We do it here only for single-value combos
                // because multiple-value combos have different way of how-and-when the same aim should be reached -
                // attributes deletion for multiple-value combos is implemented in this.bindDelete() function of this script
                if ($('#'+name+'-info').hasClass('i-combo-info-multiple') == false && $('#'+name).val() == '0') {
                    if (instance.store[name].attrs && instance.store[name].attrs.length) {
                        for(var n = 0; n < instance.store[name].attrs.length; n++) {
                            $('#'+name).removeAttr(instance.store[name].attrs[n]);
                        }
                    }

                    // Also we remove a .i-combo-color-box element, related to previously selected option
                    if ($('#'+name+'-keyword').val() == '#' || $('#'+name+'-keyword').val() == '')
                        $('#'+name+'-keyword').parent().find('> .i-combo-color-box').remove();
                }

                if ($(this).val() == '0' && $(this).attr('boolean') != 'true') $(this).val('');

                if ($('#'+name+'-info').hasClass('i-combo-info-multiple')) {
                    var selectedItemsTotalWidth = 0;
                    $('#'+name).parent().find('.i-combo-selected-item').each(function(index){
                        selectedItemsTotalWidth += $(this).width();
                    })
                    selectedItemsTotalWidth += $('#'+name).width();
                    $('#'+name).parent().width(selectedItemsTotalWidth);
                }

                // If current combo is a satellite for one or more other combos, we should refres data in that other combos
                $('.i-combo-info[satellite="'+name+'"]').each(function(){
                    var satellited = $(this).parents('.i-combo').find('.i-combo-keyword').attr('lookup');
                    $('#'+satellited).attr('change-by-refresh-children', 'true');
                    instance.setDisabled(satellited);

                    // Here we unset dependent filter combo value, because if we won't do that, combo value will
                    // be included as a filtering param of grid rowset retrieving request, and there will be wrong results
                    $('#'+satellited).val(0);
                    $('#'+satellited+'-keyword').val('');
                });

                $('.i-combo-info[satellite="'+name+'"]').each(function(){
                    var satellited = $(this).parents('.i-combo').find('.i-combo-keyword').attr('lookup');
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') == false) {
                        $('#'+satellited).attr('change-by-refresh-children', 'true');
                        instance.remoteFetch({
                            field: satellited,
                            satellite: $('#'+$(this).attr('satellite')).val(),
                            mode: 'refresh-children'
                        });
                    }
                });

                // We should do the check, because if combo has a dependent combos, they are also call their change handlers
                // but here we do not need that
                if (!$('#'+name).attr('change-by-refresh-children')) {

                    // Provide a delay befre for multiple-combo value change handler will run
                    if ($('#'+name+'-info').hasClass('i-combo-info-multiple')) {
                        clearTimeout(instance.multipleComboFilterDelay);
                        instance.multipleComboFilterDelay = setTimeout(function(){
                            indi.action.index.filterChange({noReload: false, xtype: 'combobox'});
                        }, 400);
                    } else {
                        indi.action.index.filterChange({noReload: false, xtype: 'combobox'});
                    }
                } else {
                    $('#'+name).removeAttr('change-by-refresh-children');
                }
            }

            /**
             * Builds a path to make a fetch request to
             *
             * @return string
             */
            this.fetchRelativePath = function() {
                return indi.pre + '/' + indi.trail.item().section.alias + '/form';
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
                        $(this).parents('table').find('.i-combo-keyword').addClass('x-form-focus').addClass('x-field-form-focus').addClass('x-field-default-form-focus');
                        $(this).removeClass('x-form-arrow-trigger-click').removeClass('x-form-trigger-click');
                    }
                });
            }
        }
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof indi.combo !== 'undefined') {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));
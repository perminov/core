var Indi = (function (indi) {
    "use strict";
    var process = function () {
        /**
         * Setup empty indi.proto.combo object
         *
         * @type {Object}
         */
        indi.proto.combo = indi.proto.combo || {};

        /**
         * Setup `form` property of indi.proto.combo object - this will be main prototype for all combos
         */
        indi.proto.combo.form = function(){

            /**
             * This is for context stabilization
             *
             * @type {*}
             */
            var instance = this;

            /**
             * Options data storage
             *
             * @type {Object}
             */
            this.store = {};

            /**
             * This will be used at the stage of request uri constructing while within remoteFetch()
             * and also, is used to get a proper stack of callbacks that should be called in run()
             *
             * @type {String}
             */
            this.componentName = 'combo.form';

            /**
             * Configuration
             *
             * @type {Object}
             */
            this.options = {
                removeComboDataDivs: true
            }

            this.averageTitleCharWidth = 6.5;

            /**
             * We need this to be able to separate options div visibility after keyword was erased
             * At form combos options wont be hidden, but at indi.proto.combo.filter same param is set to true
             *
             * @type {Boolean}
             */
            this.hideOptionsAfterKeywordErased = false;

            /**
             * Number of items, that will be visible by default
             *
             * @type {Number}
             */
            this.visibleCount = 20;

            /**
             * Regular expression for color detecting
             *
             * @type {RegExp}
             */
            this.colorReg = new RegExp('^[0-9]{3}(#[0-9a-fA-F]{6})$', 'i');

            /**
             * Ajdust left margin for '.combo-info' elements
             *
             * @param name
             */
            this.adjustComboInfoLeftMargin = function(name, forceAdjust) {
                var input = $('.i-combo-keyword[lookup="'+name+'"]');
                var width = input.parent().find('.i-combo-info').width();

                // We set margin only once, or if forceAdjust agrument is passed.
                // forceAdjust argument is passed when response json has 'found' property
                if (!parseInt(input.parent().find('.i-combo-info').css('margin-left')) || forceAdjust) {
                    if ($('#'+name+'-info').hasClass('i-combo-info-multiple')) {
                        input.parent().find('.i-combo-info').css('margin-left', (input.parent().width() - width - 3) + 'px');
                    } else {
                        input.parent().find('.i-combo-info').css('margin-left', (input.parent().width() - width - 16) + 'px');
                    }
                }
            }

            /**
             * Ajdust left margin for '.i-combo-trigger' elements
             *
             * @param name
             */
            this.adjustComboTriggerLeftMargin = function(name) {
                var input = $('.i-combo-keyword[lookup="'+name+'"]');
                input.parents('.i-combo').find('.i-combo-trigger').css('margin-left', (input.parents('.i-combo').width() - 18) + 'px');
                input.parents('.i-combo').find('.i-combo-trigger').css('visibility', 'visible');
            }

            /**
             * Adjust height of div, containing ul with options
             * @param name
             */
            this.adjustComboOptionsDivHeight = function(name) {
                if ($('#'+name+'-suggestions ul li').length >= instance.visibleCount) {
                    $('#'+name+'-suggestions').css('height', (instance.visibleCount * instance.store[name].optionHeight + 1) + 'px');
                } else {
                    $('#'+name+'-suggestions').css('height', ($('#'+name+'-suggestions ul li').length * instance.store[name].optionHeight + 1) + 'px');
                }
            }


            /**
             * Empty function here, but is redeclared in indi.proto.combo.filter, because
             * at filters, options divs are appended to different dom nodes and different coordinates are applied
             *
             * @param name
             */
            this.adjustComboOptionsDivPosition = function(name) {}

            /**
             * Adjust keyword input field after each append new selected item to list of selected items or delete it from list
             * Function is used only if combo is running in multiple mode
             *
             * @param name
             */
            this.adjustKeywordFieldWidth = function(name) {
                var width, decrease;
                if ($('#'+name+'-keyword').parents('.i-combo-multiple').length) {
                    decrease = 10;
                } else {
                    decrease = 10;
                }
                if ($('#'+name+'-keyword').parent().find('.i-combo-selected-item').length) {
                    decrease += $('#'+name+'-keyword').parent().find('.i-combo-selected-item').last().position().left;
                    decrease += $('#'+name+'-keyword').parent().find('.i-combo-selected-item').last().width() + 10;
                } else if ($('#'+name+'-keyword').parent().find('> .i-combo-color-box').length) {
                    decrease += $('#'+name+'-keyword').parent().find(' > .i-combo-color-box').width() + 14;
                }
                width = $('#'+name+'-keyword').parents('.i-combo').width() - decrease;
                $('#'+name+'-keyword').width(width);
            }

            /**
             * Quotes string that later will be used in regular expression.
             *
             * @param str
             * @param delimiter
             * @return {String}
             */
            this.pregQuote = function(str, delimiter) {
                return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
            }

            /**
             * Function for hiding options list
             *
             * @param name
             */
            this.hideSuggestions = function(name) {

                // Hide options
                $('#'+name+'-suggestions').hide();

                // Hide info about count and found
                $('#'+name+'-info').hide();
            }


            /**
             * Builds html for new options list, bind events and do some more things
             *
             * @param requestData
             * @param responseData
             */
            this.afterFetchAdjustments = function(requestData, responseData) {
                // Get name of field
                var name = requestData.field;

                // Remove more attribute
                $('#'+name+'-suggestions').removeAttr('more');

                // Rebuild options list
                var html = instance.suggestions(instance.store[name], name);
                $('#'+name+'-suggestions').html(html);

                // Set scrolling if number of options more than instance.visibleCount
                $('#'+name+'-suggestions').css('overflow-y', $('#'+name+'-suggestions ul li').length > instance.visibleCount ? 'scroll' : '');

                // Adjust options div height
                instance.adjustComboOptionsDivHeight(name);
                // If at least one result was found
                if (responseData['found']) {

                    // Adjust left margin for '.i-combo-info' because width of info could be changed
                    instance.adjustComboInfoLeftMargin(name, true);

                    // We get json['found'] value only in case if we are running 'keyword' fetch mode,
                    // and in json is stored first portion of results and this mean that paging up shoud be disabled
                    $('#'+name+'-info').attr('page-top-reached', 'true');

                    // Also, we should renew 'page-btm-reached' attribute value
                    if (responseData['found'] <= instance.visibleCount) {
                        $('#'+name+'-info').attr('page-btm-reached', 'true');
                    } else {
                        $('#'+name+'-info').attr('page-btm-reached', 'false');
                    }
                }

                // Bind handlers for hover and click events for each option
                $('#'+name+'-suggestions'+' ul li[class!="disabled"]').hover(
                    function(){
                        $(this).parent().find('li').removeClass('selected');
                        $(this).addClass('selected');
                        var k = $(this).parent().find('li[class!="disabled"]').index(this);
                        $('#'+name+'-keyword').attr('selectedIndex', k+1);
                    }
                );
                $('#'+name+'-suggestions'+' ul li[class!="disabled"]').click(instance.select);

                // If results set is not empty
                if ($(html).find('li[class!="disabled"]').length) {

                    // Show options list after keyword typing is finished
                    if ($('#'+name+'-suggestions').css('display') == 'none') {

                        // If we selected some option in satellite and current results are
                        // results for satellited field, we do not expand them at this time.
                        // We just remove 'no-results-within' class from satellited field
                        // keyword and set keyword to empty string
                        if (requestData.mode == 'refresh-children') {
                            $('#'+name+'-keyword').removeClass('no-results-within').val('');
                            $('#'+name).change();

                        // Show results
                        } else {
                            $('#'+name+'-keyword').click();
                        }
                    }

                    // Options selected adjustments
                    if (requestData.more && requestData.more.toString().match(/^(upper|lower)$/)) {

                        // If these was no more results
                        if (responseData.ids['length'] <= instance.visibleCount) {

                            // We mark that top|bottom range is reached
                            if (responseData.ids['length'] < instance.visibleCount)
                                $('#'+name+'-info').attr('page-'+(requestData.more == 'upper' ? 'top' : 'btm')+'-reached', 'true');

                            // Move selectedIndex at the most top
                            if (requestData.more == 'upper') {
                                $('#'+name+'-keyword').attr('selectedIndex', 1);

                                // Move selectedIndex at the most bottom
                            } else if (requestData.more == 'lower' && responseData.ids['length'] < instance.visibleCount) {
                                $('#'+name+'-keyword').attr('selectedIndex', $('#'+name+'-suggestions'+' ul li[class!="disabled"]').size());
                            }
                        }

                        if (requestData.more.toString() == 'upper') {
                            instance.store[name].fetchedByPageUps = instance.store[name].fetchedByPageUps || 0;
                            instance.store[name].fetchedByPageUps += responseData.data.length;

                            for (var i = 0; i < responseData.data.length; i++)
                                if (responseData['data'][i].system && responseData['data'][i].system['disabled'])
                                    instance.store[name].fetchedByPageUps--;
                        }

                        // Adjust selection based on selectedIndex
                        instance.keyDownHandler(name, requestData.more == 'upper' ? 33 : 34);

                        // Update page-top|page-btm value
                        $('#'+name+'-info').attr('page-'+ (requestData.more == 'upper' ? 'top' : 'btm'), requestData.page);
                    }

                    // Increase combo width, if needed
                    var prevMaxLength, backup;
                    if (instance.store[name].backup
                        && instance.store[name].backup.options
                        && instance.store[name].backup.options.titleMaxLength) {
                        prevMaxLength = instance.store[name].backup.options.titleMaxLength;
                        backup = true;
                    } else {
                        prevMaxLength = instance.store[name].titleMaxLength;
                        backup = false;
                    }

                    if (responseData.titleMaxLength > prevMaxLength) {
                        var increasedMarginLeft = instance.increaseWidthBy(name, Math.round(
                            (responseData.titleMaxLength - prevMaxLength) *
                                instance.averageTitleCharWidth
                        ));
                        instance.store[name].titleMaxLength = responseData.titleMaxLength;
                        if (backup && increasedMarginLeft) {
                            instance.store[name].backup.options.titleMaxLength = responseData.titleMaxLength;
                            instance.store[name].backup.info = instance.store[name].backup.info.replace(/margin-left: [0-9]+px/, 'margin-left: ' + increasedMarginLeft + 'px');
                        }
                    }

                    // Restore trigger pic because previously it could have disabled-style of appearance
                    if ($('#'+name+'-keyword').parents('.i-combo').hasClass('simple-disabled') == false)
                        $('#'+name+'-trigger').attr('src', STD+'/i/admin/trigger-system.png');

                // Else if results set is empty (no non-disabled options), we hide options, and set red
                // color for keyword, as there was no related results found
                } else {

                    // Hide options list div
                    if ($('#'+name+'-suggestions').css('display') != 'none') $('#'+name+'-keyword').click();

                    // If just got resuts are result for satellited combo, autofetched after satellite value was changed
                    // and we have no results related to current satellite value, we disable satellited combo
                    if (requestData.mode == 'refresh-children') {
                        instance.setDisabled(name, true);

                    // Else if reason of no results was not in satellite, we add special css class for that case
                    } else {
                        $('#'+name+'-keyword').addClass('i-combo-keyword-no-results');
                        $('#'+name+'-trigger').attr('src', STD+'/i/admin/trigger-system-disabled.png');
                    }
                }
            }


            /**
             * Builds a path to make a fetch request to
             *
             * @return string
             */
            this.fetchRelativePath = function() {
                if (window.comboFetchRelativePath) {
                    return STD + window.comboFetchRelativePath;
                } else {
                    return '.';
                }
            }

            this.increaseWidthBy = function(name, pixels) {

            }

            /**
             * Prepare request parameters, do request, fetch data and rebuild combo
             *
             * @param data
             */
            this.remoteFetch = function(data){
                // Get name of field
                var name = data.field;

                // Show loading pic
                $('#'+name+'-count')
                    .html('<img src="' + STD + '/i/admin/combo-loading-pic.gif" class="i-combo-loader" width="15">')
                    .addClass('i-combo-count-visible');

                // Appendix
                var parts = instance.componentName.split('.'), appendix = [];
                for (var i = 0; i < parts.length; i++) appendix.push(parts[i], 1); appendix = appendix.join('/');

                // Fetch request
                $.post(instance.fetchRelativePath() + '/'+appendix+'/', data,
                    function(json) {
                        // Save current options to backup
                        var backupOptions = []; backupOptions = Indi.copy(instance.store[name]);

                        // If current options list should be prepended with fetched options
                        if (data.more == 'upper') {

                            // Empty current options
                            instance.store[name]['ids'] = [];
                            instance.store[name]['data'] = [];

                            // So now we start to fill instance.store array with fetched options
                            for (var key = 0; key < json['ids'].length; key++) {
                                instance.store[name]['ids'].push(json['ids'][key]);
                                instance.store[name]['data'].push(json['data'][key]);
                            }

                            // And after that we append options from backupOptions, so as the result
                            // we will have full options list in correct order
                            for (var key = 0; key < backupOptions['ids'].length; key++) {
                                instance.store[name]['ids'].push(backupOptions['ids'][key]);
                                instance.store[name]['data'].push(backupOptions['data'][key]);
                            }

                            // Merge optgroup info
                            if (instance.store[name].optgroup)
                                instance.store[name].optgroup = instance.mergeOptgroupInfo(instance.store[name].optgroup, json.optgroup);

                        // Else if fetched options should be appended to current options list
                        } else if (data.more == 'lower') {

                            // If we are dealing with tree of results, we should merge existing options tree
                            // with tree of just received additional page of results
                            if (instance.store[name].tree) {

                                // Merge trees
                                instance.store[name] = instance.merge(instance.store[name], json);

                            // Else we just append fetched options to existing options
                            } else {
                                for (var key in json['ids']) {
                                    instance.store[name]['ids'].push(json['ids'][key]);
                                    instance.store[name]['data'].push(json['data'][key]);
                                }
                            }

                            // Merge optgroup info
                            if (instance.store[name].optgroup)
                                instance.store[name].optgroup = instance.mergeOptgroupInfo(instance.store[name].optgroup, json.optgroup);

                        // Otherwise we just replace current options with fetched options
                        } else {
                            var jsBackup = instance.store[name].js;
                            var optionHeightBackup = instance.store[name].optionHeight;
                            instance.store[name] = json;
                            instance.store[name].js = jsBackup;
                            instance.store[name].optionHeight = optionHeightBackup;
                        }
                        instance.store[name].backup = backupOptions.backup;

                        // Build html for options, and do all other things
                        instance.afterFetchAdjustments(data, json);
                    }, 'json'
                )
            }

            /**
             * Function is used in case if all possible options, within which keyword-search will be processing - are already collected.
             * They can be collected initially (if their total count <= Indi_Db_Table_Row_Beautiful::$comboOptionsVisibleCount) or
             * can be collected step by step while paging upper/lower. So, since they are a got, any keyword search will run
             * without requests to database, and will be completely handled by javascript. Such scheme will be used until next
             * database request - this can happen if current combo field has a satellite, and satellite value was changed
             *
             * @param data Request data object, containing same properties, as per remote-fetch scheme
             */
            this.localFetch = function(data) {

                // Get the name of combo field
                var name = data.field;

                // Empty options
                instance.store[name].data = [];
                instance.store[name].ids = [];

                // Prepare regular expression for keyword search
                var keywordReg = new RegExp('^'+instance.pregQuote(data.keyword, '/'), 'i');

                // This variable will contain a title, which will be tested against a keyword
                var against;

                // If we are dealing with tree of options, we should find not only search results, but also all level parents
                // for user to be able to view all parents of each result
                if (instance.store[name].tree) {
                    var results = [];
                    var parents = [];
                    var parentId, currentIndex;

                    // Collect ids of options that are primary results of search, and collect ids of all their distinct parents
                    for (var i = 0; i < instance.store[name].backup.options.data.length; i++) {

                        // If tested title is a color, we should strip hue part of title, before keyword match will be performed
                        against = instance.color(instance.store[name].backup.options.data[i].title).title;

                        // Test title against a keyword
                        if (keywordReg.test(against)) {
                            results.push(instance.store[name].backup.options.ids[i]);
                            currentIndex = i;
                            while (parentId = parseInt(instance.store[name].backup.options.data[currentIndex].system.parentId)) {
                                if (parents.indexOf(parentId) == -1) {
                                    parents.push(parentId);
                                }
                                currentIndex = instance.store[name].backup.options.ids.indexOf(parentId);
                            }
                            parentId = 0;
                        }
                    }

                    // Remove items (from parents array), that also are primary results
                    for (var i = 0; i < results.length; i++) {
                        if (parents.indexOf(results[i]) != -1) {
                            parents.splice(parents.indexOf(results[i]), 1);
                        }
                    }

                    // Walk though full backuped options list and pick items, that are primary results or are parents for
                    // primary results
                    for (var i = 0; i < instance.store[name].backup.options.data.length; i++) {
                        var optionId = instance.store[name].backup.options.ids[i];
                        if (results.indexOf(optionId) != -1 || parents.indexOf(optionId) != -1) {
                            instance.store[name].ids.push(instance.store[name].backup.options.ids[i]);
                            instance.store[name].data.push(Indi.copy(instance.store[name].backup.options.data[i]));

                            // Mark parents as disabled, so they will be no selectable
                            if (parents.indexOf(optionId) != -1) {
                                var disabledIndex = instance.store[name].data.length - 1;
                                instance.store[name].data[disabledIndex].system.disabled = true;
                            }
                        }
                    }

                    // Set up number of found (primary) results
                    instance.store[name].found = results.length;

                // If we are dealing with non-tree list of options, all it simpler for a bit
                } else {
                    for (var i in instance.store[name].backup.options.data) {

                        // If tested title is a color, we should strip hue part of title, before keyword match will be performed
                        against = instance.color(instance.store[name].backup.options.data[i].title).title;

                        // Test title against a keyword
                        if (keywordReg.test(against)) {
                            instance.store[name].data.push(instance.store[name].backup.options.data[i]);
                            instance.store[name].ids.push(instance.store[name].backup.options.ids[i]);
                        }
                    }
                    instance.store[name].found = instance.store[name].ids.length;
                }

                // Here we build html for options list, setup scrolling if needed, adjust combo options div height and
                // margin-left for .i-combo-info, bind hover and click handlers on each option and do other things
                instance.afterFetchAdjustments(data, instance.store[name]);
            }

            /**
             * Rebuild html of options list of combo data, apply some styles, props, attrs and events
             *
             * @param name
             */
            this.rebuildComboData = function(name) {
                // Set initial 'index' and 'selectedIndex' attribs values
                if ($('#'+name+'-keyword').attr('selectedIndex') == undefined) {
                    $('#'+name+'-keyword').attr('selectedIndex', 0);
                }

                // Rebuild html for options
                var html = instance.suggestions(instance.store[name], name);
                $('#'+name+'-suggestions').html(html);

                // Set height of options list div
                instance.adjustComboOptionsDivHeight(name);

                // Adjust positioning
                instance.adjustComboOptionsDivPosition(name);

                // Set height of option height for all options
                $('#'+name+'-suggestions li').css({height: instance.store[name].optionHeight + 'px', overflow: 'hidden', 'vertical-align': 'top'});
                $('#'+name+'-suggestions li:last-child').css({height: (instance.store[name].optionHeight-1) + 'px', overflow: 'hidden', 'vertical-align': 'top'});

                // Set special css class for options if optionHeight > 14
                if (instance.store[name].optionHeight > 14) $('#'+name+'-suggestions ul').addClass('tall');

                // Set scrolling if number of options more than instance.visibleCount
                $('#'+name+'-suggestions').css('overflow-y', $('#'+name+'-suggestions ul li').length > instance.visibleCount ? 'scroll' : '');

                // Bind a 'selected' class adding on hover
                $('#'+name+'-suggestions ul li[class!="disabled"]').hover(
                    function(){
                        $(this).parent().find('li').removeClass('selected');
                        $(this).addClass('selected');
                        var k = $(this).parent().find('li[class!="disabled"]').index(this);
                        $('#'+name+'-keyword').attr('selectedIndex', k+1);
                    }
                );

                // Bind a click event to each option
                $('#'+name+'-suggestions ul li[class!="disabled"]').click(instance.select);
            }

            /**
             * Try to find a color declaration in option title or option value, and if found, get that color,
             * build .i-combo-color-box element
             *
             * @param title
             * @param value
             * @return {Object}
             */
            this.color = function(title, value) {
                value = value || '';

                // Declare `info` object
                var info = {title: title.trim(), color: '', src: '', box: '', css: {color: ''}}, color;

                // Check if `title` or `value` contain a color definition
                if (color = value.toString().match(instance.colorReg)) {
                    info.src = 'value';
                } else if (color = info.title.match(instance.colorReg)) {
                    info.src = 'title';
                }

                // If contains, we prepare a color box element, to be later inserted in dom - before keyword field
                if (color && color.length && color[1]) {

                    // Setup color
                    info.color = color[1];

                    // Build color box
                    info.box = '<span class="i-combo-color-box" style="background: ' + info.color + ';"></span> ';

                    // If color was got from title (means that title was in format hue#rrggbb), set title as same color
                    // but without hue
                    if (info.src == 'title') info.title = info.color;

                }

                // Creates .i-combo-color-box element with if it doesn't yet exists, or updates it's background color
                info.apply = function(name) {
                    if ($('#'+name+'-keyword').parent().find('> .i-combo-color-box').length) {
                        $('#'+name+'-keyword').parent().find('> .i-combo-color-box').css('background', info.color);
                    } else {
                        $(info.box).insertBefore('#'+name+'-keyword');
                        $('#'+name+'-keyword').parent().find('> .i-combo-color-box').click(function(){
                            $('#'+name+'-keyword').click();
                        });
                    }
                }

                // Return `info` object
                return info;
            }

            /**
             * Set some option as selected, autosets value for hidden field
             */
            this.select = function (){
                var name, li, index, title, color, css = {color: ''};

                if (typeof arguments[0] == 'string') {
                    name = arguments[0];
                    li = $('#'+name+'-suggestions ul li.selected');
                    if (li.length == 0) return;
                } else {
                    name = $(this).parents('.i-combo-data').attr('id').replace('-suggestions', '');
                    li = $(this);
                }

                // Get the index of selected option id in instance.store[name].ids
                if (instance.store[name].enumset) {
                    if (!li.attr(name).toString().match(/^[0-9]+$/)) {
                        index = instance.store[name].ids.indexOf(li.attr(name));
                    } else {
                        index = instance.store[name].ids.indexOf(parseInt(li.attr(name)));
                    }
                } else {
                    index = instance.store[name].ids.indexOf(parseInt(li.attr(name)));
                }

                // Find related title property in instance.store[name].data
                title = instance.store[name].data[index].title;

                // Apply css color, if it was passed within store. Currently this feature is used for
                // cases then item title got from database was something like
                // <span style="color: red">Some title</span>. At such cases, php code which is preparing
                // combo data, strips that html from option, but detect defined color and
                // store it in ...['data'][i].system['color'] property
                if (instance.store[name].data[index].system && instance.store[name].data[index].system['color']
                    && typeof instance.store[name].data[index].system['color'] == 'string')
                    css.color = instance.store[name].data[index].system['color'];

                // Detect if colorbox should be applied
                var color = instance.color(title, li.attr(name));

                // If combo is in multiple-value mode
                if ($('#'+name+'-info').hasClass('i-combo-info-multiple')) {
                    var selected = $('#'+name).val() ? $('#'+name).val().split(',') : [];

                    // If option, that is going to be added to selected list, is not already exists there
                    if (selected.indexOf(li.attr(name)) == -1) {

                        // Create visual representation and append it to existing
                        $('<span class="i-combo-selected-item" selected-id="'+li.attr(name)+'">'+
                            color.box +
                            color.title +
                            '<span class="i-combo-selected-item-delete"></span>' +
                            '</span>')
                        .css(css).insertBefore('#'+name+'-keyword');
                        instance.bindDelete($('#'+name+'-info').parent().find('.i-combo-selected-item').last().find('.i-combo-selected-item-delete'));

                        // Determine way of how to deal with .i-combo-data (rebuild|rebuild-and-show|no-rebuild)
                        var mode = $('#'+name+'-keyword').val() ? 'selected-but-found-with-lookup' : '';

                        // Reset keyword field and it's 'prev' attr, append just selected value to already selected and
                        // adjust keyword field width
                        $('#'+name+'-keyword').val('');
                        $('#'+name+'-keyword').attr('prev', '');
                        selected.push(li.attr(name));
                        $('#'+name).val(selected.length > 1 ? selected.join(',') : selected[0]);

                        // Hide options
						var e = window.event || (typeof arguments[0] == 'object' ? arguments[0] : null);
                        if ((e && !(e.metaKey || e.ctrlKey)) || !e) instance.hideSuggestions(name);

                        // Restore list of options
                        instance.keywordErased(name, mode);

                        // Execute javascript-code, assigned to selected item
                        if (instance.store[name].enumset) {
                            var index = instance.store[name]['ids'].indexOf(li.attr(name));
                            if (index != -1 && instance.store[name]['data'][index].system.js) {
                                eval(instance.store[name]['data'][index].system.js);
                            }
                        }

                        // Additional operations, that should be done after some option was selected
                        instance.postSelect(name, li);

                        // Indicate that option can't be once more selected because it's already selected
                    } else {
                        var existing = $('#'+name+'-info').parent().find('.i-combo-selected-item[selected-id="'+li.attr(name)+'"] .i-combo-selected-item-delete');
                        existing.fadeTo('fast', 0.2);
                        existing.fadeTo(0, 1);
                    }

                // Else if combo is running in single-value mode
                } else {

                    // Apply selected color
                    color.apply(name);

                    // Apply color got from store, or unset css color property, if no color
                    $('#'+name+'-keyword').css(css);

                    // Set keyword text
                    $('#'+name+'-keyword').val(color.title);
                    $('#'+name+'-keyword').attr('prev', color.title);

                    // Update field value
                    $('#'+name).val(li.attr(name));

                    // Hide options
                    instance.hideSuggestions(name);

                    // Additional operations, that should be done after some option was selected
                    instance.postSelect(name, li);
                }
            }

            /**
             * Perform some additional things after option was selected
             *
             * @param name
             * @param li
             */
            this.postSelect = function(name, li) {
                // Detect multiple mode
                var multiple = $('#'+name+'-info').hasClass('i-combo-info-multiple');

                // Apply selected option additional attributes to a hidden input,
                // so attributes and their values to be accessible within hidden input context
                if (instance.store[name].attrs && instance.store[name].attrs.length) {
                    for(var n = 0; n < instance.store[name].attrs.length; n++) {

                        // If combo is running in multiple mode, we add a postfix to attribute names, for making a posibillity
                        // of picking up attributes, related to each separate selected value from the whole list of selected values
                        if (multiple) {
                            $('#'+name).attr(instance.store[name].attrs[n]+'-'+li.attr(name), li.attr(instance.store[name].attrs[n]));
                        } else {
                            $('#'+name).attr(instance.store[name].attrs[n], li.attr(instance.store[name].attrs[n]));
                        }
                    }
                }

                // Adjust keyword filed width
                instance.adjustKeywordFieldWidth(name);

                // Fire 'change' event
                $('#'+name).change();

                // We set 'changed' attribute to 'true' to remember the fact of at least one time change.
                // We will need this fact in request data prepare process, because if at the moment of sending
                // request 'changed' will still be 'false' (initial value), satellite property won't be set in
                // request data object. We need this to get upper and lower page results fetched from currently selected
                // value as startpoint. And after 'changed' attribute set to 'false', upper and lower page results will
                // have start point different to selected value, and based on most top alphabetic order.
                $('#'+name+'-info').attr('changed', 'true');
            }

            /**
             * Build options html
             *
             * @param json Source data for html building
             * @param name Name of param, which value option will contain
             * @return {String} html-code for options list
             */
            this.suggestions = function(json, name){
                var items = [];
                var groups = json.optgroup ? json.optgroup.groups : {none: 'none'};
                var groupIndent = json.optgroup ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '';
                var disabledCount = 0;

                for (var j in groups) {
                    if (j != 'none') {
                        items.push('<li class="disabled" group>' + groups[j] + '</li>');
                    }
                    for (var i in json['ids']) {
                        if (json['ids'][i] != undefined /*&& !isNaN(json['ids'][i])*/ && !isNaN(i)
                            && (j == 'none' || json['data'][i].system.group == j)) {
                            // Classes for option
                            var cls = [];

                            // Open <li>
                            var item = '<li';
                            item += ' ' + name + '="' + json['ids'][i] + '"';

                            // Additional attributes for option
                            if (json.attrs && json.attrs.length) {
                                for (var n in json['data'][i].attrs) {
                                    item += ' ' + n + '="' + json['data'][i].attrs[n] + '"';
                                }
                            }

                            // Mark as disabled
                            if (json['data'][i].system && json['data'][i].system['disabled']) {
                                cls.push('disabled');

                                // We are counting disabled options, to decrease json['ids'].length with
                                disabledCount++;
                            }

                            // If one this option is selected
                            if ($('#'+name).val() == json['ids'][i]) {

                                // We need to cover situation then we had two searches: at first search some element was
                                // selected and at next search same element is disabled, so while constructing html we
                                // shoud not mark disabled element as selected
                                if (cls.indexOf('disabled') == -1) {
                                    // Mark as selected
                                    cls.push('selected');
                                }
                            }

                            // Append css classes list as 'class' attribute for an option
                            if (cls.length) item += ' class="' + cls.join(' ') + '"';

                            // Apply css color, if it was passed within store. Currently this feature is used for
                            // cases then item title got from database was something like
                            // <span style="color: red">Some title</span>. At such cases, php code which is preparing
                            // combo data, strips that html from option, but detect defined color and
                            // store it in ...['data'][i].system['color'] property
                            if (json['data'][i].system && json['data'][i].system['color']
                                && typeof json['data'][i].system['color'] == 'string')
                                item += ' style="color: ' + json['data'][i].system['color'] + ';"';

                            // Enclose opening <li> tag
                            item += '>';

                            // Prepend option title with optgroup indent, if optgroups are used
                            item += groupIndent;

                            // Prepend option title with indent if needed
                            if (json['data'][i].system && json['data'][i].system['indent']
                                && typeof json['data'][i].system['indent'] == 'string')
                                item += json['data'][i].system['indent'];

                            // If 'option' property exists (mean that 'template' combo param is used),
                            // we use 'option' property contents as <li> inner contents, instead of 'title' contents
                            if (json['data'][i].option) {
                                item += json['data'][i].option;
                            } else {
                                var color = instance.color(json['data'][i].title, json['ids'][i]);
                                item += color.box;
                                item += color.title;
                            }

                            // Close <li> tag
                            item += '</li>';

                            items.push(item);
                        }
                    }
                }

                // If optgroups is used and we are deaing with items tree, we should distibute items by optgroups,
                // but insert all parents for options, if these parents not in same groups as child options
                if (json.optgroup != undefined && json.tree) items = instance.appendNotSameGroupParents(items, json, name);

                // Stat info
                $('#'+name+'-count').removeClass('i-combo-count-visible').text(Indi.numberFormat(json['ids'].length - disabledCount));
                $('#'+name+'-found').text(Indi.numberFormat(json['found']));

                if (json['ids'].length - disabledCount == json['found']) {
                    $('#'+name+'-info').addClass('i-combo-info-fetched-all');
                } else {
                    $('#'+name+'-info').removeClass('i-combo-info-fetched-all');
                }

                // Info should be displayed only if maximum possible results per page is less that total found results
                // or combo is running in 'keyword' mode
                if (($('#'+name+'-info').attr('fetch-mode') == 'keyword' ||
                    parseInt($('#'+name+'-found').text().replace(',','')) > instance.visibleCount) &&
                    $('#'+name+'-keyword').attr('disabled') != 'disabled') {
                    $('#'+name+'-info').css('visibility', 'visible');
                }

                var html = items.length ? '<ul>'+items.join("\n")+'</ul>' : '';

                // We setup selectedIndex attribute
                if ($(html).find('li').length) {

                    // Get current selectedIndex, and if it is 0, calculate it
                    var currentSelectedIndex = parseInt($('#'+name+'-keyword').attr('selectedIndex'));
                    if (currentSelectedIndex == 0) {

                        // We reset disabledCount here, because now, we should count all disabled html-items, not only json-items
                        // because now in options html there can be another disabled options, appeared as a result of using 'group'
                        // (mean 'optgroup') ability and as result of instance.appendNotSameGroupParents() function execution
                        disabledCount = 0;
                        var selectedIndex = 0;
                        var selectedFound = false;
                        $(html).find('li').each(function(index, li){
                            if ($(li).hasClass('selected')) {
                                selectedIndex = index - disabledCount + 1;
                                selectedFound = true;

                                // We increment disabledCount until selected value is found
                            } else if (selectedFound == false && $(li).hasClass('disabled')) {
                                disabledCount++;
                            }
                        });
                        $('#'+name+'-keyword').attr('selectedIndex', selectedIndex);
                    }
                } else {
                    $('#'+name+'-keyword').attr('selectedIndex', 0);
                }

                return html;
            }

            /**
             * Set keyborad keys handling, related to data fetch (lookup, results pagination, etc)
             *
             * @param event Used to get code of pressed key on keyboard
             */
            this.keyUpHandler = function (event){

                // Get input element
                var input = $(this);

                // Get field name
                var name = input.attr('lookup');

                // We will be fetching results with a timeout, so fetch requests will be
                // sent after keyword typing is finished (or seems to be finished)
                clearTimeout(instance.timeout);

                // Variable for detecting fetch mode. Fetch mode can be 'keyword' and 'no-keyword', and is 'no-keyword' by default
                var fetchMode = $('#'+name+'-info').attr('fetch-mode');

                // Setup variables for range of pages that's results are already fetched and displayed in combo as options
                // This variables will be used if current fetchMode is 'no-keyword', because for 'keyword' fetchMode will be
                // used different logic
                var pageTop = parseInt($('#'+name+'-info').attr('page-top'));
                var pageBtm = parseInt($('#'+name+'-info').attr('page-btm'));

                // Variable for detection if next|prev page of results should be fetched
                var moreResultsNeeded = event.keyCode.toString().match(/^(34|33)$/) && $('#'+name+'-suggestions').attr('more') && $('#'+name+'-suggestions').attr('more').toString().match(/^(upper|lower)$/) ? $('#'+name+'-suggestions').attr('more') : false;

                // We are detecting the change of keyword value by using 'keyup' event, instead of 'input' event, because 'input'
                // is supported by not al browsers. But with 'keyup' event there is a small problem - if we will be inputting
                // too fast
                var tooFastKeyUp = instance.store[name].lastTimeKeyUp && (new Date().getTime() - instance.store[name].lastTimeKeyUp < 200);

                // Variable for detection if keyword was changed and first page of related results should be fetched
                var keywordChanged = (($(this).attr('prev') != input.val() || tooFastKeyUp) && input.val() != '' && !event.keyCode.toString().match(/^(13|40|38|34|33)$/));

                // Check if keyword was emptied
                var keywordChangedToEmpty = (($(this).attr('prev') != input.val() || tooFastKeyUp) && input.val() == '' && !event.keyCode.toString().match(/^(13|40|38|34|33)$/));

                instance.store[name].lastTimeKeyUp = new Date().getTime();

                // If keyword was at least once changed, we switch fetch mode to 'keyword'.
                // We need to take it to attention, because PgUp fetching is impossible in case
                // if we have no keyword
                if (keywordChanged) {

                    // Here we have a situation when we are going to run 'keyword' fetch mode at first time.
                    // At this moment we backup current instance.store[name] object - we will need it if keyword
                    // will be changed to '' (empty string), and in this case it will be user-friendly to display last
                    // available results got by 'no-keyword' fetch mode, and we will be able to restore them from backup
                    if ($('#'+name+'-info').attr('fetch-mode') == 'no-keyword') {
                        var backup = {
                            options: Indi.copy(instance.store[name]),
                            info: $('#'+name+'-info')[0].outerHTML
                        };
                        instance.store[name].backup = backup;
                    }

                    // Update fetch mode and remember the keyword for further changes detection
                    $('#'+name+'-info').attr('fetch-mode', 'keyword');
                    $('#'+name+'-info').attr('keyword', input.val());

                    // Temporary strip red color from input, as we do not know if there will be at least
                    // one result related to specified keyword, and if no - keyword will be coloured in red
                    $('#'+name+'-keyword').removeClass('i-combo-keyword-no-results');

                    // Reset selected index
                    $('#'+name+'-keyword').attr('selectedIndex', 0);

                    // Scroll options list to the most top
                    $('#'+name+'-suggestions').scrollTo('0px');

                }

                // We will fetch data only if keyword was changed or if next|prev page of results
                // related to current keyword should be fetched
                if (keywordChanged || moreResultsNeeded) {

                    // Get field satellite
                    var satellite = $('#'+name+'-info').attr('satellite');

                    // Prepare data for fetch request
                    var data = {field: name};

                    // Pass satellite value only if it was at east one time changed. Otherwise default satellite value will be used
                    if ($('#'+satellite+'-info').attr('changed') == 'true') data.satellite = $('#'+satellite).val();

                    // If we are paging
                    if (moreResultsNeeded) {

                        // If previous page needed
                        if (event.keyCode == '33') {

                            // If keyword was at least once changed
                            if (fetchMode == 'keyword') {
                                $('#'+data.field+'-keyword').attr('selectedIndex', 1);
                                instance.keyDownHandler(name, 33);
                                return;

                            // Else if we are still walking through pages of all (not filtered by keyword) results
                            } else if (fetchMode == 'no-keyword') {
                                // If top border of range of displayed pages is not yet 1
                                // we will be requesting decremented page. Attribute 'page-top',
                                // there pageTop variable value was got, will be decremented
                                // later - after request will be done and results fetched
                                if ($('#'+name+'-info').attr('page-top-reached') == 'false') {
                                    data.page = pageTop - 1;

                                // Otherwise, if top border of range of displayed pages is already 1
                                // so it is smallest possible value and therefore we won't do any request,
                                // and we only should move selection to first option
                                } else {

                                    $('#'+data.field+'-keyword').attr('selectedIndex', 1);
                                    instance.keyDownHandler(name, 33);
                                    return;
                                }
                            }

                        // If next page needed
                        } else if (event.keyCode == '34') {

                            // If keyword was at least once changed
                            if (fetchMode == 'keyword') {
                                data.keyword = $('#'+name+'-info').attr('keyword');
                            }

                            // If requested page of results is out of range of already fetched options
                            // and bottom border of range of displayed pages is not already reached
                            if ($('#'+name+'-info').attr('page-btm-reached') == 'false') {
                                data.page = pageBtm + 1;

                            // Otherwise, if bottom border of range of displayed pages is already reached,
                            // so it is biggest possible value for page number and therefore we won't do any request
                            } else {
                                $('#'+data.field+'-keyword').attr('selectedIndex', $('#'+name+'-suggestions'+' ul li[class!="disabled"]').size());
                                instance.keyDownHandler(name, 34);
                                return;
                            }
                        }
                        data.more = moreResultsNeeded;

                        // Fetch request
                        instance.remoteFetch(data);

                    // If we are searching by keyword
                    } else if (event.keyCode != '33') {

                        // Setup request keyword
                        data.keyword = input.val();

                        // Setup previous keyword
                        input.attr('prev', input.val());

                        // Setup range borders as they were by default
                        $('#'+name+'-info').attr('page-top', '0');
                        $('#'+name+'-info').attr('page-btm', '0');

                        // Here we check if all possible results are already fetched, and if so, we will use local fetch
                        // instead of remote fetch, so we will search keyword within currently loaded set of options. Such
                        // scheme is useful for situations then number of results is not too large, and all results ARE already
                        // collected (initially, by first combo load, and/or by additional hoarding while upper/lower pages fetching)
                        if (instance.store[name].backup &&
                            instance.store[name].backup.options.data.length >= parseInt(instance.store[name].backup.options.found) &&
                            data.keyword.length) {
                            instance.localFetch(data);
                        } else {
                            instance.timeout = setTimeout(instance.remoteFetch, 500, data);
                        }
                    }
                }

                // If keyword was changed to empty we fire 'change' event. We do that for being sure
                // that dependent combos (combos that are satellited by current combo) are disabled. Also,
                // after keyword was changed to empty, hidden value was set to 0, so we should call .change() anyway
                // Note: 'change' event firing is need only if combo is running in non-multiple mode.
                if (keywordChangedToEmpty) {

                    // Hide options ist
                    instance.hideSuggestions(name);

                    // Do some things after keyword was erased
                    instance.keywordErased(name, 'only-erased-not-selected');
                }
            }

            /**
             * Do several thins after keyword was erased
             *
             * @param name
             */
            this.keywordErased = function(name, mode) {

                // Get input
                var input = $('#'+name+'-keyword');

                // Correct value of 'prev' attr
                input.attr('prev', input.val());

                // Remove color
                input.css({color: ''});

                // Remove color-box
                input.parent().find('> .i-combo-color-box').remove();

                // We need to fire 'change' event only if combo is running in single-value mode.
                // In that mode no keyword = no value. But in multiple-value mode combo may have a
                // value without a keyword. Also, we fire change only if previous value was not 0
                if($('#'+name+'-info').hasClass('i-combo-info-multiple') == false && $('#'+name).val()) {
                    $('#'+name).val(0).change();
                }

                // We restore combo state, that is had before first run of 'keyword' fetch mode
                if (instance.store[name].backup) {
                    $('#'+name+'-info')[0].outerHTML = instance.store[name].backup.info;
                    $('#'+name+'-info').hide();
                    var restore = Indi.copy(instance.store[name].backup.options);
                    instance.store[name] = {};
                    instance.store[name] = restore;
                }

                // If user erases wrong keyword, remove 'i-combo-keyword-no-results' class and show options list, that was available
                // before first run of 'keyword' fetch mode
                if (input.hasClass('i-combo-keyword-no-results')) input.removeClass('i-combo-keyword-no-results');

                // Rebuild combo and show it
                if (mode == 'only-erased-not-selected' && instance.hideOptionsAfterKeywordErased == false) {
                    input.click();

                // Rebuild combo but do not show at this time
                } else if (mode == 'selected-but-found-with-lookup'){
                    instance.rebuildComboData(name);
                }
            }

            /**
             * Set keyboard keys handling (Up, Down, PgUp, PgDn, Esc, Enter), related to visual appearance
             *
             * @param eventOrName Used to get code of pressed key on keyboard in case if this is event object
             * @param code Alternative way for getting code of pressed key on keyboard
             * @return {Boolean}
             */
            this.keyDownHandler = function(eventOrName, code){
                // Setup code, name and input variables
                var code, name, input;
                if (arguments[1]) {
                    code = arguments[1];
                    name = eventOrName;
                    input = $('#'+name+'-keyword');
                } else {
                    code = eventOrName.keyCode;
                    input = $(this);
                    name = input.attr('lookup');
                }

                // Enter - select an option
                if (code == '13') {
                    if ($('#'+name+'-suggestions').css('display') == 'block' || arguments[2]) instance.select(name);
                    return false;

                // Up or Down arrows
                } else if (code == '40' || code == '38' || code == '34' || code == '33') {
                    if (code == '40' && $('#'+name+'-suggestions').css('display') == 'none' && !arguments[2]) {
                        $('#'+name+'-suggestions').show();
                        $('#'+name+'-info').show();
                    } else {
                        // Get items count for calculations
                        var size = $('#'+name+'-suggestions'+' ul li[class!="disabled"]').size();

                        // Down key
                        if (code == '40'){
                            if (parseInt(input.attr('selectedIndex')) < size) {
                                input.attr('selectedIndex', parseInt(input.attr('selectedIndex'))+1);
                            }
                        // Up key
                        } else  if (code == '38'){
                            if (parseInt(input.attr('selectedIndex')) > 1) {
                                input.attr('selectedIndex', parseInt(input.attr('selectedIndex'))-1);
                            }
                        // PgDn key
                        } else if (code == '34') {
                            if (parseInt(input.attr('selectedIndex')) < size - instance.visibleCount) {
                                input.attr('selectedIndex', parseInt(input.attr('selectedIndex'))+instance.visibleCount);
                                $('#'+name+'-suggestions').attr('more', '');
                            } else if (parseInt(input.attr('selectedIndex')) <= size) {
                                if (parseInt($('#'+name+'-count').text()) < parseInt($('#'+name+'-found').text().replace(',',''))){
                                    $('#'+name+'-suggestions').attr('more', 'lower');
                                } else {
                                    input.attr('selectedIndex', size);
                                    $('#'+name+'-suggestions').attr('more', '');
                                }
                            }

                        // PgUp key
                        } else if (code == '33') {
                            if (parseInt(input.attr('selectedIndex')) > instance.visibleCount) {
                                input.attr('selectedIndex', parseInt(input.attr('selectedIndex'))-instance.visibleCount);
                                $('#'+name+'-suggestions').attr('more', '');
                            } else {
                                if (parseInt($('#'+name+'-count').text()) < parseInt($('#'+name+'-found').text().replace(',',''))/* && !$('#'+name+'-suggestions').attr('find') &&*/){
                                    $('#'+name+'-suggestions').attr('more', 'upper');
                                } else {
                                    input.attr('selectedIndex', 1);
                                    $('#'+name+'-suggestions').attr('more', '');
                                }
                            }
                        }

                        // Set up selected item, depending on what key was pressed, and deal scroll list of options if need
                        var disabledCount = 0;
                        $('#'+name+'-suggestions'+' ul li').each(function(liIndex){
                            if ($(this).hasClass('disabled')) disabledCount++;
                            if (!$(this).hasClass('disabled') && parseInt(input.attr('selectedIndex')) > 0 && liIndex == parseInt(input.attr('selectedIndex'))-1 + disabledCount) {
                                $(this).addClass('selected');
                                input.attr('selectedIndex', liIndex + 1 - disabledCount);
                                disabledCount = 0;
                                var visibleS = $('#'+name+'-suggestions').scrollTop()/instance.store[name].optionHeight;
                                var visibleE = visibleS + instance.visibleCount - 1;
                                var delta = 0;
                                if (liIndex > visibleE) {
                                    delta = (liIndex - visibleE) * instance.store[name].optionHeight;
                                } else if (liIndex < visibleS) {
                                    delta = (liIndex - visibleS) * instance.store[name].optionHeight;
                                }
                                var expr = (delta > 0 ? '+' : '-')+'='+Math.abs(delta)+'px';
                                if (delta) $('#'+name+'-suggestions').scrollTo(expr);
                            } else {
                                $(this).removeClass('selected');
                            }
                        });

                        // Set value while walking trough options list
                        var id = $('#'+name+'-suggestions'+' ul li.selected').attr(name);

                        // If we were running fetch in 'keyword' mode, but then switched to 'no-keyword' mode,
                        // There can be a situation that there will be no li.selected in options list, so we wrap
                        // following code with a condition of li.selected existence
                        if (id != undefined && $('#'+name+'-info').hasClass('i-combo-info-multiple') == false) {
                            //$('#'+name).val(id);

                            // Get the index of selected option id in instance.store[name].ids
                            var index = instance.store[name].ids.indexOf(instance.store[name].enumset ? id : parseInt(id));

                            // Find related title property in instance.store[name].data
                            var title = instance.store[name].data[index].title.toString().trim();

                            // Setup color box if needed
                            var color = instance.color(title, instance.store[name].ids[index]);
                            color.apply(name);

                            // Apply css color, if it was passed within store. Currently this feature is used for
                            // cases then item title got from database was something like
                            // <span style="color: red">Some title</span>. At such cases, php code which is preparing
                            // combo data, strips that html from option, but detect defined color and
                            // store it in ...['data'][i].system['color'] property
                            var css = {color: ''};
                            if (instance.store[name].data[index].system && instance.store[name].data[index].system['color']
                                && typeof instance.store[name].data[index].system['color'] == 'string')
                                css.color = instance.store[name].data[index].system['color'];
                            $('#'+name+'-keyword').css(css);

                            // Adjust keyword filed width
                            instance.adjustKeywordFieldWidth(name);

                            // Set keyword text
                            $('#'+name+'-keyword').val(color.title);
                            $('#'+name+'-keyword').attr('prev', color.title);
                        }
                    }
                    return false;

                // Esc key
                } else if (code == '27') {
                    instance.hideSuggestions(name);

                // Other keys
                } else {
                    // If combo is multiple
                    if ($('#'+name+'-info').hasClass('i-combo-info-multiple')) {

                        // If Delete or Backspace is pressed and current keyword value is '' - we should delete last selected
                        // value from list of selected values. We will do it by firing 'click' event on .i-combo-selected-item-delete
                        // because this element has a handler for that event, and that handler will perform all necessary operations
                        if ((code == '8' || code == '46') && !$('#'+name+'-keyword').val()) {
                            $('#'+name).parent().find('.i-combo-selected-item').last().find('.i-combo-selected-item-delete').click();

                            if (instance.hideOptionsAfterKeywordErased) instance.hideSuggestions(name);

                            // Otherwise, is any other key was pressed and no-lookup is true then ignore that key
                        } else if ($('#'+name+'-keyword').attr('no-lookup') == 'true') {
                            return false;
                        }

                    // If combo is not multiple
                    } else {

                        // We provide necessary operations if combo is running with no-lookup option
                        if ($('#'+name+'-keyword').attr('no-lookup') == 'true') {

                            // If Backspace or Del key is pressed, we should set current value as 0 and set keyword to '',
                            // but only if instance.store[name].enumset == false, because there can be only one case then
                            // both "instance.store[name].enumset == true" and "multiple" are used - combo field is dealing
                            // with ENUM database table column type and within that type no empty or zero values allowed,
                            // except empty or zero value is in the list of ENUM values, specified in the process of column
                            // declaration
                            if ((code == '8' || code == '46') && (!instance.store[name].enumset || $('#'+name+'-keyword').parents('.i-combo').hasClass('i-combo-filter'))){
                                instance.clearCombo(name);
                                // If any other key was pressed, there should be no reaction
                            } else {
                                return false;
                            }
                        }
                    }
                }
            }

            this.clearCombo = function(name) {
                // Remove color-box
                $('#'+name+'-keyword').parent().find('> .i-combo-color-box').remove();
                // Remove color
                $('#'+name+'-keyword').css({color: ''});
                $('#'+name+'-keyword').val('');
                $('#'+name).val(0);
            }

            /**
             * If combo has a satellite, and satellite value is 0, combo should be disabled.
             * Otherwise, combo wil be enabled.
             *
             * @param name
             * @param name
             * @param isv  - Imitate satellite value
             */
            this.setDisabled = function(name, force){
                // Check if this combo should be disabled
                var satellite = $('#'+name+'-info').attr('satellite').toString();
                if (satellite.length && $('#'+satellite).length) {
                    var sv = $('#'+satellite).val().toString();
                    sv = sv.length == 0 ? 0 : parseInt(sv);
                    if (sv == 0 || force == true) {
                        $('#'+name+'-keyword').attr('disabled', 'disabled');
                        $('#'+name+'-keyword').parents('.i-combo').addClass('i-combo-disabled x-item-disabled');
                        $('#'+name+'-keyword').parents('.i-combo').find('.i-combo-trigger').attr('src', STD+'/i/admin/trigger-system-disabled.png');
                        $('#'+name+'-keyword').val('');

                        // We set hidden field value as 0 (or '', if multiple), and fire 'change event' because there can be
                        // satellited combos for current combo, so if we have, for example 5 cascading combos,
                        // that are satellited to each other, this code provide that if top combo is disabled,
                        // all dependent (satellited) combos will also be disabled recursively
                        if ($('#'+name+'-info').hasClass('i-combo-info-multiple')) {

                            $('#'+name+'-keyword')
                                .parents('.i-combo-multiple')
                                .find('.i-combo-selected-item-delete')
                                .attr('no-change', 'true')
                                .click();

                            $('#'+name).val('');
                        } else {
                            $('#'+name).val(0);
                        }
                        $('#'+name).change();

                        // There is currently only one case when 'force' param is passed,
                        // and this case only happen if we were fetching satellited (dependent) results,
                        // and we had no success for that search (nothing found). That is why we are giving
                        // a corresponding message, that there is no results related to such satellite value
                        if (force && sv != 0) {
                            var satellite = $('#'+name+'-info').attr('satellite');
                            $('#'+name+'-keyword').addClass('no-results-within');
                            $('#'+name+'-keyword').removeAttr('readonly'); // ? think about need
                            //$('#'+name+'-keyword').val(Indi.lang.I_COMBO_NO_RESULTS_WITHIN + '\'' + $('#'+satellite+'-keyword').val()+ '\'');
                            $('#'+name+'-keyword').val('');
                        }

                        // Enable combo
                    } else {
                        $('#'+name+'-keyword').removeAttr('disabled');
                        $('#'+name+'-keyword').parents('.i-combo').removeClass('i-combo-disabled x-item-disabled');
                        $('#'+name+'-keyword').parents('.i-combo').find('.i-combo-trigger').attr('src', STD+'/i/admin/trigger-system.png');
                    }
                }

                // Restore default values for auxillary attributes
                $('#'+name+'-info').attr('fetch-mode', 'no-keyword');
                $('#'+name+'-info').attr('page-top-reached', 'false');
                $('#'+name+'-info').attr('page-btm-reached', 'false');
                $('#'+name+'-info').attr('page-top', '0');
                $('#'+name+'-info').attr('page-btm', '0');
                $('#'+name+'-keyword').attr('selectedIndex', 0);
            }

            /**
             * Disable or enable combo depending on a given param
             *
             * @param name
             * @param disable true|false
             */
            this.toggle = function(name, disable){
                if (disable) {
                    $('#'+name+'-keyword').attr('disabled', 'disabled');
                    $('#'+name+'-keyword').parents('.i-combo').addClass('simple-disabled');
                    $('#'+name+'-keyword').parents('.i-combo').addClass('i-combo-disabled x-item-disabled');
                    $('#'+name+'-keyword').parents('.i-combo').find('.i-combo-trigger').attr('src', STD+'/i/admin/trigger-system-disabled.png');
                    $('#'+name+'-keyword').val('');
                    // We set hidden field value as 0, and fire 'change event' because there can be
                    // satellited combos for current combo, so if we have, for example 5 cascading combos,
                    // that are satellited to each other, this code provide that if top combo is disabled,
                    // all dependent (satellited) combos will also be disabled recursively
                    //$('#'+name).val(0);
                    //$('#'+name).change();

                // Enable combo
                } else {
                    $('#'+name+'-keyword').removeAttr('disabled');
                    $('#'+name+'-keyword').parents('.i-combo').removeClass('i-combo-disabled x-item-disabled');
                    $('#'+name+'-keyword').parents('.i-combo').removeClass('simple-disabled');
                    $('#'+name+'-keyword').parents('.i-combo').find('.i-combo-trigger').attr('src', STD+'/i/admin/trigger-system.png');
                }
            }

            /**
             * Mark some options as disabled
             *
             * @param name
             * @param disabledIds
             */
            this.setDisabledOptions = function(name, disabledIds) {
                for (var i in instance.store[name].data) {
                    instance.store[name].data[i].system.disabled = disabledIds.indexOf(instance.store[name].ids[i]) != -1;
                }
                instance.store[name].found = instance.store[name].data.length - disabledIds.length;
            }

            /**
             * Merge two trees of options
             *
             * @param tree1
             * @param tree2
             * @return array
             */
            this.merge = function(tree1, tree2) {
                for (var index2 in tree2['ids']) {
                    if (!isNaN(index2)) {
                        var id = tree2['ids'][index2];
                        var parentId = tree2['data'][index2].system.parentId;

                        // If there is no such an option in existing tree, we add it
                        if (tree1['ids'].indexOf(id) == -1) {

                            // If this is a one of top-level options, we just push it to the end of options list
                            if (parseInt(parentId) == 0) {
                                tree1['ids'].push(id);
                                tree1['data'].push(tree2['data'][index2]);

                            // Else we implement bit more complicated logic
                            } else {

                                // At first we are checking if in existing options there are at least one
                                // option with the same parent identifier as parent identifier of new option,
                                // and if found, insert new option after last/single existing option
                                var insertAfter = -1;
                                for (var index1 in tree1['ids']) {
                                    if (parentId == tree1['data'][index1].system.parentId) {
                                        insertAfter = index1;

                                        // We also take in attention that new option should be inserted not simply after last sibling,
                                        // but after all lower-levels children of that last sibling
                                    } else if (insertAfter != -1 &&
                                        tree1['data'][index1].system.indent > tree2['data'][index2].system.indent) {
                                        insertAfter = index1;
                                    }
                                }

                                // If such an option was not found, we are trying to find a parent option for
                                // new option in existing options
                                if (insertAfter == -1) {
                                    for (var index1 in tree1['ids']) {
                                        if (parentId == tree1['ids'][index1]) {
                                            insertAfter = index1;
                                        } else if (insertAfter != -1 &&
                                            tree1['data'][index1].system.indent > tree2['data'][index2].system.indent) {
                                            insertAfter = index1;
                                        }
                                    }
                                }
                                insertAfter = parseInt(insertAfter) + 1;
                                tree1['ids'].splice(insertAfter, 0, id);
                                tree1['data'].splice(insertAfter, 0, tree2['data'][index2]);
                            }

                        // Else if such an option is already presented in existing tree, we check if it is
                        // disabled there but not in new tree and if so we set 'disabled' property to 'false'
                        } else {

                            // Find index
                            index1 = tree1['ids'].indexOf(id);

                            // Set 'disabled' to true
                            if (tree1['data'][index1].system.disabled == true && tree2['data'][index2].system.disabled != true) {
                                tree1['data'][index1].system.disabled = false;
                            }
                        }
                    }
                }

                return tree1;
            }

            /**
             * Merge two sets of optgroup info
             *
             * @param info1
             * @param info2
             * @return {*}
             */
            this.mergeOptgroupInfo = function (info1, info2) {
                for (var j in info2.groups) {
                    if (info1.groups[j] == undefined) {
                        info1.groups[j] = info2.groups[j];
                    }
                }
                return info1;
            }

            /**
             * If optgroups is used and we are dealing with items tree, we should distibute items by optgroups,
             * but insert all parents for options, if these parents not in same groups as child options
             *
             * @param items
             * @param json
             * @return []
             */
            this.appendNotSameGroupParents = function(items, json, name) {
                // Store info about parents that were aready added, to prevent adding them more that once
                var addedParents = [];

                // Html for seaching <li> of parent options
                var html = '<ul>' + items.join('') + '</ul>';

                for (var i = 0; i < items.length; i++) {

                    // If item is a option, not optgroup
                    if ($(items[i]).attr(name)) {

                        // Get some basic data about current option
                        var id = parseInt($(items[i]).attr(name));
                        var index = json['ids'].indexOf(id);
                        var group = json['data'][index].system.group;
                        var parentId = parseInt(json['data'][index].system.parentId);

                        // We check all-level parents of current option
                        while (parentId) {

                            // Get index of parent option within json['ids']
                            var parentIndex = json['ids'].indexOf(parentId);

                            // If we are dealing with page of that was started from certain selected option,
                            // there is a possibility that parent option can be not found
                            if (parentIndex != -1) {

                                // Get group of parent option
                                var parentGroup = json['data'][parentIndex].system.group;

                                // If groups of current option and parent option do not match, we add parent
                                if (group != parentGroup && addedParents.indexOf(parentId) == -1) {

                                    // Get html for parent option
                                    var parentOption = $(html).find('li['+name+'="'+parentId+'"]').
                                        addClass('disabled').removeClass('selected').wrap('<p>').parent().html();

                                    // Insert parent in certain position within options
                                    items.splice(i, 0, parentOption);

                                    // Collect added parents
                                    addedParents.push(parentId);
                                }

                                // Replace parentId for next upper level check
                                parentId = parseInt(json['data'][parentIndex].system.parentId);
                            } else break;
                        }
                    }
                }

                // After parents were found and used for insertion in not-same groups, there is a possibility
                // that some of them not more needed

                // Html for seaching <li> of parent options
                var html = '<ul>' + items.join('') + '</ul>';

                // Array for colecting needed options indexes
                var neededIndexes = [];

                // Variable for stepping up once non-disabled option is catched
                var groupIndex = 0;
                for (var i = 0; i < items.length; i++) {

                    // We do this action only if it is a non-disabled option
                    if (!$(items[i]).hasClass('disabled')) {

                        // Collect needed ids
                        neededIndexes.push(i);

                        // We check all-level parents of current option.
                        var reg = items[i].match(/<li[^>]+>([&nbsp;]*)/);
                        var level = reg[1] ? reg[1].length/6/5 : 0;
                        for (var j = i - 1; j >= groupIndex; j--) {
                            var reg = items[j].match(/<li[^>]+>([&nbsp;]*)/);
                            var previousLevel = reg[1] ? reg[1].length/6/5 : 0;
                            if (previousLevel < level) {
                                if (neededIndexes.indexOf(j) == -1) {
                                    neededIndexes.push(j);
                                }
                            }
                        }
                    } else if ($(items[i]).attr('group') == '') {
                        groupIndex = i;
                    }
                }

                // Get needed items by needed indexes
                var neededItems = [];
                for (var i = 0; i < items.length; i++) {
                    if (neededIndexes.indexOf(i) != -1) {
                        neededItems.push(items[i]);
                    }
                }
                return neededItems;
            }

            /**
             * Set trigger button icon (pressed or unpressed)
             */
            this.bindTrigger = function(){
                $(instance.componentNameClass() + ' .i-combo-trigger').mousedown(function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find(instance.keywordSelector()).hasClass('i-combo-keyword-no-results') ==  false)
                        $(this).addClass('x-form-arrow-trigger-click').addClass('x-form-trigger-click');
                });
                $(instance.componentNameClass() + ' .i-combo-trigger').mouseover(function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find(instance.keywordSelector()).hasClass('i-combo-keyword-no-results') ==  false)
                        $(this).parent().addClass('x-form-trigger-wrap-focus');
                });
                $(instance.componentNameClass() + ' .i-combo-trigger').mouseleave(function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find(instance.keywordSelector()).hasClass('i-combo-keyword-no-results') ==  false)
                        $(this).parent().removeClass('x-form-trigger-wrap-focus');
                });
                $(instance.componentNameClass() + ' .i-combo-trigger').mouseup(function(){
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') ==  false &&
                        $(this).parents('.i-combo').find(instance.keywordSelector()).hasClass('i-combo-keyword-no-results') ==  false) {
                        $(this).parents('.i-combo').find('.i-combo-keyword').click();
                        $(this).removeClass('x-form-arrow-trigger-click').removeClass('x-form-trigger-click');
                    }
                });
            }

            /**
             * Bind handers for clicks on .i-combo-selected-item-delete items
             * @param appended
             */
            this.bindDelete = function(appended){
                var scope = appended ? appended : $('.i-combo-selected-item-delete');
                scope.click(function(){

                    // Set up auxilary variabes
                    var name = $(this).parents('.i-combo').parent().find(instance.keywordSelector()).attr('lookup');
                    var selected = $('#'+name).val().split(',');
                    var deleted = $(this).parents('.i-combo-selected-item').attr('selected-id');
                    var index = selected.indexOf(deleted);

                    // Unset item from selected items array
                    selected.splice(index, 1);

                    // Check if change() handler for current combo should not be fired. Currently there is a only one
                    // case there this feature is used - in case if current combo is multiple and have a satellite, which
                    // value has just changed, so current combo data will should be reloaded and currently selected options
                    // should be removed. Usually, .change() fires each time when .i-combo-selected-item-delete was clicked
                    // and if we clicked on several items with such class, .change() handler will be fired several times,
                    // not once - as we need in that situation. So noChange variable will prevent .change() handler firing.
                    // .change() handler will be fired, but only once, and separately from current (current - mean click
                    // handler for .i-combo-selected-item-delete items) handler
                    var noChange = $(this).attr('no-change') ? true : false;

                    // Remove visual representation of deleted item from combo
                    $(this).parents('.i-combo-selected-item').remove();

                    // Adjust width of keyword field
                    instance.adjustKeywordFieldWidth(name);

                    // Remove attributes
                    if (instance.store[name].attrs && instance.store[name].attrs.length) {
                        for(var n = 0; n < instance.store[name].attrs.length; n++) {
                            $('#'+name).removeAttr(instance.store[name].attrs[n]+'-'+deleted);
                        }
                    }

                    // Set the updated value and fire change event
                    $('#'+name).val(selected.join(','));
                    if (noChange == false) $('#'+name).change();
                });
            }

            /**
             * Setup the dom item, that options html should be appended to
             *
             * @param name
             * @return {*}
             */
            this.getComboDataAppendToEl = function(name) {
                return $('#'+name+'-keyword').parent();
            }


            /**
             * This is an extraction, having the aim to be able to setup a different logic in indi.proto.combo.filter
             *
             * @param name
             */
            this.setReadonlyIfNeeded = function(name) {
                if (($('#'+name+'-keyword').attr('disabled') != 'disabled' && instance.store[name].enumset && !$('#'+name+'-info').hasClass('i-combo-info-multiple'))) {
                    $('#'+name+'-keyword').attr('readonly', 'readonly');
                    $('#'+name+'-keyword').addClass('readonly');
                }
            }

            /**
             * Function that will be called after combo value change. Contain auxillary operations such as
             * dependent-combos reloading, javascript execution and others
             */
            this.changeHandler = function() {
                // Get name of the combo
                var name = $(this).attr('id');

                // Remove attributes from hidden field, if it's value became 0. We do it here only for single-value combos
                // because multiple-value combos have different way of how-and-when the same aim should be reached -
                // attributes deletion for multiple-value combos is implemented in instance.bindDelete() function of this script
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
                // If current combo is a satelite for one or more other combos, we should refres data in that other combos
                $('.i-combo-info[satellite="'+name+'"]').each(function(){
                    var satellited = $(this).parents('.i-combo').parent().find(instance.keywordSelector()).attr('lookup');
                    instance.setDisabled(satellited);
                    if ($(this).parents('.i-combo').hasClass('i-combo-disabled') == false) {

                        // Here we are emptying the satellited combo selected values, either hidden and visible
                        // because if we would do it in afterFetchAdjustmetns, there would be a delay until fetch
                        // request would be completed
                        if ($('#'+satellited+'-info').hasClass('i-combo-info-multiple')) {

                            $('#'+satellited+'-keyword')
                                .parents('.i-combo-multiple')
                                .find('.i-combo-selected-item-delete')
                                .attr('no-change', 'true')
                                .click();

                            $('#'+satellited).val('');
                        } else {
                            $('#'+satellited).val(0);
                        }

                        instance.remoteFetch({
                            field: satellited,
                            satellite: $('#'+$(this).attr('satellite')).val(),
                            mode: 'refresh-children'
                        });
                    }
                });

                // Execute javascript code, if it was assigned to selected option. The additional clause for execution
                // is that combo should run in single-value mode, because if it's not - we do not know what exactly item
                // was selected and we are unable to get js, related to that exactly item. Even more - we do not exactly
                // know about the fact of new item was added, it also could be removed, because .change() (if combo is
                // running in multiple-value mode) if firing in both cases. So, for the aim of selected item assigned javascript
                // execution to be reached, we need this execution to be provided at instance.select() function of this script
                if (instance.store[name].enumset && $('#'+name+'-info').hasClass('i-combo-info-multiple') == false) {
                    var index = instance.store[name]['ids'].indexOf($(this).val());
                    if (index != -1 && instance.store[name]['data'][index].system.js) {
                        eval(instance.store[name]['data'][index].system.js);
                    }
                }

                // Execute javascript code, assigned as an additional handler for 'select' event
                if (instance.store[name].js) {
                    eval(instance.store[name].js);
                }
            }

            this.keywordSelector = function(){
                return instance.componentNameClass() + ' .i-combo-keyword';
            }

            this.componentNameClass = function(){
                return '.i-' + instance.componentName.replace('.', '-');
            }

            /**
             * The enter point.
             */
            this.run = function() {

                if (indi.callbacks && indi.callbacks[instance.componentName] && indi.callbacks[instance.componentName].length) {
                    for (var i = 0; i < indi.callbacks[instance.componentName].length; i++) {
                        indi.callbacks[instance.componentName][i]();
                    }
                }

                if (instance.options.removeComboDataDivs) {
                    for (var i in instance.store) {
                        top.window.$('body #' + i + '-suggestions').remove();
                    }
                }

                /**
                 * Bind keyUpHandler on keyup event for keyword html-input
                 */
                $(instance.keywordSelector()).keyup(instance.keyUpHandler);

                /**
                 * Bind keyDownHandler on keyup event for keyword html-input
                 */
                $(instance.keywordSelector()).keydown(instance.keyDownHandler);

                /**
                 * Bind handlers for click, blur events and misc things for keyword html-input
                 */
                $(instance.keywordSelector()).each(function(index){

                    // Get name
                    var name = $(this).attr('lookup');

                    // Prevent standard autocompletion
                    $(this).attr('autocomplete', 'off');

                    // Setup combo as disabled, if needed
                    instance.setDisabled(name, instance.store[name]['ids'].length == 0);

                    // Initially, we setup each combo as not able to lookup if there take place one of conditions:
                    // 1. combo is used in enumset field and is not disabled ('non-disabled' condition is here due to css styles
                    // conflict between input[disabled] and input[readonly]. ? - think about need)
                    // 2. combo lookup ability was manually switched off by special param
                    instance.setReadonlyIfNeeded(name);

                    // Set previous value as current value at initialisation
                    $(this).attr('prev', $(this).val());

                    // Call 'hideSuggestions' function on blur, if current mouse position is not over options
                    $(this).blur(function(){
                        if ($('#'+name+'-suggestions').attr('hover') != 'true') instance.hideSuggestions(name);
                    });

                    $(this).click(function(){
                        var name = $(this).attr('lookup');
                        // Check if combo is disabled
                        if ($(this).parents('.i-combo').hasClass('i-combo-disabled') || $(this).hasClass('i-combo-keyword-no-results')) return;

                        if ($('#'+name+'-suggestions').css('display') == 'none') {
                            instance.rebuildComboData(name);
                        }

                        $('.i-combo-keyword').each(function(){
                            if ($(this).attr('lookup') != name) $(this).blur();
                        });

                        // Toggle options and info
                        $('#'+name+'-suggestions').toggle();
                        if ($(this).parent().find('.i-combo-info').css('display') == 'none') {
                            $(this).parent().find('.i-combo-info').css('display', 'block');
                        } else {
                            $(this).parent().find('.i-combo-info').css('display', 'none');
                        }
                        instance.adjustComboInfoLeftMargin(name);
                    });

                    $(this).parent().find('> .i-combo-color-box').click(function(){
                        $('#'+name+'-keyword').click();
                    });

                    instance.adjustComboTriggerLeftMargin(name);
                    instance.adjustKeywordFieldWidth(name);

                    $('#'+name).change(instance.changeHandler);

                    $('#'+name+'-trigger').click(function(){
                        $(this).parent().find(instance.keywordSelector()).click();
                        $(this).parent().find(instance.keywordSelector()).focus();
                    });

                    instance.getComboDataAppendToEl(name).append('<div id="'+name+'-suggestions" class="i-combo-data" style="z-index: 1000' + index + '; width: ' + $(this).parents('.i-combo').width() + 'px; margin-top: 1px; overflow-y: hidden;" hover="false"/>');

                    $('#'+name+'-suggestions').scroll(function(){
                        $('#'+name+'-keyword').focus();
                    });
                    $(this).parent().find('.i-combo-info').click(function(){
                        $('#'+name+'-keyword').focus();
                    });
                    $('#'+name+'-suggestions').hover(function(){
                        $(this).attr('hover','true');
                    }, function(){
                        $(this).attr('hover','false');
                    });

                    // Execute javascript code, if it was assigned to default selected option
                    if (instance.store[name].enumset) {
                        var index = instance.store[name]['ids'].indexOf($('#'+name).val());
                        if (index != -1 && instance.store[name]['data'][index].system.js) {
                            eval(instance.store[name]['data'][index].system.js);
                        }
                    }
                });

                instance.bindTrigger();

                instance.bindDelete();

            }
        }

        indi.combo = {};
        if ($('.i-combo-form').length) {
            indi.combo.form = new indi.proto.combo.form();
            indi.combo.form.run();
        }
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if ((typeof indi !== 'undefined')) {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));
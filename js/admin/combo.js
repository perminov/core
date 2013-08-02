var COMBO = (function (dselect) {
    "use strict";
    var process = function () {
        var keyUpHandler, keyDownHandler, suggestions, input, select, hideSuggestions, timeout, bindTrigger, fetch, merge,
            adjustComboInfoLeftMargin, adjustComboTriggerLeftMargin, adjustComboOptionsDivHeight, visibleCount = 20,
            mergeOptgroupInfo, appendNotSameGroupParents, setDisabled;

        /**
         * Ajdust left margin for '.combo-info' elements
         *
         * @param name
         */
        adjustComboInfoLeftMargin = function(name, forceAdjust) {
            var input = $('.combo-keyword[lookup="'+name+'"]');
            var text = [];
            var width = input.parent().find('.combo-info').width();

            // We set margin only once, or if forceAdjust agrument is passed.
            // forceAdjust argument is passed when response json has 'found' property
            if(!parseInt(input.parent().find('.combo-info').css('margin-left')) || forceAdjust)
                input.parent().find('.combo-info').css('margin-left', (input.width() - width - 15) + 'px');
        }

        /**
         * Ajdust left margin for '.combo-trigger' elements
         *
         * @param name
         */
        adjustComboTriggerLeftMargin = function(name) {
            var input = $('.combo-keyword[lookup="'+name+'"]');
            input.parent().find('.combo-trigger').css('margin-left', (input.width() - 17) + 'px');
        }

        /**
         * Adjust height of div, containing ul with options
         * @param name
         */
        adjustComboOptionsDivHeight = function(name) {
            if ($('#'+name+'-suggestions ul li').length >= visibleCount) {
                $('#'+name+'-suggestions').css('height', '281px');
            } else {
                $('#'+name+'-suggestions').css('height', $('#'+name+'-suggestions ul li').length * 14 + 1);
            }
        }

        /**
         * Function for hiding options list, as Esc key was pressed
         *
         * @param name
         */
        hideSuggestions = function(name) {

            // Hide options
            $('#'+name+'-suggestions').hide();

            // If keyword contents does not equal to any option, empty hidden value field
            // 1. Set notEqual as true
            var notEqual = true;

            // 2. Get current keyword value
            var lookup = $('#'+name+'-keyword').val();

            // 3. Check if keyword value match any existing option
            for (var key in comboOptions[name]['data'])
                if (comboOptions[name]['data'][key].title == lookup)
                    notEqual = false;

            // 4. If no match found, empty hidden value field
            if (notEqual) $('#'+name).val("0");

            // 5. Hide info about count and found
            $('#'+name+'-info').hide();
        }

        /**
         * Prepare request parameters, do request, fetch data and rebuild combo
         *
         * @param data
         */
        fetch = function(data){
            // Get name of field
            var name = data.field;

            // Show loading pic
            $('#'+name+'-count').html('<img src="/i/loading/loading35.gif" class="dselect-loader" width="15">');

            // Fetch request
            $.post("./combo/1/", data,
                function(json) {

                    // If current options list should be prepended with fetched options
                    if (data.more == 'upper') {

                        // Save current options to backup
                        var backupOptions = []; backupOptions = deepObjCopy(comboOptions[name]);

                        // Empty current options
                        comboOptions[name]['ids'] = [];
                        comboOptions[name]['data'] = [];

                        // Reverse fetched options list, as it was got in reverted order, because
                        // it was the only way to get previous page of options
                        //json['ids'].reverse();
                        //json['data'].reverse();

                        // So now we start to fill comboOptions array with fetched options
                        for (var key in json['ids']) {
                            comboOptions[name]['ids'].push(json['ids'][key]);
                            comboOptions[name]['data'].push(json['data'][key]);
                        }

                        // And after that we append options from backupOptions, so as the result
                        // we will have full options list in correct order
                        for (var key in backupOptions['ids']) {
                            comboOptions[name]['ids'].push(backupOptions['ids'][key]);
                            comboOptions[name]['data'].push(backupOptions['data'][key]);
                        }

                        // Merge optgroup info
                        if (comboOptions[name].optgroup)
                            comboOptions[name].optgroup = mergeOptgroupInfo(comboOptions[name].optgroup, json.optgroup);

                    // Else if fetched options should be appended to current options list
                    } else if (data.more == 'lower') {

                        // If we are dealing with tree of results, we should merge existing options tree
                        // with tree of just received additional page of results
                        if (comboOptions[name].tree) {

                            // Merge trees
                            comboOptions[name] = merge(comboOptions[name], json);

                        // Else we just append fetched options to existing options
                        } else {
                            for (var key in json['ids']) {
                                comboOptions[name]['ids'].push(json['ids'][key]);
                                comboOptions[name]['data'].push(json['data'][key]);
                            }
                        }

                        // Merge optgroup info
                        if (comboOptions[name].optgroup)
                            comboOptions[name].optgroup = mergeOptgroupInfo(comboOptions[name].optgroup, json.optgroup);

                    // Otherwise we just replace current options with fetched options
                    } else {
                        comboOptions[name] = json;
                    }

                    // Remove more attribute
                    $('#'+name+'-suggestions').removeAttr('more');

                    // Rebuild options list
                    var html = suggestions(comboOptions[name], name);
                    $('#'+name+'-suggestions').html(html);

                    // Set scrolling if number of options more than visibleCount
                    $('#'+name+'-suggestions').css('overflow-y', $('#'+name+'-suggestions ul li').length > visibleCount ? 'scroll' : '');

                    // Adjust options div height
                    adjustComboOptionsDivHeight(name);

                    if (json['found']) {
                        // Adjust left margin for '.combo-info' because width of info could be changed
                        adjustComboInfoLeftMargin(name, true);

                        // We get json['found'] value only in case if we are running 'keyword' fetch mode,
                        // and in json is stored first portion of results and this mean that paging up shoud be disabled
                        $('#'+name+'-info').attr('page-top-reached', 'true');

                        // Also, we should renew 'page-btm-reached' attribute value
                        if (json['found'] <= visibleCount) {
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
                    $('#'+name+'-suggestions'+' ul li[class!="disabled"]').click(select);

                    // If results set is not empty
                    if ($(html).find('li[class!="disabled"]').length) {

                        // Show options list after keyword typing is finished
                        if ($('#'+name+'-suggestions').css('display') == 'none') {

                            // If we selected some option in satellite and current results are
                            // results for satellited field, we do not expand them at this time.
                            // We just remove 'no-results-within' class from satellited field
                            // keyword and set keyword to empty string
                            if (data.mode == 'refresh-children') {
                                $('#'+name+'-keyword').removeClass('no-results-within').val('');

                            // Show results
                            } else {
                                $('#'+name+'-keyword').click();
                            }
                        }

                        // Options selected adjustments
                        if (data.more && data.more.toString().match(/^(upper|lower)$/)) {

                            // If these was no more results
                            if (json.ids['length'] <= visibleCount) {

                                // We mark that top|bottom range is reached
                                if (json.ids['length'] < visibleCount)
                                    $('#'+name+'-info').attr('page-'+(data.more == 'upper' ? 'top' : 'btm')+'-reached', 'true');

                                // Move selectedIndex at the most top
                                if (data.more == 'upper') {
                                    $('#'+name+'-keyword').attr('selectedIndex', 1);

                                // Move selectedIndex at the most bottom
                                } else if (data.more == 'lower' && json.ids['length'] < visibleCount) {
                                    $('#'+name+'-keyword').attr('selectedIndex', $('#'+name+'-suggestions'+' ul li[class!="disabled"]').size());
                                }
                            }

                            // Adjust selection based on selectedIndex
                            keyDownHandler(name, data.more == 'upper' ? 33 : 34);

                            // Update page-top|page-btm value
                            $('#'+name+'-info').attr('page-'+ (data.more == 'upper' ? 'top' : 'btm'), data.page);
                        }

                        // Restore trigger pic because previously it could have disabled-style of appearance
                        $('#'+name+'-trigger').attr('src', '/i/admin/trigger-system.png');

                    // Else if results set is empty (no non-disabled options), we hide options, and set red
                    // color for keyword, as there was no related results found
                    } else {
                        // Hide options list div
                        if ($('#'+name+'-suggestions').css('display') != 'none') $('#'+name+'-keyword').click();

                        // If just got resuts are result for satellited combo, autofetched after satellite value was changed
                        // and we have no results related to current satellite value, we disable satellited combo
                        if (data.mode == 'refresh-children') {
                            setDisabled(name, true);

                        // Else if reason of no results was not in satellite, we add special css class for that case
                        } else {
                            $('#'+name+'-keyword').addClass('no-results');
                            $('#'+name+'-trigger').attr('src', '/i/admin/trigger-system-disabled.png');
                        }
                    }
                }, 'json'
            )
        }

        /**
         * Set some option as selected, autosets value for hidden field
         */
        select = function (){
            var name, li;

            if (typeof arguments[0] == 'string') {
                name = arguments[0];
                li = $('#'+name+'-suggestions ul li.selected');
            } else {
                name = $(this).parents('.combo-div').find('.combo-keyword').attr('lookup');
                li = $(this);
            }

            $('#'+name).val(li.attr(name));
            $('#'+name+'-keyword').val(li.text().trim());
            $('#'+name+'-keyword').attr('prev', li.text().trim());
            $('#'+name).change();
            hideSuggestions(name);

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
        suggestions = function(json, name){
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

                        // Close <li>
                        item += '>';

                        // Prepend option title with optgroup indent, if optgroups are used
                        item += groupIndent;

                        // Prepend option title with indent if needed
                        if (json['data'][i].system && json['data'][i].system['indent']
                            && typeof json['data'][i].system['indent'] == 'string')
                            item += json['data'][i].system['indent'];

                        item += json['data'][i].title;
                        item += '</li>';
                        items.push(item);
                    }
                }
            }

            // If optgroups is used and we are deaing with items tree, we should distibute items by optgroups,
            // but insert all parents for options, if these parents not in same groups as child options
            if (json.optgroup != undefined && json.tree) items = appendNotSameGroupParents(items, json, name);

            // Stat info
            $('#'+name+'-count').text(number_format(json['ids'].length - disabledCount));
            $('#'+name+'-found').text(number_format(json['found']));

            // Info should be displayed only if maximum possible results per page is less that total found results

            // We shoud mark keyword input field as readonly if:
            // 1. We are running 'no-keyword' fetch-mode
            // 2. Keyword input field is not already marked as 'disabled'
            // 3. Current displayed count of results is not greater than visibleCount variable
            if ($('#'+name+'-info').attr('fetch-mode') == 'no-keyword' &&
                       $('#'+name+'-keyword').attr('disabed') != 'disabled' &&
                       parseInt($('#'+name+'-found').text().replace(',','')) <= visibleCount
                ) {
                $('#'+name+'-keyword').addClass('readonly').attr('readonly', 'readonly');

            // Otherwise info should be
            } else {
                $('#'+name+'-info').css('visibility', 'visible');
                $('#'+name+'-keyword').removeClass('readonly').removeAttr('readonly');
            }
            var html = items.length ? '<ul>'+items.join("\n")+'</ul>' : '';

            // We setup selectedIndex attribute
            if ($(html).find('li').length) {

                // Get current selectedIndex, and if it is 0, calculate it
                var currentSelectedIndex = parseInt($('#'+name+'-keyword').attr('selectedIndex'));
                if (currentSelectedIndex == 0) {

                    // We reset disabledCount here, because now, we should count all disabled html-items, not only json-items
                    // because now in options html there can be another disabled options, appeared as a result of using 'group'
                    // (mean 'optgroup') ability and as result of appendNotSameGroupParents() function execution
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
        keyUpHandler = function (event){

            // Get input element
            input = $(this);

            // Get field name
            var name = input.attr('lookup');

            // We will be fetching results with a timeout, so fetch requests will be
            // sent after keyword typing is finished (or seems to be finished)
            clearTimeout(timeout);

            // Variable for detecting fetch mode. Fetch mode can be 'keyword' and 'no-keyword', and is 'no-keyword' by default
            var fetchMode = $('#'+name+'-info').attr('fetch-mode');

            // Setup variables for range of pages that's results are already fetched and displayed in combo as options
            // This variables will be used if current fetchMode is 'no-keyword', because for 'keyword' fetchMode will be
            // used different logic
            var pageTop = parseInt($('#'+name+'-info').attr('page-top'));
            var pageBtm = parseInt($('#'+name+'-info').attr('page-btm'));

            // Variable for detection if next|prev page of results should be fetched
            var moreResultsNeeded = event.keyCode.toString().match(/^(34|33)$/) && $('#'+name+'-suggestions').attr('more') && $('#'+name+'-suggestions').attr('more').toString().match(/^(upper|lower)$/) ? $('#'+name+'-suggestions').attr('more') : false;

            // Variable for detection if keyword was changed and first page of related results should be fetched
            var keywordChanged = ($(this).attr('prev') != input.val() && input.val() != '' && !event.keyCode.toString().match(/^(13|40|38|34|33)$/));

            // Check if keyword was emptied
            var keywordChangedToEmpty = ($(this).attr('prev') != input.val() && input.val() == '' && !event.keyCode.toString().match(/^(13|40|38|34|33)$/));

            // If keyword was at least once changed, we switch fetch mode to 'keyword'.
            // We need to take it to attention, because PgUp fetching is impossible in case
            // if we have no keyword
            if (keywordChanged) {
                $('#'+name+'-info').attr('fetch-mode', 'keyword');
                $('#'+name+'-info').attr('keyword', input.val());

                // Temporary strip red color from input, as we do not know if there will be at least
                // one result related to specified keyword, and if no - keyword will be coloured in red
                $('#'+name+'-keyword').removeClass('no-results');

                // Reset selected index
                $('#'+name+'-keyword').attr('selectedIndex', 0);

                // Scroll options list to the most top
                $('#'+name+'-suggestions').scrollTo('0px');

                // Set hidden value field as 0, and fire 'change' event, because if we are running keyword search
                // hidden value should be 0 until some of search results will be selected
                $('#'+name).val(0).change();

                // We set 'changed' attribute to 'true' to remember the fact of at least one time change.
                // We will need this fact in request data prepare process, because if at the moment of sending
                // request 'changed' will still be 'false' (initial value), satellite property won't be set in
                // request data object. We need this to get upper and lower page results fetched from currently selected
                // value as startpoint. And after 'changed' attribute set to 'false', upper and lower page results will
                // have start point different to selected value, and based on most top alphabetic order.
                $('#'+name+'-info').attr('changed', 'true');
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
                            keyDownHandler(name, 33);
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
                                keyDownHandler(name, 33);
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
                            keyDownHandler(name, 34);
                            return;
                        }
                    }
                    data.more = moreResultsNeeded;

                    // Fetch request
                    fetch(data);

                // If we are searching by keyword
                } else if (event.keyCode != '33') {
                    // Setup request keyword
                    data.keyword = input.val();

                    // Setup previous keyword
                    input.attr('prev', input.val());

                    // Setup range borders as they were by default
                    $('#'+name+'-info').attr('page-top', '0');
                    $('#'+name+'-info').attr('page-btm', '0');

                    //$('#'+name+'-suggestions').attr('find', data.find);
                    //data.page = $('#'+name+'-suggestions').attr('page');
                    timeout = setTimeout(fetch, 500, data);
                }
            }

            // If keyword was changed to empty we fire 'change' event. We do that for being sure
            // that dependent combos (combos that are satellited by current combo) are disabled. Also,
            // after keyword was changed to empty, hidden value was set to 0, so we should call .change() anyway
            if (keywordChangedToEmpty) {
                hideSuggestions(name);
                input.attr('prev', input.val());
                $('#'+name).change();
            }
        }

        /**
         * Bind keyUpHandler on keyup event for keyword html-input
         */
        $('.combo-keyword').keyup(keyUpHandler);

        /**
         * Set keyboard keys handling (Up, Down, PgUp, PgDn, Esc, Enter), related to visual appearance
         *
         * @param eventOrName Used to get code of pressed key on keyboard in case if this is event object
         * @param code Alternative way for getting code of pressed key on keyboard
         * @return {Boolean}
         */
        keyDownHandler = function(eventOrName, code){
            // Setup code, name and input variables
            var code, name, input;
            if (arguments[1]) {
                code = arguments[1];
                name = eventOrName;
                input = $('#'+name+'-keyword');
            } else {
                code = event.keyCode;
                input = $(this);
                name = input.attr('lookup');
            }

            // Enter - select an option
            if (code == '13') {
                select(name);

            // Up or Down arrows
            } else if (code == '40' || code == '38' || code == '34' || code == '33') {
                if (code == '40' && $('#'+name+'-suggestions').css('display') == 'none') {
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
                        var ib = input.attr('selectedIndex');
                        if (parseInt(input.attr('selectedIndex')) < size - visibleCount) {
                            input.attr('selectedIndex', parseInt(input.attr('selectedIndex'))+visibleCount);
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
                        if (parseInt(input.attr('selectedIndex')) > visibleCount) {
                            input.attr('selectedIndex', parseInt(input.attr('selectedIndex'))-visibleCount);
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
                            var visibleS = $('#'+name+'-suggestions').scrollTop()/14;
                            var visibleE = visibleS + visibleCount - 1;
                            var delta = 0;
                            if (liIndex > visibleE) {
                                delta = (liIndex - visibleE) * 14;
                            } else if (liIndex < visibleS) {
                                delta = (liIndex - visibleS) * 14;
                            }
                            var expr = (delta > 0 ? '+' : '-')+'='+Math.abs(delta)+'px';
                            if (delta) $('#'+name+'-suggestions').scrollTo(expr);
                        } else {
                            $(this).removeClass('selected');
                        }
                    });

                    // Set value while walking trough options list
                    $('#'+name).val($('#'+name+'-suggestions'+' ul li.selected').attr(name));
                    var text = $('#'+name+'-suggestions'+' ul li.selected').text();
                    $('#'+name+'-keyword').val(text.trim());
                    $('#'+name+'-keyword').attr('prev', text.trim());
                }
                return false;
            } else if (event.keyCode == '27') {
                hideSuggestions(name);
            }
        }

        /**
         * If combo has a satellite, and satellite value is 0, combo should be disabled.
         * Otherwise, combo wil be enabled.
         *
         * @param name
         */
        setDisabled = function(name, force){
            // Check if this combo should be disabled
            var satellite = $('#'+name+'-info').attr('satellite').toString();
            if(satellite.length) {
                var sv = $('#'+satellite).val();
                sv = sv.length == 0 ? 0 : parseInt(sv);
                if (sv == 0 || force == true) {
                    $('#'+name+'-keyword').attr('disabled', 'disabled');
                    $('#'+name+'-keyword').parents('.combo-div').addClass('disabled');
                    $('#'+name+'-keyword').parents('.combo-div').find('.combo-trigger').attr('src', '/i/admin/trigger-system-disabled.png');
                    $('#'+name+'-keyword').val('');

                    // We set hidden field value as 0, and fire 'change event' because there can be
                    // satellited combos for current combo, so if we have, for example 5 cascading combos,
                    // that are satellited to each other, this code provide that if top combo is disabled,
                    // all dependent (satellited) combos will also be disabled recursively
                    $('#'+name).val(0);
                    $('#'+name).change();

                    // There is currently only one case when 'force' param is passed,
                    // and this case only happen if we were fetching satellited (dependent) results,
                    // and we had no success for that search (nothing found). That is why we are giving
                    // a corresponding message, that there is no results related to such satellite value
                    if (force && sv != 0) {
                        var satellite = $('#'+name+'-info').attr('satellite');
                        $('#'+name+'-keyword').addClass('no-results-within');
                        $('#'+name+'-keyword').removeAttr('readonly');
                        $('#'+name+'-keyword').val('No results within \'' + $('#'+satellite+'-keyword').val()+ '\'');
                    }

                // Enable combo
                } else {
                    $('#'+name+'-keyword').removeAttr('disabled');
                    $('#'+name+'-keyword').parents('.combo-div').removeClass('disabled');
                    $('#'+name+'-keyword').parents('.combo-div').find('.combo-trigger').attr('src', '/i/admin/trigger-system.png');
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
         * Bind keyDownHandler on keyup event for keyword html-input
         */
        $('.combo-keyword').keydown(keyDownHandler);

        /**
         * Bind handlers for click, blur events and misc things for keyword html-input
         */
        $('.combo-keyword').each(function(index){

            // Get name
            var name = $(this).attr('lookup');

            // Prevent standard autocompletion
            $(this).attr('autocomplete', 'off');

            // Setup combo as disabled, if needed
            setDisabled(name, comboOptions[name]['ids'].length == 0);

            // Initially, setup all combos as not able to lookup
            // However, if 'found' > 'count', lookup ability will be enabled after first click on keyword field
            if ($(this).attr('disabled') != 'disabled') {
                $(this).attr('readonly', 'readonly');
                $(this).addClass('readonly');
            }

            // Set previous value as current value at initialisation
            $(this).attr('prev', $(this).val());

            // Call 'hideSuggestions' function on blur, if current mouse position is not over options
            $(this).blur(function(){
                if ($('#'+name+'-suggestions').attr('hover') != 'true') hideSuggestions(name);
            });


            $(this).click(function(){
                // Check if combo is disabled
                if ($(this).parents('.combo-div').hasClass('disabled') || $(this).hasClass('no-results')) return;

                if ($(this).parent().find('.combo-data').css('display') == 'none') {

                    // Set initial 'index' and 'selectedIndex' attribs values
                    if ($('#'+name+'-keyword').attr('selectedIndex') == undefined) {
                        $('#'+name+'-keyword').attr('selectedIndex', 0);
                    }

                    // Rebuild html for options
                    var html = suggestions(comboOptions[name], name);
                    $(this).parent().find('.combo-data').html(html);

                    // Set height of options list div
                    adjustComboOptionsDivHeight(name);

                    // Set scrolling if number of options more than visibleCount
                    $('#'+name+'-suggestions').css('overflow-y', $('#'+name+'-suggestions ul li').length > visibleCount ? 'scroll' : '');

                    // Bind a 'selected' class adding on hover
                    $(this).parent().find('.combo-data ul li[class!="disabled"]').hover(
                        function(){
                            $(this).parent().find('li').removeClass('selected');
                            $(this).addClass('selected');
                            var k = $(this).parent().find('li[class!="disabled"]').index(this);
                            $('#'+name+'-keyword').attr('selectedIndex', k+1);
                        }
                    );

                    // Bind a click event to each option
                    $(this).parent().find('.combo-data ul li[class!="disabled"]').click(select);
                }

                // Toggle options and info
                $(this).parent().find('.combo-data').toggle();
                $(this).parent().find('.combo-info').toggle();
                adjustComboInfoLeftMargin(name);
            });
            adjustComboTriggerLeftMargin(name);

            $('#'+name).change(function(){
                $('.combo-info[satellite="'+name+'"]').each(function(){
                    var satellited = $(this).parents('.combo-div').find('.combo-keyword').attr('lookup')
                    setDisabled(satellited);
                    if ($(this).parents('.combo-div').hasClass('disabled') == false) {
                        fetch({
                            field: satellited,
                            satellite: $('#'+$(this).attr('satellite')).val(),
                            mode: 'refresh-children'
                        });
                    }
                });

                // Execute javascript code, if it was assigned to selected option
                if (comboOptions[name].enumset) {
                    var index = comboOptions[name]['ids'].indexOf($(this).val());
                    if (index != -1 && comboOptions[name]['data'][index].system.js) {
                        eval(comboOptions[name]['data'][index].system.js);
                    }
                }

                // Execute javascript code, assigned as an additional handler for 'select' event
                if (comboOptions[name].js) {
                    eval(comboOptions[name].js);
                }
            });

            $('#'+name+'-trigger').click(function(){
                $(this).parent().find('.combo-keyword').click();
            });

            var page = comboOptions[name] && comboOptions[name].page ? comboOptions[name].page : 1;

            $(this).parent().append('<div id="'+name+'-suggestions" class="combo-data" style="z-index: 1000' + index + '; width: ' + $(this).width() + 'px; margin-top: -1px; overflow-y: hidden;" hover="false"/>');

            $(this).parent().find('.combo-data').scroll(function(){
                $('#'+name+'-keyword').focus();
            });
            $(this).parent().find('.combo-info').click(function(){
                $('#'+name+'-keyword').focus();
            });
            $(this).parent().find('.combo-data').hover(function(){
                $(this).attr('hover','true');
            }, function(){
                $(this).attr('hover','false');
            });

            // Execute javascript code, if it was assigned to default selected option
            if (comboOptions[name].enumset) {
                var index = comboOptions[name]['ids'].indexOf($('#'+name).val());
                if (index != -1 && comboOptions[name]['data'][index].system.js) {
                    eval(comboOptions[name]['data'][index].system.js);
                }
            }
        });

        /**
         * Merge two trees of options
         *
         * @param tree1
         * @param tree2
         * @return array
         */
        merge = function(tree1, tree2) {
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
        mergeOptgroupInfo = function (info1, info2) {
            for (var j in info2.groups) {
                if (info1.groups[j] == undefined) {
                    info1.groups[j] = info2.groups[j];
                }
            }
            return info1;
        }

        /**
         * If optgroups is used and we are deaing with items tree, we should distibute items by optgroups,
         * but insert all parents for options, if these parents not in same groups as child options
         *
         * @param items
         * @param json
         * @return []
         */
        appendNotSameGroupParents = function(items, json, name) {
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
        bindTrigger = function(){
            $('.combo-trigger').mousedown(function(){
                if ($(this).parents('.combo-div').hasClass('disabled') ==  false &&
                    $(this).parents('.combo-div').find('.combo-keyword').hasClass('no-results') ==  false)
                    $(this).attr('src', '/i/admin/trigger-system-pressed.png');
            });
            $('.combo-trigger').mouseleave(function(){
                if ($(this).parents('.combo-div').hasClass('disabled') ==  false &&
                    $(this).parents('.combo-div').find('.combo-keyword').hasClass('no-results') ==  false)
                    $(this).attr('src', '/i/admin/trigger-system.png');
            });
            $('.combo-trigger').mouseup(function(){
                if ($(this).parents('.combo-div').hasClass('disabled') ==  false &&
                    $(this).parents('.combo-div').find('.combo-keyword').hasClass('no-results') ==  false)
                    $(this).attr('src', '/i/admin/trigger-system.png');
            });
        }
        bindTrigger();
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if ((typeof jQuery !== 'undefined')) {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    if ($('.combo-div').length) process();
                })
            }
        }, 25);
    }());

    return dselect;

}(COMBO || {}));
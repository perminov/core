var COMBO = (function (dselect) {
    "use strict";
    var process = function () {
        var keyUpHandler, keyDownHandler, suggestions, input, select, hide, timeout, bindTrigger, fetch, merge,
            adjustComboInfoLeftMargin, adjustComboTriggerLeftMargin, adjustComboOptionsDivHeight, visibleCount = 20;

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
            if (comboOptions[name]['ids'].length >= visibleCount) {
                $('#'+name+'-suggestions').css('height', '281px');
            } else {
                $('#'+name+'-suggestions').css('height', comboOptions[name]['ids'].length * 14 + 1);
            }
        }

        /**
         * Function for hiding options list, as Esc key was pressed
         *
         * @param name
         */
        hide = function(name) {

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
                    $('#'+name+'-suggestions').css('overflow-y', comboOptions[name]['ids'].length > visibleCount ? 'scroll' : '');

                    // Adjust options div height
                    adjustComboOptionsDivHeight(name);

                    if (json['found']) {
                        // Adjust left margin for '.combo-info' because width
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

                        // Also, we shoud reset selectedIndex
                        //$('#'+name+'-keyword').attr('selectedIndex', 0);
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
                        //if (input.attr('prev').length > 0) {
                            // Show options list after keyword typing is finished
                            if ($('#'+name+'-suggestions').css('display') == 'none') $('#'+name+'-keyword').click();

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
                                keyDownHandler(null, data.more == 'upper' ? 33 : 34);

                                // Update page-top|page-btm value
                                $('#'+name+'-info').attr('page-'+ (data.more == 'upper' ? 'top' : 'btm'), data.page);
                            }

                        //}

                    // Else hide options, and set red color for keyword, as there was no related results found
                    } else {
                        if ($('#'+name+'-suggestions').css('display') != 'none') $('#'+name+'-keyword').click();
                        $('#'+name+'-keyword').addClass('no-results');
                    }
                }, 'json'
            )
        }

        /**
         * Set some option as selected, autosets value for hidden field
         */
        select = function (){
            var name = $(this).parents('.combo-div').find('.combo-keyword').attr('lookup');
            $('#'+name+'-keyword').val($(this).text().trim());
            $('#'+name).val($(this).attr(name));
            $('#'+name).change();
            hide(name);
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
            var disabledCount = 0;
            for (var i in json['ids']) {
                if (json['ids'][i] != undefined && !isNaN(json['ids'][i]) && !isNaN(i)) {
                    // Classes for option
                    var cls = [];

                    // Open <li>
                    var item = '<li';
                    item += ' ' + name + '="' + json['ids'][i] + '"';

                    // Mark as disabled
                    if (json['data'][i].system && json['data'][i].system['disabled']) {
                        cls.push('disabled');
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

                            // We setup selectedIndex attribute
                            $('#'+name+'-keyword').attr('selectedIndex', parseInt(i)+1 - disabledCount);

                        }

                    }

                    // Append css classes list as 'class' attribute for an option
                    if (cls.length) item += ' class="' + cls.join(' ') + '"';

                    // Close <li>
                    item += '>';

                    if (json['data'][i].system && json['data'][i].system['indent']
                        && typeof json['data'][i].system['indent'] == 'string')
                        item += json['data'][i].system['indent'];

                    item += json['data'][i].title;
                    item += '</li>';
                    items.push(item);
                }
            }
            $('#'+name+'-count').text(number_format(json['ids'].length - disabledCount));
            $('#'+name+'-found').text(number_format(json['found']));
            if (parseInt(json['found']) > json['ids'].length) $('#'+name+'-info').css('visibility', 'visible');
            return items.length ? '<ul>'+items.join("\n")+'</ul>' : '';
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

                // Scroll options list to thу most top
                $('#'+name+'-suggestions').scrollTo('0px');
            }

            // We will fetch data only if keyword was changed or if next|prev page of results
            // related to current keyword should be fetched
            if (keywordChanged || moreResultsNeeded) {

                // Get field satellite
                var satellite = $('#'+name).attr('satellite');

                // Prepare data for fetch request
                var data = {field: name/*, satellite: $('#'+satellite).attr('value')*/};

                // If we are paging
                if (moreResultsNeeded) {

                    // If previous page needed
                    if (event.keyCode == '33') {

                        // If keyword was at least once changed
                        if (fetchMode == 'keyword') {
                            $('#'+data.field+'-keyword').attr('selectedIndex', 1);
                            keyDownHandler(null, 33);
                            return;

                        // Else if we are still walking through pages of all (not filtered by keyword) results
                        } else if (fetchMode == 'no-keyword') {
                            // If top border of range of displayed pages is not yet 1
                            // we will be requesting deremented page. Attribute 'page-top',
                            // there pageTop variable value was got, will be decremented
                            // later - after request will be done and results fetched
                            if ($('#'+name+'-info').attr('page-top-reached') == 'false') {
                                data.page = pageTop - 1;

                            // Otherwise, if top border of range of displayed pages is already 1
                            // so it is smallest possible value and therefore we won't do any request,
                            // and we only should move selection to first option
                            } else {

                                $('#'+data.field+'-keyword').attr('selectedIndex', 1);
                                keyDownHandler(null, 33);
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
                            keyDownHandler(null, 34);
                            return;
                        }
                        //$('#'+name+'-suggestions').attr('page', parseInt($('#'+name+'-suggestions').attr('page'))+1);
                        //data.page = $('#'+name+'-suggestions').attr('page');
                    }
                    data.more = moreResultsNeeded;

                    // Fetch request
                    fetch(data);

                    /*if (data.value) fetch(data); else {
                        if (data.more == 'upper' && $('#'+name+'-info').attr('fetch-mode') == 'no-keyword'){
                            keyDownHandler(null, 33);
                        }
                    }*/

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
            if (keywordChangedToEmpty) {
                hide(name);
                input.attr('prev', input.val());
            }
        }

        /**
         * Bind keyUpHandler on keyup event for keyword html-input
         */
        $('.combo-keyword').keyup(keyUpHandler);

        /**
         * Set keyboard keys handling (Up, Down, PgUp, PgDn, Esc, Enter), related to visual appearance
         *
         * @param event Used to get code of pressed key on keyboard
         * @param code Alternative way for getting code of pressed key on keyboard
         * @param noUpdateLookup
         * @return {Boolean}
         */
        keyDownHandler = function(event, code, noUpdateLookup){
            var code = arguments[1] ? arguments[1] : event.keyCode;
            input = input || $(this);
            var name = input.attr('lookup');

            // Enter
            if (code == '13') {
                $('#'+name).val($('#'+name+'-suggestions'+' ul li.selected').attr(name));
                $('#'+name+'-keyword').val($('#'+name+'-suggestions'+' ul li.selected').text().trim());
                $('#'+name).change();
                hide(name);

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
                    if (!noUpdateLookup) {
                        $('#'+name).val($('#'+name+'-suggestions'+' ul li.selected').attr(name));
                        var text = $('#'+name+'-suggestions'+' ul li.selected').text();
                        $('#'+name+'-keyword').val(text.trim());
                        $('#'+name+'-keyword').attr('prev', text.trim());
                    }
                }
                return false;
            } else if (event.keyCode == '27') {
                hide(name);
            }
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

            //$(this).val(comboOptions[name][$('#'+name).val()]);

            // Set previous value as current value at initialisation
            $(this).attr('prev', $(this).val());

            // Call 'hide' function on blur, if current mouse position is not over options
            $(this).blur(function(){
                if ($('#'+name+'-suggestions').attr('hover') != 'true') hide(name);
            });


            $(this).click(function(){
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

            /*var satellite = $('#'+name).attr('satellite');
            if (satellite) {
                $('#'+satellite).change(function(){
                    $('#'+name+'-suggestions').hide();
                    var data = {field: name, satellite: $(this).attr('value'), value: $('#'+name).attr('value')};
                    $.post('./json/1/', data, function(json){
                        var found = 0; for (var id in json['keys']) if (json['keys'][id] == $('#'+name).attr('value')) found = id;
                        if ( ! found) $('#'+name).attr('value', 0);
                        $('#'+name+'-keyword').attr('value', json['values'][found]);
                        $('#'+name+'-keyword').attr('prev', json['values'][found]);
                        comboOptions[name] = json;
                        // назначем индекс по умолчанию, чтобы от него отталкиваться при обработке нажатия
                        // клавиш Up, Down
                        var index = 1;
                        for (var i in comboOptions[name]['keys']) {
                            if ($('#'+name).val() == comboOptions[name]['keys'][i]) {
                                break;
                            } else {
                                index++;
                            }
                        }
                        $('#'+name+'-keyword').attr('index', index);
                        $('#'+name+'-keyword').attr('selectedIndex', index-1);
                        additionalCallback(satellite);
                    }, 'json');
                });
                $('#'+satellite).change();
            }

            //$xhtml .= "\$('#". $satellite ."').change(function(){\$.post('./json/1/', { field: '" . $name . "', satellite: \$('#". $satellite ."').attr('value') },   function(data) {     \$('#". $name ."').html(data);" . str_replace(array('"', "\n", "\r"), array('\"',"",""), $satelliteRow->javascript) . "; additionalCallback('" . $satellite . "')},'html');}); \$('#". $satellite ."').change();";
            */

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
                    }
                }
            }

            return tree1;
        }

        /**
         * Set trigger button icon (pressed or unpressed)
         */
        bindTrigger = function(){
            $('.combo-trigger').mousedown(function(){
                $(this).attr('src', '/i/admin/trigger-system-pressed.png');
            });
            $('.combo-trigger').mouseleave(function(){
                $(this).attr('src', '/i/admin/trigger-system.png');
            });
            $('.combo-trigger').mouseup(function(){
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
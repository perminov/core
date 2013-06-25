var COMBO = (function (dselect) {
    "use strict";
    var process = function () {
        var suggest, keyboard, suggestions, input, select, hide, timeout, post, bindTrigger;

        // Function for hiding options list, as Esc key was pressed
        hide = function(name) {

            // Hide options
            $('#'+name+'-suggestions').hide();

            // If keyword contents does not equal to any option, empty hidden value field
            // 1. Set notEqual as true
            var notEqual = true;

            // 2. Get current keyword value
            var lookup = $('#'+name+'-keyword').val();

            // 3. Check if keyword value match any existing option
            for (var key in dselectOptions[name]['data'])
                if (dselectOptions[name]['data'][key].title == lookup)
                    notEqual = false;

            // 4. If no match found, empty hidden value field
            if (notEqual) $('#'+name).val("0");

            // 5. Hide info about count and found
            $('#'+name+'-info').hide();
        }

        post = function(name, data, input, more){
        $('#'+name+'-count').html('<img src="/i/loading/loading35.gif" class="dselect-loader" width="15">');
            $.post("./json/1/", data,
                function(json) {
                    if (more == true) {
                        if (data.up == 'true') {
                            var backupOptions = [];
                            backupOptions = deepObjCopy(dselectOptions[name]);

                            dselectOptions[name]['ids'] = [];
                            dselectOptions[name]['data'] = [];
                            //dselectOptions[name]['keys'].push('0');
                            //dselectOptions[name]['values'].push('Выберите');

                            json['ids'].reverse();
                            json['data'].reverse();
                            for (var key in json['ids']) {
                                if (!isNaN(json['ids'][key])) {
                                    dselectOptions[name]['ids'].push(json['ids'][key]);
                                    dselectOptions[name]['data'].push(json['data'][key]);
                                }
                            }

                            for (var key in backupOptions['ids']) {
                                if (key != 0 && !isNaN(backupOptions['ids'][key])) {
                                    dselectOptions[name]['ids'].push(backupOptions['ids'][key]);
                                    dselectOptions[name]['data'].push(backupOptions['data'][key]);
                                }
                            }
                        } else {
                            for (var key in json['ids']) {
                                var cond = $('#'+name+'-suggestions').attr('page') > 0 ? true : key != 0;
                                if (cond && !isNaN(json['ids'][key])) {
                                    dselectOptions[name]['ids'].push(json['ids'][key]);
                                    dselectOptions[name]['data'].push(json['data'][key]);
                                }
                            }
                        }
                        $('#'+name+'-suggestions').attr('more', 'false');
                    } else {
                        dselectOptions[name] = json;
                    }
                    var html = suggestions(dselectOptions[name], name);
                    $('#'+name+'-suggestions').html(html);
                    $('#'+name+'-suggestions'+' ul li').hover(
                        function(){
                            $(this).parent().find('li').removeClass('selected');
                            $(this).addClass('selected');
                            var k = $(this).parent().find('li').index(this);
                            $('#'+name+'-keyword').attr('index', k+1);
                            $('#'+name+'-keyword').attr('selectedIndex', k+1);
                        }
                    );
                    $('#'+name+'-suggestions'+' ul li').click(select);
                    if (html.match('li')) {
                        if (input.attr('prev').length > 0) {
                            $('#'+name+'-suggestions').show();
                            if (more) {
                            } else {
                                input.attr('selectedIndex', 0);
                                input.attr('index', 0);
                            }
                            $('#'+name+'-count').text($('#'+name+'-suggestions'+' ul li').size());
//                            $('#'+name+'-found').text('из ' + number_format($('#'+name+'-suggestions').attr('count')));
                            if (!more) {
                                keyboard(0, 40, true);
                            } else if (data.up == 'true'){
                                input.attr('selectedIndex', parseInt(input.attr('selectedIndex')) + json['ids'].length);
                                input.attr('index', parseInt(input.attr('index')) + json['ids'].length);
                                $('#'+name+'-suggestions').scrollTo('+='+((json['ids'].length-1)*15)+'px');
                                $('#'+name+'-suggestions'+' ul li').removeClass('selected');
                                $('#'+name+'-suggestions'+' ul li:nth-child('+(parseInt(input.attr('index'))-1)+')').addClass('selected');
                            }
                        }
                    } else {
                        hide(name);
                        $('#'+name+'-keyword').css('color','red');
                        $('#'+name+'-count').text($('#'+name+'-suggestions'+' ul li').size());
                        //$('#'+name+'-count').text('из ' + number_format($('#'+name+'-suggestions').attr('count')));
                    }
                }, 'json'
            )
        }

        suggest = function (event){
            input = $(this);
            var name = input.attr('lookup');
            var more = $('#'+name+'-suggestions').attr('more') == 'true' ? true : false;
            clearTimeout(timeout);
            if (($(this).attr('prev') != input.val() && event.keyCode != '13') || (event.keyCode == '40' && $('#'+name+'-suggestions').css('display') == 'none') || more) {
                if (($(this).attr('prev') != input.val() && input.val() != '' && event.keyCode != '40' && event.keyCode != '38' && event.keyCode != '34' && event.keyCode != '33') || more) {
                    $('#'+name+'-keyword').css('color','');
                    var satellite = $('#'+name).attr('satellite');
                    var data = {field: name, satellite: $('#'+satellite).attr('value'), noempty: true};
                    if (more) {
                        if ($('#'+name+'-suggestions').attr('page') > 0) {
                            $('#'+name+'-suggestions').attr('page', parseInt($('#'+name+'-suggestions').attr('page'))+1);
                            data.page = $('#'+name+'-suggestions').attr('page');
                            data.find = $('#'+name+'-suggestions').attr('find');
                        } else {
                            if (event.keyCode == '33') {
                                data.up = 'true';
                                data.value = $('#'+name+'-suggestions'+' ul li:eq(1)').attr(name);
                            } else if (event.keyCode == '34') {
                                data.value = $('#'+name+'-suggestions'+' ul li:last').attr(name);
                            }
                        }
                        data.more = more;
                        post(name, data, input, more);
                    } else if (event.keyCode != '33'){
                        data.find = input.val();
                        $('#'+name+'-suggestions').attr('page', '1');
                        $('#'+name+'-suggestions').attr('find', data.find);
                        data.page = $('#'+name+'-suggestions').attr('page');
                        timeout = setTimeout(post, 500, name, data, input, more);
                    }
                }
            }
            if (!more) {
                if (input.val() == '' && event.keyCode != 40) hide(name);
                input.attr('prev', input.val());
            }
        }

        $('.combo-keyword').each(function(index){

            // Get name
            var name = $(this).attr('lookup');

            // Prevent standard autocompletion
            $(this).attr('autocomplete', 'off');

            //$(this).val(dselectOptions[name][$('#'+name).val()]);

            // Set previous value as current value at initialisation
            $(this).attr('prev', $(this).val());

            // Call 'hide' function on blur, if current mouse position is not over options
            $(this).blur(function(){
                if ($('#'+name+'-suggestions').attr('hover') != 'true') hide(name);
            });


            $(this).click(function(){
                if ($(this).parent().find('.combo-data').css('display') == 'none') {

                    // Set initial 'index' and 'selectedIndex' attribs values
                    if ($('#'+name+'-keyword').attr('index') == undefined) {
                        $('#'+name+'-keyword').attr('index', 0);
                        $('#'+name+'-keyword').attr('selectedIndex', 0);
                    }

                    // Rebuild html for options
                    var html = suggestions(dselectOptions[name], name);
                    $(this).parent().find('.combo-data').html(html);

                    // Bind a 'selected' class adding on hover
                    $(this).parent().find('.combo-data ul li').hover(
                        function(){
                            $(this).parent().find('li').removeClass('selected');
                            $(this).addClass('selected');
                            var k = $(this).parent().find('li').index(this);
                            $('#'+name+'-keyword').attr('index', k+1);
                            $('#'+name+'-keyword').attr('selectedIndex', k+1);
                        }
                    );

                    // Bind a click event to each option
                    $(this).parent().find('.combo-data ul li').click(select);
                }

                // Toggle options and info
                $(this).parent().find('.combo-data').toggle();
                $(this).parent().find('.combo-info').toggle();
            });

            var satellite = $('#'+name).attr('satellite');
            if (satellite) {
                $('#'+satellite).change(function(){
                    $('#'+name+'-suggestions').hide();
                    var data = {field: name, satellite: $(this).attr('value'), value: $('#'+name).attr('value')};
                    $.post('./json/1/', data, function(json){
                        var found = 0; for (var id in json['keys']) if (json['keys'][id] == $('#'+name).attr('value')) found = id;
                        if ( ! found) $('#'+name).attr('value', 0);
                        $('#'+name+'-keyword').attr('value', json['values'][found]);
                        $('#'+name+'-keyword').attr('prev', json['values'][found]);
                        dselectOptions[name] = json;
                        // назначем индекс по умолчанию, чтобы от него отталкиваться при обработке нажатия
                        // клавиш Up, Down
                        var index = 1;
                        for (var i in dselectOptions[name]['keys']) {
                            if ($('#'+name).val() == dselectOptions[name]['keys'][i]) {
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

            $('#'+name+'-trigger').click(function(){
                $(this).parent().find('.combo-keyword').click();
            });

            $(this).parent().append('<div id="'+name+'-suggestions" class="combo-data" style="z-index: 1000' + index + '; width: ' + $(this).width() + 'px; margin-top: -1px;" hover="false"/>');

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

        select = function (){
            var name = $(this).parents('.combo-div').find('.combo-keyword').attr('lookup');
            $('#'+name+'-keyword').val($(this).text().trim());
            $('#'+name).val($(this).attr(name));
            $('#'+name).change();
            hide(name);
        }

        // Build options as list items
        suggestions = function(json, name){
            var html = '';
            var items = [];
            for (var i in json['ids']) {
                if (json['ids'][i] != undefined && !isNaN(json['ids'][i])) {
                    var item = '<li';
                    item += ' ' + name + '="' + json['ids'][i] + '"';
                    if ($('#'+name).val() == json['ids'][i]) item += ' class="selected"';
                    item += '>';
                    if (json['data'][i].system && json['data'][i].system.indent) item += json['data'][i].system.indent;
                    item += json['data'][i].title;
                    item += '</li>';
                    items.push(item);
                }
            }
            $('#'+name+'-count').text(json['ids'].length);
            $('#'+name+'-found').text(json['found']);
            if (parseInt(json['found']) > json['ids'].length) $('#'+name+'-info').css('visibility', 'visible');
            return items.length ? '<ul>'+items.join("\n")+'</ul>' : '';
        }

        // Set keyboard keys handling (Up, Down, PgUp, PgDn, Esc, Enter)
        keyboard = function(event, code, noUpdateLookup){
            var code = arguments[1] ? arguments[1] : event.keyCode;
            var visibleCount = 10;
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
                    var size = $('#'+name+'-suggestions'+' ul li').size();

                    // Down key
                    if (code == '40'){
                        if (parseInt(input.attr('selectedIndex')) < size - 1) {
                            input.attr('index', parseInt(input.attr('index'))+1);
                        }
                    // Up key
                    } else  if (code == '38'){
                        if (parseInt(input.attr('selectedIndex')) > 0) {
                            input.attr('index', parseInt(input.attr('index'))-1);
                        }
                    // PgUp key
                    } else if (code == '34') {
                        if (parseInt(input.attr('selectedIndex')) < size - 1 - visibleCount) {
                            input.attr('index', parseInt(input.attr('index'))+visibleCount);
                        } else if (parseInt(input.attr('index')) <= size) {
                            if (parseInt($('#'+name+'-count').text()) < parseInt($('#'+name+'-found').text())){
                                $('#'+name+'-count').html('<img src="/i/loading/loading35.gif" class="dselect-loader" width="15">');
                                $('#'+name+'-suggestions').attr('more', 'true');
                            } else {
                                input.attr('index', size);
                            }
                        }

                    // PgUp key
                    } else if (code == '33') {
                        if (parseInt(input.attr('selectedIndex')) > visibleCount) {
                            input.attr('index', parseInt(input.attr('index'))-visibleCount);
                        } else {
                            if (parseInt($('#'+name+'-count').text()) < parseInt($('#'+name+'-found').text()) && !$('#'+name+'-suggestions').attr('find')){
                                $('#'+name+'-count').html('<img src="/i/loading/loading35.gif" class="dselect-loader" width="15">');
                                $('#'+name+'-suggestions').attr('more', 'true');
                            } else {
                                input.attr('index', 1);
                            }
                        }
                    }

                    // Set up selected item, depending on what key was pressed, and deal scroll list of options if need
                    $('#'+name+'-suggestions'+' ul li').each(function(liIndex){
                        if ((parseInt(input.attr('index')) > 0 && liIndex == (parseInt(input.attr('index'))-1)%size) || (parseInt(input.attr('index')) <= 0 && liIndex+1 == size-Math.abs(parseInt(input.attr('index'))%size))) {
                            $(this).addClass('selected');
                            input.attr('selectedIndex', liIndex);
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
                    }
                }
                return false;
            } else if (event.keyCode == '27') {
                hide(name);
            }
        }

        // Set keyword search handling
        $('.combo-keyword').keyup(suggest);

        // Set keyboard keys handling (Up, Down, PgUp, PgDn, Esc, Enter)
        $('.combo-keyword').keydown(keyboard);

        // Set trigger button icon (pressed or unpressed)
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
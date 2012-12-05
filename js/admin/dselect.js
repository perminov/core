var DSELECT = (function (dselect) {
	"use strict";
	var process = function () {
		var suggest, keyboard, suggestions, input, select, hide, timeout, post;

		hide = function(name) {
			// Скрываем опции, так как нажата клавиша Esc
			$('#suggest-'+name).hide();
			// Если содержимое лукап-поля не соответствует ни одной опции, то опустошаем hidden-поле
			var lookup = $('#'+name+'-lookup').val();
			var novalue = true; for (var key in dselectOptions[name]['values']) if (dselectOptions[name]['values'][key] == lookup) novalue = false;
			if (novalue) $('#'+name).val("0");
		}

		post = function(name, data, input, more){
			$('#'+name+'-current').html('<img src="/i/loading/loading35.gif" class="dselect-loader" width="15">');
			$.post("./json/1/", data,
				function(json) {
					if (more == true) {
						if (data.up == 'true') {
							var backupOptions = [];
							backupOptions = deepObjCopy(dselectOptions[name]);

							dselectOptions[name]['keys'] = [];
							dselectOptions[name]['values'] = [];
							dselectOptions[name]['keys'].push('0');
							dselectOptions[name]['values'].push('Выберите');

							json['keys'].reverse();
							json['values'].reverse();
							for (var key in json['keys']) {
								if (!isNaN(json['keys'][key])) {
									dselectOptions[name]['keys'].push(json['keys'][key]);
									dselectOptions[name]['values'].push(json['values'][key]);
								}
							}

							for (var key in backupOptions['keys']) {
								if (key != 0 && !isNaN(backupOptions['keys'][key])) {
									dselectOptions[name]['keys'].push(backupOptions['keys'][key]);
									dselectOptions[name]['values'].push(backupOptions['values'][key]);
								}
							}
						} else {
							for (var key in json['keys']) {
								var cond = $('#suggest-'+name).attr('page') > 0 ? true : key != 0;
								if (cond && !isNaN(json['keys'][key])) {
									dselectOptions[name]['keys'].push(json['keys'][key]);
									dselectOptions[name]['values'].push(json['values'][key]);
								}
							}
						}
						$('#suggest-'+name).attr('more', 'false');
					} else {
						dselectOptions[name] = json;
					}
					var html = suggestions(dselectOptions[name], name);
					$('#suggest-'+name).html(html);
					$('#suggest-'+name+' ul li').hover(
						function(){
							$(this).parent().find('li').removeClass('selected');
							$(this).addClass('selected');
							var k = $(this).parent().find('li').index(this);
							$('#'+name+'-lookup').attr('index', k+1);
							$('#'+name+'-lookup').attr('selectedIndex', k+1);
						}
					);
					$('#suggest-'+name+' ul li').click(select);
					if (html.match('li')) {
						if (input.attr('prev').length > 0) {
							$('#suggest-'+name).show();
							if (more) {
							} else {
								input.attr('selectedIndex', 0);
								input.attr('index', 0);
							}
							$('#'+name+'-current').text($('#suggest-'+name+' ul li['+name+'!=0]').size());
							$('#'+name+'-count').text('из ' + number_format($('#suggest-'+name).attr('count')));
							if (!more) {
								keyboard(0, 40, true); 
							} else if (data.up == 'true'){
								input.attr('selectedIndex', parseInt(input.attr('selectedIndex')) + json['keys'].length);
								input.attr('index', parseInt(input.attr('index')) + json['keys'].length);
								$('#suggest-'+name).scrollTo('+='+((json['keys'].length-1)*15)+'px');
								$('#suggest-'+name+' ul li').removeClass('selected');
								$('#suggest-'+name+' ul li:nth-child('+(parseInt(input.attr('index'))-1)+')').addClass('selected');
							}
						}
					} else {
						hide(name);
						$('#'+name+'-lookup').css('color','red');
						$('#'+name+'-current').text($('#suggest-'+name+' ul li['+name+'!=0]').size());
						$('#'+name+'-count').text('из ' + number_format($('#suggest-'+name).attr('count')));
					}
				}, 'json'
			)
		}
		
		suggest = function (event){
			input = $(this);
			var name = input.attr('lookup');
			var more = $('#suggest-'+name).attr('more') == 'true' ? true : false;
			clearTimeout(timeout);
			if (($(this).attr('prev') != input.val() && event.keyCode != '13') || (event.keyCode == '40' && $('#suggest-'+name).css('display') == 'none') || more) {
				if (($(this).attr('prev') != input.val() && input.val() != '' && event.keyCode != '40' && event.keyCode != '38' && event.keyCode != '34' && event.keyCode != '33') || more) {
					$('#'+name+'-lookup').css('color','');
					var satellite = $('#'+name).attr('satellite');
					var data = {field: name, satellite: $('#'+satellite).attr('value'), noempty: true};
					if (more) {
						if ($('#suggest-'+name).attr('page') > 0) {
							$('#suggest-'+name).attr('page', parseInt($('#suggest-'+name).attr('page'))+1);
							data.page = $('#suggest-'+name).attr('page');
							data.find = $('#suggest-'+name).attr('find');
						} else {
							if (event.keyCode == '33') {
								data.up = 'true';
								data.value = $('#suggest-'+name+' ul li:eq(1)').attr(name);
								window.console.log(data.value);
							} else if (event.keyCode == '34') {
								data.value = $('#suggest-'+name+' ul li:last').attr(name);
							}
						}
						data.more = more;
						post(name, data, input, more);
					} else if (event.keyCode != '33'){
						data.find = input.val();
						$('#suggest-'+name).attr('page', '1');
						$('#suggest-'+name).attr('find', data.find);
						data.page = $('#suggest-'+name).attr('page');
						timeout = setTimeout(post, 500, name, data, input, more);
					}
				}
			}
			if (!more) {
				if (input.val() == '' && event.keyCode != 40) hide(name);
				input.attr('prev', input.val());
			}
		}
		
		$('.dselect-lookup').each(function(index){
			var name = $(this).attr('lookup');
			$(this).attr('autocomplete', 'off');
			$(this).parent().css('width', ($(this).parent().parent().find('.dselect-button').width() - 20) + 'px');
			$(this).val(dselectOptions[name][$('#'+name).val()]);
			$(this).attr('prev', $(this).val());
			
			// определяем и назначем индекс выбранной опции, чтобы потом от него отталкиваться при расчетах Up и Down
			$(this).blur(function(){
				$('#'+name+'-button').css('outline', '');
				if ($("#suggest-"+name).attr('hover') == 'false') $("#suggest-"+name).hide();
			});
			$(this).click(function(){
				$('#'+name+'-button').css('outline', 'rgb(229, 151, 0) auto 5px');
				if ($(this).parent().find('.suggestions').css('display') == 'none') {
					var html = suggestions(dselectOptions[name], name);
					$(this).parent().find('.suggestions').html(html);
					$(this).parent().find('.suggestions ul li').hover(
						function(){
							$(this).parent().find('li').removeClass('selected');
							$(this).addClass('selected');
							var k = $(this).parent().find('li').index(this);
							$('#'+name+'-lookup').attr('index', k+1);
							$('#'+name+'-lookup').attr('selectedIndex', k+1);
						}					
					);
					$(this).parent().find('.suggestions ul li').click(select);
					$('#'+name+'-current').text($('#suggest-'+name+' ul li['+name+'!=0]').size());
					$('#'+name+'-count').text('из ' + number_format($('#suggest-'+name).attr('count')));
				}
				$(this).parent().find('.suggestions').toggle();
				if ($(this).parent().find('.suggestions').css('display') == 'none') {
					hide(name);
				}
			});
			
			var satellite = $('#'+name).attr('satellite');
			if (satellite) {
				$('#'+satellite).change(function(){
					$('#suggest-'+name).hide();
					var data = {field: name, satellite: $(this).attr('value'), value: $('#'+name).attr('value')};
					$.post('./json/1/', data, function(json){
						var found = 0; for (var id in json['keys']) if (json['keys'][id] == $('#'+name).attr('value')) found = id;
						if ( ! found) $('#'+name).attr('value', 0);
						$('#'+name+'-lookup').attr('value', json['values'][found]);
						$('#'+name+'-lookup').attr('prev', json['values'][found]);
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
						$('#'+name+'-lookup').attr('index', index);
						$('#'+name+'-lookup').attr('selectedIndex', index-1);
						additionalCallback(satellite);
					}, 'json');
				});
				$('#'+satellite).change();
			}

			//$xhtml .= "\$('#". $satellite ."').change(function(){\$.post('./json/1/', { field: '" . $name . "', satellite: \$('#". $satellite ."').attr('value') },   function(data) {     \$('#". $name ."').html(data);" . str_replace(array('"', "\n", "\r"), array('\"',"",""), $satelliteRow->javascript) . "; additionalCallback('" . $satellite . "')},'html');}); \$('#". $satellite ."').change();";
			
			$('#'+name+'-button').click(function(){
				$(this).parent().find('.dselect-lookup').click();
			});
			$(this).parent().append('<div id="suggest-'+name+'" class="suggestions dselect" style="z-index: 1000' + index + ';" hover="false"/>');
			$(this).parent().find('.suggestions').css({
				width: $('#'+name+'-button').width()+'px', 
				top: '21px'
			});
			$(this).parent().find('.suggestions').scroll(function(){
				$('#'+name+'-lookup').focus();
			});
			$(this).parent().find('.dselect-info').click(function(){
				$('#'+name+'-lookup').focus();
			});
			$(this).parent().find('.suggestions').hover(function(){
				$(this).attr('hover','true');
			}, function(){
				$(this).attr('hover','false');
			});
		});
		
		select = function (){
			var name = $(this).parents('.dselect-div').find('.dselect-lookup').attr('lookup');
			$('#'+name+'-lookup').val($(this).text());
			$('#'+name).val($(this).attr(name));
//			eval(name+'AutocompleteSelectHandler(this);');
			$('#'+name).change();
			$('#suggest-'+name).hide();
		}

		suggestions = function(json, name){
			var html = '';
			var items = [];
			for (var key in json['keys']) {
				if (json['keys'][key] != undefined && !isNaN(json['keys'][key])) {
					items.push('<li '+name+'="' + json['keys'][key] + '"' + ($('#'+name).val() == json['keys'][key] ? ' class="selected"' : '')+'>' + json['values'][key] + '</li>');
				} else if (json['keys'][key] == 'data') {
					var data = json['values'][key];
					$('#suggest-'+name).attr('count', data['count']);
				}
			}
			
			return items.length ? '<ul>'+items.join("\n")+'</ul>' : '';
		}
		
		keyboard = function(event, code, noUpdateLookup){
			var code = arguments[1] ? arguments[1] : event.keyCode;
			var visibleCount = 10;
			input = input || $(this);
			var name = input.attr('lookup');
			// Enter
			if (code == '13') {
				$('#'+name).val($('#suggest-'+name+' ul li.selected').attr(name));
				$('#'+name+'-lookup').val($('#suggest-'+name+' ul li.selected').text());
				$('#'+name).change();
				$('#suggest-'+name).hide();
			// Up or Down arrows
			} else if (code == '40' || code == '38' || code == '34' || code == '33') {
				if (code == '40' && $('#suggest-'+name).css('display') == 'none') {
					$('#suggest-'+name).show();
				} else {
					var size = $('#suggest-'+name+' ul li').size();
					if (code == '40'){
						if (parseInt(input.attr('selectedIndex')) < size - 1) {
							input.attr('index', parseInt(input.attr('index'))+1);
						}
					} else  if (code == '38'){
						if (parseInt(input.attr('selectedIndex')) > 0) {
							input.attr('index', parseInt(input.attr('index'))-1);
						}
					} else if (code == '34') {
						if (parseInt(input.attr('selectedIndex')) < size - 1 - visibleCount) {
							input.attr('index', parseInt(input.attr('index'))+visibleCount);
						} else if (parseInt(input.attr('index')) <= size) {
							if (parseInt($('#'+name+'-current').text()) < parseInt($('#suggest-'+name).attr('count'))){
								$('#'+name+'-current').html('<img src="/i/loading/loading35.gif" class="dselect-loader" width="15">');
								$('#suggest-'+name).attr('more', 'true');
							} else {
								input.attr('index', size);
							}
						}
					} else if (code == '33') {
						if (parseInt(input.attr('selectedIndex')) > visibleCount) {
							input.attr('index', parseInt(input.attr('index'))-visibleCount);
						} else {
							if (parseInt($('#'+name+'-current').text()) < parseInt($('#suggest-'+name).attr('count')) && !$('#suggest-'+name).attr('find')){
								$('#'+name+'-current').html('<img src="/i/loading/loading35.gif" class="dselect-loader" width="15">');
								$('#suggest-'+name).attr('more', 'true');
							} else {
								input.attr('index', 1);
							}
						}
					}
					$('#suggest-'+name+' ul li').each(function(liIndex){
						if ((parseInt(input.attr('index')) > 0 && liIndex == (parseInt(input.attr('index'))-1)%size) || (parseInt(input.attr('index')) <= 0 && liIndex+1 == size-Math.abs(parseInt(input.attr('index'))%size))) {
							$(this).addClass('selected');
							input.attr('selectedIndex', liIndex);
							var visibleS = $('#suggest-'+name).scrollTop()/15;
							var visibleE = visibleS + visibleCount - 1;
							var delta = 0;
							if (liIndex > visibleE) {
								delta = (liIndex - visibleE) * 15;
							} else if (liIndex < visibleS) {
								delta = (liIndex - visibleS) * 15;
							}
							var expr = (delta > 0 ? '+' : '-')+'='+Math.abs(delta)+'px';
							if (delta) $('#suggest-'+name).scrollTo(expr);
						} else {
							$(this).removeClass('selected');
						}
					});
					
					if (!noUpdateLookup) {
						$('#'+name).val($('#suggest-'+name+' ul li.selected').attr(name));
						$('#'+name+'-lookup').val($('#suggest-'+name+' ul li.selected').text());
					}
				}
				return false;
			} else if (event.keyCode == '27') {
				hide(name);
			}
		}
		
		$('.dselect-lookup').keyup(suggest);
		$('.dselect-lookup').keydown(keyboard);
	};
	
	
	(function () {
		var checkRequirementsId = setInterval(function () {
			if ((typeof jQuery !== 'undefined')) {
				clearInterval(checkRequirementsId);
				$(document).ready(function(){
					if ($('.dselect-div').length) process();
				})
            }
		}, 25);
	}());

	return dselect;
}(DSELECT || {}));
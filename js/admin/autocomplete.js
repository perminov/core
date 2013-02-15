var AUTOCOMPLETE = (function (clective) {
	"use strict";
	var process = function () {
		var suggest, keyboard, suggestions, input, select;
		
		suggest = function (event){
			input = $(this);
			var name = $(this).attr('name');
			if (($(this).attr('prev') != input.val() && event.keyCode != '13') || (event.keyCode == '40' && $('#suggest-'+name).css('display') == 'none')) {
				if (input.val().length > 0) {
					$.post((window.cmsOnlyMode?'':'/admin')+"/auxillary/autocomplete", {value : input.val(), id: input.attr('identifier')},
						function(json) {
							var html = suggestions(json);
							$('#suggest-'+name).html(html);
							$('#suggest-'+name).css('width', input.width()+'px');
							$('#suggest-'+name).css('width', input.width()+'px');
							$('#suggest-'+name+' ul li').hover(
								function(){
									$(this).attr('color', $(this).css('background-color'));
									$(this).css('background-color', '#ecffeb');
								},
								function(){
									$(this).css('background-color', $(this).attr('color'));
								}
								
							);
							$('#suggest-'+name+' ul li').click(select);
							if (html.match('li')) {
								if (input.attr('prev').length > 0) {
									$('#suggest-'+name).show();
									input.attr('selectedIndex', 0);
									input.attr('index', 0);
									keyboard(0, 40);
								}
							} else {
								$('#suggest-'+name).hide();
							}
						}, 'json'
					);
				} else $('#suggest-'+name).hide();
			}
			input.attr('prev', input.val());
		}
		
		select = function (){
			input = input || $(this);
			input.val($(this).text());
			var name = input.attr('name');
			eval(name+'AutocompleteSelectHandler(this);');
			$('#suggest-'+name).hide();
		}
		
		suggestions = function(json){
			var html = '';
			var items = [];
			var general = json['general'];
			for (var key in json['options']) {
				if (json['options'][key]['text'] != undefined) {
					var data = []; for (var dataItem in json['options'][key]['data']) {
						data.push(dataItem+ '="' + json['options'][key]['data'][dataItem] + '"');
					}
					data.push(general+'="' + key + '"');
					items.push('<li' + (data.length ? ' ' + data.join(' ') : '') + '>' + json['options'][key]['text'] + '</li>');
				}
			}
			return items.length ? '<ul>'+items.join("\n")+'</ul>' : '';
		}
		
		keyboard = function(event){
			var code = arguments[1] ? arguments[1] : event.keyCode;
			input = input || $(this);
			var name = input.attr('name');
			// Enter
			if (code == '13') {
				var oneSelected=false;
				$('#suggest-'+name+' ul li').each(function(liIndex){
					//if ($(this).css('background-color') == 'rgb(201, 201, 201)' || $(this).css('background-color') == '#c9c9c9') {
					if ($(this).css('background-color') == 'rgb(223, 255, 223)' || $(this).css('background-color') == '#dfffdf') {
						$(this).click();
					}
				});
			} else if (code == '40' || code == '38') {
				if (code == '40' && $('#suggest-'+name).css('display') == 'none') {
					if (input.attr('selected') == 'true') {
						suggest(input.val(), event);
						input.attr('selected', 'false');
					} else if (input.val()){
						suggest(input.val(), event);
					}
				} else {
					if (code == '40'){
						input.attr('index', parseInt(input.attr('index'))+1);
					} else  if (code == '38'){
						input.attr('index', parseInt(input.attr('index'))-1);
					}
					var size = $('#suggest-'+name+' ul li').size();
					$('#suggest-'+name+' ul li').each(function(liIndex){
						if (parseInt(input.attr('index')) > 0) {
							if(liIndex == (parseInt(input.attr('index'))-1)%size) {
								$(this).css('background-color','#dfffdf');
								input.attr('selectedIndex', liIndex);
							} else {
								$(this).css('background-color','#ffffff');
							}
						} else {
							if(liIndex+1 == size-Math.abs(parseInt(input.attr('index'))%size)) {
								input.attr('selectedIndex', liIndex);
								$(this).css('background-color','#dfffdf');
							} else {
								$(this).css('background-color','#ffffff');
							}
						}
					});
				}
			} else if (event.keyCode == '27') {
				$('#suggest-'+name).hide();
			}
//			input.focus();
		}
		
		//autocomplete="off" onkeyup="suggestHeader(this.value, event)" onkeydown="keyboardHeader(event)"
		$('.autocomplete').each(function(index){
			$(this).attr('autocomplete', 'off');
			$(this).attr('selected', 'false');
			$(this).parent().append('<div id="suggest-'+$(this).attr('name')+'" class="suggestions" style="z-index: 1000' + index + '"/>');
		});
		
		$('.autocomplete').keyup(suggest);
		$('.autocomplete').keydown(keyboard);
    };
	
	
	(function () {
		var checkRequirementsId = setInterval(function () {
			if ((typeof jQuery !== 'undefined')) {
				clearInterval(checkRequirementsId);
				$(document).ready(function(){
					if ($('.autocomplete').length) process();
				})
            }
		}, 25);
	}());

	return clective;
}(AUTOCOMPLETE || {}));


/*
$(document).ready(function(){
});
*/
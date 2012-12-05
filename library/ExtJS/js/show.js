function serializeLeftMenu()
{
	var str = '';
	
	$('ul#left_menu li:not(.noSubmenu) span').each(function() {
		var li = $(this).parents('li')[0];
		var idValue = $(li).attr('value');
		
		if (parseInt(idValue) > 0) {
			if ($(li).hasClass('subOpened')) {
				str += idValue + ':1;'; 
			} else {
				str += idValue + ':0;';
			}
		}
	});

	return str.substr(0, str.length - 1);
}

$(document).ready(function() {
	$('ul#left_menu li:not(.noSubmenu) span').click(function() {
		var li = $(this).parents('li')[0];
			
		if ($(li).hasClass('subOpened')) {
			$(li).removeClass('subOpened');
			$(li).children('ul.submenu').each(function () {
				$(this).hide();
			});
		} else {
			$(li).addClass('subOpened');
			$(li).children('ul.submenu').each(function () {
				$(this).show();
			});
		}
		
		$.cookie("serializedLeftMenu" , serializeLeftMenu(), {
	  		path: "/"
		});
	});
	//IE6 Fix
	var version = $.browser.version;
	if(($.browser.msie)&&(version == '6.0')) {
		$('.submenu li').bind("mouseenter",function(){
			$(this).addClass('current');
		}).bind("mouseleave",function(){
			$(this).removeClass('current');
		});
	}	
});
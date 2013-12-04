function page(rowsetAlias, page){
	$.post("./", {page : page, rowsetAlias: rowsetAlias, rowsetParams: true},
		function(data){
			window.location.reload();
		}
	);
	return false;
}
function vkAuth(response) {
	if (response.session) {
		VK.Api.call('getUserInfo', {}, function(r) {
			if(r.response) {
				MYid=r.response['user_id'];
				VK.Api.call('getProfiles',{uids:MYid,fields:"nickname,sex,bdate,city,country,photo_big",format:"JSON"},function(z) {
					// получаем страну и город по id контакта
					var countryTitle = '';
					var cityTitle = '';
					if (z.response[0]['country']){
						VK.Api.call('getCountries',{api_id:2732852,v:"2.0",cids:z.response[0]['country'], format: "JSON"},function(country){
							z.response[0]['countryTitle'] = country.response[0]['name'];
							// получаем город по id контакта
							if (z.response[0]['city']){
								VK.Api.call('getCities',{api_id:2732852,v:"2.0",cids:z.response[0]['city'], format: "JSON"},function(city){
									z.response[0]['cityTitle'] = city.response[0]['name'];
									$.post('./', {authType: 'vk', params: z.response}, function(data) {
										eval(data);
									});
								});
							} else {
								$.post('./', {authType: 'vk', params: z.response}, function(data) {
									eval(data);
								});
							}
						});
					} else {
						$.post('./', {authType: 'vk', params: z.response}, function(data) {
							eval(data);
						});
					}
				});
			} 
		});	
//		alert('user: '+response.session.mid);
	} else {
//		alert('not auth');
	}
}
function snLogout(sn){
	if (sn == 'vkontakte') {
		$('#logoutIframe').attr('src','http://oauth.vkontakte.ru/oauth/logout?success_url=client_id%3D2732852%26redirect_uri%3Dclose.html%26response_type%3Dtoken%26scope%3D0%26state%3D%26display%3Dpopup');
	} else if (sn == 'facebook') {
		FB.logout();
	} else if (sn == 'mymailru') {
		mailru.connect.logout();
	}
	setTimeout(logout, 500);
	return false;
}
function logout(){
	window.location.replace('/logout/');
}
$(document).ready(function(){
  if($.isFunction($.fn.fancybox)) {
    $(".fancybox-gallery ul a").fancybox({
      margin: 20, //расстояние от границ экрана до области просмотра
      overlayOpacity: 0.6,
      overlayColor: "#000",
      padding: 3,
      titlePosition: 'inside'
    });
  }
});
/**
 * Converts an RGB color value to HSL. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes r, g, and b are contained in the set [0, 255] and
 * returns h, s, and l in the set [0, 1].
 *
 * @param   Number  r       The red color value
 * @param   Number  g       The green color value
 * @param   Number  b       The blue color value
 * @return  Array           The HSL representation
 */
function rgbToHsl(r, g, b){
    r /= 255, g /= 255, b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2;

    if(max == min){
        h = s = 0; // achromatic
    }else{
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max){
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }

    return [h, s, l];
}

/**
 * Converts an HSL color value to RGB. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes h, s, and l are contained in the set [0, 1] and
 * returns r, g, and b in the set [0, 255].
 *
 * @param   Number  h       The hue
 * @param   Number  s       The saturation
 * @param   Number  l       The lightness
 * @return  Array           The RGB representation
 */
function hslToRgb(h, s, l){
    var r, g, b;

    if(s == 0){
        r = g = b = l; // achromatic
    }else{
        function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return [r * 255, g * 255, b * 255];
}
function hexToRgb(hex) {
    var bigint = parseInt(hex, 16);
    var r = (bigint >> 16) & 255;
    var g = (bigint >> 8) & 255;
    var b = bigint & 255;

    return r + "," + g + "," + b;
}
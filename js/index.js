function inArray(value,arr){
	for(var i = 0; i<arr.length; i++){
		if (value == arr[i]) return arguments[2] ? i : true;
	}
	return arguments[2] ? -1 : false;
}
// Убирает пробельные символы слева
function ltrim(str) {
	var ptrn = /\s*((\S+\s*)*)/;
	return str.replace(ptrn, "$1");
}
// Убирает пробельные символы справа
function rtrim(str) {
	var ptrn = /((\s*\S+)*)\s*/;
	return str.replace(ptrn, "$1");
}
// Убирает пробельные символы с обоих концов
function trim(str) {
	return ltrim(rtrim(str));
}
function title2alias(title){
	var s = new Array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","№"," ","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","-");
	var r = new Array("a","b","v","g","d","e","yo","zh","z","i","i","k","l","m","n","o","p","r","c","t","u","f","h","ts","ch","sh","shh","","y","","e","yu","ya","#","-","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","-");
	var alias = '';
	title = trim(title.toLowerCase());
	for (var i = 0; i < title.length; i++) {
		var c = title.substr(i, 1);
		var index = inArray(c, s, 1);
		if (index != -1) alias = alias + r[index];
	}
	alias = alias.replace(/^\-+/, '');
	alias = alias.replace(/\-+$/, '');
	alias = alias.replace(/\-{2,}/g, '-');
	return alias;
}

function d(target){
    var obj = target;
    var info = '';
    for (i in obj) {
    	info += i + '=' + obj[i] + '\n';
    }
	if (arguments[1]) {
		var nw = window.open('','','');
		nw.document.write(info);
	} else {
		alert(info);
	}
}
function echo(info){
	var nw = window.open('','','');
	nw.document.write(info);
}
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
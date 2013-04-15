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
//Ë À Ì Â Í Ã Î Ä Ï Ç Ò È Ó É Ô Ê Õ Ö ê Ù ë Ú î Û ï Ü ô Ý õ â û ã ÿ ç
//E A I A I A I A I C O E O E O E O O e U e U i U i U o Y o a u a y c
	var s = new Array("а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я","№"," ","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","-","0","1","2","3","4","5","6","7","8","9"	   ,"Ë","À","Ì","Â","Í","Ã","Î","Ä","Ï","Ç","Ò","È","Ó","É","Ô","Ê","Õ","Ö","ê","Ù","ë","Ú","î","Û","ï","Ü","ô","Ý","õ","â","û","ã","ÿ","ç");
	var r = new Array("a","b","v","g","d","e","yo","zh","z","i","i","k","l","m","n","o","p","r","s","t","u","f","h","c","ch","sh","shh","","y","","e","yu","ya","#","-","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","-","0","1","2","3","4","5","6","7","8","9","e","a","i","a","i","a","i","a","i","c","o","e","o","e","o","e","o","o","e","u","e","u","i","u","i","u","o","u","o","a","u","a","y","c");
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

function change(object, arrow)
{
    if (object.style.display == "") {
        object.style.display = "none";
        arrow.src = "/i/admin/arr_right.gif";
    } else {
        object.style.display = "";
        arrow.src = "/i/admin/arr_bottom.gif";
    }
}
function print_r(obj){
   str="";
   for(i in obj){
      str+=i+":"+obj[i]+"\n<br>";
   }
   return str;
}
function sendAdd(name, value, action)
{    
    document.getElementById("formPost")[0].name = name;
    document.getElementById("formPost")[0].value = value;
    document.getElementById("formPost").action = action;
    document.getElementById("formPost").submit();
}
function selectChangeAjax(id, div)
{
	var name = id;
	var i;
	var action;
	for (i = 0; i< div.length; i++)
	{
	  action = '/admin/ajax/'+div[i]+'/'+name+'/'+document.getElementById(id).value;
	  ajaxgowithimg(action,div[i],'');   
	}
}
function formOk(){
	return true;
}
function selectChange(id)
{
    var value=document.getElementById(id).value;
    document.getElementById("formPost")[0].name = id;
    document.getElementById("formPost")[0].value = value;
    document.getElementById("formPost").submit();
}
function selectChangeMany(id)
{
    var value=document.getElementById(id).value;
    document.getElementById("formPost")[0].name = id;
    document.getElementById("formPost")[0].value = value;
    document.getElementById("formPost").submit();
}
function urlalias(value, target)
{
    value = new String(value);
    value = value.toLowerCase();
    value = value.split('\'').join('');
    value = value.split('\\').join('');
    value = value.split('/').join('');
    value = value.split('|').join('');
    value = value.split('`').join('');
    value = value.split('"').join('');
    value = value.split('&').join('-and-');
    value = value.split(',').join('');
    value = value.split(' ').join('-');
    value = value.split('.').join('');
    value = value.split('--').join('-');
    value = value.split('--').join('-');
    value = value.replace('/( )+/');
    target = target ? target : document.forms[1].alias;
    target.value = value;
}
function d(target)
{
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
function number(str)
{
	var number = '';
	for (var i = 0; i < str.length; i++) {
		code = str.charCodeAt(i);
		if ((code >= 48 && code <= 57) || str.charAt(i) == '-') {
			number += str.charAt(i);
		}
	}
	return number;
}
function $(ElId) {
	return document.getElementById(ElId);
}
/*
function inArray(value,arr){
	for(var i = 0; i<arr.length; i++){
		if (value == arr[i]) return true;
	}
	return false;
}*/
function g(id){return document.getElementById(id);}
function turnOn(e){
	if (!arguments[1]){
		e.style.display = 'inline';
	}
	e.disabled = false;
}
function turnOff(e){
	if (!arguments[1]){
		e.style.display = 'none';
	}
	e.disabled = true;
}
function decimal(value){
	if (value.length == 0) {
		value = '00';
	} else if (value.length == 1) {
		value = value + '0';
	}
	return value;
}
function hide(ids){
	ids = ids.split(',');
	for (var i = 0; i < ids.length; i++) {
		$('#' + ids[i]).hide();
	}
}
function show(ids){
	ids = ids.split(',');
	for (var i = 0; i < ids.length; i++) {
		$('#' + ids[i]).show();
	}
}
function setCookie (name, value, expires, path, domain, secure) {
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}
function additionalCallback(selector){
	callback = $('#'+selector).attr('callback');
	eval(callback);
}
function refresh(name, value) {
	if ($('#'+name+'-lookup')) {
		$('#'+name+'-lookup').attr('value', dselectOptions[name]['values'][dselectOptions[name]['keys'].indexOf(value)]);
	}
	$('#'+name).attr('value', value);
	$('#'+name).change();
}
function number_format (number, decimals, dec_point, thousands_sep) {
   number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
function deepObjCopy (dupeObj) {
	var retObj = new Object();
	if (typeof(dupeObj) == 'object') {
		if (typeof(dupeObj.length) != 'undefined')
			var retObj = new Array();
		for (var objInd in dupeObj) {	
			if (typeof(dupeObj[objInd]) == 'object') {
				retObj[objInd] = deepObjCopy(dupeObj[objInd]);
			} else if (typeof(dupeObj[objInd]) == 'string') {
				retObj[objInd] = dupeObj[objInd];
			} else if (typeof(dupeObj[objInd]) == 'number') {
				retObj[objInd] = dupeObj[objInd];
			} else if (typeof(dupeObj[objInd]) == 'boolean') {
				((dupeObj[objInd] == true) ? retObj[objInd] = true : retObj[objInd] = false);
			}
		}
	}
	return retObj;
}
alert = function(){
    var message = arguments[0];
    Ext.MessageBox.show({
        title: "Сообщение",
        msg: message,
        buttons: Ext.MessageBox.OK,
        icon: Ext.MessageBox.WARNING
    });
}
bindTrail = function(){
    $('.trail-item-section').hover(function(){
        $('.trail-siblings').hide();
        var itemIndex = $(this).attr('item-index');
        var width = (parseInt($(this).width()) + 27);
        if ($('#trail-item-' + itemIndex + '-sections ul li').length) {
            $('#trail-item-' + itemIndex + '-sections').css('min-width', width + 'px');
            $('#trail-item-' + itemIndex + '-sections').css('display', 'inline-block');
        }
    }, function(){
        if (parseInt(event.pageY) < parseInt($(this).offset().top) || parseInt(event.pageX) < parseInt($(this).offset().left)) $('.trail-siblings').hide();
    });
    $('.trail-siblings').mouseleave(function(){
        $(this).hide();
    });
}
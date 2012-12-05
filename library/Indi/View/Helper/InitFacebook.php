<?php
class Indi_View_Helper_InitFacebook extends Indi_View_Helper_Abstract{
	public function initFacebook($apiId){
		ob_start();?>
<div id="fb-root"></div>
<script>
window.fbAsyncInit=function(){
	FB.init({
		appId:'<?=$apiId?>',
		status:true,
		cookie:true,
		xfbml:true,
		oauth:true,
	});
	FB.Event.subscribe('auth.login', function(lr) {
//		alert('login');
		FB.api('/me', function(response) {
//			alert('/me');
			$.post('./', {authType: 'fb', params: response}, function(data) {
//				alert('insert');
//				echo(data);
				eval(data);
			});
		});	
    });	
};
(function(d){var js,id='facebook-jssdk';if(d.getElementById(id)){return;}js=d.createElement('script');js.id=id;js.async=true;js.src = "//connect.facebook.net/ru_RU/all.js";
d.getElementsByTagName('head')[0].appendChild(js);}(document));
</script>
<div class="fb-login-button" style="display: none;" data-scope="email">Login with Facebook</div>
		<?$xhtml = ob_get_clean();
		return $xhtml;
	}
}
<?php
class Indi_View_Helper_InitMymailru extends Indi_View_Helper_Abstract{
	public function initMymailru($appId, $privateKey){
		ob_start();?>
  <script type="text/javascript" src="http://cdn.connect.mail.ru/js/loader.js"></script>
  <script type="text/javascript">
	$('document').ready(function(){
	if (typeof(mailru) == 'undefined') return;
    mailru.loader.require('api', function() {
     mailru.connect.init('<?=$appId?>', '<?=$privateKey?>');
     mailru.events.listen(mailru.connect.events.login, function(session){
       mailru.common.users.getInfo(
		function(result){
			$.post('./', {authType: 'mm', params: result[0]}, function(data){
				window.location.reload();
			})
		}
	   );
     });
     mailru.events.listen(mailru.connect.events.logout, function(){
//      window.location.reload();
     });
     mailru.connect.getLoginStatus(function(result) {
      if (result.is_app_user != 1) {
		 $('<a class="mrc__connectButton" style="display: none !important;">вход@mail.ru</a>').appendTo('body');
       mailru.connect.initButton();
	     $('.mrc__connectButton').hide();
      } else {
      }
     });
    });
	});
  </script>
		<?$xhtml = ob_get_clean();
		return $xhtml;
	}
}
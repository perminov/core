<?php
class Indi_View_Helper_InitVkontakte extends Indi_View_Helper_Abstract{
	public function initVkontakte($apiId){
	// 	http://vkontakte.ru/editapp?id=2732852
		ob_start();?>
<script async src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript" charset="windows-1251"></script>
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?45"></script>
<script type="text/javascript">VK.init({apiId: <?=$apiId?>});</script>	
<div id="vk_auth"></div>
		<?$xhtml = ob_get_clean();
		return $xhtml;
	}
}
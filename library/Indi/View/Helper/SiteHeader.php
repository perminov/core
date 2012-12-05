<?php
class Indi_View_Helper_SiteHeader extends Indi_View_Helper_Abstract{
	public function siteHeader(){
		ob_start();?>
  <table class="main" width="1000" height="100%" align="center" border style="background-color: white;">
  <tr><td colspan="4" height="100">header will be here</td></tr>
  <tr>
	<td width="200" valign="top"><div id="authDepending"><?=$_SESSION['userId'] ? $this->view->userEntered() : $this->view->userLogin()?></div></td>
	<td valign="top">
	  <div>  
		
		<?return ob_get_clean();
	}
}
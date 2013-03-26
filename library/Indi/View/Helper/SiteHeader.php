<?php
class Indi_View_Helper_SiteHeader extends Indi_View_Helper_Abstract{
	public function siteHeader(){
		ob_start();?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <?=$this->view->siteFavicon()?>
        <title><?=$this->view->seoTDK('title')?></title>
        <meta name="description" content="<?=$this->view->seoTDK('description')?>">
        <meta name="keywords" content="<?=$this->view->seoTDK('keyword')?>">
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <link rel="stylesheet" type="text/css" href="/css/adjust.css">
        <script src="/js/jquery-1.9.1.min.js"></script>
        <script src="/js/jquery-migrate-1.1.1.min.js"></script>
        <script src="/js/jquery.scrollTo-min.js"></script>
    </head>
    <body>
  <table class="main" width="1000" height="100%" align="center" border style="background-color: white;">
  <tr><td colspan="4" height="100">header will be here</td></tr>
  <tr>
	<td width="200" valign="top"><div id="authDepending"><?=$_SESSION['userId'] ? $this->view->userEntered() : $this->view->userLogin()?></div></td>
	<td valign="top">
	  <div>  
		
		<?return ob_get_clean();
	}
}
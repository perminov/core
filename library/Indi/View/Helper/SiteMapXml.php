<?php
class Indi_View_Helper_SiteMapXml extends Indi_View_Helper_Abstract{
    public function siteMapXml(){
        ob_start();?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?foreach($this->view->tree as $item){?>
    <url>
        <loc>http://<?=$_SERVER['HTTP_HOST']?><?=$item['href'] == '/index/'? '/' : $item['href']?></loc>
        <changefreq>daily</changefreq>
        <priority><?=$item['href'] == '/index/'?'1.0':'0.8'?></priority>
    </url>
<?}?>
</urlset><?
        $xml = ob_get_clean();
        if ($GLOBALS['enableSeoUrls'] == 'true') $xml = Indi_Uri::sys2seo($xml, false, '/<loc>(http\:\/\/' . $_SERVER['HTTP_HOST'] . ')([0-9a-z\/#]+)<\/loc>/');
        return $xml;
    }
}
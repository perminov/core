<?php
class Indi_View_Helper_SiteFavicon extends Indi_View_Helper_Abstract{
    public function siteFavicon(){
        $faviconR = $this->view->blocks['favicon-path'];
        $faviconA = $_SERVER['DOCUMENT_ROOT'] . $faviconR;
        if (preg_match('/\.ico$/', $faviconR) && file_exists($faviconA)){
            $nocache = '?' . filemtime($faviconA);
            ob_start();
			?><link rel="icon" href="<?=$faviconR . $nocache?>" type="image/x-icon"><?
			?><link rel="shortcut icon" href="<?=$faviconR . $nocache?>" type="image/x-icon"><?
			$favicon = ob_get_clean();
		} 
        return $favicon;
    }
}
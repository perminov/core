<?php
class Indi_View_Helper_Admin_FormHtml extends Indi_View_Helper_Abstract
{
    public function formHtml($name, $value = null, $height = 300, $toolbar = 'Custom')
    {
        $toolbar = $toolbar ? $toolbar : 'Default';
        if ($value === null) {
            $value = $this->view->row->$name;
        }
		$config = Indi_Registry::get('config');
		$standalone = $config['general']->standalone == 'true' ? '/admin' : '';
		
		$field = $this->view->trail->getItem()->getFieldByAlias($name);
		$params = $field->getParams();

        require_once('ckeditor/ckeditor.php');
		require_once('ckfinder/ckfinder.php');
		$CKEditor = new CKEditor();
		$CKEditor->basePath = $standalone . '/library/ckeditor/';

        $customParams = array('wide','width','height','bodyClass','style','script','sourceStripper');
        foreach($customParams as $customParam) {
            if ($this->view->row->{$name . ucfirst($customParam)}) {
                $params[$customParam] = $this->view->row->{$name . ucfirst($customParam)};
            }
        }

        if ($params['style']) $CKEditor->config['style'] = $params['style'];
        if ($params['script']) $CKEditor->config['script'] = $params['script'];
        if ($params['sourceStripper']) $CKEditor->config['sourceStripper'] = $params['sourceStripper'];
        if ($params['bodyClass']) $CKEditor->config['bodyClass'] = $params['bodyClass'];
		if ($params['contentsCss']) $CKEditor->config['contentsCss'] = preg_match('/^\[/', $params['contentsCss']) ? json_decode($params['contentsCss']) : $params['contentsCss'];
		if ($params['contentsJs']) $CKEditor->config['contentsJs'] = preg_match('/^\[/', $params['contentsJs']) ? json_decode($params['contentsJs']) : $params['contentsJs'];
		if ($params['width']) $CKEditor->config['width'] = $params['width'] + 52;
		if ($params['height']) $CKEditor->config['height'] = $params['height'];
		$CKEditor->config['style'] .= 'body{max-width: auto;min-width: auto;width: auto;}';

		$ckfinder = new CKFinder();
		$ckfinder->BasePath = $standalone . '/library/ckfinder/';
		$ckfinder->SetupCKEditorObject($CKEditor);

		$CKEditor->returnOutput = true;
		$xhtml = $CKEditor->editor($name, $value);
        return $xhtml;
    }
}
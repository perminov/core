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
		
        require_once('ckeditor/ckeditor.php');
		require_once('ckfinder/ckfinder.php');
		$CKEditor = new CKEditor();
		$CKEditor->basePath = $standalone . '/library/ckeditor/';
		
		$ckfinder = new CKFinder();
		$ckfinder->BasePath = $standalone . '/library/ckfinder/';
		$ckfinder->SetupCKEditorObject($CKEditor);
		
		$CKEditor->returnOutput = true;
		$xhtml = $CKEditor->editor($name, $value);
		
        return $xhtml;
    }
}
/*
<?php
class Indi_View_Helper_Admin_FormHtml extends Indi_View_Helper_Abstract
{
    public function formHtml($name, $value = null, $height = 300, $toolbar = 'Custom')
    {
        $toolbar = $toolbar ? $toolbar : 'Default';
        if ($value === null) {
            $value = $this->view->row->$name;
        }
        require_once('FCKeditor/fckeditor.php');
		$config = Indi_Registry::get('config');
        $_SESSION['UserFilesPath'] = '/' . trim($config['upload']->uploadPath, '/') . '/' . trim($config['fckeditor']->uploadPath, '\\/') . '/';
        $_SESSION['UserFilesAbsolutePath'] = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/') . $_SESSION['UserFilesPath'];
        ob_start();        
        $oFCKeditor = new FCKeditor($name);
        $oFCKeditor->Height = $height;
        $oFCKeditor->BasePath = '/library/FCKeditor/';
        $oFCKeditor->Value      = $value;
        $oFCKeditor->ToolbarSet = $toolbar;
        $oFCKeditor->Create();        
        $xhtml = ob_get_clean();
        return $xhtml;
    }
}*/
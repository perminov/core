<?php
class Resize_Row extends Indi_Db_Table_Row
{
	public function delete($branchId = null){
		// delete copies of images, that have name equal to $this->alias
		$this->deleteUploadedImageCopiesByCopyName($this->alias);

		// standart Db_Table_Row deletion
		parent::delete($branchId);
	}

	public function deleteUploadedImageCopiesByCopyName($copyname){
		// get folder name where files of entity are stored
		$entity = Entity::getInstance()->fetchRow('`id` = (SELECT `entityId` FROM `field` WHERE `id` = "' . $this->fieldId . '")')->table;
		$image = $this->getForeignRowByForeignKey('fieldId')->alias;

		// get upload path from config
        $uploadPath = Indi_Image::getUploadPath();
        
        // absolute upload path  in filesystem
        $absolute = trim($_SERVER['DOCUMENT_ROOT'], '\\/') . '/' . $uploadPath . '/' . $entity . '/';
        
		// array for filenames that should be deleted
		$files = array();

		// all copies  with specified name are to be deleted too
		$files = glob($absolute . '*'.($image ? '_' . $image . '*' : '') .',' . $copyname . '.*');
		if (!$image) {
			$filtered = array();
			for($i = 0; $i < count($files); $i++) {
				$info = pathinfo($files[$i]);
				$info = explode(',', $info['filename']);
				if (is_numeric($info[0])) $filtered[] = $files[$i];
			}
			$files = $filtered;
		}
		for ($j = 0; $j < count($files); $j++) {
            try {
                unlink($files[$j]);
            } catch (Exception $e) {
//                throw new Exception($e->__toString());
            }
        }
	}

}
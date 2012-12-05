<?php
class Entity_Row extends Indi_Db_Table_Row
{
	public function delete($branchId = null){
		Indi_Registry::set('entityToDelete', $this->table);

		// standart Db_Table_Row deletion
		parent::delete($branchId);

		// delete all uploaded files of entity rows and folder they were stored in
		$this->deleteAllUploadedFilesAndUploadFolder();

		// delete db table
		$this->getTable()->getAdapter()->query('DROP TABLE `' . Indi_Registry::get('entityToDelete') . '`');

		// delete model file and row, rowset classes
		$this->deleteClasses();
	}

	public function deleteAllUploadedFilesAndUploadFolder(){
		// get folder name where files of entity are stored
		$entity = Indi_Registry::get('entityToDelete');

		// get upload path from config
		$uploadPath = Indi_Image::getUploadPath();
		
		// absolute upload path  in filesystem
		$absolute = trim($_SERVER['DOCUMENT_ROOT'], '\\/') . '/' . $uploadPath . '/' . $entity . '/';
		
		// array for filenames that should be deleted
		$files = array();
		// all copies  with specified name are to be deleted too
		$files = glob($absolute . '*_*');
		$files = array_merge(glob($absolute . '*,*'), $files);
		$files = array_unique(array_merge(glob($absolute . '*.*'), $files));

		for ($j = 0; $j < count($files); $j++) {
			try {
				unlink($files[$j]);
			} catch (Exception $e) {
			}
		}
		if (is_dir($absolute)) {
			rmdir($absolute);
		}
	}

	public function deleteClasses(){
		// get folder name where files of entity are stored
		$entity = Indi_Registry::get('entityToDelete');

		
		$modelsDir = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/') . '/application/models/';

		$modelFile = $modelsDir . ucfirst($entity) . '.php';
		if (file_exists($modelFile)) {
			unlink($modelFile);
		}
		$files = glob($modelsDir . ucfirst($entity) . '/*.*');
		for ($j = 0; $j < count($files); $j++) {
			try {
				unlink($files[$j]);
			} catch (Exception $e) {
			}
		}
		if (is_dir($modelsDir . ucfirst($entity) . '/')) {
			rmdir($modelsDir . ucfirst($entity) . '/');
		}
	}
}
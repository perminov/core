<?php
class Entity_Row extends Indi_Db_Table_Row
{
	public function delete(){
		// standart Db_Table_Row deletion
		parent::delete();

		// delete all uploaded files of entity rows and folder they were stored in
		$this->deleteAllUploadedFilesAndUploadFolder();

		// delete db table
		Indi::db()->query('DROP TABLE `' . $this->table . '`');

		// delete model file and row, rowset classes
		//$this->deleteClasses();
	}

	public function deleteAllUploadedFilesAndUploadFolder(){
		// get upload path from config
		$uploadPath = Indi_Image::getUploadPath();
		
		// absolute upload path  in filesystem
		$absolute = trim($_SERVER['DOCUMENT_ROOT'], '\\/') . '/' . $uploadPath . '/' . $this->table . '/';
		
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
		$modelsDir = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '\\/') . '/application/models/';

		$modelFile = $modelsDir . ucfirst($this->table) . '.php';
		if (file_exists($modelFile)) {
			unlink($modelFile);
		}
		$files = glob($modelsDir . ucfirst($this->table) . '/*.*');
		for ($j = 0; $j < count($files); $j++) {
			try {
				unlink($files[$j]);
			} catch (Exception $e) {
			}
		}
		if (is_dir($modelsDir . ucfirst($this->table) . '/')) {
			rmdir($modelsDir . ucfirst($this->table) . '/');
		}
	}

    public function save() {
        if (!$this->id) {
            $query = 'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        } else if ($this->_original['table'] && $this->_modified['table'] && $this->_original['table'] != $this->_modified['table']) {
            $query = 'RENAME TABLE  `' . $this->_original['table'] . '` TO  `' . $this->_modified['table'] . '` ;';
        }
        if ($query) Indi::db()->query($query);
        return parent::save();
    }
}
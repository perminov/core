<?php
class Field_Row extends Indi_Db_Table_Row
{
	public function delete($branchId = null){
		// delete uploaded images or files as they were uploaded as values
		// of this field if they were uploaded
		$this->deleteUploadedFilesIfTheyWere();

		// standart Db_Table_Row deletion
		parent::delete($branchId);

        // delete db table assotiated column
        $this->deleteDbTableColumnIfFieldIsAssotiatedWithOne();
    }

	public function deleteDbTableColumnIfFieldIsAssotiatedWithOne(){
		if ($this->columnTypeId) {
			$tableName = Entity::getInstance()->fetchRow('`id` = "' . $this->entityId . '"')->table;
			$query = 'ALTER TABLE `' . $tableName . '` DROP `' . $this->alias . '`';
			$this->getTable()->getAdapter()->query($query);
		}
	}

	public function deleteUploadedFilesIfTheyWere(){
		if (!$this->columnTypeId) {
			// get folder name where files of entity are stored
			$entity = Entity::getInstance()->fetchRow('`id` = "' . $this->entityId . '"')->table;
			$image = $this->alias;

			// get upload path from config
			$uploadPath = Indi_Image::getUploadPath();
			
			// absolute upload path  in filesystem
			$absolute = trim($_SERVER['DOCUMENT_ROOT'], '\\/') . $_SERVER['STD'] . '/' . $uploadPath . '/' . $entity . '/';
			
			// array for filenames that should be deleted
			$files = array();

			// all copies  with specified name are to be deleted too
			$files = glob($absolute . '*'.($image ? '_' . $image . '*' : '') .'*');
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
	public function isSatellite(){
		if ($satelliteForField = $this->getTable()->fetchRow('`satellite` = "' . $this->id . '"')){
			return $satelliteForField;
		} else {
			return false;
		}
	}
	
	public function getParams(){
		$possibleParams = Misc::loadModel('PossibleElementParam')->fetchAll('`elementId` = "' . $this->elementId . '"')->toArray();
		$redefinedParams = Misc::loadModel('Param')->fetchAll('`fieldId` = "' . $this->id . '"')->toArray();
		$redefine = array();
		for ($i = 0; $i < count ($redefinedParams); $i++) {
			$redefine[$redefinedParams[$i]['possibleParamId']] = $redefinedParams[$i]['value'];
		}
		$params = array();
		for ($i = 0; $i < count($possibleParams); $i++) {
			$params[$possibleParams[$i]['alias']] = in_array($possibleParams[$i]['id'], array_keys($redefine)) ? $redefine[$possibleParams[$i]['id']] : $possibleParams[$i]['defaultValue'];
		}
		return $params;
//		d($possibleParams->toArray());
//		die('asd');
	}
}
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

    public function save() {
        if ($this->id) {
            $was = $this->_original;
            $became = $this->toArray();
            if ($was != $became) {
                // Get files of copies to be resized
                $field = $this->getForeignRowByForeignKey('fieldId');
                $entity = Entity::getInstance()->getModelById($field->entityId)->info('name');
                $uploadPath = Indi_Image::getUploadPath();
                $relative = '/' . trim($uploadPath, '\\/') . '/' . $entity . '/';
                $absolute = $_SERVER['DOCUMENT_ROOT'] . STD . $relative;
                $key = $field->alias;
                $copy = $was['alias'];

                if ($was['alias'] == $became['alias']) {

                    $name = ($key !== null && !empty($key) ? '_' . $key : '') . '.';
                    $pat = $absolute . '*' . $name . '*';

                    foreach (glob($pat) as $existing) {
                        // Get resize expression
                        switch($became['proportions']){
                            case 'o': // original
                                $size = getimagesize($existing);
                                $size = $size[0] . 'x' . $size[1];
                                break;
                            case 'c':
                                $size = $became['masterDimensionValue'] . 'x' . $became['slaveDimensionValue'];
                                break;
                            case 'p':
                                $size = array($became['masterDimensionValue'], $became['slaveDimensionValue']);
                                if ($became['masterDimensionAlias'] == 'height'){
                                    $size = array_reverse($size);
                                }
                                if($became['slaveDimensionLimitation']) $size[1] .= 'M';
                                $size = implode('x', $size);
                                break;
                            default:
                                $size = '';
                                break;
                        }

                        Indi_Image::resize($existing, $copy, $size);
                    }

                } else {
                    $key = ($key !== null && !empty($key) ? '_' . $key : '');
                    $copy = ($copy != null ? ',' . $copy : '');
                    $name = $key . $copy;
                    $pat = $absolute . '*' . $name . '*';
                    if ($became['alias']) $became['alias'] = ',' . $became['alias'];
                    foreach (glob($pat) as $existing) {
                        $renameTo = preg_replace('~(' . $absolute . '[0-9]+' . $key . ')' . $copy . '(\.[a-z]{2,4})~', '$1' . $became['alias'] . '$2', $existing);
                        rename($existing, $renameTo);
                    }
                }
            }
        }
        return parent::save();
    }
}
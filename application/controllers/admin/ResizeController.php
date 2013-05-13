<?php
class Admin_ResizeController extends Indi_Controller_Admin{
	public function preSave(){
		if ($this->row) {
			$this->was  = $this->row->toArray();
		} else {
			$this->was = false;
		}
	}
	public function postSave(){
		if ($this->was == false) return;
		$this->became = $this->trail->getItem()->model->fetchRow('`id` = "' . $this->identifier . '"')->toArray();
		if ($this->was != $this->became) {

			// Get files of copies to be resized
			$entity = Entity::getInstance()->getModelById($this->trail->getItem(1)->row->entityId)->info('name');
			$uploadPath = Indi_Image::getUploadPath();
			$relative = '/' . trim($uploadPath, '\\/') . '/' . $entity . '/';
			$absolute = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] . $relative;
			$key = $this->trail->getItem(1)->row->alias;
			$copy = $this->was['alias'];

			if ($this->was['alias'] == $this->became['alias']) {

				$name = ($key !== null && !empty($key) ? '_' . $key : '') . '.';
				$pat = $absolute . '*' . $name . '*';

				foreach (glob($pat) as $existing) {
					// Get resize expression
					switch($this->became['proportions']){
						case 'o': // original
							$size = getimagesize($existing);
							$size = $size[0] . 'x' . $size[1];
							break;
						case 'c':
							$size = $this->became['masterDimensionValue'] . 'x' . $this->became['slaveDimensionValue'];
							break;
						case 'p':
							$size = array($this->became['masterDimensionValue'], $this->became['slaveDimensionValue']);
							if ($this->became['masterDimensionAlias'] == 'height'){
								$size = array_reverse($size);
							}
							if($this->became['slaveDimensionLimitation']) $size[1] .= 'M';
							$size = implode('x', $size);
							break;
						default:
							$size = '';
							break;
					}

					Indi_Image::resize($existing, $copy, $size);
				}

			}
			if ($this->was['alias'] != $this->became['alias']) {
				$key = ($key !== null && !empty($key) ? '_' . $key : '');
				$copy = ($copy != null ? ',' . $copy : '');
				$name = $key . $copy;
				$pat = $absolute . '*' . $name . '*';
				if ($this->became['alias']) $this->became['alias'] = ',' . $this->became['alias'];
				foreach (glob($pat) as $existing) {
					$renameTo = preg_replace('~(' . $absolute . '[0-9]+' . $key . ')' . $copy . '(\.[a-z]{2,4})~', '$1' . $this->became['alias'] . '$2', $existing);
					rename($existing, $renameTo);
				}
			}
		}
	}
}
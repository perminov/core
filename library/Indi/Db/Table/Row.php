<?php
class Indi_Db_Table_Row extends Indi_Db_Table_Row_Beautiful
{
    /**
     * Default method for row classes
     *
     * @return string
     */
    public function getTitle() {
        if ( !$this->title ) {
            return  'No title';
        } else {
            return $this->title;
        }
    }
    
    public function getImageSrc($imageName, $copyName = null) {
        $entity = $this->_table;
        $web =  STD . '/' . Indi_Image::getUploadPath(). '/' . $entity . '/';
        $abs = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

        $pat = $abs . $web . $this->id . ($imageName ? '_' . $imageName : '') . ($copyName ? ',' . $copyName : '') . '.' ;

        $files = glob($pat . '*');
        if(count($files) == 0) {
            return false;    
        }
        
        $src = str_replace($abs, '', $files[0]);
        return $src;
    }

    public function getImageAbs($imageName, $copyName = '') {
        $entity = $this->_table;
        $web = Indi_Image::getUploadPath(). '/' . $entity . '/';
        $abs = rtrim($_SERVER['DOCUMENT_ROOT'] . STD, '/');
        $pat = $abs . '/' .$web . $this->id . ($imageName ? '_' . $imageName : '') . ($copyName ? ',' . $copyName : '') . '.' ;
        $files = glob($pat . '*');
        if(count($files) == 0) {
            return false;
        }
        return $files[0];
    }

    public function image($imageName = null, $copyName = null, $attrib = null, $noCache = false, $sizeinfo = false) {
        if ($src = $this->getImageSrc($imageName, $copyName)) {
            if ($sizeinfo) {
                $info = getimagesize($_SERVER['DOCUMENT_ROOT'] . $src);
                $info = $info[3];
                $info = ' ' . preg_replace('/(width|height)/', 'real-$1', $info);
            }
            return '<img src="' . $src .($noCache?'?'.rand():'') . '" ' . $attrib .$info. (preg_match('/alt="/', $attrib) ? '' : ' alt=""') . '>';
        } else {
            return false;
        }
    }

    public function flash($imageName = null, $attrib = null) {
        if ($src = $this->getImageSrc($imageName)) {        
            return '<embed src="' . $src .'" border="0" ' . $attrib .'>';
        } else {
            return false;
        }
    }

	public function setForeignRowsByForeignKeys($info){
		if (is_object($info)) {
			foreach ($info as $rowToGet) {
				$fields[] = $rowToGet->fieldId;
				$returnAs[] = $rowToGet->returnAs;
			}
		} else {
			$fields = explode(',', $info);
			$entityId = Indi::model('Entity')->fetchRow('`table` = "' . $this->_table . '"')->id;
			$fieldsRs = Indi::model('Field')->fetchAll('`entityId` = "' . $entityId . '" AND `alias` IN ("' . implode('","', $fields) . '")');
			$fields = array();
			foreach ($fieldsRs as $fieldR) $fields[] = $fieldR->id;
		}
		for ($i = 0; $i < count($fields); $i++) {
			$field = Indi::model('Field')->fetchRow('`id` = "' . $fields[$i] . '"');
			$field = $field->toArray();
			if ($field['relation'] && $model = Indi::model($field['relation'])) {
				if ($field['storeRelationAbility'] == 'one') {
					if ($field['relation'] == 6) {
						$foreignR = $model->fetchRow('`alias` = "' . $this->{$field['alias']} . '" AND `fieldId` = "' . $field['id'] . '"');
					} else {
						$foreignR = $model->fetchRow('`id` = "' . $this->{$field['alias']} . '"');
					}
					if (!$foreignR) {
						$foreignR = $model->createRow();
					}
					$this->_original['foreign'][$field['alias']] = $returnAs[$i] == 'a'? $foreignR->toArray(): $foreignR;
				} else if ($field['storeRelationAbility'] == 'many') {
					if ($field['relation'] == 6) {
						$foreignR = $model->fetchAll('FIND_IN_SET(`alias`,"' . $this->{$field['alias']} . '") AND `fieldId` = "' . $field['id'] . '"');
					} else {
						$foreignR = $model->fetchAll('FIND_IN_SET(`id`,"' . $this->{$field['alias']} . '")');
					}
					if ($foreignR) $this->_original['foreign'][$field['alias']] = $returnAs[$i] == 'a'? $foreignR->toArray(): $foreignR;
				}
			}
		}
	}
	public function setDependentRowsets($info) {
		$name = $this->_table;
		$selfEntityId = Indi::model('Entity')->fetchRow('`table` = "' . $name . '"')->id;
		foreach ($info as $entity) {
			$where = null;
			if ($related = Indi::model('Field')->fetchRow('`entityId` = "' . $entity->entityId . '" AND `relation` = "' . $selfEntityId . '"')){
				if ($related->storeRelationAbility == 'many') {
					$where = 'FIND_IN_SET("' . $this->id. '", `' . $related->alias .'`)';
				} else {
					$where = '`' . $name . 'Id` = "' . $this->id . '"';
				}
			} else if($self = Indi::model('Field')->fetchRow('`entityId` = "' . $selfEntityId . '" AND `relation` = "' . $entity->entityId . '"')){
				if ($self->storeRelationAbility == 'many') {
					$where = 'FIND_IN_SET(`id`, "' . $this->{$self->alias} . '")';
				}
			}
			if ($where) {
				if ($entity->where) {
					if (preg_match('/\$/', $entity->where)) {
						eval('$entity->where = \'' . $entity->where . '\';');
					}
					$where .= ' AND ' . $entity->where;
				}
				$order = $entity->orderBy == 'c' && $entity->foreign('orderColumn')->alias ? $entity->foreign('orderColumn')->alias  . ' ' . $entity->orderDirection : $entity->orderExpression;
				$limit = $entity->limit ? $entity->limit : null;
				$rowset = Indi::model($entity->entityId)->fetchAll($where, $order ? $order : null, $limit);

				$info = Indi::model('JoinFkForDependentRowset')->fetchAll('`dependentRowsetId` = "' . $entity->id . '"');
				if ($info->count()) $rowset->setForeignRowsByForeignKeys($info);

				$info = Indi::model('DependentCountForDependentRowset')->fetchAll('`dependentRowsetId` = "' . $entity->id . '"');
				if ($info->count()) $rowset->setDependentCounts($info);

				$this->_original['dependent'][$entity->alias] = $entity->returnAs == 'a' ? $rowset->toArray() : $rowset;
			}
		}
	}
}
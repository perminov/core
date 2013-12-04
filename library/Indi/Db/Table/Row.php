<?php
class Indi_Db_Table_Row extends Indi_Db_Table_Row_Beautiful
{
    /**
     * Searches all foreign keys in table structure
     * and set up corresponding row objects within
     * $this->foreignRows variable
     * 
     * @uses Indi_Db_Table::getForeignKeys(), self::getForeignRowByForeignKey()
     * @return void
     */
    public function setForeignRowsByForeignKeysOld() {
        $foreignKeys = $this->getTable()->getForeignKeys();
        if ($foreignKeys) {
            $this->foreignRows = new stdClass();
            foreach ($foreignKeys as $foreignKey) {
                $this->foreignRows->$foreignKey = $this->getForeignRowByForeignKey($foreignKey);
            }
        }
        return $this;
    }

    /**
     * Get Indi_Db_Table_Row assotiated to foreign key
     *
     * @param string $foreignKey
     * @uses Entity::getModelByTable(), Indi_Db_Table::getTableByKeyName()
     * @return Indi_Db_Table_Row
     */
    public function getForeignRowByForeignKey($foreignKey) {
		$ownerEntityId = Entity::getInstance()->fetchRow('`table` = "' . $this->getTable()->info('name') . '"')->id;
		$field = Misc::loadModel('Field')->fetchRow('`alias` = "' . $foreignKey . '" AND `entityId` = "' . $ownerEntityId . '"');
		$searchingEntityId = $field->relation;
		if ($field->relation) {
            if ($field->storeRelationAbility == 'one') {
                return Entity::getInstance()->getModelById($field->relation)->fetchRow('`id` = "' . $this->$foreignKey . '"');
            } else if ($field->storeRelationAbility == 'many') {
                return Entity::getInstance()->getModelById($field->relation)->fetchAll('FIND_IN_SET(`id`, "' . $this->$foreignKey . '")');
            }
		}

		if (!$entityId = $this->getEntityIdForVariableForeignKey($foreignKey)){
	        // if $search param is not set, then it's default value is the table name of $foreignKey
	        if (!$search) {
	            $search = $this->getTable()->getTableNameByKeyName($foreignKey);
			}
	        $current = $this->getTable()->getTableNameByKeyName($foreignKey);
	        if ($current == $search) {
	            if (is_array($ids = $this->isListOfIds($this->$foreignKey))) {
	                for($i = 0; $i < count($ids); $i++) {
	                    $return[] = Misc::loadModel('Entity')->getModelByTable($current)->fetchRow('`id` = "' . $ids[$i] . '"');
	                }
	                return $return;
	            } else  {
					// get entity, corresponding to current row ($this)
					$tableName = $this->getTable()->info('name');
					$entityModel = Misc::loadModel('Entity');
					$entityRow = $entityModel->fetchRow('`table` = "' . $tableName . '"');
					
					// get field in found enitity structure, corresponding to $foreignKey
					$fieldModel = Misc::loadModel('Field');
					$fieldRow = $fieldModel->fetchRow('`alias`="' . $foreignKey . '" AND `entityId` = "' . $entityRow->id . '"');
					if ($entityModel && $foreignModel = $entityModel->getModelById($fieldRow->relation)){
						return $foreignModel->fetchRow('`id` = "' . $this->$foreignKey . '"');
					}
	            }
	        }
		} else {
			$entityId = $this->getEntityIdForVariableForeignKey($foreignKey);
			return Misc::loadModel('Entity')->getModelById($entityId)->fetchRow('`id` = "' . $this->$foreignKey .'"');
		}
    }
	public function getEntityIdForVariableForeignKey($foreignKey, $satellite = null){

		// get entity, corresponding to current row ($this)
		$tableName = $this->getTable()->info('name');
		$entityModel = Misc::loadModel('Entity');
		$entityRow = $entityModel->fetchRow('`table` = "' . $tableName . '"');
		
		// get field in found enitity structure, corresponding to $foreignKey
		$fieldModel = Misc::loadModel('Field');
		$fieldRow = $fieldModel->fetchRow('`alias` = "' . $foreignKey . '" AND `entityId` = "' . $entityRow->id . '"');
		if ($satelliteFieldId = $fieldRow->satellite) {
			// find column row, that is corresponding to satellite column
			$satelliteFieldRow = $fieldModel->fetchRow('`id` = "' . $satelliteFieldId . '"');
			
			// if we got satellite values as a passed params we should use this value instead
			if ($satellite) $this->{$satelliteFieldRow->alias} = $satellite;
			
			$linkedEntity = $entityModel->getModelById($satelliteFieldRow->relation);
//			$linkedEntityRow = $entityModel->fetchRow('`id` = "' . $satelliteFieldRow->relation . '"');
			// if satellite column store ids of rows in 'entity' table
			if ($linkedEntity->info('name') == 'entity') {
				return $this->{$satelliteFieldRow->alias};
			
			// if satellite column store ids of rows in some another table
			} else {
				// we try to find an 'entityId' column in structure of that another table, so we think that it's the link to searched entity
				if ($linkedEntity->fieldExists('entityId')) {
					return $this->getForeignRowByForeignKey($satelliteFieldRow->alias)->entityId;
				// we use anouther custom logic to get entity id
				} else {
					return $this->getEntityIdForVariableForeignKeyByCustomLogic($foreignKey);
				}
			}
		} else {
			return 0;
		}
	}
	
	public function getEntityIdForVariableForeignKeyByCustomLogic(){
		return 0;
	}

	public function getDropdownData($fieldAlias, $trail, $config = array()){
	
		$fields = $trail->getItem()->fields;
		for($i = 0; $i < count($fields); $i++) {
			if ($fields[$i]->alias == $fieldAlias){
				$entityId = $fields[$i]->relation;
				$fieldId = $fields[$i]['id'];
				$staticFilter = $fields[$i]['filter'];
				$satellite = $fields[$i]['satellite'];
				$alternative = $fields[$i]['alternative'];
				$dependency = $fields[$i]['dependency'];
				break;
			}
		}
		$f=false;
		if ($satellite) {
			$satelliteField = Misc::loadModel('Field')->fetchRow('`id` = "' . $satellite . '"');
			if (!strpos($trail->getItem()->dropdownWhere[$fieldAlias], $satelliteField->alias)) {
				if ($satelliteField->satellitealias) {
					if (!strpos($trail->getItem()->dropdownWhere[$fieldAlias], $satelliteField->satellitealias)) $f = true;
				} else {
					$f=true;
				}
				//if ($f) $filterBySatellite = '`' . ($satelliteField->satellitealias ? $satelliteField->satellitealias : $satelliteField->alias) . '` = "' . $this->{$satelliteField->alias} . '"';
				if ($f && $dependency != 'e') $filterBySatellite = 'FIND_IN_SET("' . $this->{$satelliteField->alias} . '", `' . ($satelliteField->satellitealias ? $satelliteField->satellitealias : $satelliteField->alias) . '`)';
				
			}
			if ($alternative && $dependency != 'e') {
				$row = $this->getForeignRowByForeignKey($satelliteField->alias);
				//$filterBySatellite = '`' . $alternative . '` = "' . $row->$alternative . '"';
				$filterBySatellite = 'FIND_IN_SET("' . $row->$alternative . '", `' . $alternative . '`)';
			}
		}
		// set up dropdown filters
		if (!$trail->getItem()->dropdownWhere[$fieldAlias]){
			$trail->getItem()->dropdownWhere[$fieldAlias] = array();
		} else if (!is_array($trail->getItem()->dropdownWhere[$fieldAlias])){
			$trail->getItem()->dropdownWhere[$fieldAlias] = array($trail->getItem()->dropdownWhere[$fieldAlias]);
		}
		if ($staticFilter) {
            if (preg_match('/(\$|::)/', $staticFilter)) {
                eval('$staticFilter = \'' . $staticFilter . '\';');
            }
            $trail->getItem()->dropdownWhere[$fieldAlias][] = $staticFilter;
        }
		if ($filterBySatellite) $trail->getItem()->dropdownWhere[$fieldAlias][] = $filterBySatellite;
		if ($config['find']) $trail->getItem()->dropdownWhere[$fieldAlias][] = '`title` LIKE "%' . $config['find'] . '%"';
		
		$trail->getItem()->dropdownWhere[$fieldAlias] = implode(' AND ', $trail->getItem()->dropdownWhere[$fieldAlias]);
		if (!$trail->getItem()->dropdownWhere[$fieldAlias]) $trail->getItem()->dropdownWhere[$fieldAlias] = null;
		if ($entityId == 6) {
			$array = Entity::getInstance()->getModelById($entityId)->fetchAll('`fieldId` = "' . $fieldId . '"','move')->toArray();
			foreach ($array as $item) $options[$item['alias']] = Misc::usubstr($item['title'], 90);
		} else if ($entityId != 0){
			$fieldModel = new Field();
			$entityFields = $fieldModel->getFieldsByEntityId($entityId);
			foreach ($entityFields as $entityField) {
				if (!$entityRow) {
					$entityRow = Entity::getInstance()->fetchRow('`id`="' . $entityId . '"');
				}
				if ($entityField['alias'] == $entityRow->table . 'Id' && $entityField['relation'] == $entityRow->id) {
					$treeColumnInEntityStructure = $entityField['alias'];
					break;
				}
			}

			if($treeColumnInEntityStructure){
				$rowset = Entity::getInstance()->getModelById($entityId)->fetchTree($trail->getItem()->dropdownWhere[$fieldAlias]);
				foreach ($rowset as $row) $options[$row->id] = $row->indent . Misc::usubstr($row->getTitle(), 90);
			} else {
				$params = Entity::getInstance()->getModelByTable('param')->fetchAll('`fieldId` = "' . $fieldId . '"');
				foreach ($params as $param) $pairs[$param->getForeignRowByForeignKey('possibleParamId')->alias] = $param->value;
				if (is_array($pairs) && in_array('groupBy', array_keys($pairs))) {
					$groupByColumnAlias = $pairs['groupBy'];
					$entityFields = Misc::loadModel('Field')->getFieldsByEntityId($entityId)->toArray();
					for($i = 0; $i < count($entityFields); $i++) {
						if ($entityFields[$i]['alias'] == $groupByColumnAlias){
							$groupEntityId = $entityFields[$i]['relation'];
							$entityFieldId = $entityFields[$i]['id'];
							break;
						}
					}					
					$rowset = Entity::getInstance()->getModelById($entityId)->fetchAll($trail->getItem()->dropdownWhere[$fieldAlias], '`' . $groupByColumnAlias . '`');
					foreach ($rowset as $row) {
						$currentGroupByColumnValue = $row->$groupByColumnAlias;
						if ($currentGroupByColumnValue != $prevValue) {
							if ($groupEntityId != 6) {
								$groupRow = Entity::getModelById($groupEntityId)->fetchRow('`id` = "' . $currentGroupByColumnValue . '"');
								$title = $groupRow ? $groupRow->getTitle() : 'No title';
							} else if ($groupEntityId == 6){
								$title = strip_tags(Entity::getModelById($groupEntityId)->fetchRow('`alias` = "' . $currentGroupByColumnValue . '" AND `fieldId` = "' . $entityFieldId . '"')->getTitle());
							} else {
								$title = $currentGroupByColumnValue;
							}
						}
						if ($pairs['groupByRequirement']) eval($pairs['groupByRequirement']);
						if ($title && ($pairs['groupByRequirement'] ? $groupByCondition : true)) $options[$title][$row->id] = Misc::usubstr($row->getTitle(), 90);
						$prevValue = $currentGroupByColumnValue;
					}
					
				} else {
					$entity = Entity::getInstance()->getModelById($entityId);
					$order = null;
					$limit = null;
					$page = null;
					if ($config['element'] == 'dselect') {
						$count = current($this->getTable()->getAdapter()->query('SELECT COUNT(`id`) FROM `' . $entity->info('name') . '`' . ($trail->getItem()->dropdownWhere[$fieldAlias] ? ' WHERE ' . $trail->getItem()->dropdownWhere[$fieldAlias] : ''))->fetch());
						$limit = 50;
						if ($config['find']) {
							$order = 'POSITION("' . $config['find']. '" IN `title`) = 1 DESC, TRIM(SUBSTR(`title`, 1)) ASC';
							if ($config['page']) $page = $config['page'];
						} else if ($config['value'] && $count > $limit) {
							$trail->getItem()->dropdownWhere[$fieldAlias] = explode(' AND ', $trail->getItem()->dropdownWhere[$fieldAlias]);
							$trail->getItem()->dropdownWhere[$fieldAlias][] = '`title` '.($config['up'] ? '<' : '>=').' "' . str_replace('"','\"', $config['value']) . '"';
							$trail->getItem()->dropdownWhere[$fieldAlias] = implode(' AND ', $trail->getItem()->dropdownWhere[$fieldAlias]);
							if ($config['more'] && !$config['up']) $limit++;
							if ($config['up']) $order = '(`title`) DESC';
						}
					} else {
                        $fields = $entity->getFields();
                        if (in_array('move', $fields)) $order = 'move';
                        else if (in_array('title', $fields)) $order = 'title';
                    }
					$rowset = $entity->fetchAll($trail->getItem()->dropdownWhere[$fieldAlias], $order, $limit, $page);
					foreach ($rowset as $row) {
						$options[$row->id] = Misc::usubstr($row->getTitle(), 90);
					}
					if ($config['element'] == 'dselect') $options['data'] = array('count' => $count);
				}
			}
		} else $options = array();
		
		return $options; 
	}

    /**
     * Provide Toggle On/Off action
     *
     */
    public function toggle() {
        if ($this->getTable()->fieldExists('toggle')) {
            $this->toggle = $this->toggle == 'y' ? 'n' : 'y';
            $this->save();
        } else {
			die('Вам необходимо добавить поле "toggle" в структру сущности, экземпляр которой Вы хотите ' . ($this->toggle == 'y' ? 'включить' : 'выключить'));
		}
    }

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
        $entity = $this->getTable()->info('name');
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
        $entity = $this->getTable()->info('name');
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
    
    public function isListOfIds($value) {
        $value = explode(',', $value);
        for ($i = 0; $i < count($value); $i++) {
            if (!preg_match('/^\'(\d+)\'$/', $value[$i], $id)) return false; else $ids[] = $id[1];
        }
        return $ids;
    }

	public function deleteUploadedFiles($name = '', $entity = ''){
        if (!$entity) $entity = strtolower($this->getTable()->info('name'));

		// get upload path from config
        $uploadPath = Indi_Image::getUploadPath();
        
        // absolute upload path  in filesystem
        $absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/') . STD . '/' . $uploadPath . '/' . $entity . '/';
		// array for filenames that should be deleted
		$files = array();

		// we delete all files in case if there is no aim to delete only specified image copies
		if (!$name){
			$nonamed = glob($absolute . $this->id . '.*');
			$named = glob($absolute . $this->id . ',*');
			if (is_array($nonamed)) $files = array_merge($nonamed, $files);
			if (is_array($named)) $files = array_merge($named, $files);
		}

		// all resized copies are to be deleted too
		$resized = glob($absolute . $this->id . '_' . $name . '*.*');
		if (is_array($resized)) $files = array_merge($resized, $files);

		for ($j = 0; $j < count($files); $j++) {
            try {
                unlink($files[$j]);
            } catch (Exception $e) {
                throw new Exception($e->__toString());
            }
        }
	}
	public function deleteRowChildrenIfEntityHasATreeStructure(){
		// at first, we should detect, has the current entity a tree structure or not
		$cols = $this->getTable()->info('cols');
		$treeKeyName = strtolower($this->getTable()->info('name')) . 'Id';

		if (in_array($treeKeyName, $cols)) {
			// delete children
			$children = $this->getTable()->fetchTree(null, null, null, null, $this->id);
			foreach ($children as $child) $child->delete();
		}
	}

	public function deleteDependentRowsets(){
        $entityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $this->_table->_name . '"')->id;
        $sectionRs = Misc::loadModel('Section')->fetchAll('`entityId` = "' . $entityId . '"');
        foreach ($sectionRs as $sectionR) {
            $branchId = $sectionR->id;
			$dependentSections = Misc::loadModel('Section')->fetchAll('`sectionId` = "' . $branchId . '"');
			foreach ($dependentSections as $dependentSection) {
				if ($dependentSection->entityId && $entity = Misc::loadModel(ucfirst(Entity::getInstance()->fetchRow('`id` = "' . $dependentSection->entityId . '"')->table))) {
                    if ($dependentSection->parentSectionConnector) {
                        $keyName = $dependentSection->getForeignRowByForeignKey('parentSectionConnector')->alias;
                    } else {
                        $keyName = strtolower($this->getTable()->info('name')) . 'Id';
                    }
					$entity->fetchAll($entity->fieldExists($keyName) ? '`' . $keyName . '` = "' . $this->id . '"' : null)->delete();
				}
			}
		}
	}

    public function deleteForeignKeysUsages(){
        // Declare entities array
        $entities = array();

        // Determine entity, this row is owned by
        $entityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $this->_table->_name . '"')->id;

        // Get all fields in whole database, which are containing keys related to this entity
        $fieldRs = Misc::loadModel('Field')->fetchAll('`relation` = "' . $entityId . '"');
        foreach ($fieldRs as $fieldR) $entities[$fieldR->entityId]['fields'][] = $fieldR;

        // Get auxillary deletion info within each entity
        foreach ($entities as $eid => $data) {
            $model = Entity::getModelById($eid);

            foreach ($data['fields'] as $field) {
                // We should check that column - which will be used in WHERE clause for retrieving a dependent rowset -
                // still exists. We need to perform this check because this column may have already been deleted, if
                // it was dependent of other column that was deleted.
                if ($model->fieldExists($field->alias)) {

                    // We delete rows there $this->id in at least one field, which ->storeRelationAbility = 'one'
                    if ($field->storeRelationAbility == 'one') {
                        $model->fetchAll('`' . $field->alias . '` = "' . $this->id . '"')->delete();

                        // If storeRelationAbility = 'many', we do not delete rows, but we delete
                        // mentions of $this->id from comma-separated sets of keys
                    } else if ($field->storeRelationAbility == 'many') {
                        $rs = $model->fetchAll('FIND_IN_SET(' . $this->id . ', `' . $field->alias . '`)');
                        foreach ($rs as $r) {
                            $set = explode(',', $r->{$field->alias});
                            $found = array_search($this->id, $set);
                            if ($found !== false) unset($set[$found]);
                            $r->{$field->alias} = implode(',', $set);
                            $r->save(true);
                        }
                    }
                }
            }
        }
    }

	public function setDependentCounts($info){
		foreach ($info as $countToGet){
			$subsectionIds[] = $countToGet->sectionId;
			$where[] = $countToGet->where;
		}
		$subsections = Misc::loadModel('Section')->fetchAll('`id` IN (' . implode(',', $subsectionIds) . ')');
		foreach ($subsections as $subsection) $entityIds[] = $subsection->entityId;
		$entities = Misc::loadModel('Entity')->fetchAll('`id` IN (' . implode(',', $entityIds) . ')');
		foreach ($entities as $entity) $tables[] = $entity->table;
		if (count($tables)) {
			$counts = array();
			for ($j = 0; $j < count($tables); $j++) {
				$dependentTable = $tables[$j];
				$sql = '
					SELECT 
						COUNT(`s`.`id`) AS `count`
					FROM 
						`' . $this->getTable()->info('name') . '` `m` 
						LEFT JOIN `' . $dependentTable . '` `s` ON (`m`.`id`=`s`.`' . $this->getTable()->info('name') . 'Id`)
					WHERE 1
						AND `m`.`id`  = "' . $this->id . '"
						' . ($where[$j] != '' ? 'AND `s`.' . $where[$j] .'' : '') . '
					GROUP BY `m`.`id`
				';
				$result = $this->getTable()->getAdapter()->query($sql)->fetch();
				$this->_original['counts'][$info[$j]['alias']]['count'] = $result['count'];
				$this->_original['counts'][$info[$j]['alias']]['title'] = $info[$j]['title'];
			}
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
			$entityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $this->getTable()->info('name') . '"')->id;
			$fieldsRs = Misc::loadModel('Field')->fetchAll('`entityId` = "' . $entityId . '" AND `alias` IN ("' . implode('","', $fields) . '")');
			$fields = array();
			foreach ($fieldsRs as $fieldR) $fields[] = $fieldR->id;
		}
		for ($i = 0; $i < count($fields); $i++) {
			$field = Misc::loadModel('Field')->fetchRow('`id` = "' . $fields[$i] . '"');
			$field = $field->toArray();
			if ($field['relation'] && $model = Misc::loadModel('Entity')->getModelById($field['relation'])) {
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
		$name = $this->getTable()->info('name');
		$selfEntityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $name . '"')->id;
		foreach ($info as $entity) {
			$where = null;
			if ($related = Misc::loadModel('Field')->fetchRow('`entityId` = "' . $entity->entityId . '" AND `relation` = "' . $selfEntityId . '"')){
				if ($related->storeRelationAbility == 'many') {
					$where = 'FIND_IN_SET("' . $this->id. '", `' . $related->alias .'`)';
				} else {
					$where = '`' . $name . 'Id` = "' . $this->id . '"';
				}
			} else if($self = Misc::loadModel('Field')->fetchRow('`entityId` = "' . $selfEntityId . '" AND `relation` = "' . $entity->entityId . '"')){
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
				$order = $entity->orderBy == 'c' && $entity->getForeignRowByForeignKey('orderColumn')->alias ? $entity->getForeignRowByForeignKey('orderColumn')->alias  . ' ' . $entity->orderDirection : $entity->orderExpression;
				$limit = $entity->limit ? $entity->limit : null;
				$rowset = Entity::getInstance()->getModelById($entity->entityId)->fetchAll($where, $order ? $order : null, $limit);

				$info = Misc::loadModel('JoinFkForDependentRowset')->fetchAll('`dependentRowsetId` = "' . $entity->id . '"');
				if ($info->count()) $rowset->setForeignRowsByForeignKeys($info);

				$info = Misc::loadModel('DependentCountForDependentRowset')->fetchAll('`dependentRowsetId` = "' . $entity->id . '"');
				if ($info->count()) $rowset->setDependentCounts($info);

				$this->_original['dependent'][$entity->alias] = $entity->returnAs == 'a' ? $rowset->toArray() : $rowset;
			}
		}
	}
	public function getRequestParam($name){
		$params = Indi::registry('request');
		return $params[$name];
	}
}
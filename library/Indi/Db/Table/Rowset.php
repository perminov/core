<?php
class Indi_Db_Table_Rowset extends Indi_Db_Table_Rowset_Abstract
{
    /**
     * Store number of rows, which would be found
     * if the LIMIT clause was disregarded
     *
     * @var mixed (null,int)
     */
    public $foundRows = null;
    
    /**
     * Constructor.
     * 
     * Found rows set up added
     */
    public function __construct(array $config)
    {
        if (isset($config['foundRows'])) {
            $this->foundRows     = $config['foundRows'];
        }
        parent::__construct($config);
    }

    /**
     * Sets foreign rows by foreign keys 
     * for each row in rowset
     *
     * @uses Indi_Db_Table_Row::setForeignRowsByForeignKeys()
     * @return Indi_Db_Table_Rowset object
     */
    public function last($stepsBack = 0){
        $savePointer = $this->_pointer;
        $this->_pointer = $this->_count - 1 - $stepsBack;
        $last = $this->current();
        $this->_pointer = $savePointer;
        return $last;
    }
    public function exclude($excludeIds = array()){
        if (!is_array($excludeIds)) $excludeIds = explode(',', $excludeIds);
        $filteredData = array();
        foreach ($this->_data as $item) {
            if (!in_array($item['id'], $excludeIds)) {
                $filteredData[] = $item;
            }
        }
        $this->_data = $filteredData;
    }
    public function setData($data){
        $this->_data = $data;
    }
	public function setDependentCounts($info){
		foreach ($info as $countToGet){
			$subsectionIds[] = $countToGet->sectionId;
			$where[] = $countToGet->where;
		}
		$subsections = Misc::loadModel('Section')->fetchAll('`id` IN (' . implode(',', $subsectionIds) . ')', 'FIND_IN_SET(`id`, "' . implode(',', $subsectionIds) . '")');
		foreach ($subsections as $subsection) $entityIds[] = $subsection->entityId;
		$entities = Misc::loadModel('Entity')->fetchAll('`id` IN (' . implode(',', $entityIds) . ')', 'FIND_IN_SET(`id`, "' . implode(',', $entityIds) . '")');
		foreach ($entities as $entity) $tables[] = $entity->table;
		$data = $this->toArray();
		$ids = array();
		for ($i = 0; $i < count($data); $i++) $ids[] = $data[$i]['id'];
		if (count($ids) && count($tables)) {
			$counts = array();
			for ($j = 0; $j < count($tables); $j++) {
				$dependentTable = $tables[$j];
				$sql = '
					SELECT 
						`m`.`id`, 
						COUNT(`s`.`id`) AS `count`
					FROM 
						`' . $this->getTable()->info('name') . '` `m` 
						LEFT JOIN `' . $dependentTable . '` `s` ON (`m`.`id`=`s`.`' . $this->getTable()->info('name') . 'Id`)
					WHERE 1
						AND `m`.`id` IN (' . implode(',', $ids) .')
						' . ($where[$j] ? ' AND `s`.' . $where[$j] : '') . '
					GROUP BY `m`.`id`
				';
//				echo $sql . '<br>';
				$result = $this->getTable()->getAdapter()->query($sql)->fetchAll();
				for ($i = 0; $i < count($result); $i++) {
					$counts[$result[$i]['id']][$info[$j]['alias']] = $result[$i]['count'];
				}
				for ($i = 0; $i < count($this->_data); $i++) {
					$this->_data[$i]['counts'][$info[$j]['alias']]['count'] = $counts[$this->_data[$i]['id']][$info[$j]['alias']];
					$this->_data[$i]['counts'][$info[$j]['alias']]['title'] = $info[$j]['title'];
				}
			}
//			d($this->_data);
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
			if (!$field) continue;
			if ($field['relation'] && $model = Misc::loadModel('Entity')->getModelById($field['relation'])) {
				if ($field['storeRelationAbility'] == 'one') {
					$keys = array();
					for ($j = 0; $j < count($this->_data); $j++) { 
						if (!in_array($this->_data[$j][$field['alias']], $keys)) {
							$keys[] = $this->_data[$j][$field['alias']];
						}
					}
					if (count($keys)) {
						if ($field['relation'] == 6) {
							$foreignRs = $model->fetchAll('`alias` IN ("' . implode('","', $keys) . '") AND `fieldId` = "' . $field['id'] . '"');
							$arrayOfFr = array();
							for($k = 0; $k < $foreignRs->count(); $k++) {
//								$arrayOfFr[$foreignRs[$k]->alias] = $foreignRs[$k];
								$arrayOfFr[$foreignRs[$k]->alias] = $returnAs[$i] == 'o' ? $foreignRs[$k] : $foreignRs[$k]->toArray();
							}
							for ($j = 0; $j < count($this->_data); $j++) { 
								$this->_data[$j]['foreign'][$field['alias']] = $arrayOfFr[$this->_data[$j][$field['alias']]];
							}
						} else {
							$foreignRs = $model->fetchAll('`id` IN ("' . implode('","', $keys) . '")');
							$arrayOfFr = array();
							for($k = 0; $k < $foreignRs->count(); $k++) {
								$arrayOfFr[$foreignRs[$k]->id] = $returnAs[$i] == 'o' ? $foreignRs[$k] : $foreignRs[$k]->toArray();
							}
							for ($j = 0; $j < count($this->_data); $j++) { 
								$this->_data[$j]['foreign'][$field['alias']] = $arrayOfFr[$this->_data[$j][$field['alias']]];
							}
						}
					}
				} else if ($field['storeRelationAbility'] == 'many') {
					$keys = array();
					for ($j = 0; $j < count($this->_data); $j++) { 
						$explodedKeys = explode(',', $this->_data[$j][$field['alias']]);
						for ($m = 0; $m < count($explodedKeys); $m++) {
							if (!in_array($explodedKeys[$m], $keys)) {
								$keys[] = $explodedKeys[$m];
							}
						}
					}
					if (count($keys)) {
						if ($field['relation'] == 6) {
							$foreignRs = $model->fetchAll('`alias` IN ("' . implode('","', $keys) . '") AND `fieldId` = "' . $field['id'] . '"');
							$arrayOfFr = array();
							for($k = 0; $k < $foreignRs->count(); $k++) {
								$arrayOfFr[$foreignRs[$k]->alias] = $returnAs[$i] == 'o' ? $foreignRs[$k] : $foreignRs[$k]->toArray();
							}
						} else {
							$foreignRs = $model->fetchAll('`id` IN ("' . implode('","', $keys) . '")');
							$arrayOfFr = array();
							for($k = 0; $k < $foreignRs->count(); $k++) {
								$arrayOfFr[$foreignRs[$k]->id] = $returnAs[$i] == 'o' ? $foreignRs[$k] : $foreignRs[$k]->toArray();
							}
						}
						for ($j = 0; $j < count($this->_data); $j++) {
							$explodedKeys = explode(',', $this->_data[$j][$field['alias']]);
							$toAssign = array();
							foreach($arrayOfFr as $key => $fr) {
								if (in_array($key, $explodedKeys)) {
									$toAssign[] = $fr;
								}
							}
							$this->_data[$j]['foreign'][$field['alias']] = $toAssign;
						}
					}
				}
			}
		}
		return $this;
	}
	public function setDependentRowsets($info) {
		$name = $this->getTable()->info('name');
		$selfEntityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $name . '"')->id;
		$keys = array();
		for ($j = 0; $j < count($this->_data); $j++) { 
			if (!in_array($this->_data[$j]['id'], $keys)) {
				$keys[] = $this->_data[$j]['id'];
			}
		}
		foreach ($info as $entity) {
			$related = Misc::loadModel('Field')->fetchRow('`entityId` = "' . $entity->entityId . '" AND `relation` = "' . $selfEntityId . '"');
			if ($related->storeRelationAbility == 'many') {
				foreach ($keys as $key) $find[] = 'FIND_IN_SET("' . $key. '", `' . $related->alias .'`)';
				$where = '1 AND (' . implode(' OR ', $find) . ')';
				$rowset = Entity::getInstance()->getModelById($entity->entityId)->fetchAll($where);
				$distributed = array();
				foreach($rowset as $row) {
					$masterIds = explode(',', $row->{$related->alias});
					for ($k = 0; $k < count($masterIds); $k++) $distributed[$masterIds[$k]][] = $row;
				}
				for ($i = 0; $i < count($this->_data); $i++) {
					$this->_data[$i]['dependent'][$entity->alias] = $distributed[$this->_data[$i]['id']];
				}
			} else if ($related->storeRelationAbility == 'one') {
				$where = array();
				$where[] = '`' . $related->alias . '` IN ("' . implode('","', $keys) .'")';
				if ($entity->where) $where[] = $entity->where;
				// set up order clause
				$order = null;
				if ($entity->orderBy == 'c') {
					if($entity->orderColumn) {
						if($orderColumn = $entity->getForeignRowByForeignKey('orderColumn')) {
							$order = $orderColumn->alias . ' ' . $entity->orderDirection;
						}
					}
				} else if ($entity->orderBy == 'e'){
					if ($entity->orderExpression) {
						$order = $entity->orderExpression;
					}
				}
				// get dependent rowset
				$rowset = Entity::getInstance()->getModelById($entity->entityId)->fetchAll(implode(' AND ', $where), $order);

				$info = Misc::loadModel('JoinFkForDependentRowset')->fetchAll('`dependentRowsetId` = "' . $entity->id . '"');
				if ($info->count()) $rowset->setForeignRowsByForeignKeys($info);

				$info = Misc::loadModel('DependentCountForDependentRowset')->fetchAll('`dependentRowsetId` = "' . $entity->id . '"');
				if ($info->count()) $rowset->setDependentCounts($info);
				

				foreach($rowset as $row) {
					if ($entity->limit) {
						if(count($distributed[$row->{$related->alias}]) < $entity->limit) {
							$distributed[$row->{$related->alias}][] = $entity->returnAs == 'o' ? $row : $row->toArray();
						}
					} else {
						$distributed[$row->{$related->alias}][] = $entity->returnAs == 'o' ? $row : $row->toArray();
					}
				}
				for ($i = 0; $i < count($this->_data); $i++) {
					$this->_data[$i]['dependent'][$entity->alias] = $distributed[$this->_data[$i]['id']];
				}
			}
		}
	}
	public function filter($ids, $key = 'id'){
		$filtered = array();
		if (!is_array($ids)) $ids = explode(',', $ids);
		$this->_data = array();
		for ($i = 0; $i < count($this->_rows); $i++) if (in_array($this->_rows[$i]->$key, $ids)) {
			$filtered[] = $this->_rows[$i];
			$this->_data[] = $this->_rows[$i]->toArray();
		}
		$this->_rows = $filtered;
		$this->_count = count($this->_rows);
		return $this;
	}
	public function toArray(){
		return $this->_data;
	}
}
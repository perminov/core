<?php
class Admin_FieldsController extends Indi_Controller_Admin
{
	public function formAction(){
		$this->trail->getItem()->dropdownWhere['satellite'] = '`entityId` = "' . $this->row->entityId . '"';
		parent::formAction();
    }
	public function prepareJsonDataForIndexAction(){
		// set up raw grid data
		$data = $this->rowset->toArray();

		// get info about columns that will be presented in grid
		$gridFields = $this->trail->getItem()->gridFields->toArray();
		$gridFieldsAliases = array('id'); for ($i = 0; $i < count ($gridFields); $i++) $gridFieldsAliases[] = $gridFields[$i]['alias'];
		
		// get info about all columns that are exists at the present moment in $data
		$columns = count($data) ? array_keys($data[0]) : array();

		// unset columns in $data that will not be used in grid, except 'satellite' column, as 
		// it will be used in some stage of process, and it will be unset after using
		if (!in_array('satellite', $gridFieldsAliases)) $gridFieldsAliases[] = 'satellite';
		for ($i = 0; $i < count($data); $i++) {
			foreach ($columns as $column) {
				if (!in_array($column, $gridFieldsAliases)) {
					unset($data[$i][$column]); 
				}
			}
		}

        $gridFieldsAliasesThatStoreBoolean = array();
		// get info about grid columns, that store relations and boolean values
		for ($i = 0; $i < count ($gridFields); $i++) {
			if ($gridFields[$i]['relation']) $gridFieldsThatStoreRelation[$gridFields[$i]['alias']] = $gridFields[$i]['relation'];
            if ($gridFields[$i]['elementId'] == 9) $gridFieldsAliasesThatStoreBoolean[] = $gridFields[$i]['alias'];
		}


		if (count($gridFieldsThatStoreRelation)) {
			// get info about grid columns, that store relations, and their columns have SET and ENUM types
			// we need this info because there will be another logic to get titles for them
			// at first, get ids of 'columntypes' db table rows there was specified in 'type' column that they have SET or ENUM types
			$columntype = Misc::loadModel('ColumnType');
			$irregularColumnTypesIds = $columntype->getImplodedIds('`type` LIKE "ENUM%" OR `type` LIKE "SET%"', true);

			$irregularGridFieldsThatStoreRelation = array();
			foreach($gridFields as $gridField){
				if(in_array($gridField['columnTypeId'], $irregularColumnTypesIds)) $irregularGridFieldsThatStoreRelation[$gridField['alias']] = $gridField['id'];
			}

			// get distinct values for grid columns, that store relations
			$gridFieldsAliasesThatStoreRelation = array_keys($gridFieldsThatStoreRelation);
			for ($i = 0; $i < count($data); $i++) {
				foreach ($gridFieldsAliasesThatStoreRelation as $alias) {
					if ($data[$i][$alias] && @!in_array($data[$i][$alias], $keys[$alias])) $keys[$alias][] = $data[$i][$alias];
				}
			}
			$irregularGridFieldsAliasesThatStoreRelation = array_keys($irregularGridFieldsThatStoreRelation);
			// get custom titles for values of grid columns, that store relations
			if (count($keys))
			foreach ($keys as $fieldAlias => $foreignKeyValues) {
				if (count($foreignKeyValues)) {

					// get titles for ENUM and SET columns (we called them 'irregular')
					if (in_array($fieldAlias, $irregularGridFieldsAliasesThatStoreRelation)) {
						$condition  = '`alias` IN ("' . implode('","', $foreignKeyValues) . '")';
						$condition .= ' AND `fieldId` = "' . $irregularGridFieldsThatStoreRelation[$fieldAlias] . '"';
						$foreignRowset = Entity::getInstance()->getModelById($gridFieldsThatStoreRelation[$fieldAlias])->fetchAll($condition);
						foreach ($foreignRowset as $foreignRow) $titles[$fieldAlias][$foreignRow->alias] = $foreignRow->getTitle();

					// get title for other columns that store relations
					} else {
						$foreignRowset = Entity::getInstance()->getModelById($gridFieldsThatStoreRelation[$fieldAlias])->fetchAll('`id` IN (' . implode(',', $foreignKeyValues) . ')');
						foreach ($foreignRowset as $foreignRow) $titles[$fieldAlias][$foreignRow->id] = $foreignRow->getTitle();
					}
				}
			}
			// get info about default values and related entities
			for ($i = 0; $i < count($data); $i++) {
				$model = Entity::getInstance()->getModelById($data[$i]['relation']);
				if ($data[$i]['defaultValue'] || $data[$i]['relation'] == 6) {
					if ($model) {
						if ($data[$i]['relation'] != 6) {
							if ($foreignRow = $model->fetchRow('`id` = "' . $data[$i]['defaultValue'] . '"')){
								$data[$i]['defaultValue'] = '"' . $foreignRow->getTitle() . '"';
							}
						} else {
							$condition  = '`alias` = "' . $data[$i]['defaultValue'] . '"';
							$condition .= ' AND `fieldId` = "' . $data[$i]['id'] . '"';
							$foreignRow = $model->fetchRow($condition);
							if ($foreignRow) $data[$i]['defaultValue'] = '"' . $foreignRow->getTitle() . '"';
						}
					}
				} else  if ($data[$i]['defaultValue'] == ''){
					$data[$i]['defaultValue'] = '<font color="#aaaaaa">Не задано</font>';
				} else {
					$data[$i]['defaultValue'] = '"' . $data[$i]['defaultValue'] . '"';
				}
				if (!$model && !$titles['relation'][$data[$i]['relation']]) {
					if($data[$i]['satellite']) {
						if (!$fieldModel) $fieldModel = Misc::loadModel('Field');
						$data[$i]['relation'] = '<font color="#aaaaaa">Зависит от поля "' . $fieldModel->fetchRow('`id` = "' . $data[$i]['satellite'] . '"')->getTitle() . '"</font>';
					} else {
						$data[$i]['relation'] = '<font color="#aaaaaa">Не будут</font>';
					}
				}
			}

            // apply up custom titles
            for ($i = 0; $i < count($data); $i++) {
                foreach ($gridFieldsAliasesThatStoreRelation as $alias) {
                    $title = $titles[$alias][$data[$i][$alias]];
                    if ($title) $data[$i][$alias] = $title;
                }
            }

        }

        // apply up custom titles
        for ($i = 0; $i < count($data); $i++) {
            foreach ($gridFieldsAliasesThatStoreBoolean as $alias) {
                $data[$i][$alias] = $data[$i][$alias] ? 'Да' : 'Нет';
            }
        }

        $jsonData = '({"totalCount":"'.$this->rowset->foundRows.'","blocks":'.json_encode($data).'})';
		return $jsonData;
	}

	public function postSave(){
        // Load columnType model
        $columnType = Misc::loadModel('ColumnType');

        // first part of ALTER query
        $query = 'ALTER TABLE  `' . $this->trail->getItem(1)->row->table . '` ';

        // if entity field was previously linked to db table column, but now it is not, we remove db table column
        if ($this->trail->getItem()->row->columnTypeId && !$this->post['columnTypeId']) {
            $query .= 'DROP `' . $this->trail->getItem()->row->alias . '`';
            $this->db->query($query);

            $oldColumnTypeRow = $columnType->fetchRow('`id`="' . $this->trail->getItem()->row->columnTypeId . '"');
            if (preg_match('/ENUM/', $oldColumnTypeRow->type) || preg_match('/SET/', $oldColumnTypeRow->type)) {
                $this->db->query('DELETE FROM `enumset` WHERE `fieldId` = "' . $this->identifier . '"');
            }
        }

        if (!$this->post['columnTypeId']) return false;

		$columnTypeRow = $columnType->fetchRow('`id` = "' . $this->post['columnTypeId'] . '"');

		// delete rows from 'enumset' table which became unused when column type changed from ENUM or SET to any other type of column
		// note that if column type was 'enum' and now it changed to 'set' or 'set' to 'enum' - the old values in 'enuset' table will
		// also be removed 
		if ($this->trail->getItem()->row) {
			$oldColumnTypeRow = $columnType->fetchRow('`id`="' . $this->trail->getItem()->row->columnTypeId . '"');
			if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
				$this->db->query('DELETE FROM `enumset` WHERE `fieldId` = "' . $this->identifier . '"');
			}
		}
		
		if (preg_match('/ENUM|SET/', $columnTypeRow->type)) {
			// check if correct relation for ENUM and SET column type was set -  must be set to 6 (-id of 'enumset' entity)
			$this->db->query('UPDATE `field` SET `relation`="6" WHERE `id` = "' . $this->identifier . '"');

			// before adding we check if these values are not already exist in 'enumset' table for that field
			$enumset = Misc::loadModel('Enumset');
			$enumsetArray = $enumset->fetchAll('`fieldId` = "' . $this->identifier . '"')->toArray();
			$existingValues = array();
			for ($i = 0; $i < count($enumsetArray); $i++) {
				$existingValues[] = $enumsetArray[$i]['alias'];
			}

            // set up at least one value (-got from $this->post['defaultValue']) for ENUM and SET
            // column types, because DB columns of these types cannot be created with no values at all
            $this->post['defaultValue'] = str_replace('"','\"', $this->post['defaultValue']);
            $toInsert = array();
            if((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
                $toInsert[] = $this->post['defaultValue'];
                $columnTypeRow->type = $columnTypeRow->type . '("' . $toInsert[0] . '") ';
            } else if ($this->trail->getItem()->row->defaultValue != $this->post['defaultValue'] || $this->trail->getItem()->row->alias != $this->post['alias']) {
                $enumsetValues = $existingValues;
                if (preg_match('/ENUM/', $columnTypeRow->type)) {
                    if  (!in_array($this->post['defaultValue'], $enumsetValues) || !count($enumsetValues)) {
                        $toInsert[] = $this->post['defaultValue'];
                        $enumsetValues[] = $toInsert[0];
                    }
                    $columnTypeRow->type = $columnTypeRow->type . '("' . implode('","', $enumsetValues) . '") ';
                } else if (preg_match('/SET/', $columnTypeRow->type)) {
                    $defaultValues = explode(',', $this->post['defaultValue']);
                    for($i = 0; $i < count($defaultValues); $i++) {
                        if (!in_array($defaultValues[$i], $enumsetValues)){
                            $enumsetValues[] = $defaultValues[$i];
                            $toInsert[] = $defaultValues[$i];
                        }
                    }
                    $columnTypeRow->type = $columnTypeRow->type . '("' . implode('","', $enumsetValues) . '") ';
                }
            }

            // adding new values if their aliases are not already exists
            for ($i = 0; $i < count($toInsert); $i++) {

                $enumsetInsertQuery = 'INSERT INTO `enumset` SET
                    `fieldId` = "' . $this->identifier .'",
                    `title` = "Укажите наименование для ' . ((is_array($defaultValues) ? !in_array($toInsert[$i], $defaultValues) : $toInsert[$i] != $this->post['defaultValue']) ? 'псевдонима' : (($this->post['defaultValue'] || preg_match('/ENUM/', $columnTypeRow->type)) ? 'значения по умолчанию' : 'минимум одного возможного значения')) .' \'' . str_replace('"', '\"', $toInsert[$i]) . '\'",
                    `alias` = "' . str_replace('"', '\"', $toInsert[$i]) . '",
                    `javascript` = "";
                ';
                if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type)) || ($this->post['defaultValue'] != $this->trail->getItem()->row->defaultValue)) {
                    if ($this->post['defaultValue'] == '' && count($enumsetValues) && preg_match('/SET/', $columnTypeRow->type)) {

                    } else {
                        $this->db->query($enumsetInsertQuery);
                    }
                }
			}

			// delete values that shouldn't be allowed. This situation can cause when we change column type of field from ENUM('value1','value2','value3')
			// (custom values, titles for which are stored in 'enumset'  table to ENUM('y','n')
			if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
				$delete= 'DELETE FROM `enumset` WHERE `fieldId` = "' . $this->identifier . '" AND `alias` NOT IN ("' . implode('","', $toInsert) . '")';
				$this->db->query($delete);
			}
		}

		// different query behaviour depends on adding new or changing an existing column
		if ($this->trail->getItem()->row) {
			$query .= 'CHANGE  `' . $this->trail->getItem()->row->alias . '`  `' . $this->post['alias'] . '` ' . $columnTypeRow->type . ' ';
		} else {
			$query .= 'ADD  `' . $this->post['alias'] . '` ' . $columnTypeRow->type . ' ';
		}

        //////////////////////// ////////////////////
        // Dealing with MySQL collation for column //
        /////////////////////////////////////////////

        $noNeedCollationColumnTypes = array('INT','DATE','DECIMAL','FLOAT','DOUBLE','REAL','BIT','BOOLEAN','SERIAL','TIME','YEAR','BINARY','BLOB');
        $sqlCollation = ' CHARACTER SET utf8 COLLATE utf8_general_ci';
        for ($i = 0; $i < count($noNeedCollationColumnTypes); $i++) if (preg_match('/' . $noNeedCollationColumnTypes[$i] . '/', $columnTypeRow->type)) $sqlCollation = '';
        $query .= $sqlCollation . ' NOT NULL ';

        //////////////////////// ////////////////////////
        // Dealing with MySQL default value for column //
        /////////////////////////////////////////////////

        if (!in_array($columnTypeRow->type, array('BLOB','TEXT'))) {
            $this->post['defaultValue'] = str_replace('"','&quot;',trim($this->post['defaultValue']));
            $valid = true;

            if (preg_match('/INT/', $columnTypeRow->type)) {
                $valid = is_numeric($this->post['defaultValue']);
                if (!$valid) $this->post['defaultValue'] = '0';

            } else if ($columnTypeRow->type == 'BOOLEAN') {
                $valid = preg_match('/^1|0$/', $this->post['defaultValue']);
                if (!$valid) $this->post['defaultValue'] = '0';

            } else if ($columnTypeRow->type == 'DATE') {
                $valid = preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $this->post['defaultValue'], $parts);
                if ($valid) $valid = checkdate(preg_replace('/^0/', '', $parts[1]), preg_replace('/^0/', '', $parts[2]), $parts[0]);
                if (!$valid) $this->post['defaultValue'] = '0000-00-00';

            } else if ($columnTypeRow->type == 'TIME') {
                $valid = preg_match('/^([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $this->post['defaultValue'], $parts);
                if ($valid) {
                    $h = (int) preg_replace('/^0/', '', $parts[1]);
                    $m = (int) preg_replace('/^0/', '', $parts[2]);
                    $s = (int) preg_replace('/^0/', '', $parts[3]);
                    if ($valid) $valid = $h >=0 && $h < 24 && $m >= 0 && $m < 60 && $s >= 0 && $s < 60;
                }
                if (!$valid) $this->post['defaultValue'] = '00:00:00';

            } else if ($columnTypeRow->type == 'YEAR') {
                $valid = preg_match('/^[0-9]{4}$/', $this->post['defaultValue']);
                if (!$valid) $this->post['defaultValue'] = '0000';

            } else if ($columnTypeRow->type == 'DATETIME') {
                // check datetime format
                $valid = preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $this->post['defaultValue'], $parts);
                // check date existence
                if ($valid) $valid = checkdate(preg_replace('/^0/', '', $parts[1]), preg_replace('/^0/', '', $parts[2]), $parts[0]);
                // check time existence
                if ($valid) {
                    $h = (int) preg_replace('/^0/', '', $parts[4]);
                    $m = (int) preg_replace('/^0/', '', $parts[5]);
                    $s = (int) preg_replace('/^0/', '', $parts[6]);
                    if ($valid) $valid = $h >=0 && $h < 24 && $m >= 0 && $m < 60 && $s >= 0 && $s < 60;
                }
                if (!$valid) $this->post['defaultValue'] = '0000-00-00 00:00:00';

            } else if (preg_match('/ENUM|SET|VARCHAR/', $columnTypeRow->type)) {

            } else if (preg_match('/^DOUBLE\(([0-9]+),([0-9]+)\)$/', $columnTypeRow->type, $digits)) {
                $valid = preg_match('/^[0-9]{1,' . ($digits[1] - $digits[2]) . '}\.[0-9]{1,' . $digits[2] . '}$/', $this->post['defaultValue']);
                if (!$valid) $this->post['defaultValue'] = '0.' . str_repeat('0', $digits[2]);
            }
            $query .= 'DEFAULT  "' . $this->post['defaultValue'] . '"';
            if (!$valid) $this->db->query('UPDATE `field` SET `defaultValue` = "' . $this->post['defaultValue'] . '" WHERE `id` = "' . $this->identifier . '"');
        }

        // If field changes affect db column properties we exec an ALTER sql query
		if ($this->trail->getItem()->row->columnTypeId != $this->post['columnTypeId'] || 
			$this->trail->getItem()->row->defaultValue != $this->post['defaultValue'] ||
			$this->trail->getItem()->row->alias != $this->post['alias']) {
			$this->db->query($query);
		}

        // If current column became 'move' column we automatically setup it's values
        if ($this->trail->getItem()->row->alias != $this->post['alias'] && $this->post['alias'] == 'move') {
            $this->db->query('UPDATE `' . $this->trail->getItem(1)->row->table . '` SET `move` = `id`');
        }

        ////////////////////////////
        // Managing MySQL indexes //
        ////////////////////////////

        // check if where was a relation, but now there is not, so we should remove an INDEX index
        $remove = $this->trail->getItem()->row && $this->trail->getItem()->row->storeRelationAbility != 'none' && $this->post['storeRelationAbility'] == 'none';
        if ($remove) {
            $indexes = $this->db->query('SHOW INDEXES FROM `' . $this->trail->getItem(1)->row->table .'` WHERE `Column_name` = "' . $this->post['alias'] . '"')->fetchAll();
            foreach ($indexes as $index) $this->db->query('ALTER TABLE  `' . $this->trail->getItem(1)->row->table .'` DROP INDEX `' . $index['Key_name'] . '`');
        }
        // check if where was no relation, but now it exist, so we should add an INDEX index
        $appear = (!$this->trail->getItem()->row || $this->trail->getItem()->row->storeRelationAbility == 'none') && $this->post['storeRelationAbility'] != 'none';
        if (preg_match('/INT|SET|ENUM|VARCHAR/', $columnTypeRow->type) && $appear) {
            $this->db->query('ALTER TABLE  `' . $this->trail->getItem(1)->row->table .'` ADD INDEX (`' . $this->post['alias'] . '`)');
        }

        // check if where was a TEXT column, but now there is not, so we should remove a FULLTEXT index
        $remove = $this->trail->getItem()->row && $this->trail->getItem()->row->columnTypeId == 4 && $this->post['columnTypeId'] != 4;
        if ($remove) {
            $indexes = $this->db->query('SHOW INDEXES FROM `' . $this->trail->getItem(1)->row->table .'` WHERE `Column_name` = "' . $this->post['alias'] . '"')->fetchAll();
            foreach ($indexes as $index) $this->db->query('ALTER TABLE  `' . $this->trail->getItem(1)->row->table .'` DROP INDEX `' . $index['Key_name'] . '`');
        }
        // check if where was no TEXT column, but now it exist, so we should add a FULLTEXT index
        $appear = (!$this->trail->getItem()->row || $this->trail->getItem()->row->columnTypeId != 4) && $this->post['columnTypeId'] == 4;
        if (preg_match('/TEXT/', $columnTypeRow->type) && $appear) {
            $this->db->query('ALTER TABLE  `' . $this->trail->getItem(1)->row->table .'` ADD FULLTEXT (`' . $this->post['alias'] . '`)');
        }
    }
}
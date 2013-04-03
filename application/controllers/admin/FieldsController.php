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
		if (!$this->post['columnTypeId']) return false;
		$query = 'ALTER TABLE  `' . $this->trail->getItem(1)->row->table . '` ';
		// determine does it need to specify collation and character set in sql query while adding or changing column in DB table 
		$columnType = Misc::loadModel('ColumnType'); 
		$columnTypeRow = $columnType->fetchRow('`id` = "' . $this->post['columnTypeId'] . '"');
		$noNeedCollationColumnTypes = array('INT','DATE','DECIMAL','FLOAT','DOUBLE','REAL','BIT','BOOLEAN','SERIAL','TIME','YEAR','BINARY','BLOB');
		$noNeedDefaultValuesColumnTypes = array('BLOB','TEXT');
		$sqlCollation = ' CHARACTER SET utf8 COLLATE utf8_general_ci';
		for ($i = 0; $i < count($noNeedCollationColumnTypes); $i++) if (preg_match('/' . $noNeedCollationColumnTypes[$i] . '/', $columnTypeRow->type)) $sqlCollation = '';
		
		// delete rows from 'enumset' table which became unused when column type changed from ENUM or SET to any other type of column
		// note that if column type was 'enum' and now it changed to 'set' or 'set' to 'enum' - the old values in 'enuset' table will
		// also be removed 
		if ($this->trail->getItem()->row) {
			$oldColumnTypeRow = $columnType->fetchRow('`id`="' . $this->trail->getItem()->row->columnTypeId . '"');
//			if (preg_match('/ENUM|SET/', $oldColumnTypeRow->type) && !preg_match('/ENUM|SET/', $columnTypeRow->type)) {
			if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
				$this->db->query('DELETE FROM `enumset` WHERE `fieldId` = "' . $this->identifier . '"');
			}
		}
		
		if (preg_match('/ENUM|SET/', $columnTypeRow->type)) {
			// check if correct relation for ENUM and SET column type was set -  must be set to 6 (-id of 'enumset' entity)
			$fieldRelationCorrectionQuery = 'UPDATE `field` SET `relation`="6" WHERE `id` = "' . $this->identifier . '"';
			$this->db->query($fieldRelationCorrectionQuery);

			// before adding we check if these values are not already exist in 'enumset' table for that field
			$enumset = Misc::loadModel('Enumset');
			$enumsetArray = $enumset->fetchAll('`fieldId` = "' . $this->identifier . '"')->toArray();
			$existingValues = array();
			for ($i = 0; $i < count($enumsetArray); $i++) {
				$existingValues[] = $enumsetArray[$i]['alias'];
			}

			// check if values for column types SET and ENUM are already presented in selected $columnTypeRow->type
			if (preg_match("/\((.*)\)/iu", $columnTypeRow->type, $matches)){
				$values = explode(',',$matches[1]);
				for ($i = 0; $i < count($values); $i++) $values[$i] = trim($values[$i], '\'"');
				// check if specified $this->post['defaultValue'] exists in list of already presented values
				// if no, default values will be set as the first value in list of presented values
				if (!in_array($this->post['defaultValue'], $values)) {
					$this->post['defaultValue'] = $values[0];
					// we change 'defaultValue' of field because the old value was incorrect
					$fieldDefaultValueCorrectionQuery = 'UPDATE `field` SET `defaultValue` = "' . $this->post['defaultValue'] . '", `relation`="6" WHERE `id` = "' . $this->identifier . '"';
					$this->db->query($fieldDefaultValueCorrectionQuery);
				}
			// set up at least one value (-got from $this->post['defaultValue']) for ENUM and SET 
			// column types, because DB columns of these types cannot be created with no values at all
			} else if((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
				$columnTypeRow->type = $columnTypeRow->type . '("' . str_replace('"','\"', $this->post['defaultValue']) . '") ';
			} else if ($this->trail->getItem()->row->defaultValue != $this->post['defaultValue'] || $this->trail->getItem()->row->alias != $this->post['alias']) {
				$enumsetValues = $existingValues;
				if (preg_match('/ENUM/', $columnTypeRow->type)) {
					if  (
						!in_array($this->post['defaultValue'], $enumsetValues) && 
						(!(preg_match('/SET/', $columnTypeRow->type) && $this->post['defaultValue'] == '')) || !count($enumsetValues)
						) 
					{
						$enumsetValues[] = $this->post['defaultValue'];
						$hasNotBeenIn = true;
					}
					$columnTypeRow->type = $columnTypeRow->type . '("' . implode('","', $enumsetValues) . '") ';
				} else if (preg_match('/SET/', $columnTypeRow->type)){
					$defaultValues = explode(',', $this->post['defaultValue']);
					for($i = 0; $i < count($defaultValues); $i++) {
						if (!in_array($defaultValues[$i], $enumsetValues)){
							$enumsetValues[] = $defaultValues[$i];
							$hasNotBeenInSet[] = $defaultValues[$i];
						}
					}
					$columnTypeRow->type = $columnTypeRow->type . '("' . implode('","', $enumsetValues) . '") ';
				}
				
			}
			if (!$enumsetValues) $enumsetValues = $existingValues;
			if ($hasNotBeenIn) unset($enumsetValues[array_search($this->post['defaultValue'], $enumsetValues)]);

			if ($hasNotBeenInSet) {
				for ($i = 0; $i < count($hasNotBeenInSet); $i++){
					unset($enumsetValues[array_search($hasNotBeenInSet[$i], $enumsetValues)]);
				}
			}

			// we automatically add a rows into 'enumset' table for values (aliases), and for $this->post['defaultValue']
			if (!count($values)) {
				$values = preg_match('/SET/', $columnTypeRow->type) ? $defaultValues : array($this->post['defaultValue']);
			} else if (!in_array($this->post['defaultValue'], $values)) {
//				$values[] = $this->post['defaultValue'];
			}

			// adding new values if their aliases are not already exists
			for ($i = 0; $i < count($values); $i++) { 
				if (!in_array($values[$i], $enumsetValues)) {
					$enumsetInsertQuery = 'INSERT INTO `enumset` SET 
						`fieldId` = "' . $this->identifier .'", 
						`title` = "Укажите наименование для ' . ($values[$i] != $this->post['defaultValue'] ? 'псевдонима' : (($this->post['defaultValue'] || preg_match('/ENUM/', $columnTypeRow->type)) ? 'значения по умолчанию' : 'минимум одного возможного значения')) .' \'' . str_replace('"', '\"', $values[$i]) . '\'", 
						`alias` = "' . str_replace('"', '\"', $values[$i]) . '",
						`javascript` = "";
					';
					if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type)) || ($this->post['defaultValue'] != $this->trail->getItem()->row->defaultValue)) {
						if ($this->post['defaultValue'] == '' && count($enumsetValues) && preg_match('/SET/', $columnTypeRow->type)) {

						} else {
							$this->db->query($enumsetInsertQuery);
						}
					}
				}
			}
			// delete values that shouldn't be allowed. This situation can cause when we change column type of field from ENUM('value1','value2','value3')
			// (custom values, titles for which are stored in 'enumset'  table to ENUM('y','n')
			if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
				$delete= 'DELETE FROM `enumset` WHERE `fieldId` = "' . $this->identifier . '" AND `alias` NOT IN ("' . implode('","', $values) . '")';
				$this->db->query($delete);
			}
		}

		// different query behaviour depends on adding new or changing an existing column
		if ($this->trail->getItem()->row) {
			$query .= 'CHANGE  `' . $this->trail->getItem()->row->alias . '`  `' . $this->post['alias'] . '` ' . $columnTypeRow->type . ' ';
		} else {
			$query .= 'ADD  `' . $this->post['alias'] . '` ' . $columnTypeRow->type . ' ';
		}

		// set default value as 0 if column type is some king of integer type
		if (preg_match('/INT/', $columnTypeRow->type)) {
			// is specified $this->post['defaultValue'] is not a number - set it to '0'
			if (!is_numeric($this->post['defaultValue'])) $this->post['defaultValue'] = '0';

			// we are doing it to be sure that there will be a correct 'defaultValue' of field because 
			// the old value may be incorrect if case of old field column type was not 'INT'
			$fieldDefaultValueCorrectionQuery = 'UPDATE `field` SET `defaultValue` = "' . $this->post['defaultValue'] . '" WHERE `id` = "' . $this->identifier . '"';
			$this->db->query($fieldDefaultValueCorrectionQuery);

			// set default value for ALTER TABLE query
			$sqlDefault = 'DEFAULT  "' . $this->post['defaultValue'] . '"';

		// 
		} else if (
					preg_match('/ENUM/', $columnTypeRow->type) 
					|| 
//					(preg_match('/SET/', $columnTypeRow->type) && $this->post['defaultValue'] != '') 
					(preg_match('/SET/', $columnTypeRow->type)) 
					|| 
					($this->post['defaultValue'] && !in_array($columnTypeRow->type, $noNeedDefaultValuesColumnTypes))
 				  ) {
			$sqlDefault = 'DEFAULT  "' . $this->post['defaultValue'] . '"';
		} else if ($columnTypeRow->type == 'BOOLEAN') {
			$sqlDefault = 'DEFAULT "' . ($this->post['defaultValue'] ? '1' : '0') . '"';

			// we are doing it to be sure that there will be a correct 'defaultValue' of field because 
			// the given value may be incorrect if it is not '0' or '1'
			$fieldDefaultValueCorrectionQuery = 'UPDATE `field` SET `defaultValue` = "' . ($this->post['defaultValue'] ? '1' : '0') . '" WHERE `id` = "' . $this->identifier . '"';
			$this->db->query($fieldDefaultValueCorrectionQuery);
		} else {
			// we are doing it because such column types as BLOB and TEXT don't require a default value definition
			// and there will be an mysql error if we allow to define default value
			// so there we syncronize values that are in column definition in database and in row at 'field' table
			if (in_array($columnTypeRow->type, $noNeedDefaultValuesColumnTypes)){
				$fieldDefaultValueCorrectionQuery = 'UPDATE `field` SET `defaultValue` = "" WHERE `id` = "' . $this->identifier . '"';
				$this->db->query($fieldDefaultValueCorrectionQuery);
			}
			$sqlDefault = '';
		}
		$query .= $sqlCollation . ' NOT NULL ' . $sqlDefault; 

		if ($this->trail->getItem()->row->columnTypeId != $this->post['columnTypeId'] || 
			$this->trail->getItem()->row->defaultValue != $this->post['defaultValue'] ||
			$this->trail->getItem()->row->alias != $this->post['alias']) {
			$this->db->query($query);
		}

        if ($this->trail->getItem()->row->alias != $this->post['alias'] && $this->post['alias'] == 'move') {
            $this->db->query('UPDATE `' . $this->trail->getItem(1)->row->table . '` SET `move` = `id`');
        }
	}
}
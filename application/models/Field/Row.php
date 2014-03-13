<?php
class Field_Row extends Indi_Db_Table_Row
{
	public function delete(){
		// delete uploaded images or files as they were uploaded as values
		// of this field if they were uploaded
		$this->deleteUploadedFilesIfTheyWere();

        // standart Db_Table_Row deletion
        $GLOBALS['enumsetForceDelete'] = true;
        parent::delete();
        unset($GLOBALS['enumsetForceDelete']);

        // delete db table assotiated column
        $this->deleteDbTableColumnIfFieldIsAssotiatedWithOne();
    }

	public function deleteDbTableColumnIfFieldIsAssotiatedWithOne(){
		if ($this->columnTypeId) {
			$tableName = Indi::model('Entity')->fetchRow('`id` = "' . $this->entityId . '"')->table;
			$query = 'ALTER TABLE `' . $tableName . '` DROP `' . $this->alias . '`';
			Indi::db()->query($query, true);
		}
	}

	public function deleteUploadedFilesIfTheyWere(){
		if (!$this->columnTypeId) {
			// get folder name where files of entity are stored
			$entity = Indi::model('Entity')->fetchRow('`id` = "' . $this->entityId . '"')->table;
			$image = $this->alias;

			// get upload path from config
			$uploadPath = Indi_Image::getUploadPath();
			
			// absolute upload path  in filesystem
			$absolute = trim($_SERVER['DOCUMENT_ROOT'], '\\/') . STD . '/' . $uploadPath . '/' . $entity . '/';
			
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
		if ($satelliteForField = $this->model()->fetchRow('`satellite` = "' . $this->id . '"')){
			return $satelliteForField;
		} else {
			return false;
		}
	}
	
	public function getParams(){
		$possibleParams = Indi::model('PossibleElementParam')->fetchAll('`elementId` = "' . $this->elementId . '"')->toArray();
		$redefinedParams = Indi::model('Param')->fetchAll('`fieldId` = "' . $this->id . '"')->toArray();
		$redefine = array();
		for ($i = 0; $i < count ($redefinedParams); $i++) {
			$redefine[$redefinedParams[$i]['possibleParamId']] = $redefinedParams[$i]['value'];
		}
		$params = array();
		for ($i = 0; $i < count($possibleParams); $i++) {
			$params[$possibleParams[$i]['alias']] = in_array($possibleParams[$i]['id'], array_keys($redefine)) ? $redefine[$possibleParams[$i]['id']] : $possibleParams[$i]['defaultValue'];
		}
		return $params;
	}

    public function save1() {
        // Here we check if there were a field structure changes that should result
        // an adjustments of related db table column SQL declaration
        $lookAt = array('alias', 'columnTypeId', 'defaultValue', 'storeRelationAbility');
        foreach ($lookAt as $property) if (array_key_exists($property, $this->_modified)) $modified[$property] = $this->_modified[$property];

        // If there were no such changes we just do parent::save();
        if (!is_array($modified)) {
            return parent::save();

        // Otherwise we do some number of additional things
        } else {
            $original = $this->_original;
            $return = parent::save();
        }

        // Load columnType model
        $columnTypeM = Indi::model('ColumnType');

        // Load enumset model
        $enumsetM = Indi::model('Enumset');

        // Get current entity
        $entityR = Indi::model('Entity')->fetchRow('`id` = "' . $this->entityId . '"');

        // Get previous column type row
        $oldColumnTypeR = $columnTypeM->fetchRow('`id` = "' . $original['columnTypeId'] . '"');

        // Get current column type row
        $columnTypeR = $columnTypeM->fetchRow('`id` = "' . $this->columnTypeId . '"');

        // Delete rows from 'enumset' table which became unused when column type changed from ENUM or SET to any other type of column
        // note that if column type was 'enum' and now it changed to 'set' or 'set' to 'enum' - the old values in 'enumset' table will
        // also be removed
        if ($original['id']) {
            if (in_array($oldColumnTypeR->type, array('ENUM', 'SET')) && $oldColumnTypeR->type != $columnTypeR->type) {
                $enumsetM->fetchAll('`fieldId` = "' . $original['id'] . '"')->delete(true);
            }
        }

        // If entity field was previously linked to db table column, but now it is not, we remove db table column
        if (array_key_exists('columnTypeId', $modified) && !$modified['columnTypeId']) {
            $query = 'ALTER TABLE  `' . $entityR->table . '` DROP `' . $original['alias'] . '`';
            Indi::db()->query($query);
        }

        // If current column type is ENUM or SET we:
        // 1.Check if relation is set correctly
        // 2.Construct "ENUM(....)" or "SET(...)" part of ALTER sql query
        // 3.Create on or more needed rows in `enumset` table
        if (preg_match('/ENUM|SET/', $columnTypeR->type)) {

            // Check if correct relation for ENUM and SET column type was set -  must be set to 6 (-id of 'enumset' entity)
            if ($this->relation != 6) {
                $this->relation = 6;
                parent::save();
            }

            // Before first value adding, we check if these values are not already exist in 'enumset' table for that field
            $enumsetA = $enumsetM->fetchAll('`fieldId` = "' . $this->id . '"')->toArray();
            $existingValues = array();
            for ($i = 0; $i < count($enumsetA); $i++) {
                $existingValues[] = $enumsetA[$i]['alias'];
            }

            // Set up at least one value (-got from $this->defaultValue) for ENUM and SET
            // column types, because DB columns of these types cannot be created with no values at all
            $toInsert = array();
            if($columnTypeR->type != $oldColumnTypeR->type) {
                $toInsert[] = $this->defaultValue;
                $sqlType = $columnTypeR->type . '("' . $toInsert[0] . '") ';
            } else if ($modified['defaultValue'] || $modified['alias']) {
                $enumsetValues = $existingValues;
                if ($columnTypeR->type == 'ENUM') {
                    if  (!in_array($this->defaultValue, $enumsetValues) || !count($enumsetValues)) {
                        $toInsert[] = $this->defaultValue;
                        $enumsetValues[] = $toInsert[0];
                    }
                    $sqlType = $columnTypeR->type . '("' . implode('","', $enumsetValues) . '") ';
                } else if ($columnTypeR->type == 'SET') {
                    $defaultValues = explode(',', $this->defaultValue);
                    for($i = 0; $i < count($defaultValues); $i++) {
                        if (!in_array($defaultValues[$i], $enumsetValues)){
                            $enumsetValues[] = $defaultValues[$i];
                            $toInsert[] = $defaultValues[$i];
                        }
                    }
                    $sqlType = $columnTypeR->type . '("' . implode('","', $enumsetValues) . '") ';
                }
            }

            // adding new values if their aliases are not already exists
            for ($i = 0; $i < count($toInsert); $i++) {
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $toInsert[$i], $matches)) $toInsertTitle[$i] = '#' . $matches[1];
                $enumsetInsertQuery = 'INSERT INTO `enumset` SET
                    `fieldId` = "' . $this->identifier .'",
                    `title` = "Укажите наименование для ' . ((is_array($defaultValues) ? !in_array($toInsert[$i], $defaultValues) : $toInsert[$i] != $this->post['defaultValue']) ? 'псевдонима' : (($this->post['defaultValue'] || preg_match('/ENUM/', $columnTypeR->type)) ? 'значения по умолчанию' : 'минимум одного возможного значения')) .' \'' . str_replace('"', '\"', $toInsertTitle[$i]) . '\'",
                    `alias` = "' . str_replace('"', '\"', $toInsert[$i]) . '",
                    `javascript` = "";
                ';
                if ($modified['columnTypeId'] || $modified['defaultValue']) {
                    if ($this->defaultValue == '' && count($enumsetValues) && preg_match('/SET/', $columnTypeR->type)) {

                    } else {
                        $this->db->query($enumsetInsertQuery);
                    }
                }
            }

        }

        $query = 'ALTER TABLE `' . $entityR->table . '` ';

        // different query behaviour depends on adding new or changing an existing column
        if ($original['id']) {
            $query .= 'CHANGE `' . $original['alias'] . '` `' . $this->alias . '` ' . $sqlType . ' ';
        } else {
            $query .= 'ADD `' . $this->alias . '` ' . $sqlType . ' ';
        }

        //////////////////////// ////////////////////
        // Dealing with MySQL collation for column //
        /////////////////////////////////////////////

        $noNeedCollationColumnTypes = array('INT','DATE','DECIMAL','FLOAT','DOUBLE','REAL','BIT','BOOLEAN','SERIAL','TIME','YEAR','BINARY','BLOB');
        $sqlCollation = ' CHARACTER SET utf8 COLLATE utf8_general_ci';
        for ($i = 0; $i < count($noNeedCollationColumnTypes); $i++) if (preg_match('/' . $noNeedCollationColumnTypes[$i] . '/', $columnTypeR->type)) $sqlCollation = '';
        $query .= $sqlCollation . ' NOT NULL ';

        //////////////////////// ////////////////////////
        // Dealing with MySQL default value for column //
        /////////////////////////////////////////////////

        if (!in_array($columnTypeR->type, array('BLOB','TEXT'))) {
            $this->defaultValue = str_replace('"','&quot;',trim($this->defaultValue));
            $valid = true;

            if (preg_match('/INT/', $columnTypeR->type)) {
                $valid = is_numeric($this->defaultValue);
                if (!$valid) $this->defaultValue = '0';

            } else if ($columnTypeR->type == 'BOOLEAN') {
                $valid = preg_match('/^1|0$/', $this->defaultValue);
                if (!$valid) $this->defaultValue = '0';

            } else if ($columnTypeR->type == 'DATE') {
                $valid = preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $this->defaultValue, $parts);
                if ($valid) $valid = checkdate(preg_replace('/^0/', '', $parts[1]), preg_replace('/^0/', '', $parts[2]), $parts[0]);
                if (!$valid) $this->defaultValue = '0000-00-00';

            } else if ($columnTypeR->type == 'TIME') {
                $valid = preg_match('/^([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $this->defaultValue, $parts);
                if ($valid) {
                    $h = (int) preg_replace('/^0/', '', $parts[1]);
                    $m = (int) preg_replace('/^0/', '', $parts[2]);
                    $s = (int) preg_replace('/^0/', '', $parts[3]);
                    if ($valid) $valid = $h >=0 && $h < 24 && $m >= 0 && $m < 60 && $s >= 0 && $s < 60;
                }
                if (!$valid) $this->defaultValue = '00:00:00';

            } else if ($columnTypeR->type == 'YEAR') {
                $valid = preg_match('/^[0-9]{4}$/', $this->defaultValue);
                if (!$valid) $this->defaultValue = '0000';

            } else if ($columnTypeR->type == 'DATETIME') {
                // check datetime format
                $valid = preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $this->defaultValue, $parts);
                // check date existence
                if ($valid) $valid = checkdate(preg_replace('/^0/', '', $parts[1]), preg_replace('/^0/', '', $parts[2]), $parts[0]);
                // check time existence
                if ($valid) {
                    $h = (int) preg_replace('/^0/', '', $parts[4]);
                    $m = (int) preg_replace('/^0/', '', $parts[5]);
                    $s = (int) preg_replace('/^0/', '', $parts[6]);
                    if ($valid) $valid = $h >=0 && $h < 24 && $m >= 0 && $m < 60 && $s >= 0 && $s < 60;
                }
                if (!$valid) $this->defaultValue = '0000-00-00 00:00:00';

            } else if (preg_match('/ENUM|SET|VARCHAR/', $columnTypeR->type)) {

            } else if (preg_match('/^DOUBLE\(([0-9]+),([0-9]+)\)$/', $columnTypeR->type, $digits)) {
                $valid = preg_match('/^[0-9]{1,' . ($digits[1] - $digits[2]) . '}\.[0-9]{1,' . $digits[2] . '}$/', $this->defaultValue);
                if (!$valid) $this->defaultValue = '0.' . str_repeat('0', $digits[2]);
            }
            $query .= 'DEFAULT  "' . $this->defaultValue . '"';
            if (!$valid) $this->db->query('UPDATE `field` SET `defaultValue` = "' . $this->defaultValue . '" WHERE `id` = "' . $this->id . '"');
        }

        $this->db->query($query);


        // If current column became 'move' column we automatically setup it's values
        if ($modified['alias'] && $this->alias == 'move') {
            $this->db->query('UPDATE `' . $entityR->table . '` SET `move` = `id`');
        }

        ////////////////////////////
        // Managing MySQL indexes //
        ////////////////////////////

        // check if where was a relation, but now there is not, so we should remove an INDEX index
        $remove = $original['id'] && $this->trail->getItem()->row->storeRelationAbility != 'none' && $this->post['storeRelationAbility'] == 'none';
        if ($remove) {
            $indexes = $this->db->query('SHOW INDEXES FROM `' . $this->trail->getItem(1)->row->table .'` WHERE `Column_name` = "' . $this->post['alias'] . '"')->fetchAll();
            foreach ($indexes as $index) $this->db->query('ALTER TABLE  `' . $this->trail->getItem(1)->row->table .'` DROP INDEX `' . $index['Key_name'] . '`');
        }
        // check if where was no relation, but now it exist, so we should add an INDEX index
        $appear = (!$this->trail->getItem()->row || $this->trail->getItem()->row->storeRelationAbility == 'none') && $this->post['storeRelationAbility'] != 'none';
        if (preg_match('/INT|SET|ENUM|VARCHAR/', $columnTypeR->type) && $appear) {
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
        if (preg_match('/TEXT/', $columnTypeR->type) && $appear) {
            $this->db->query('ALTER TABLE  `' . $this->trail->getItem(1)->row->table .'` ADD FULLTEXT (`' . $this->post['alias'] . '`)');
        }
    }

    public function save($parentSave = false) {
        if ($parentSave) return parent::save();
        $original = $this->_original;
        $modified = $this->_modified;
        $return = parent::save();
        if (preg_match('/^#([0-9a-fA-F]{6})$/', $this->defaultValue)) {
            $this->defaultValue = Misc::rgbPrependHue($this->defaultValue);
            $this->save(true);
        }
        // Load columnType model
        $columnType = Indi::model('ColumnType');

        // Get current entity
        $entityR = Indi::model('Entity')->fetchRow('`id` = "' . $this->entityId . '"');

        // first part of ALTER query
        $query = 'ALTER TABLE  `' . $entityR->table . '` ';

        // if entity field was previously linked to db table column, but now it is not, we remove db table column
        if ($original['columnTypeId'] && !$this->columnTypeId) {
            $query .= 'DROP `' . $original['alias'] . '`';
            Indi::db()->query($query);

            $oldColumnTypeRow = $columnType->fetchRow('`id`="' . $original['columnTypeId'] . '"');
            if (preg_match('/ENUM/', $oldColumnTypeRow->type) || preg_match('/SET/', $oldColumnTypeRow->type)) {
                Indi::db()->query('DELETE FROM `enumset` WHERE `fieldId` = "' . $this->id . '"');
            }
        }

        if (!$this->columnTypeId) return $return;

        $columnTypeRow = $columnType->fetchRow('`id` = "' . $this->columnTypeId . '"');

        // delete rows from 'enumset' table which became unused when column type changed from ENUM or SET to any other type of column
        // note that if column type was 'enum' and now it changed to 'set' or 'set' to 'enum' - the old values in 'enuset' table will
        // also be removed
        if ($original['id']) {
            $oldColumnTypeRow = $columnType->fetchRow('`id`="' . $original['columnTypeId'] . '"');
            if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
                Indi::db()->query('DELETE FROM `enumset` WHERE `fieldId` = "' . $this->id . '"');
            }
        }

        if (preg_match('/ENUM|SET/', $columnTypeRow->type)) {
            // check if correct relation for ENUM and SET column type was set -  must be set to 6 (-id of 'enumset' entity)
            Indi::db()->query('UPDATE `field` SET `relation`="6" WHERE `id` = "' . $this->id . '"');

            // before adding we check if these values are not already exist in 'enumset' table for that field
            $enumset = Indi::model('Enumset');
            $enumsetArray = $enumset->fetchAll('`fieldId` = "' . $this->id . '"')->toArray();
            $existingValues = array();
            for ($i = 0; $i < count($enumsetArray); $i++) {
                $existingValues[] = $enumsetArray[$i]['alias'];
            }

            // set up at least one value (-got from $this->post['defaultValue']) for ENUM and SET
            // column types, because DB columns of these types cannot be created with no values at all
            $this->defaultValue = str_replace('"','\"', $this->defaultValue);
            $toInsert = array();
            if((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
                $toInsert[] = $this->defaultValue;
                $columnTypeRow->type = $columnTypeRow->type . '("' . $toInsert[0] . '") ';
            } else if ($original['defaultValue'] != $this->defaultValue || $original['alias'] != $this->alias) {
                $enumsetValues = $existingValues;
                if (preg_match('/ENUM/', $columnTypeRow->type)) {
                    if  (!in_array($this->defaultValue, $enumsetValues) || !count($enumsetValues)) {
                        $toInsert[] = $this->defaultValue;
                        $enumsetValues[] = $toInsert[0];
                    }
                    $columnTypeRow->type = $columnTypeRow->type . '("' . implode('","', $enumsetValues) . '") ';
                } else if (preg_match('/SET/', $columnTypeRow->type)) {
                    $defaultValues = explode(',', $this->defaultValue);
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
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $toInsert[$i], $matches)) {
                    $toInsertTitle[$i] = '#' . $matches[1];
                } else {
                    $toInsertTitle[$i] = $toInsert[$i];
                }
                $enumsetInsertQuery = 'INSERT INTO `enumset` SET
                    `fieldId` = "' . $this->id .'",
                    `title` = "Укажите наименование для ' . ((is_array($defaultValues) ? !in_array($toInsert[$i], $defaultValues) : $toInsert[$i] != $this->defaultValue) ? 'псевдонима' : (($this->defaultValue || preg_match('/ENUM/', $columnTypeRow->type)) ? 'значения по умолчанию' : 'минимум одного возможного значения')) .' \'' . str_replace('"', '\"', $toInsertTitle[$i]) . '\'",
                    `alias` = "' . str_replace('"', '\"', $toInsert[$i]) . '",
                    `javascript` = "";
                ';
                if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type)) || ($this->defaultValue != $original['defaultValue'])) {
                    if ($this->defaultValue == '' && count($enumsetValues) && preg_match('/SET/', $columnTypeRow->type)) {

                    } else {
                        Indi::db()->query($enumsetInsertQuery);
                    }
                }
            }

            // delete values that shouldn't be allowed. This situation can cause when we change column type of field from ENUM('value1','value2','value3')
            // (custom values, titles for which are stored in 'enumset'  table to ENUM('y','n')
            if ((preg_match('/ENUM/', $oldColumnTypeRow->type) && !preg_match('/ENUM/', $columnTypeRow->type)) || (preg_match('/SET/', $oldColumnTypeRow->type) && !preg_match('/SET/', $columnTypeRow->type))) {
                $delete= 'DELETE FROM `enumset` WHERE `fieldId` = "' . $this->id . '" AND `alias` NOT IN ("' . implode('","', $toInsert) . '")';
                Indi::db()->query($delete);
            }
        }

        // different query behaviour depends on adding new or changing an existing column
        if ($original['id']) {
            $query .= 'CHANGE  `' . $original['alias'] . '`  `' . $this->alias . '` ' . $columnTypeRow->type . ' ';
        } else {
            $query .= 'ADD  `' . $this->alias . '` ' . $columnTypeRow->type . ' ';
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
            $this->defaultValue = str_replace('"','&quot;',trim($this->defaultValue));
            $valid = true;

            if (preg_match('/INT/', $columnTypeRow->type)) {
                $valid = is_numeric($this->defaultValue);
                if (!$valid) $this->defaultValueSql = '0';

            } else if ($columnTypeRow->type == 'BOOLEAN') {
                $valid = preg_match('/^1|0$/', $this->defaultValue);
                if (!$valid) $this->defaultValue = '0';

            } else if ($columnTypeRow->type == 'DATE') {
                $valid = preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $this->defaultValue, $parts);
                if ($valid) $valid = checkdate(preg_replace('/^0/', '', $parts[2]), preg_replace('/^0/', '', $parts[3]), $parts[1]);
                if (!$valid) $this->defaultValueSql = '0000-00-00';

            } else if ($columnTypeRow->type == 'TIME') {
                $valid = preg_match('/^([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $this->defaultValue, $parts);
                if ($valid) {
                    $h = (int) preg_replace('/^0/', '', $parts[1]);
                    $m = (int) preg_replace('/^0/', '', $parts[2]);
                    $s = (int) preg_replace('/^0/', '', $parts[3]);
                    if ($valid) $valid = $h >=0 && $h < 24 && $m >= 0 && $m < 60 && $s >= 0 && $s < 60;
                }
                if (!$valid) $this->defaultValue = '00:00:00';

            } else if ($columnTypeRow->type == 'YEAR') {
                $valid = preg_match('/^[0-9]{4}$/', $this->defaultValue);
                if (!$valid) $this->defaultValue = '0000';

            } else if ($columnTypeRow->type == 'DATETIME') {
                // check datetime format
                $valid = preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $this->defaultValue, $parts);
                // check date existence
                if ($valid) $valid = checkdate(preg_replace('/^0/', '', $parts[2]), preg_replace('/^0/', '', $parts[3]), $parts[1]);
                // check time existence
                if ($valid) {
                    $h = (int) preg_replace('/^0/', '', $parts[4]);
                    $m = (int) preg_replace('/^0/', '', $parts[5]);
                    $s = (int) preg_replace('/^0/', '', $parts[6]);
                    if ($valid) $valid = $h >=0 && $h < 24 && $m >= 0 && $m < 60 && $s >= 0 && $s < 60;
                }
                if (!$valid) $this->defaultValueSql = '0000-00-00 00:00:00';

            } else if (preg_match('/ENUM|SET|VARCHAR/', $columnTypeRow->type)) {

            } else if (preg_match('/^DOUBLE\(([0-9]+),([0-9]+)\)$/', $columnTypeRow->type, $digits)) {
                $valid = preg_match('/^[0-9]{1,' . ($digits[1] - $digits[2]) . '}\.[0-9]{1,' . $digits[2] . '}$/', $this->defaultValue);
                if (!$valid) $this->defaultValue = '0.' . str_repeat('0', $digits[2]);
            }
            $query .= 'DEFAULT  "' . (strlen($this->defaultValueSql) ? $this->defaultValueSql : $this->defaultValue) . '"';
            if (!$valid) Indi::db()->query('UPDATE `field` SET `defaultValue` = "' . $this->defaultValue . '" WHERE `id` = "' . $this->id . '"');
        }

        // If field changes affect db column properties we exec an ALTER sql query
        if ($original['columnTypeId'] != $this->columnTypeId ||
            $original['defaultValue'] != $this->defaultValue ||
            $original['alias'] != $this->alias) {
            Indi::db()->query($query);
        }

        // If current column became 'move' column we automatically setup it's values
        if ($original['alias'] != $this->alias && $this->alias == 'move') {
            Indi::db()->query('UPDATE `' . $entityR->table . '` SET `move` = `id`');
        }

        ////////////////////////////
        // Managing MySQL indexes //
        ////////////////////////////

        // check if where was a relation, but now there is not, so we should remove an INDEX index
        $remove = $original['id'] && $original['storeRelationAbility'] != 'none' && $this->storeRelationAbility == 'none';
        if ($remove) {
            $indexes = Indi::db()->query('SHOW INDEXES FROM `' . $entityR->table .'` WHERE `Column_name` = "' . $this->alias . '"')->fetchAll();
            foreach ($indexes as $index) Indi::db()->query('ALTER TABLE  `' . $entityR->table .'` DROP INDEX `' . $index['Key_name'] . '`');
        }
        // check if where was no relation, but now it exist, so we should add an INDEX index
        $appear = (!$original['id'] || $original['storeRelationAbility'] == 'none') && $this->storeRelationAbility != 'none';
        if (preg_match('/INT|SET|ENUM|VARCHAR/', $columnTypeRow->type) && $appear) {
            Indi::db()->query('ALTER TABLE  `' . $entityR->table .'` ADD INDEX (`' . $this->alias . '`)');
        }

        // check if where was a TEXT column, but now there is not, so we should remove a FULLTEXT index
        $remove = $original['id'] && $original['columnTypeId'] == 4 && $this->columnTypeId != 4;
        if ($remove) {
            $indexes = Indi::db()->query('SHOW INDEXES FROM `' . $entityR->table .'` WHERE `Column_name` = "' . $this->alias . '"')->fetchAll();
            foreach ($indexes as $index) Indi::db()->query('ALTER TABLE  `' . $entityR->table .'` DROP INDEX `' . $index['Key_name'] . '`');
        }
        // check if where was no TEXT column, but now it exist, so we should add a FULLTEXT index
        $appear = (!$original['id'] || $original['columnTypeId'] != 4) && $this->columnTypeId == 4;
        if (preg_match('/TEXT/', $columnTypeRow->type) && $appear) {
            Indi::db()->query('ALTER TABLE  `' . $entityR->table .'` ADD FULLTEXT (`' . $this->alias . '`)');
        }
        return $return;
    }
}
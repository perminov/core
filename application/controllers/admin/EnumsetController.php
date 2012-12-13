<?php
class Admin_EnumsetController extends Indi_Controller_Admin{
	public function postSave(){
		$columnTypeRow = $this->trail->getItem(1)->row->getForeignRowByForeignKey('columnTypeId');
		if (strpos($columnTypeRow->type, 'ENUM') !== false) {
			$type = 'ENUM';
		} else if(strpos($columnTypeRow->type,'SET') !== false) {
			$type = 'SET';
		}
		if ($type) {
			$values = $this->trail->getItem()->model->fetchAll('`fieldId` = "' . $this->trail->getItem(1)->row->id . '"')->toArray();
			for ($i = 0; $i < count($values); $i++) $v[] = $values[$i]['alias'];
			$defaultValue = $this->trail->getItem(1)->row->defaultValue;
			if (!in_array($defaultValue, $v) && ($type == 'ENUM' || ($type == 'SET' && $defaultValue != ''))) {
				$v[] = $defaultValue;
				$query = 'INSERT INTO `enumset` SET 
					`fieldId` = "' . $this->trail->getItem(1)->row->id . '", 
					`alias` = "' . $defaultValue . '",
					`title` = "Укажите наименование для значения по умолчанию - \'' . $defaultValue . '\'"';
				$this->db->query($query);
				//d($query);
			}
			$query  = 'ALTER TABLE `' . $this->trail->getItem(2)->row->table . '` ';
			$query .= 'CHANGE `' . $this->trail->getItem(1)->row->alias . '` `' . $this->trail->getItem(1)->row->alias . '` ';
			$sqlType = $type . '(' . "'" . implode("','", $v) . "'" . ')';
			$query .= $sqlType;
			$query .= ' CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ';
			$query .= (($type == 'ENUM' || ($type == 'SET' && $defaultValue != '')) ? 'DEFAULT ' . "'" . $defaultValue . "'" : '');
			$this->db->query($query);
			//d($query);
			if (trim($columnTypeRow->type) != $type && strpos($columnTypeRow->type, 'ENUM') !== false) {
				$query = 'UPDATE `field` SET `columnTypeId` = (SELECT `id` FROM `columnType` WHERE `type`="ENUM" LIMIT 1) WHERE `id` = "' . $this->trail->getItem(1)->row->id . '"';
				$this->db->query($query);
				//d($query);
			}
		}
	}
	public function preDelete(){
		$type = $this->trail->getItem(1)->row->getForeignRowByForeignKey('columnTypeId')->type;
		if (eregi('ENUM|SET', $type)) {
			// get aliases of values
			$values = $this->trail->getItem()->model->fetchAll('`fieldId` = "' . $this->trail->getItem(1)->row->id . '"')->toArray();
			$v = array(); for ($i = 0; $i < count($values); $i++) $v[] = $values[$i]['alias'];

			$forDeletion = $this->trail->getItem()->model->fetchRow('`id` = "' . $this->identifier . '"')->toArray();

			if (count($values) == 1) {
				die('Нельзя удалять последнее значение из набора возможных');
			} else if (count($values) > 1) {
				$defaultValue = $this->trail->getItem(1)->row->defaultValue;
				if ($defaultValue == $forDeletion['alias']) {
					if (eregi('ENUM', $type) || (eregi('SET', $type) && $defaultValue != '')) {
						die('Нельзя удалять значение по умолчанию');
					}
				}

			}

			// unset value that will be deleted for the correct altering
			unset($v[array_search($forDeletion['alias'], $v)]);
			$sqlType   = (eregi('ENUM', $type) ? 'ENUM' : 'SET') . "('" . implode("','", $v) . "')";
			$query  = 'ALTER TABLE `' . $this->trail->getItem(2)->row->table . '` ';
			$query .= 'CHANGE `' . $this->trail->getItem(1)->row->alias . '` `' . $this->trail->getItem(1)->row->alias . '` ';
			$query .= $sqlType . ' ';
			$query .= 'CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ';
			$sqlDefault = ((eregi('SET', $type) && $defaultValue == '' ) ? false : "'" . $defaultValue . "'");
			$query .= $sqlDefault === false ? '' : 'DEFAULT ' .  $sqlDefault;
			$this->db->query($query);
			if ($sqlType != $type && !in_array($type, array('ENUM', 'SET'))) {
				$query = 'UPDATE `field` SET `columnTypeId` = (SELECT `id` FROM `columnType` WHERE `type`="' . (eregi('ENUM', $type) ? 'ENUM' : 'SET') . '" LIMIT 1) WHERE `id` = "' . $this->trail->getItem(1)->row->id . '"';
				$this->db->query($query);
			}
		}
	}
}
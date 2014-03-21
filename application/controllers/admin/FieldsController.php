<?php
class Admin_FieldsController extends Indi_Controller_Admin {
	public function formAction(){
        if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $this->row->defaultValue, $matches)) {
            $this->row->modified('defaultValue', '#' . $matches[1]);
        }
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
			$columntype = Indi::model('ColumnType');
			$irregularColumnTypesIds = $columntype->fetchColumn('id', '`type` IN ("ENUM", "SET")');

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
						$foreignRowset = Indi::model($gridFieldsThatStoreRelation[$fieldAlias])->fetchAll($condition);
						foreach ($foreignRowset as $foreignRow) $titles[$fieldAlias][$foreignRow->alias] = $foreignRow->title;

					// get title for other columns that store relations
					} else {
						$foreignRowset = Indi::model($gridFieldsThatStoreRelation[$fieldAlias])->fetchAll('`id` IN (' . implode(',', $foreignKeyValues) . ')');
						foreach ($foreignRowset as $foreignRow) $titles[$fieldAlias][$foreignRow->id] = $foreignRow->title;
					}
				}
			}
			// get info about default values and related entities
			for ($i = 0; $i < count($data); $i++) {
				if ($data[$i]['defaultValue'] || $data[$i]['relation'] == 6) {
					if ($data[$i]['relation'] && $model = Indi::model($data[$i]['relation'])) {
						if ($data[$i]['relation'] != 6) {
							if ($foreignRow = $model->fetchRow('`id` = "' . $data[$i]['defaultValue'] . '"')){
								$data[$i]['defaultValue'] = '"' . $foreignRow->title . '"';
							}
						} else {
							$condition  = '`alias` = "' . $data[$i]['defaultValue'] . '"';
							$condition .= ' AND `fieldId` = "' . $data[$i]['id'] . '"';
							$foreignRow = $model->fetchRow($condition);
							if ($foreignRow) {
                                $title = $foreignRow->title;
                                if ($title != strip_tags($title)) {
                                    $data[$i]['defaultValue'] = $foreignRow->title;
                                } else {
                                    $data[$i]['defaultValue'] = '"' . $foreignRow->title . '"';
                                }
                            }
						}
					}
				} else  if ($data[$i]['defaultValue'] == ''){
					$data[$i]['defaultValue'] = '<font color="#aaaaaa">Не задано</font>';
				} else {
					$data[$i]['defaultValue'] = '"' . $data[$i]['defaultValue'] . '"';
				}
				if (!$data[$i]['relation'] && !$titles['relation'][$data[$i]['relation']]) {
					if($data[$i]['satellite']) {
						if (!$fieldModel) $fieldModel = Indi::model('Field');
						$data[$i]['relation'] = '<font color="#aaaaaa">Зависит от поля "' . $fieldModel->fetchRow('`id` = "' . $data[$i]['satellite'] . '"')->title . '"</font>';
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

        // check if data at any column has color format and convert hue part to color box
        for ($i = 0; $i < count($gridFields); $i++) {
            for ($j = 0; $j < count ($data); $j++) {
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $data[$j][$gridFields[$i]['alias']], $matches)) {
                    $data[$j][$gridFields[$i]['alias']] = '<span class="i-color-box" style="background: #' . $matches[1] . ';"></span>#'. $matches[1];
                }
            }
        }

        $jsonData = '({"totalCount":"'.$this->rowset->found().'","blocks":'.json_encode($data).'})';
		return $jsonData;
	}}
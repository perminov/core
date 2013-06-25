<?php
class Indi_Db_Table extends Indi_Db_Table_Beautiful
{
	/**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Indi_Db_Table_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Indi_Db_Table_Rowset';

	/**
     * Get table columns
     */
    public function getFields()
    {
		$structure = self::$_defaultDb->query('DESCRIBE `' . $this->_name . '`')->fetchAll();
		foreach ($structure as $column) {
			$cols[] = $column['Field'];
		}
        return $cols;
    }

    /**
     * Check if a given fields exists in table structure
     *
     * @param string $field
     * @return bool
     */
    public function fieldExists($field)
    {
        return in_array($field, $this->getFields());
    }
    
    /**
     * Return incremented by 1 maximum value of `move` column
     *
     * @return int
     */
    public function getLastPosition()
    {
        $last = $this->fetchRow('`move`!="0"', 'move DESC');
        if ($last) {
            return $last->move + 1;
        } else {
            return 1;
        }
    }

    /**
     * Get table name by foreignkey name according
     * to previously set up in Entity table
     * 
     * @param string $foreignKey
     * @return string $tableName
     */
    public function getTableNameByKeyName($foreignKey)
    {
        return Entity::getInstance()->fetchRow('`table` = "' . str_replace(array('Id', 'Ids'), array('', ''), $foreignKey) . '"')->table;
    }
        
    /**
     * Get foreign keys fields from model table structure
     * 
     * @uses self::getFields(), self::getTableNameByKeyName();
     * @return array of foreign keys
     */
    public function getForeignKeys()
    {
        $keys = array();
        $fields = $this->getFields();
        for ($i = 0; $i < count ($fields); $i++) {
            if ($this->getTableNameByKeyName($fields[$i])) {
                $keys[] = $fields[$i];
            }
        }
        return $keys;
    }

    /**
     * update function is redeclared to prevent trying to save 
     * values in $data array that have keys not existing in 
     * model table structure, so that keys and their value will
     * be unset
     * 
     * @param array $data array  of keys=>values
     * @param array $where condition
     * @uses self::getFields()
     * @return bool
     */
    public function update($data, $where)
    {
        $structure = $this->getFields();
        foreach ($data as $key => $value) {
            if (in_array($key, $structure)) {
                $filteredData[$key] = $value;
            }            
        }
        return parent::update($filteredData, $where);
    }

    /**
     * Insert function is redeclared to prevent trying to save
     * values in $data array that have keys not existing in 
     * model table structure, so that keys and their value will
     * be unset
     * 
     * @param array $data array of keys=>values
     * @uses self::getFields()
     * @return bool
     */
    public function insert($data)
    {
        $structure = $this->getFields();
        foreach($data as $key => $value) {
            if (in_array($key, $structure)) {
                $filteredData[$key] = $value;
            }
        }
        return parent::insert($filteredData);
    }
    
    /**
     * Gets dropdown data for formSelect helpers,
     * function by default called in preDispatch(), then action == 'form'
     * 
     * @uses self::fieldExists()
     * @return array options
     */
    public function getDropdownData($where = null, $order = null)
    {
        $order = $this->fieldExists('move') ? 'move' : $order;
        $order = $order ? $order : ($this->fieldExists('title') ? 'title' : null);
        
        $rowset = $this->fetchAll($where, $order);
        foreach ($rowset as $row) {
            $options[$row->id] = $row->getTitle();
        }
        return $options;
    }

    /**
     * Return metadata of all table structure, but
     * if $column specified - espesially for column
     * 
     * @param $column
     * @return array
     */
    public function getMetadata($column = null)
    {
        return $column ? $this->_metadata[$column] : $this->_metadata;
    }
    
    /**
     * Get ids of ordered rowset to use in
     * sql ORDER BY POSITION(`blabla` IN "orderedIds")
     * stamenents
     *
     * @return string imploded ids
     */
    public function getOrderedIds()
    {
        $order = $this->fieldExists('move') ? 'move' : ($this->fieldExists('title') ? 'title' : 'id') ;
        $rowset = $this->fetchAll(null, $order);
        foreach ($rowset as $row) {
            $ids[] = $row->id;
        }
        if (count($ids)) {
            return '"' . implode('","', $ids) . '"';
        }
        return '""';
    }

    /**
     * Return array containing some basic info
     *
     * @return array
     */
    public function toArray()
    {
        $array['class'] = get_class($this);
        $array['tableName'] = $this->_name;
        $array['rowClass'] = $this->getRowClass();
        $array['rowsetClass'] = $this->getRowsetClass();
        return $array;
    }

    public function getImplodedIds($where = null, $asArray = false, $order = null)
    {
        $np = $this->fetchAll($where, $order);
        $ids = array();
        foreach ($np as $npr) $ids[] = $npr->id;
        return $asArray ? $ids : '\'' . implode('\',\'', $ids) . '\'';
    }

	public function getTreeColumnName(){
        $treeColumnName = $this->info('name') . 'Id';
        return $this->fieldExists($treeColumnName) ? $treeColumnName : null;
    }
	public function getSatellitedFields($gridFieldsAliases = array()){
		$name = $this->info('name');
		$entityRow = Misc::loadModel('Entity')->fetchRow('`table`= "' . $name . '"');
		return Misc::loadModel('Field')->fetchAll('`entityId` = "' . $entityRow->id . '" AND `satellite` != "0"' . (count($gridFieldsAliases)? ' AND FIND_IN_SET(`alias`, "' . implode(',', $gridFieldsAliases) . '")' : ''));
	}
	public function useDefaultFetchMethod($use = true) {
		$this->useDefaultFetchMethod = $use;
		return $this;
	}

	public function getOptions($entityId = 0, $fieldAlias = '', $usedOnly = false, $where = '') {
		if ($usedOnly) {
			$entity = Entity::getInstance()->getModelById($entityId);
			$used = self::$_defaultDb->query('SELECT DISTINCT `' . $fieldAlias . '` FROM `' . $entity->info('name') . '`' . ($sql ? $sql : ''))->fetchAll();
			foreach ($used as $use) $distinct[] = current($use);
			$where = 'FIND_IN_SET(`id`, "' . implode(',', $distinct) . '")';
		} else if ($where) {
			if (preg_match('/\$/', $where)) {
				eval('$where = \'' . $where . '\';');
			}
		}
		$rs = $this->fetchAll($where, '`title`');
		foreach ($rs as $r) $options[$r->id] = $r->getTitle();
		return $options;
	}
}
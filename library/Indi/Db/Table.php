<?php
class Indi_Db_Table extends Indi_Db_Table_Abstract
{
	/**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Indi_Db_Table_Row';

	public $useDefaultFetchMethod = false;

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Indi_Db_Table_Rowset';

	/**
	 * Initialize table and schema names.
	 *
	 * If the table name is not set in the class definition,
	 * use the class name itself as the table name.
	 *
	 * A schema name provided with the table name (e.g., "schema.table") overrides
	 * any existing value for $this->_schema.
	 *
	 * @return void
	 */
	protected function _setupTableName()
	{
		if (! $this->_name) {
			$this->_name = strtolower(substr(get_class($this),0,1)) . substr(get_class($this),1);
		} else if (strpos($this->_name, '.')) {
			list($this->_schema, $this->_name) = explode('.', $this->_name);
			$this->_name = strtolower($this->_name);
		}
	}

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
     * insert function is redeclared to prevent trying to save 
     * values in $data array that have keys not existing in 
     * model table structure, so that keys and their value will
     * be unset
     * 
     * @param array $data array  of keys=>values
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

    /**
     * Fetches all rows.
     *
     * Honors the Indi_Db_Adapter fetch mode.
     *
     * @param string|array $where            OPTIONAL An SQL WHERE clause.
     * @param string|array $order            OPTIONAL An SQL ORDER clause.
     * @param int          $count            OPTIONAL An SQL LIMIT count.
     * @param int          $page             OPTIONAL number of page
     * @return Indi_Db_Table_Rowset_Abstract The row results per the Indi_Db_Adapter fetch mode.
     */
    public function fetchAll($where = null, $order = null, $count = null, $page = null, $calc = null, $special = false)
    {
		if (is_array($where) && count($where)) $where = implode(' AND ', $where);

		if (is_array($order) && count($order)) $order = implode(', ', $order);

		if ($count !== null || $page !== null) {
			$limit = (is_null($page) ? ($count ? '0' : $page) : $count * ($page - 1)) . ($count ? ',' : '') . $count;

			// the SQL_CALC_FOUND_ROWS flag
			if ($count != 1 && !is_null($page)) $calcFoundRows = 'SQL_CALC_FOUND_ROWS ';
		}

		$sql = 'SELECT ' . $calcFoundRows . '* FROM `' . $this->_name . '`'
				. ($where ? ' WHERE ' . $where : '')
				. ($order ? ' ORDER BY ' . $order : '')
				. ($limit ? ' LIMIT ' . $limit : '');

		$data = self::$_defaultDb->query($sql)->fetchAll();
		$data = array(
			'table'   => $this,
			'data'     => $data,
			'rowClass' => $this->_rowClass,
			'foundRows'=> current(self::$_defaultDb->query('SELECT FOUND_ROWS()')->fetch())
		);
		if ($special) d($sql . "\n", 'a');
		return new $this->_rowsetClass($data);
    }

    public function getImplodedIds($where = null, $asArray = false, $order = null)
    {
        $np = $this->fetchAll($where, $order);
        $ids = array();
        foreach ($np as $npr) $ids[] = $npr->id;
        return $asArray ? $ids : '\'' . implode('\',\'', $ids) . '\'';
    }

	/**
     * Get rowset as tree
     *
     * @param int $parentId
     * @param bool $onlyToggledOn 
     * @param bool $recursive - all levels of tree if true
     * @param int $level - needed for tree indentation
     * @return Indi_Db_Table_Rowset object
     */
    public function fetchTree($treeKeyName, $parentId = 0, $onlyToggledOn = false, $recursive = true, $level = 0, $order = null, $condition = null)
    {
        static $data;
        $rowset = $this->fetchAll(($parentId ? '`' . $treeKeyName . '` = "' . $parentId . '"' : '`' . $treeKeyName . '` = 0') . ($onlyToggledOn ? 'AND `toggle`="y"' : '') . ($condition ? ' AND ' . $condition : ''), $order);
        if ($recursive) {
            foreach ($rowset as $row) {
                $row->indent = Misc::indent($level);
                $data[] = $row->toArray();
                $this->fetchTree($treeKeyName, $row->id, $onlyToggledOn, $recursive, $level+1, $order, $condition);
            }
            if ($parentId == 0) {
                $data = array ('table' => $this, 'data' => $data, 'rowClass' => $this->_rowClass, 'stored' => true);
                return new $this->_rowsetClass($data);
            }
        }
        return $rowset;
    }
    public function getTreeColumnName(){
        $treeColumnName = $this->info('name') . 'Id';
        return $this->fieldExists($treeColumnName) ? $treeColumnName : null;
    }
	public function getSatellitedFields(){
		$name = $this->info('name');
		$entityRow = Misc::loadModel('Entity')->fetchRow('`table`= "' . $name . '"');
		return Misc::loadModel('Field')->fetchAll('`entityId` = "' . $entityRow->id . '" AND `satellite` != "0"');
	}
	public function useDefaultFetchMethod($use = true) {
		$this->useDefaultFetchMethod = $use;
		return $this;
	}
}
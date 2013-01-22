<?php
abstract class Indi_Db_Table_Abstract {
	const NAME             = 'name';
	const ROW_CLASS        = 'rowClass';
	const ROWSET_CLASS     = 'rowsetClass';
	/**
	 * Default Indi_Db object.
	 *
	 * @var Indi_Db
	 */
	protected static $_defaultDb;

	public function __construct() {
		$this->_name = strtolower(substr(get_class($this),0,1)) . substr(get_class($this),1);
		$this->_db = self::$_defaultDb;
	}
	/**
	 * Sets the default Indi_Db for all Indi_Db_Table objects.
	 *
	 * @param  $db Indi_Db object
	 * @return void
	 */
	public static function setDefaultAdapter($db = null)
	{
		self::$_defaultDb = $db;
	}

	/**
	 * Gets the default Indi_Db for all Indi_Db_Table objects.
	 *
	 * @return Indi_Db or null
	 */
	public static function getDefaultAdapter()
	{
		return self::$_defaultDb;
	}
	/**
	 * Fetches one row in an object of type Indi_Db_Table_Row_Abstract,
	 * or returns null if no row matches the specified criteria.
	 *
	 * @param string|array|Indi_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Indi_Db_Table_Select object.
	 * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
	 * @return Indi_Db_Table_Row_Abstract|null The row results per the
	 *     Indi_Db_Adapter fetch mode, or null if no row found.
	 */
	public function fetchRow($where = null, $order = null)
	{
		if (is_array($where) && count($where)) $where = implode(' AND ', $where);
		if ($data = self::$_defaultDb->query('SELECT * FROM `' . $this->_name . '` WHERE ' . $where . ($order ? ' ORDER BY ' . $order : ''))->fetch()) {
			$data = array(
				'table'   => $this,
				'data'     => $data,
			);
			$rowClass = $this->getRowClass();
			if (!class_exists($rowClass)) {
				require_once 'Indi/Loader.php';
				Indi_Loader::loadClass($rowClass);
			}
			return new $rowClass($data);
		}
		return null;
	}

	/**
	 * @return string
	 */
	public function getRowClass()
	{
		return $this->_rowClass;
	}

	/**
	 * @return string
	 */
	public function getRowsetClass()
	{
		return $this->_rowsetClass;
	}

	/**
	 * Gets the Indi_Db_Adapter_Abstract for this particular Indi_Db_Table object.
	 *
	 * @return Indi_Db_Adapter_Abstract
	 */
	public function getAdapter()
	{
		return $this->_db;
	}

	/**
	 * Returns table information.
	 *
	 * You can elect to return only a part of this information by supplying its key name,
	 * otherwise all information is returned as an array.
	 *
	 * @param  $key The specific info part to return OPTIONAL
	 * @return mixed
	 */
	public function info($key = null)
	{
//		$this->_setupPrimaryKey();

		$info = array(
			self::NAME             => $this->_name,
			self::ROW_CLASS        => $this->getRowClass(),
			self::ROWSET_CLASS     => $this->getRowsetClass(),
		);

		if ($key === null) {
			return $info;
		}

		if ($key ==  'cols') {
			$fields = $this->_db->query('
				SELECT
					`f`.`alias`
				FROM
					`field` `f`,
					`entity` `e`
				WHERE 1
					AND `e`.`table` = "' . $this->_name . '"
					AND `f`.`entityId` = `e`.`id`
					AND `f`.`columnTypeId` != "0"
			')->fetchAll();
			foreach ($fields as $field) $cols[] = $field['alias'];
			return $cols;
		}

		return $info[$key];
	}

	public function createRow($input = array()) {
		foreach ($this->info('cols') as $col) {
			$data[$col] = $input[$col];
		}
		$data = array(
			'table'   => $this,
			'data'     => $data
		);
		$rowClass = $this->getRowClass();
		if (!class_exists($rowClass)) {
			require_once 'Indi/Loader.php';
			Indi_Loader::loadClass($rowClass);
		}
		return new $rowClass($data);
	}

	public function insert($data) {
		$sql = 'INSERT INTO `' . $this->_name . '` SET ';
		$set = array();
		foreach ($data as $key => $value) {
			$set[] = '`' . $key . '` = "' . str_replace('"', '\"', $value) .'"';
		}
		$sql .= implode(', ', $set);
		self::$_defaultDb->query($sql);
		return(self::$_defaultDb->getPDO()->lastInsertId());
	}

	public function update($data, $where) {
		$sql = 'UPDATE `' . $this->_name . '` SET ';
		$set = array();
		foreach ($data as $key => $value) {
			$set[] = '`' . $key . '` = "' . str_replace('"', '\"', $value) .'"';
		}
		$sql .= implode(', ', $set);
		if ($where) {
			if (is_array($where) && count($where)) $where = implode(' AND ', $where);
			$sql .= ' WHERE ' . $where;
		}
		return self::$_defaultDb->query($sql);
	}

	public function delete($where) {
		$sql = 'DELETE FROM `' . $this->_name . '`';
		if ($where) {
			if (is_array($where) && count($where)) $where = implode(' AND ', $where);
			$sql .= ' WHERE ' . $where;
			return self::$_defaultDb->query($sql);
		}
		die($sql . '<br>No WHERE clause<br>');
	}

}
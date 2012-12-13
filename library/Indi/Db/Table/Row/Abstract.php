<?php
abstract class Indi_Db_Table_Row_Abstract implements ArrayAccess, IteratorAggregate{
	protected $_data;
	protected $_table;
	/**
	 * Constructor.
	 *
	 * Supported params for $config are:-
	 * - table       = class name or object of type Indi_Db_Table_Abstract
	 * - data        = values of columns in this row.
	 *
	 * @param  array $config OPTIONAL Array of user-specified config options.
	 * @return void
	 * @throws Indi_Db_Table_Row_Exception
	 */
	public function __construct(array $config = array())
	{
		$this->_table = $config['table'];
		$this->_data = $config['data'];
		$this->init();
	}

	/**
	 * Transform a column name from the user-specified form
	 * to the physical form used in the database.
	 * You can override this method in a custom Row class
	 * to implement column name mappings, for example inflection.
	 *
	 * @param string $columnName Column name given.
	 * @return string The column name after transformation applied (none by default).
	 */
	protected function _transformColumn($columnName)
	{
		// Perform no transformation by default
		return $columnName;
	}

	/**
	 * Initialize object
	 *
	 * Called from {@link __construct()} as final step of object instantiation.
	 *
	 * @return void
	 */
	public function init()
	{
	}

	/**
	 * Returns the table object, or null if this is disconnected row
	 *
	 * @return Indi_Db_Table_Abstract|null
	 */
	public function getTable()
	{
		return $this->_table;
	}

	public function getIterator()
	{
		return new ArrayIterator((array) $this->_data);
	}
	/**
	 * Proxy to __isset
	 * Required by the ArrayAccess implementation
	 *
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	/**
	 * Proxy to __get
	 * Required by the ArrayAccess implementation
	 *
	 * @param string $offset
	 * @return string
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * Proxy to __set
	 * Required by the ArrayAccess implementation
	 *
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}

	/**
	 * Proxy to __unset
	 * Required by the ArrayAccess implementation
	 *
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		return $this->__unset($offset);
	}

	/**
	 * Returns the column/value data as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return (array)$this->_data;
	}

	/**
	 * Test existence of row field
	 *
	 * @param  string  $columnName   The column key.
	 * @return boolean
	 */
	public function __isset($columnName)
	{
		$columnName = $this->_transformColumn($columnName);
		return isset($this->_data[$columnName]);
	}

	public function delete() {
		$sql = 'DELETE FROM `' . $this->_table->_name . '` WHERE `id` = "' . $this->id . '"';
		return Indi_Db_Table::getDefaultAdapter()->query($sql);
	}

	public function save(){
		if ($this->_data['id']) {
			$data = $this->_data;
			unset($data['id']);
			return $this->_table->update($data, '`id` = "' . $this->_data['id'] . '"');
		} else {
			return $this->_table->insert($this->_data);
		}
	}

}
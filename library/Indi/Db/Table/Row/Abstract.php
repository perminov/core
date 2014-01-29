<?php
abstract class Indi_Db_Table_Row_Abstract implements ArrayAccess, IteratorAggregate{
    /**
     * Original data
     *
     * @var array
     */
    protected $_original = array();

    /**
     * Modified data, used to construct correct sql-query for INSERT and UPDATE statements
     *
     * @var array
     */
    protected $_modified = array();

    /**
     * System data, used for internal needs
     *
     * @var array
     */
    protected $_system = array();

    /**
     * Compiled data, used for storing eval-ed values for properties, that are allowed to contain php-expressions
     *
     * @var array
     */
    protected $_compiled = array();

    /**
     * Object of type Indi_Db_Table_Abstract or some of extended class
     *
     * @var
     */
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
     */
    public function __construct(array $config = array())
    {
        $this->_table = $config['table'];
        $this->_original = $config['original'];
        $this->_modified = is_array($config['modified']) ? $config['modified'] : array();
        $this->_system = is_array($config['system']) ? $config['system'] : array();

        // Compile php expressions stored in allowed fields and assign results under separate keys in $this->_compiled
        foreach ($this->_table->getEvalFields() as $evalField) {
            Indi::$cmpTpl = $this->_original[$evalField]; eval(Indi::$cmpRun); $this->_compiled[$evalField] = Indi::$cmpOut;
        }
    }

    /**
     * Test existence of row field
     *
     * @param  string  $columnName   The column key.
     * @return boolean
     */
    public function __isset($columnName)
    {
        return array_key_exists($columnName, $this->_original);
    }

    /**
     * Retrieve row field value
     *
     * @param  string $columnName The user-specified column name.
     * @return string             The corresponding column value.
     */
    public function __get($columnName)
    {
        if ($columnName == 'title' && !$this->__isset($columnName)) {
            return $this->__isset('_title') ? $this->_title : 'No title';
        }
        return array_key_exists($columnName, $this->_modified) ? $this->_modified[$columnName] : $this->_original[$columnName];
    }

    /**
     * Set row field value, by creating an item of $this->_modified array, in case if
     * value is different from value of $this->_original at same key ($columnName)
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     */
    public function __set($columnName, $value)
    {
        // Check if value is a color in #RRGGBB format and prepend it with hue number
        if (is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
            $value = Misc::rgbPrependHue($value);
        }

        if ($this->_original[$columnName] !== $value) $this->_modified[$columnName] = $value;
    }

    /**
     * Returns the column/value data as an array.
     * If $type param is set to current (by default), the returned array will contain original data
     * with overrided values for keys of $this->_modified array
     *
     * @param string $type current|original|modified
     * @return array
     */
    public function toArray($type = 'current', $deep = true)
    {
        if ($type == 'current') {
            $array = (array) array_merge($this->_original, $this->_modified, $this->_compiled);
        } else if ($type == 'original') {
            $array = (array) $this->_original;
        } else if ($type == 'modified') {
            $array = (array) $this->_modified;
        }

        if ($deep) {
            if (is_array($array['foreign']) && count($array['foreign'])) {
                foreach ($array['foreign'] as $key => $row) {
                    if (is_object($row) && $row instanceof Indi_Db_Table_Row_Abstract)
                        $array['foreign'][$key] = $row->toArray($type, $deep);
                }
            }
        }

        return $array;
    }

    /**
     * Saves row data
     *
     * @return int Number of affected rows after UPDATE or LAST_INSERT_ID() after INSERT
     */
    public function save(){
        if ($this->_original['id']) {
			if ($affected = $this->_table->update($this->_modified, '`id` = "' . $this->_original['id'] . '"')) {
				$this->_original = (array) array_merge($this->_original, $this->_modified);
				$this->_modified = array();
			}
            return $affected;
        } else {
			$this->_original['id'] = $this->_table->insert($this->_modified);
			$this->_original = (array) array_merge($this->_original, $this->_modified);
			$this->_modified = array();
            return $this->_original['id'];
        }
    }

    /**
     * Delete row from table
     *
     * @return int Number of affected rows
     */
    public function delete() {
        return $this->_table->delete('`id` = "' . $this->_original['id'] . '"');
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

    /**
     * @return ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator((array) $this->_original);
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
     * Forces value setting for a given key at $this->_modified array,
     * without 'same-value' check. Actually this function was created
     * to deal with cases, when we need to skip prepending a hue number
     * to #RRGGBB color, because we need to display color value without hue number in forms.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function modified($key, $value) {
        return $this->_modified[$key] = $value;
    }

    /**
     * This function sets of gets a value of $this->_system array by a given key
     *
     * @return mixed
     */
    public function system() {
        if (func_num_args() == 1) {
            return $this->_system[func_get_arg(0)];
        } else if (func_num_args() == 2) {
            $this->_system[func_get_arg(0)] = func_get_arg(1);
            return $this;
        } else {
            return $this->_system;
        }
    }

    /**
     * Return results of certain field value compilation
     *
     * @param $key
     * @return mixed
     */
    public function compiled($key) {
        return $this->_compiled[$key];
    }
}
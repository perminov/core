<?php
abstract class Indi_Db_Table_Rowset_Abstract implements SeekableIterator, Countable, ArrayAccess{
    /**
     * The original data for each row.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Indi_Db_Table_Abstract object.
     *
     * @var Indi_Db_Table_Abstract
     */
    protected $_table;

    /**
     * Indi_Db_Table_Row_Abstract class name.
     *
     * @var string
     */
    protected $_rowClass = 'Indi_Db_Table_Row';

    /**
     * Iterator pointer.
     *
     * @var int
     */
    protected $_pointer = 0;

    /**
     * How many data rows there are.
     *
     * @var integer
     */
    protected $_count;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['table'])) {
            $this->_table      = $config['table'];
            $this->_tableClass = get_class($this->_table);
        }
        if (isset($config['rowClass'])) {
            $this->_rowClass   = $config['rowClass'];
        }
        if (!class_exists($this->_rowClass)) {
            require_once 'Indi/Loader.php';
            Indi_Loader::loadClass($this->_rowClass);
        }
        if (isset($config['data'])) {
            $this->_data = $config['data'];
        }
        // set the count of rows
        $this->_count = count($this->_data);
    }

    /**
     * Returns the table object, or null if this is disconnected rowset
     *
     * @return Indi_Db_Table_Abstract
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Delete rowset using row by row scheme. If $parentDelete param is set to true,
     * then there only standard Indi_Db_Table_Row deletion will take effect, with no
     * additional deletion commands, specified in delete() methods of all classes that
     * are extended from Indi_Db_Table_Row class, if they exist and have this method overrided
     *
     * @param bool $parentDelete
     * @return int
     */
    public function delete($parentDelete = false) {
        $deleted = 0;
        foreach ($this as $row) {
            $deleted += $row->delete($parentDelete);
        }
        return $deleted;
    }

    /**
     * Returns all data as an array.
     *
     * Updates the $_data property with current row object values.
     *
     * @return array
     */
    public function toArray(){
        return $this->_data;
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Indi_Db_Table_Rowset_Abstract|void Fluent interface.
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Indi_Db_Table_Row_Abstract|mixed current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        // Strip _system properties from original
        $original = $this->_data[$this->_pointer];
        unset($original['_system']);

        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $this->_rows[$this->_pointer] = new $this->_rowClass(
                array(
                    'table'    => $this->_table,
                    'original'     => $original,
                    'system'   => $this->_data[$this->_pointer]['_system']
                )
            );
        }

        return $this->_rows[$this->_pointer];
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function next()
    {
        ++$this->_pointer;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->_pointer >= 0 && $this->_pointer < $this->_count;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Implements Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Take the Iterator to position $position
     * Required by interface SeekableIterator.
     *
     * @param int $position the position to seek to
     * @throws Exception
     * @return Indi_Db_Table_Rowset_Abstract|void
     */
    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position >= $this->_count) {
            require_once 'Indi/Exception.php';
            throw new Exception("Illegal index $position");
        }
        $this->_pointer = $position;
        return $this;
    }

    /**
     * Check if an offset exists
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[(int) $offset]);
    }

    /**
     * Get the row for the given offset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @throws Exception
     * @return \Indi_Db_Table_Row_Abstract|mixed
     */
    public function offsetGet($offset)
    {
        $offset = (int) $offset;
        if ($offset < 0 || $offset >= $this->_count) {
            throw new Exception("Illegal index $offset");
        }
        $this->_pointer = $offset;

        return $this->current();
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }
}
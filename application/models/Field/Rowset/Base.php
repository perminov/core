<?php
class Field_Rowset_Base extends Indi_Db_Table_Rowset{

    /**
     * Contains an array with fields aliases as keys, and their indexes within $this->_rows array as values.
     * Is need for ability to direct fetch a needed row from rowset, by it's alias.
     *
     * @var array
     */
    protected $_indexes = array();

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        // Call parent constructor
        parent::__construct($config);

        // Setup $this->_indexes array
        if ($config['aliases']) $this->_indexes = array_flip($config['aliases']);
    }

    /**
     * Empty the current rowset. Method is redeclared to keep $this->_indexes array matched with
     * the contents if $this->_rows array
     *
     * @return Indi_Db_Table_Rowset Fluent interface
     */
    public function truncate() {

        // Reset $this->_indexes property
        $this->_indexes = array();

        // Call the truncate() method of a parent class and return it's result - rowset itself
        return parent::truncate();
    }

    /**
     * Reverse the current rowset. Method is redeclared to keep $this->_indexes array matched with
     * the contents if $this->_rows array
     */
    public function reverse() {

        // Call the reverse() method of a parent class
        parent::reverse();

        // Reverse $this->_indexes array
        $this->_indexes = array_reverse($this->_indexes, true);
    }

    /**
     * Get the values of a single column within rowset. If $column argument is 'alias', function will return keys of
     * $this->_indexes array, as they are aliases, so wen do not need to iterate through $this->_rows array.
     * Otherwise column() method of a parent class will be called
     *
     * @param $column
     * @return array
     */
    public function column($column) {
        return $column == 'alias' ? array_keys($this->_indexes) : parent::column($column) ;
    }

    /**
     * Provide direct access to a field row, stored within $this->_rows array, not by index, but by it's alias instead.
     *
     * @param $alias
     * @return mixed
     */
    public function field($alias) {
        return $this->_rows[$this->_indexes[$alias]];
    }


    /**
     * Exclude items from current rowset, that have keys, mentioned in $keys argument.
     * If $inverse argument is set to true, then function will return only rows,
     * which keys are mentioned in $keys argument. This method was redeclared to provide an approriate adjstments for
     * $this->_indexes array, as we have here bit extented logic.
     *
     * @param string|array $keys Can be array or comma-separated list of ids
     * @param string $type Name of key, which value will be tested for existence in keys list
     * @param boolean $inverse
     * @return Indi_Db_Table_Rowset Fluent interface
     */
    public function exclude($keys, $type = 'id', $inverse = false){

        // If $ids argument is not an array, we convert it to it by exploding by comma
        if (!is_array($keys)) $keys = explode(',', $keys);

        // Flip array
        $keys = array_flip($keys);

        // For each item in $this->_original array
        foreach ($this->_rows as $index => $row) {

            // If item id is in exclusion/selection list
            if ($inverse ? !array_key_exists($row->$type, $keys) : array_key_exists($row->$type, $keys)) {

                unset($this->_indexes[$this->_rows[$index]->alias]);
                unset($this->_rows[$index]);

                // Decrement count of items in current rowset
                $this->_count --;
            }
        }

        // Force zero-indexing
        $this->_rows = array_values($this->_rows);
        $this->_indexes = array_flip(array_values(array_flip($this->_indexes)));

        // Force $this->_pointer to be not out from the bounds of current rowset
        if ($this->_pointer > $this->_count) $this->_pointer = $this->_count;

        // Return rowset itself
        return $this;
    }
}
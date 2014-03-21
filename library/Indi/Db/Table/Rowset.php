<?php
class Indi_Db_Table_Rowset implements SeekableIterator, Countable, ArrayAccess {

    /**
     * Table name of table, that current rowset is related to
     *
     * @var string
     */
    protected $_table = '';

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
    protected $_count = 0;

    /**
     * Store number of rows, which would be found
     * if the LIMIT clause was disregarded
     *
     * @var mixed (null,int)
     */
    protected $_found = null;

    /**
     * Store current page number
     *
     * @var mixed (null,int)
     */
    protected $_page = null;

    /**
     * The original data for each row within current rowset
     *
     * @var array
     */
    protected $_original = array();

    /**
     * Modified data for each row within current rowset
     *
     * @var array
     */
    protected $_modified = array();

    /**
     * System data for each row within current rowset
     *
     * @var array
     */
    protected $_system = array();

    /**
     * Compiled data for each row within current rowset, used for storing eval-ed values for properties,
     * that are allowed to contain php-expressions
     *
     * @var array
     */
    protected $_compiled = array();

    /**
     * Temporary data, used for assigning some values to any row (within current rowset) object under some keys,
     * but these key => value pairs will be never involved at SQL INSERT or UPDATE query executions
     *
     * @var array
     */
    protected $_temporary = array();

    /**
     * Rows, pulled by foreign keys of any row within current rowset
     *
     * @var array
     */
    protected $_foreign = array();

    /**
     * Rowsets containing children for any row within current rowset, but related to other models
     *
     * @var array
     */
    protected $_nested = array();

    /**
     * Indi_Db_Table_Row class name.
     *
     * @var string
     */
    protected $_rowClass = 'Indi_Db_Table_Row';

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        // Setup properties from $config argument
        if (isset($config['table'])) $this->_table = $config['table'];
        if (isset($config['original'])) $this->_original = $config['original'];
        if (isset($config['system'])) $this->_system = $config['system'];
        if (isset($config['page'])) $this->_page = $config['page'];
        if (isset($config['found'])) $this->_found = $config['found'];

        // Setup row class
        $this->_rowClass = isset($config['rowClass']) ? $config['rowClass'] : $this->model()->rowClass();

        // Set the count of rows within current rowset
        $this->_count = count($this->_original);
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Indi_Db_Table_Row|mixed Current element from the current rowset
     */
    public function current()
    {
        // If $this->_pointer is out of bounds - return null
        if ($this->valid() === false) return null;

        // Get the id of current/original row
        $id = $this->_original[$this->_pointer]['id'];

        // Get the foreign rows/rowsets separately, as $this->_foreign store concept
        // is different from $this->_modified, $this->_system etc
        $_foreign = array();
        foreach ($this->_foreign as $foreignKeyName => $foreignByOriginalRowIdA) {
            foreach ($foreignByOriginalRowIdA as $originalRowId => $foreign) {
                if ($id ==  $originalRowId) $_foreign[$foreignKeyName] = $foreign;
            }
        }

        $_nested = array();
        foreach (array_keys($this->_nested) as $alias)
            $_nested[$alias] = $this->_nested[$alias][$id];

        // Create and return an instance of row
        return new $this->_rowClass(
            array(
                'table'    => $this->_table,
                'original'     => $this->_original[$this->_pointer],
                'modified'   => $this->_modified[$id],
                'system'   => $this->_system[$id],
                'compiled'   => $this->_compiled[$id],
                'temporary'   => $this->_temporary[$id],
                'foreign'   => $_foreign,
                'nested'   => $_nested,
            )
        );
    }

    /**
     * Delete rowset using row by row scheme. If $parentDelete param is set to true,
     * then there only standard Indi_Db_Table_Row deletion will take effect, with no
     * additional deletion commands, specified in delete() methods of all classes that
     * are extended from Indi_Db_Table_Row class, if they exist and have this method overrided
     *
     * @param bool $parentDelete
     * @return int Number of rows, that were deleted from current rowset
     */
    public function delete($parentDelete = false) {

        // Define a variable for counting how many rows were deleted
        $deleted = 0;

        // Row-by-row deletion
        foreach ($this as $row) $deleted += $row->delete($parentDelete);

        // Return number of deleted rows.
        return $deleted;
    }

    /**
     * Returns all data as an array. If $deep argument is specified then return value will represent an array of
     * values, got by toArray() function usage, called for each row in current rowset, instead of simply returning
     * $this->_original rowset property, so if any of rows within rowset have some additional properties, such as foreign
     * rows, nested rowsets, etc - these props will be included in the return value
     *
     * @param bool $deep
     * @return array
     */
    public function toArray($deep = false){

        // Declare an array for return values, got by deep mode
        $array = array();

        // Fulfil that array
        foreach ($this as $row) $array[] = $row->toArray('current', $deep);

        // Return result
        return $array;
    }

    /**
     * Return a model, that current rowset is related to
     *
     * @return mixed
     */
    public function model() {
        return Indi::model($this->_table);
    }

    /**
     * Return a database table name, that current rowset is dealing with
     *
     * @return mixed
     */
    public function table() {
        return $this->_table;
    }

    /**
     * Reverse order of items in $this->_original array
     */
    public function reverse(){
        $this->_original = array_reverse($this->_original);
    }

    /**
     * Returns the number of row in the rowset.
     * Required by interface Countable
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
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
        return isset($this->_original[(int) $offset]);
    }

    /**
     * Get the row for the given offset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @throws Exception
     * @return Indi_Db_Table_Row|mixed
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

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Indi_Db_Table_Rowset|void Fluent interface.
     */
    public function rewind()
    {
        // Set the internal pointer to 0
        $this->_pointer = 0;

        // Return current rowset itself
        return $this;
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return scalar
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
     * @return Indi_Db_Table_Rowset|void Fluent interface
     */
    public function next() {

        // Increment internal pointer
        $this->_pointer++;

        // Return current rowset itself
        return $this;
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
     * Take the Iterator to position $position
     * Required by interface SeekableIterator.
     *
     * @param int $position the position to seek to
     * @throws Exception
     * @return Indi_Db_Table_Rowset_Abstract|void Fluent interface
     */
    public function seek($position)
    {
        // Force $position argument to int
        $position = (int) $position;

        // If $position is out of rowset bounds, throw an exception
        if ($position < 0 || $position >= $this->_count)  throw new Exception("Illegal index $position");

        // Update an internal pointer
        $this->_pointer = $position;

        // Return current rowset itself
        return $this;
    }

    /**
     * Get the last row from current rowset
     *
     * @param int $stepsBack If this argument is set, function will return row, located at index, that is back shifted
     *                       by $stepsBack times from the index of last row
     * @return Indi_Db_Table_Row|null
     */
    public function last($stepsBack = 0) {

        // Backup internal pointer current value
        $savePointer = $this->_pointer;

        // Set internal pointer current value as last row index, decremented by $stepsBack argument
        $this->_pointer = $this->_count - 1 - (int) $stepsBack;

        // Get the row
        $last = $this->current();

        // Restore internal pointer value from backup
        $this->_pointer = $savePointer;

        // Return row
        return $last;
    }

    /**
     * Exclude items from current rowset, that have keys, mentioned in $keys argument.
     * If $inverse argument is set to true, then function will return only rows,
     * which keys are mentioned in $keys argument
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
        foreach ($this->_original as $index => $original) {

            // If item id is in exclusion/selection list
            if ($inverse ? !array_key_exists($original[$type], $keys) : array_key_exists($original[$type], $keys)) {

                // Unset foreign data for excluded rows
                foreach ($this->_foreign as $key => $data) unset($this->_foreign[$key][$original['id']]);

                // Unset other data for excluded rows
                unset(
                $this->_original[$index],
                $this->_modified[$original['id']],
                $this->_nested[$original['id']],
                $this->_system[$original['id']],
                $this->_temporary[$original['id']],
                $this->_compiled[$original['id']]
                );

                // Decrement count of items in current rowset
                $this->_count --;
            }
        }

        // Force zero-indexing
        $this->_original = array_values($this->_original);

        // Force $this->_pointer to be not out from the bounds of current rowset
        if ($this->_pointer > $this->_count) $this->_pointer = $this->_count;

        // Return rowset itself
        return $this;
    }

    /**
     * Empty rowset
     *
     * @return Indi_Db_Table_Rowset
     */
    public function truncate(){
        $this->_found = 0;
        $this->_count = 0;
        $this->_original = array();
        $this->_modified = array();
        $this->_system = array();
        $this->_temporary = array();
        $this->_compiled = array();
        $this->_foreign = array();
        $this->_nested = array();
        return $this;
    }

    /**
     * Force rowset to contain only rows, that have keys, mentioned in $keys argument
     *
     * @param $keys
     * @param string $type
     * @return Indi_Db_Table_Rowset Fluent interface
     */
    public function select($keys, $type = 'id') {
        return $this->exclude($keys, $type, true);
    }

    /**
     * Set or get $this->_original property, depending on whether or not $data argument is passed
     *
     * @param null $data
     * @return array|Indi_Db_Table_Rowset
     */
    public function original($data = null){

        // If no arguments passed, return $this->_original property
        if (is_null($data)) return $this->_original;

        // Else assign first argument to $this->_original property
        else if (is_array($data)) $this->_original = $data;

        else if (preg_match('/^[0-9]+$/', $data)) return $this->_original[$data];

        // Return rowset itself
        return $this;
    }

    /**
     * Return the number of page of results, that current rowset is representing
     *
     * @return int|null
     */
    public function page(){
        return $this->_page;
    }

    /**
     * If $found argument is null or not given, function will return the total count of rows, that can be fetched
     * in case if LIMIT clause would not be used in sql query. Else if $found argument is 'unset' function will
     * unset $this->_found property from current rowset, otherwise, if $found argument is numeric - function will
     * set $this->_found property equal to $found argument, and return that property
     *
     * @param string|int|null $found
     * @return int|null
     */
    public function found($found = null) {

        // If $found argument is 'unset' - unset the $this->_found property
        if ($found == 'unset') unset($this->_found);

        // Else if $found argument is numeric - set $this->_found property equal to $found argument, and return it
        else if (preg_match('/^[0-9]+$/', $found)) return $this->_found = $found;

        // Else just return $this->_found property
        else return $this->_found;
    }

    public function toGridData(Indi_Trail_Admin_Item $ti) {

        // If there are no rows in $this argument - return
        if ($this->_count == 0) return;

        // Declare an array for aliases of grid fields
        $columnA = array('id');

        // Build the array, containing all possible types of fields, that grid columns are linked to. We need to do that
        // because there will be different transformations performed on data, for ability to human-friendly displaying
        $typeA = array(
            'foreign' => array(
                'single' => array(),
                'multiple' => array()
            ),
            'boolean' => array(),
            'double' => array(),
            'date' => array(),
            'datetime' => array(),
            'other' => array('id' => true)
        );

        // Setup foreign rows for `columnType` property for each field row within $ti->gridFields rowset
        $ti->gridFields->foreign('columnTypeId');

        // Setup actual info about types of columns, that we will have to deal with
        foreach ($ti->gridFields as $gridFieldR) {

            // Foreign keys (single and multiple)
            if ($gridFieldR->storeRelationAbility == 'one') $typeA['foreign']['single'][$gridFieldR->alias] = true;
            else if ($gridFieldR->storeRelationAbility == 'many') $typeA['foreign']['multiple'][$gridFieldR->alias] = true;

            // Boolean values
            else if ($gridFieldR->foreign('columnTypeId')->type == 'BOOLEAN') $typeA['boolean'][$gridFieldR->alias] = true;

            // Double values (prices, etc)
            else if (preg_match('/^DOUBLE\(\d+,(\d+)\)$/', $gridFieldR->foreign('columnTypeId')->type, $m))
                $typeA['double'][$gridFieldR->alias] = $m[1];

            // Date and datetime values. Also we're getting additional params - display format at least
            else if ($gridFieldR->foreign('columnTypeId')->type == 'DATE')
                $typeA['date'][$gridFieldR->alias] = $gridFieldR->getParams();
            else if ($gridFieldR->foreign('columnTypeId')->type == 'DATETIME')
                $typeA['datetime'][$gridFieldR->alias] = $gridFieldR->getParams();

            // All other types
            else $typeA['other'][$gridFieldR->alias] = true;

            // Append current grid field alias to $columnA array
            $columnA[] = $gridFieldR->alias;
        }

        // Setup foreign rows, fetched by foreign keys, mentioned in fields, that are set up as grid columns
        $this->foreign(implode(',', array_merge(array_keys($typeA['foreign']['single']), array_keys($typeA['foreign']['multiple']))));

        // Declare an array for grid data
        $data = array();

        // Foreach row within $this rowset
        foreach ($this as $pointer => $r) {

            // Foreach field column within each row we check if we should perform any transformation
            foreach ($columnA as $columnI) {

                // If field column type is regular, e.g no foreign keys, no prices, no dates, etc. - we do no changes
                if (isset($typeA['other'][$columnI]))
                    $data[$pointer][$columnI] = $r->$columnI;

                // If field column type is 'double', we right pad column value by certain precision length
                // so if current row's price is '30.5' - we convert it to '30.50'
                if (isset($typeA['double'][$columnI]))
                    $data[$pointer][$columnI] = count($parts = explode('.', $r->$columnI))
                        ? $parts[0] . '.' . str_pad($parts[1], $typeA['double'][$columnI], '0', STR_PAD_RIGHT)
                        : $data[$pointer][$columnI] . str_pad('.', $typeA['double'][$columnI] + 1, '0', STR_PAD_RIGHT);

                // If field column type is 'boolean', we replace actual value with localized 'Yes' or 'No' strings
                if (isset($typeA['boolean'][$columnI]))
                    $data[$pointer][$columnI] = $r->$columnI ? I_YES : I_NO;

                // If field column type is a single foreign key, we use title of related foreign row
                if (isset($typeA['foreign']['single'][$columnI]))
                    $data[$pointer][$columnI] = $r->foreign($columnI)->title;

                // If field column type is a multiple foreign key, we use comma-separated titles of related foreign rows
                if (isset($typeA['foreign']['multiple'][$columnI]))
                    foreach ($r->foreign($columnI) as $m)
                        $data[$pointer][$columnI] .= $m->title .
                            ($r->foreign($columnI)->key() < $r->foreign($columnI)->count() - 1 ? ', ' : '');

                // If field column type is 'date' we adjust it's format if need. If date is '0000-00-00' we set it
                // to empty string
                if (isset($typeA['date'][$columnI])
                    && $typeA['date'][$columnI]['displayFormat']
                    && preg_match(Indi::rex('date'), $r->$columnI))

                    $data[$pointer][$columnI] = $r->$columnI == '0000-00-00'
                        ? ''
                        : date($typeA['date'][$columnI]['displayFormat'], strtotime($data[$pointer][$columnI]));

                // If field column type is datetime, we adjust it's format if need. If datetime is '0000-00-00 00:00:00'
                // we set it to empty string
                if (isset($typeA['datetime'][$columnI]) && ($typeA['datetime'][$columnI]['displayDateFormat'] ||
                    $typeA['datetime'][$columnI]['displayTimeFormat'])) {

                    if (!$typeA['datetime'][$columnI]['displayDateFormat'])
                        $typeA['datetime'][$columnI]['displayDateFormat'] = 'Y-m-d';
                    if (!$typeA['datetime'][$columnI]['displayTimeFormat'])
                        $typeA['datetime'][$columnI]['displayTimeFormat'] = 'H:i:s';

                    $data[$pointer][$columnI] = $r->$columnI == '0000-00-00 00:00:00'
                        ? '' : date( $typeA['datetime'][$columnI]['displayDateFormat'] . ' ' .
                            $typeA['datetime'][$columnI]['displayTimeFormat'], $r->$columnI);
                }

                // If there the color-value in format 'hue#rrgbb' can probably be found in field value
                // we do a try, and if found - inject a '.i-color-box' element
                if ((isset($typeA['other'][$columnI]) || isset($typeA['foreign']['single'][$columnI]) ||
                    isset($typeA['foreign']['multiple'][$columnI])) && preg_match(Indi::rex('hrgb'),
                    $data[$pointer][$columnI], $color))
                    $data[$pointer][$columnI] = '<span class="i-color-box" style="background: #'
                        . $color[1] . ';"></span>#'. $color[1];

                /*} else if (preg_match('/^<span class="i-color-box" style="background: ([#0-9a-zA-Z]{3,20});[^"]*"[^>]*>/', $data[$pointer][$columnI], $color)) {
                    $data[$pointer][$columnI] = '<span class="i-color-box" style="background: ' . $color[1] . ';"></span>'. strip_tags($data[$pointer][$columnI]);
                }*/

            }

            // Append system data
            $data[$pointer]['_system'] = $r->system();

            // Implement indents if need
            if ($data[$pointer]['title'])
                $data[$pointer]['title'] = $r->system('indent') . $data[$pointer]['title'];
        }

        // Return grid data
        return $data;
    }

    /**
     * Fetch the rowset, nested to current rowset, assing that rowset within $this->_nested array under certain key,
     * and return that rowset
     *
     * @param $table A table, where rowset will be fetched from
     * @param array $fetch Array of fetch params, that are same as Indi_Db_Table::fetchAll() possible arguments
     * @param null $alias The key, that fetched rowset will be stored in $this->_nested array under
     * @param null $field Connector field, in case if it is different from $this->_table . 'Id'
     * @param bool $fresh Flag for rowset refresh
     * @return Indi_Db_Table_Rowset object
     * @throws Exception
     */
    public function nested($table, $fetch = array(), $alias = null, $field = null, $fresh = false) {
        // Determine the nested rowset identifier. If $alias argument is not null, we will assume that needed rowset
        // is or should be stored under $alias key within $this->_nested array, or under $table key otherwise.
        // This is useful in cases when we need to deal with nested rowsets, got from same database table, but
        // with different fetch params, such as WHERE, ORDER, LIMIT clauses, etc.
        $key = $alias ? $alias : $table;

        // If needed nested rowset is already exists within $this->_nested array, and it shouldn't be refreshed
        if (array_key_exists($key, $this->_nested) && !$fresh) {

            // We return it
            return $this->_nested[$key];

            // Otherwise we fetch it, assign it under $key key within $this->_nested array and return it
        } else {

            // Determine the field, that is a connector between current row and nested rowset
            $connector = $field ? $field : $this->_table . 'Id';

            // If $fetch argument is array
            if (is_array($fetch)) {

                // Define the allowed keys within $fetch array
                $params = array('where', 'order', 'count', 'page', 'offset', 'foreign', 'nested');

                // Unset all keys within $fetch array, that are not allowed
                foreach ($fetch as $k => $v) if (!in_array($k, $params)) unset($fetch[$k]);

                // Extract allowed keys with their values from $fetch array
                extract($fetch);
            }

            // Convert $where to array
            $where = isset($where) && is_array($where) ? $where : (strlen($where) ? array($where) : array());

            // Get all the ids of rows within current rowset
            $idA = array(); foreach ($this->_original as $i) $idA[] = $i['id'];

            // If current rowset is not empty
            if ($this->_count) {

                // If connector field store relation ability is multiple
                if (Indi::model($table)->fields($connector)->storeRelationAbility == 'many')

                    // We use REGEXP sql expression for prepending $where array
                    array_unshift($where,
                        'CONCAT(",", `' . $connector . '`, ",") REGEXP ",(' . implode('|', $idA) . '),"');

                // Else if connector field store relation ability is single
                else if (Indi::model($table)->fields($connector)->storeRelationAbility == 'one')

                    // We use IN sql expression for prepending $where array. If
                    array_unshift($where,
                        count($idA)
                            ? '`' . $connector . '` IN (' . implode(',', $idA) . ')'
                            : '0'
                    );

                // Else an Exception will be thrown, as connector field do not exists or don't have store relation ability
                else throw new Exception('Connector field `' . $connector . '` do not exists or don\'t have store relation ability');

            // Else prepend $where array with '0', so no nested rows will be found
            } else array_unshift($where, '0');

            // Fetch rowset containing rows, that are nested to at least one row of current rowset
            $allNestedRs = Indi::model($table)->fetchAll($where, $order, $count, $page, $offset);

            // Setup foreign data for nested rowset, if need
            if ($foreign) $allNestedRs->foreign($foreign);

            // Setup foreign data for nested rowset, if need
            if ($nested) {
                if (is_array($nested))
                    $allNestedRs->nested($nested[0], $nested[1], $nested[2], $nested[3], $nested[4]);
                else
                    $allNestedRs->nested($nested);
            }

            // Now we should assign appropriate nested data to each row within current rowset
            for ($i = 0; $i < count($idA); $i++) {

                // Make a clone
                $rowNestedRs = clone $allNestedRs;

                // Delcare an array for keys within clone of nested rowset, that match certain row within current rowset
                $select = array();

                // Find matches, and collected keys or rows, that won't be excluded from clone of $allNestedRs rowset
                foreach ($rowNestedRs->original() as $data) {

                    // If connector field is multiple, we use regexp to find a match
                    if (Indi::model($table)->fields($connector)->storeRelationAbility == 'many') {
                        if (preg_match('/,' . $idA[$i] . ',/', ',' . $data[$connector] . ','))
                            $select[] = $data['id'];

                    // Else we use usual approach
                    } else if (Indi::model($table)->fields($connector)->storeRelationAbility == 'one') {
                        if ($idA[$i] == $data[$connector]) $select[] = $data['id'];
                    }
                }
                // Assign filtered clone of $allNestedRs rowset under $key key within $this->_nested property
                // and under certain id of row within current rowset. If there was no keys found, we do empty
                // clone before assigning it
                $this->_nested[$key][$idA[$i]] = count($select)
                    ? $rowNestedRs->select($select) : $rowNestedRs->truncate();
            }

            return $this;
        }
    }

    /**
     * Gets the foreign rows/rowsets by foreign key name, depending on foreign key field store relation ability,
     * with usage current values of these keys, mentioned in all rows in current rowset
     *
     * @param $key Single foreign key name, or comma-separated list of foreign key names, or array of foreign key names
     *             Usage examples:
     *             1. $myRowsetObject->foreign('foreignKeyName');
     *             2. $myRowsetObject->foreign('foreignKey1Name,foreignKey2Name');
     *             3. $myRowsetObject->foreign(array(
     *                  'foreignKey1Name' => 'subForeignKeyXName'
     *                ));
     *             4. $myRowsetObject->foreign(array(
     *                  'foreignKey1Name' => 'subForeignKeyAName,subForeignKeyBName'
     *                ));
     *             5. $myRowsetObject->foreign(array(
     *                  'foreignKey1Name' => 'subForeignKeyAName,subForeignKeyBName',
     *                  'foreignKey2Name' => 'subForeignKeyCName,subForeignKeyDName'
     *                ));
     *             6. $myRowsetObject->foreign(array(
     *                  'foreignKey1Name' => array(
     *                      'subForeignKeyAName' => 'subSubForeignKeyCName'
     *                      'subForeignKeyBName'
     *                  ),
     *                  'foreignKey2Name' => 'subForeignKeyCName,subForeignKeyDName'
     *                ));

     *             So, function allow to fetch
     *             1. foreign data,
     *             2. foreign data for foreign data,
     *             3. foreign data for foreign data for foreign data,
     *             4. etc
     *
     * @param null $subs Keyword, that affects format of return value and whether or not foreign data should be
     *                   refreshed. Mode can be: null, 'column', 'columns', 'refresh', 'refresh column', 'refresh columns'.
     *                   If neither 'columns' nor 'column' keywords if not exists within $mode argument, then current
     *                   rowset will be returned, else if $mode argument contains 'columns' keyword, the associative
     *                   array of foreign data, grouped by foreign key names will be returned, else if $mode argument
     *                   contains 'column' keyword, then associative array of foreign data will be returned, with ids
     *                   of rows of current rowset (let's call them 'original rows') as keys, and foreign rows/rowsets,
     *                   got for 'original rows' as values
     * @return array|Indi_Db_Table_Rowset
     * @throws Exception
     */
    public function foreign($key, $subs = null) {

        // If $key argument is string
        if (is_string($key)) {

            // If $key argument contains more than one key name - we setup rows for all keys
            if (preg_match('/,/', $key)) {

                // Prevent unsupported usage
                if (!is_string($subs)) $subs = null;

                // Convert keys list to array by exploding by comma
                $keyA = explode(',', $key);

                // If $subs argument contains 'columns' keyword
                if (preg_match('/columns/', $subs)) {

                    // If there actually was only one key name in $key argument, we replace 'columns' with 'column' in
                    // $subs argument, to avoid infinite recursion and to provide an ability for results to be returned
                    // as if there were multipler keys passed within $keys argument
                    $subs = str_replace('columns', 'column', $subs);

                    // We collect all foreign data as associative array, where keys are keys from $keys argument
                    $columnA = array(); foreach ($keyA as $keyI) $columnA[trim($keyI)] = $this->foreign(trim($keyI), $subs);

                    // Return collected data
                    return $columnA;

                    // Otherwise we do a ordinary foreign data setup
                } else foreach ($keyA as $keyI) $this->foreign(trim($keyI), $subs);

                // Return current
                return $this;
            }

        // Else if $key argument is an array
        } else if (is_array($key)) {

            // Foreach item in that array
            foreach ($key as $keyI => $subs)

                // Setup foreign data
                $this->foreign(trim($keyI), $subs);

            // Return rowset itself
            return $this;
        }

        // Detect if $key argument contains a directive to call some function on a foreign data rowset
        if (preg_match('/:/', $key)) list($key, $call) = explode(':', $key);

        // If field, representing foreign key - is exist within current entity
        if ($fieldR = $this->model()->fields($key)) {

            // If $subs argument contains no 'refresh' keyword, and foreign data for $key key is already exists within
            // $this->_foreign array, we return existing data, and if $subs argument contains 'column' keyword - we
            // return existing foreign data separately, without returning current rowset itself, otherwise, if $subs
            // argument does not contain 'column' keyword - we return rowset itself
            if (is_string($subs) && !preg_match('/refresh/', $subs))
                if (array_key_exists($key, $this->_foreign))
                    return preg_match('/column/', $subs)
                        ? (preg_match('/columns/', $subs)
                            ? array($key => $this->_foreign[$key])
                            : $this->_foreign[$key])
                        : $this;

            // If field do not store foreign keys - throw exception
            if ($fieldR->storeRelationAbility == 'none' || ($fieldR->relation == 0 && $fieldR->dependency != 'e'))
                throw new Exception('Field with alias `' . $key . '` within entity with table name `' . $this->_table .'` is not a foreign key');

            // Declare array for distinct values of foreign keys
            $distinctA = array();

            // If field dependency is 'Variable entity'
            if ($fieldR->dependency == 'e')

                // Foreach row within current rowset
                foreach ($this as $r) {

                    // Get the id of entity, that current foreign key is related to
                    $entityId = $r->{$fieldR->foreign('satellite')->alias};

                    // Collect foreign key values, grouped by entity id
                    $distinctA[$entityId] = array_merge(

                        // If there are already items exist within group, representing keys that are related
                        // to certain entity id (entity id is a satellite as per 'Variable entity' concept),
                        // - we use this group as array which will be a first array in list of merged arrays
                        is_array($distinctA[$entityId]) ? $distinctA[$entityId] : array(),

                        // If foreign key field store relation ability is 'many', and foreign key value of
                        // current row is not empty, we convert it to array by exploding by comma, and append
                        // to $distinctA array under $entityId key, otherwise we create and array that contains
                        // only one item and also append it to $distinctA array under $entityId key
                        strlen($r->$key)
                            ? ($fieldR->storeRelationAbility == 'many'
                                ? explode(',', $r->$key)
                                : array($r->$key))
                            : array()
                    );
                }

            // Else if foreign field dependency is not 'Variable entity', that mean that we deal with single entity
            // - entity, that current foreign key is related to
            else

                // Foreach row within current rowset
                foreach ($this as $r)

                    // Collect foreign key values, grouped by id of entity, that current foreign key field is related to
                    $distinctA[$fieldR->relation] = array_merge(

                        // If there are already items exist within group, representing keys that are related
                        // to related entity id - we use this group as array which will be a first array in list
                        // of merged arrays, otherwise we use empty array to aviod php warning messages
                        is_array($distinctA[$fieldR->relation]) ? $distinctA[$fieldR->relation] : array(),

                        // If foreign key field store relation ability is 'many', and foreign key value of
                        // current row is not empty, we convert it to array by exploding by comma, and append
                        // to $distinctA array under $entityId key, otherwise we create and array that contains
                        // only one item and also append it to $distinctA array under $entityId key
                        strlen($r->$key)
                            ? ($fieldR->storeRelationAbility == 'many'
                                ? explode(',', $r->$key)
                                : array($r->$key))
                            : array()
                    );

            // Strip foreign key values, that appear twice or more within $distinctA array under
            // their entity ids, so values in $distinctA array will now be truly distinct
            foreach ($distinctA as $entityId => $keys) $distinctA[$entityId] = array_unique($distinctA[$entityId]);

            // For each $entityId => $key pair within $distinctA array we fetch rowsets, that contain all rows that
            // are 'mentioned' in all rows within current rowset
            foreach ($distinctA as $entityId => $keys) {

                // Declare array for WHERE clause
                $where = array();

                // If current $entityId is id of enumset entity, we should append an additional WHERE clause,
                // that will outline the `fieldId` value, because in this case rows in current rowset store
                // aliases of rows from `enumset` table instead of ids, and aliases are not unique within that table.
                if (Indi::model($fieldR->relation)->name() == 'enumset') {

                    // Set the first part of WHERE clause
                    $where[] = '`fieldId` = "' . $fieldR->id . '"';

                    // Set the name of column, that will be involved in sql ' IN() ' statement
                    $col = 'alias';

                    // Set the quotation, as enumset keys are strings in most cases
                    $q = '"';

                } else {

                    // Set the name of column, that will be involved in sql ' IN() ' statement
                    $col = 'id';
                }

                // Finish building WHERE clause
                $where[] = '`' . $col . '` IN (' . $q . implode($q . ',' . $q, $distinctA[$entityId]) . $q . ')';

                // Fetch foreign data
                $foreignRs[$entityId] = Indi::model($entityId)->fetchAll($where);

                // Call a user-defined method for foreign data rowset, if need
                if ($call) eval('$foreignRs[$entityId]->' . $call . ';');

                // Setup a foreign data for jus fetched foreign data, if argument #3 is given. Argument #3 is not
                // presented in function signature, because it's detection is automated, and all calls of foreign()
                // method with argument #3 pass are performed within itself, by a recursive logic
                if (is_array($subs)) {
                    if ($subs['foreign']) {
                        if (is_string($subs['foreign'])) {
                            $foreignRs[$entityId]->foreign($subs['foreign']);
                        } else if (is_array($subs['foreign']) && (key($subs['foreign']) == '0')) {
                            $foreignRs[$entityId]->foreign($subs['foreign'][0], $subs['foreign'][1]);
                        } else {
                            $foreignRs[$entityId]->foreign($subs['foreign']);
                        }
                    }

                    if ($subs['nested']) {
                        if (is_array($subs['nested'])) {
                            if (key($subs['nested']) == '0') {
                                $foreignRs[$entityId]->nested($subs['nested'][0], $subs['nested'][1], $subs['nested'][2],
                                    $subs['nested'][3], $subs['nested'][4]);

                            } else {
                                foreach ($subs['nested'] as $table => $args)
                                    $foreignRs[$entityId]->nested($table, array(
                                            'where' => $args['where'], 'order' => $args['order'],
                                            'count' => $args['count'], 'page' => $args['page'],
                                            'offset' => $args['offset'],
                                        ), $args['alias'], $args['field'], $args['fresh']
                                    );
                            }
                        } else {
                            $foreignRs[$entityId]->nested($subs['nested']);
                        }
                    }
                }
            }

            // For each row within current rowset
            foreach ($this as $r) {

                // Get the id of entity, that current row's foreign key is related to. If foreign key field
                // dependency is 'Variable entity' - entity id is dynamic, that mean is may differ for each row
                $foreignKeyEntityId = $fieldR->dependency == 'e'
                    ? $r->{$fieldR->foreign('satellite')->alias}
                    : $fieldR->relation;

                // Get the column name, which value will be used for match
                $col = $foreignKeyEntityId == 6 ? 'alias' : 'id';

                // If current foreign key field is able to store only one key
                if ($fieldR->storeRelationAbility == 'one') {

                    // For each foreign row, fetched for entity, that have same id as $foreignKeyEntityId
                    foreach ($foreignRs[$foreignKeyEntityId] as $foreignR) {

                        // If foreign key value of current row is equal to foreign row id
                        if (($col == 'alias' ? $foreignR->fieldId == $fieldR->id : true) && $r->$key == $foreignR->$col) {

                            // Assign foreign row to $this->_foreign array, under foreign key name and current row id
                            $this->_foreign[$key][$r->id] = $foreignR;

                            // Stop searching for matches, as there could be only one foreign row found,
                            // and it was already found and assigned
                            break;
                        }
                    }

                    // Else if current foreign key field is able to store more that one key
                } else if ($fieldR->storeRelationAbility == 'many') {

                    // Declare the array that will be later used for rowset construction
                    $original = array();

                    // Setup an array, containing keys, that will be used to find match in
                    $set = explode(',', $r->$key);

                    // For each foreign row, fetched for entity, that have same id as $foreignKeyEntityId
                    foreach ($foreignRs[$foreignKeyEntityId] as $foreignR)

                        // If foreign row identifier (`id` or `alias`) is exists within list of keys,
                        // represented by foreign key value of current row, we get the data of that foreign row
                        // and append it to $original array. Also, before doing that, we should check
                        // if foreign key entity id is 'enumset', and if so, we do an additional check for
                        // foreign row field id to be equal to foreign key field id, because values of `alias`
                        // column/property within `enumset` table are unique only within their fields, but are not
                        // within global scope
                        if (($col == 'alias' ? $foreignR->fieldId == $fieldR->id : true) && in_array($foreignR->$col, $set))
                            $original[] = $foreignR->toArray();

                    // Create a rowset object, with usage of data, collected in $original array, and assing that rowset
                    // as a value within $this->_foreign property under current foreign key field name and current row id
                    $this->_foreign[$key][$r->id] = Indi::model($foreignKeyEntityId)
                        ->createRowset(array('original' => $original));

                    // Release the memory
                    unset($original, $set);
                }
            }

            // If $subs argument contains 'column' keyword - we return that foreign data separately, without returning
            // current rowset itself, otherwise, if $subs argument does not contain 'column' keyword - we return rowset
            // itself
            return is_string($subs) && preg_match('/column/', $subs)
                ? (preg_match('/columns/', $subs)
                    ? array($key => $this->_foreign[$key])
                    : $this->_foreign[$key])
                : $this;

            // Else there is no such a field within current entity - throw an exception
        } else {
            throw new Exception('Field with alias `' . $key . '` does not exists within entity with table name `' . $this->_table .'`');
        }
    }

    public function temporary($id = null, $arg2 = null) {

        if (is_null($id)) return $this->_temporary;

        if (is_null($arg2)) return $this->_temporary[$id];

        if (is_array($arg2)) return $this->_temporary[$id] = $arg2;

        if (func_num_args() == 2) return $this->_temporary[$id][$arg2];

        if (func_num_args() == 3) return $this->_temporary[$id][$arg2] = func_get_arg(2);
    }
}

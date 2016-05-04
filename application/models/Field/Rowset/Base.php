<?php
class Field_Rowset_Base extends Indi_Db_Table_Rowset {

    /**
     * Table name of table, that current rowset is related to
     *
     * @var string
     */
    protected $_table = 'field';

    /**
     * Contains an array with fields aliases as keys, and their indexes within $this->_rows array as values.
     * Is need for ability to direct fetch a needed row from rowset, by it's alias.
     *
     * @var array
     */
    protected $_indexes = array();

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config) {

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
     * @param bool|string $imploded
     * @return array
     */
    public function column($column, $imploded = false) {

        // If $column argument is 'alias'
        if ($column == 'alias') {

            // Get the values array
            $valueA = array_keys($this->_indexes);

            // Return column data
            return $imploded ? implode(is_string($imploded) ? $imploded : ',', $valueA) : $valueA;

        // Else call ordinary function
        } else return parent::column($column, $imploded);
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
    public function exclude($keys, $type = 'id', $inverse = false) {

        // If $ids argument is not an array, we convert it to it by exploding by comma
        if (!is_array($keys)) $keys = explode(',', $keys);

        // Flip array
        $keys = array_flip($keys);

        // For each item in $this->_original array
        foreach ($this->_rows as $index => $row) {

            // If item id is in exclusion/selection list
            if ($inverse ? !array_key_exists($row->$type, $keys) : array_key_exists($row->$type, $keys)) {

                // Unset row and it's alias
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

    /**
     * Builds an SQL string from an array of clauses, imploded with OR. String will be enclosed by round brackets, e.g.
     * '(`column1` LIKE "%keyword%" OR `column2` LIKE "%keyword%" OR `columnN` LIKE "%keyword%")'. Result string will
     * not contain search clauses for columns, that are involved in building of set of another kind of WHERE clauses -
     * related to grid filters
     *
     * @param $keyword
     * @param $exclude
     * @param $nested
     * @return string
     */
    public function keywordWHERE($keyword, $exclude = array(), array $nested = array()) {

        // Convert quotes and perform an urldecode
        $keyword = str_replace('"', '&quot;', strip_tags(urldecode($keyword)));

        // Explode the keyword by spaces
        $keywordA = explode(' ', $keyword);

        // Remove unfriendly characters from each word within the keyword
        foreach ($keywordA as $i => $keywordI) {
            $keywordI = preg_replace('/[^a-zA-Z0-9а-яА-Я]/u', '', $keywordI);
            $keywordI = trim($keywordI);
            if (!mb_strlen($keywordI, 'utf-8')) unset($keywordA[$i]);
        }

        // Update the keyword, so now we are sure that any word within the keyword doesn't contain unfriendly characters
        $keyword = implode(' ', $keywordA);

        // If keyword is empty, nothing to do here
        if (mb_strlen($keyword, 'utf-8') == 0) return;

        // Clauses stack
        $where = array();

        // Set up info about column types to be available within each grid field
        $this->foreign('columnTypeId');

        // Check each grid field's alias (same as db tabe column name) to ensure it's not in is not in exclusions,
        // and build WHERE clause for each db table column, that is presented in section's grid
        foreach ($this as $fieldR)
            if (!in_array($fieldR->alias, $exclude))
                if ($keywordFieldWHERE = $fieldR->keywordWHERE($keyword))
                    $where[] = $keywordFieldWHERE;

        // Append clauses, for deeper/nested keyword-search, if $nested argument is given
        if ($this[0]) {
            $connector = Indi::model($this[0]->entityId)->table() . 'Id';
            foreach ($nested as $table => $columns) {
                if ($nestedWHERE = Indi::model($table)->fields($columns, 'rowset')->keywordWHERE($keyword)) {
                    $idA = Indi::db()->query('
                        SELECT `' . $connector . '` FROM `block` WHERE ' . $nestedWHERE
                    )->fetchAll(PDO::FETCH_COLUMN);
                    $where[] = count($idA) ? '`id` IN (' . implode(',', $idA) . ')' : 'FALSE';
                }
            }
        }

        // Setup $nonFalseClauseA array
        $nonFALSE = array(); for ($i = 0; $i < count($where); $i++) if ($where[$i] != 'FALSE') $nonFALSE[] = $where[$i];

        // If we have at least one non-FALSE clause - return them all, imploded by 'OR', else if
        // we have only FALSE clauses - return single 'FALSE', otherwise, if we have no clauses at all - return null
        return count($nonFALSE) ? '(' . implode(' OR ', $nonFALSE) . ')' : (count($where) ? 'FALSE' : null);
    }

    /**
     * Merge with another instance of Field_Rowset_Base
     *
     * @param Field_Rowset_Base $rowset
     * @return Field_Rowset_Base|Indi_Db_Table_Rowset
     */
    public function merge(Field_Rowset_Base $rowset) {

        // Call parent
        $this->callParent();

        // Merge indexes
        foreach ($rowset->column('alias') as $alias) $this->_indexes[$alias] = count($this->_indexes);

        // Return itself
        return $this;
    }

    /**
     * Append row to current rowset, using $original argument as the base data for
     * construction of a row, that will be appended
     *
     * @param array $original
     * @return Field_Rowset_Base|Indi_Db_Table_Rowset|Field_Rowset
     */
    public function append(array $original) {

        // Push alias into indexes
        $this->_indexes[$original['alias']] = $this->_count;

        // Call parent
        $this->callParent();

        // Return
        return $this;
    }
}
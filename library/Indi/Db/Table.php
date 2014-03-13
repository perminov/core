<?php
class Indi_Db_Table
{
    /**
     * Store the id of entity, related to current model
     *
     * @var string
     */
    protected $_id = 0;

    /**
     * Store the name of database table, related to current model
     *
     * @var string
     */
    protected $_name = '';

    /**
     * Store array of fields, that current model consists from
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Store column name, which is used to detect parent-child relationship between
     * rows within rowset
     *
     * @var string
     */
    protected $_treeColumn = '';

    /**
     * Store array of aliases, related to fields, that can contain evaluable php expressions.
     *
     * @var array
     */
    protected $_evalFields = array();

    /**
     * Class name for row
     *
     * @var string
     */
    protected $_rowClass = 'Indi_Db_Table_Row';

    /**
     * Class name for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Indi_Db_Table_Rowset';

    /**
     * Construct the instance - setup table name, fields, and tree column if exists
     *
     * @param array $config
     */
    public function __construct($config = array()) {

        // Set db table name and db adapter
        $this->_id = $config['id'];

        // Set db table name and db adapter
        $this->_name = strtolower(substr(get_class($this),0,1)) . substr(get_class($this),1);

        // Set fields
        $this->_fields = is_array($config['fields']) ? $config['fields'] : array();

        // Detect tree column name
        $this->_treeColumn = $this->fields($this->_name . 'Id') ? $this->_name . 'Id' : '';
    }

    /**
     * Fetches all rows, according the given criteria
     *
     * @param null|string|array $where
     * @param null|string|array $order
     * @param null|int $count
     * @param null|int $page
     * @param null|int $offset
     * @return Indi_Db_Table_Rowset
     */
    public function fetchAll($where = null, $order = null, $count = null, $page = null, $offset = null) {
        // Build WHERE and ORDER clauses
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        // Build LIMIT clause
        if ($count !== null || $page !== null) {
            $offset = (is_null($page) ? ($count ? 0 : $page) : $count * ($page - 1)) + ($offset ? $offset : 0);
            if ($offset < 0) {
                $count -= abs($offset);
                $offset = 0;
            }
            $limit = $offset . ($count ? ',' : '') . $count;

            // the SQL_CALC_FOUND_ROWS flag
            if (!is_null($page) || !is_null($count)) $calcFoundRows = 'SQL_CALC_FOUND_ROWS ';
        } else {
            $limit = false;
        }

        // Build the query
        $sql = 'SELECT ' . ($limit ? $calcFoundRows : '') . '* FROM `' . $this->_name . '`'
            . ($where ? ' WHERE ' . $where : '')
            . ($order ? ' ORDER BY ' . $order : '')
            . ($limit ? ' LIMIT ' . $limit : '');

        // Fetch data
        $original = Indi::db()->query($sql)->fetchAll();

        // Prepare data for Indi_Db_Table_Rowset object construction
        $data = array(
            'table'   => $this->_name,
            'original'     => $original,
            'rowClass' => $this->_rowClass,
            'found'=> $limit ? current(Indi::db()->query('SELECT FOUND_ROWS()')->fetch()) : count($original),
            'page' => $page
        );

        // Return Indi_Db_Table_Rowset object
        return new $this->_rowsetClass($data);
    }

    /**
     * Get rowset as tree
     *
     * @param null|array|string $where
     * @param null|array|string $order
     * @param null|int $count
     * @param null|int $page
     * @param int $parentId
     * @param int $selected
     * @param null|string $keyword
     * @return Indi_Db_Table_Rowset object
     */
    public function fetchTree($where = null, $order = null, $count = null, $page = null, $parentId = 0, $selected = 0, $keyword = null) {
        // Get raw tree
        $tree = $this->fetchRawTree($order, $where);

        // If we have WHERE clause, we extract values from $tree handled with keys 'tree', 'found' and 'disabledA'
        if ($where) {
            extract($tree);

            // Else we just set $found
        } else {
            $found = count($tree);
        }

        // If we have to deal a keyword search clause we have different behaviour
        if ($keyword) {

            // Check if keyword is a part of color value in format #rrggbb, and if so, we use RLIKE mysql command instead
            // of LIKE, and prepare a special regular expression
            if (preg_match('/^#[0-9a-fA-F]{0,6}$/', $keyword)) {
                $rlike = '^[0-9]{3}' . $keyword . '[0-9a-fA-F]{' . (7 - mb_strlen($keyword, 'utf-8')) . '}$';
                $where[] = '`' . $this->titleColumn() . '` RLIKE "' . $rlike . '"';
            } else {
                $where[] = '`' . $this->titleColumn() . '` LIKE "' . $keyword . '%"';
            }

            // Fetch rows that match $where clause, ant set foundRows
            $foundRs = $this->fetchAll($where, $order, $count, $page);
            $found = $foundRs->found();
            $foundA = $foundRs->toArray();

            // We replace indexes with actual ids in $foundA array
            $tmp = array(); foreach ($foundA as $foundI) {
                $tmp[$foundI['id']] = $foundI;
                unset($foundI);
            }
            $foundA = $tmp;

            // Release memory
            unset($foundRs);

            // Remaining branch counter
            $counter = 0;

            // Array of ids. Rows with that ids should be presented in final results.
            // There rows are - needed rows, found by primary search, and all parents rows for each
            // of needed, up to level 0
            foreach ($foundA as $currentId => $foundI) {
                do {
                    // Counter increment. We do it for having total number of branches
                    // that will be displayed. We will use this number to check if
                    // all needed ids are already got, so there is no more need to
                    // contunue walking through tree. This check will be performed
                    // within the process of getting final list of needed ids
                    // Also, we perform additonal check before $counter increment,
                    // because we need count of unique branches ids, because search results
                    // may have same parents
                    if (!$tree[$currentId][2]) $counter++;

                    // We mark branch of global tree with 2 ways:
                    // If current branch is a result of primary search, we mark it with 1 at index 2
                    // Else if it is a one of the parent branches - we mark it with 2 at index 2
                    // This need because in the results (grid rows or dropdown items) we should visually separate
                    // results of primary search and their parents, because parents should not be clickable,
                    // or have other abilities, because they are NOT a results actually, they are displaying
                    // just for visual recognition of results of primary search, and recognition of their parents
                    // Also, we should use integer indexation instead of string (eg $tree[$currentId][2],
                    // not $tree[$currentId]['mark']) because size of trees can be very large, and we should
                    // do all this things using a way, that use mininum memory
                    $tree[$currentId][2] = $foundA[$currentId] ? 1 : 2;

                    // Remember indents
                    $indents[$currentId] = Misc::indent($tree[$currentId][1]);
                } while ($currentId = $tree[$currentId][0]);
            }

            // Get the final list of needed ids
            $i = 0;
            $ids = array();
            $disabledA = array();
            foreach ($tree as $id => $info) {
                if ($info[2]) {

                    // Collect all (primary and auxillary) results rows ids
                    $ids[] = $id;
                    $i++;

                    // Remember id of rows that should be disabled (auxillary results)
                    if ($info[2] == 2) $disabledA[] = $id;

                    // Break loop if known count of ids is already got
                    if ($i == $counter) break;
                }
            }

            // Standard behaviour
        } else {

            // If $selected argument is specified, we should return page of results, containing that selected branch,
            // so we should calculate needed page number, and replace $page argument with calculated value
            // Also, while retrieving upper and lower page (than page with selected vaue) results, we use $selected
            // argument as start point for distance and scope calculations
            if ($selected && $found > Indi_Db_Table_Row::$comboOptionsVisibleCount){

                // Get index of selected branch in raw tree
                $i = 0;
                foreach ($tree as $id => $info) {
                    if ($id == $selected)  {
                        $start = $i + ($page ? $page * $count: 0);
                        $selectedIndex = $i;

                        // If we are trying to retrieve upper pages (upper than initial page containing selected value) of results
                        // we need to remember start point, that would be if we would like to get previous page results.
                        // Previous mean that is a one page upper than page with selected value.
                        // We will need this 'previous' start point to properly calculate 'current' start and end point shifts
                        // regarding to possibility of some options to be disabled
                        if ($page < 0) {
                            $prevStart = $start + $count;
                        }
                        break;
                    }
                    unset($id, $info);
                    $i++;
                }

                // Here we calculate $shiftUp, that will be used to adjust page start and end points for 'current' page
                if ($page < 0) {
                    $k = 0;
                    $shiftUp = 0;
                    foreach ($tree as $id => $info) {
                        // Bottom border of range of page results
                        if ($k < $i) {
                            // Top border of range of page results
                            if ($k >= $prevStart) {
                                if ($disabledA[$id]) {
                                    $shiftUp++;
                                }
                            }
                            unset($id, $info);
                        } else break;
                        $k++;
                    }
                }
            }

            // Get list of ids, related to current page of results
            if (isset($start)) {
                $end = $start + $count;
            } else {
                if ($count !== null || $page !== null) {
                    $start = (is_null($page) ? 0 : $count * ($page - 1));
                    $end = $start + $count;
                } else {
                    $start = 0;
                    $end = count($tree);
                }
            }

            $ids = array();
            $i = 0;

            // Declare ids history
            $idsHistory = array();

            foreach ($tree as $id => $info) {
                // Push in idsHistory
                $idsHistory[$i] = $id;

                // Bottom border of range of page results
                if ($i < $end) {

                    // Top border of range of page results
                    if ($i >= $start) {

                        // If we were doing pageUp, and while retrieving results we faced disabled options
                        // we should simulate $start decremention. One disabled = one additional shift upper of start point,
                        // so we will shift start point upper until we face non-disabled option. The reason of this that we
                        // need to get certain number of NON-DISABLED options, not certain number of NOT-MATTER-DISABLED-OR-ENABLED
                        // options
                        if ($page < 0 && $disabledA[$id]) {
                            $ids = array_reverse($ids);
                            do {
                                $start--;
                                $prevId = $idsHistory[$start];
                                $ids[] = $prevId;
                                $indents[$prevId] = Misc::indent($tree[$prevId][1]);
                            } while ($disabledA[$prevId]);
                            $ids = array_reverse($ids);
                        }

                        // Normal appending
                        $ids[] = $id;
                        $indents[$id] = Misc::indent($tree[$id][1]);

                        // We shift end point because disabled items should be ignored
                        if ($disabledA[$id] && (is_null($page) || $page > 0)) $end++;

                        // If we have not yet reached start point but faced a disabled option
                        // we shift both start and end points because disabled items should be ignored
                        // and start and end points of page range should be calculated with taking in attention
                        // about disabled options.
                    } else if ($disabledA[$id] && (is_null($page) || $page > 0)) {
                        if (!$selected || $i >= $selectedIndex) {
                            $start++;
                            $end++;
                        }
                    }
                    $i++;
                } else {
                    unset($idsHistory);
                    break;
                }
                unset($id, $info);
            }
        }

        // Construct a WHERE and ORDER clauses for getting that particular
        // page of results, get it, and setup nesting level indents
        $wo = 'FIND_IN_SET(`id`, "' . implode(',', $ids) . '")';
        $data = $this->fetchAll($wo, $wo)->toArray();
        $assocDataA = array();
        for ($i = 0; $i < count($data); $i++) {
            $assocDataI = $data[$i];
            //$assocDataI['indent'] = $indents[$data[$i]['id']];
            $assocDataI['_system']['indent'] = $indents[$data[$i]['id']];
            $assocDataA[$data[$i]['id']] = $assocDataI;
        }
        $data = $assocDataA;
        unset($assocDataA);


        // Set 'disabled' system property for results that should have such a property
        if (is_array($disabledA)) {
            // Here we replace $disabledA values with it's keys, as we have no more need
            // to store info about disabled in array keys instead of store it in array values
            // We need to do this replacement only if we are not running keyword-search, because
            // if we are, disabled array is already filled with ids as values, not keys
            if (!$keyword) $disabledA = array_keys($disabledA);

            // We setup 'disabled' property only for rows, which are to be returned
            foreach ($disabledA as $disabledI) if ($data[$disabledI]) $data[$disabledI]['_system']['disabled'] = true;
        }

        // Set 'parentId' system property. Despite of existence of parent branch identifier in list of properties,
        // we additionally set up this property as system property using static 'parentId' key, (e.g $row->system('parentId'))
        // instead of using $row->{$row->model()->treeColumn()} expression. Also, this will be useful in javascript, where
        // we have no ability to use such an expressions.
        foreach ($data as $id => $props) $data[$id]['_system']['parentId'] = $props[$this->_treeColumn];

        $system = array();
        foreach ($data as $id => $props) {
            $system[$id] = $data[$id]['_system'];
            unset($data[$id]['_system']);
        }


        // Setup rowset info
        $data = array (
            'table' => $this->_name,
            'original' => array_values($data),
            'system' => $system,
            'rowClass' => $this->_rowClass,
            'found' => $found,
            'page' => $page
        );

        // Return rowset
        return new $this->_rowsetClass($data);
    }

    /**
     * Fetches a full tree of items, but it will
     * retrieve only `id` and `treeColumn` columns
     *
     * @param null|string|array $order
     * @param null|string|array $where
     * @return array
     */
    public function fetchRawTree($order = null, $where = null) {
        // ORDER clause
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        // Get tree column name
        $tc = $this->_name . 'Id';

        // Construct sql query
        $query = 'SELECT `id`, `' . $tc . '` FROM `' . $this->_name . '`';
        $query .= ($order ? ' ORDER BY ' . $order : '');

        // Get general tree data for whole table, but only `id` and `treeColumn` columns
        $tree = Indi::db()->query($query)->fetchAll();
        $nested = array();
        foreach ($tree as $item) {
            $nested[$item[$tc]][] = $item;
            unset($item);
        }

        // Release memory
        unset($tree);

        // Re-setup tree
        $tree = $this->_append(0, array(), $nested, 0);

        // Release memory
        unset($nested);

        // Then we get an associative array, where keys are ids, and values are arrays containing from parent ids and levels
        $return = array(); for ($i = 0; $i < count($tree); $i++) $return[$tree[$i]['id']] = array($tree[$i][$tc], $tree[$i]['level']);

        // Release memory
        unset($tree);

        // If we have WHERE clause, we should filter tree so there should remain only needed branches
        if ($where) {

            // Needed branches can be two categories:
            // 1. Branches that directly match WHERE clause (primary results)
            // 2. Branches that do not, but that are parent to branches mentioned in point 1 (disabled results)

            // First we should find primary results
            $primary = array();
            if (is_array($where) && count($where)) $where = implode(' AND ', $where);
            $foundA = Indi::db()->query('SELECT `id` FROM `' . $this->_name . '` WHERE ' . $where)->fetchAll();
            foreach ($foundA as $foundI) {
                $primary[$foundI['id']] = true;
                unset($foundI);
            }

            // Get found rows
            $found = count($primary);

            // Then we should find disabled results
            $disabled = array();
            foreach ($primary as $id => $true) {
                $parentId = $return[$id][0];
                while ($parentId) {
                    // We mark branch as disabled only if it is not primary
                    if (!$primary[$parentId]) {
                        $disabled[$parentId] = true;
                    }
                    $parentId = $return[$parentId][0];
                }
                unset($id, $true);
            }

            // Get final tree
            $tmp = array();
            foreach ($return as $id => $data) if ($primary[$id] || $disabled[$id]) {
                $tmp[$id] = $data;
                unset($id, $data);
            }

            // Release memory
            unset($return, $primary);

            // Return array(data, foundRows)
            return array('tree' => $tmp, 'found' => $found, 'disabledA' => $disabled);
        } else {

            // Return array
            return $return;
        }

    }

    /**
     * Recursively create a rows tree
     *
     * @param $parentId
     * @param $data
     * @param $nested
     * @param int $level
     * @param bool $recursive
     * @return mixed
     */
    protected function _append($parentId, $data, $nested, $level = 0, $recursive = true) {
        if (is_array($nested[$parentId])) foreach ($nested[$parentId] as $item) {
            $item['level'] = $level;
            $id = $item['id'];
            $data[] = $item;
            unset($item);
            if ($recursive) $data = $this->_append($id, $data, $nested, $level + 1);
        }
        return $data;
    }

    public function titleColumn(){
        // Get array of existing columns
        $existing = $this->fields(null, 'cols');

        // Check if `title` column already exists
        if (in_array('title', $existing)) {
            $this->titleColumn = 'title';

        // Else create it
        } else {
            $fieldR = Indi::model('Field')->createRow();
            $fieldR->entityId = Indi::model('Entity')->fetchRow('`table` = "' . $this->_name . '"')->id;
            $fieldR->title = 'Auto title';
            $fieldR->alias = 'title';
            $fieldR->storeRelationAbility = 'none';
            $fieldR->columnTypeId = 1;
            $fieldR->elementId = 1;
            $fieldR->save();
            $this->titleColumn = 'title';
        }
        return $this->titleColumn;
    }

    /**
     * Detect index of certain row in a ordered scope of rows. Offset is 1-based, unlike mysql OFFSET
     *
     * @param $where
     * @param $order
     * @param $id
     * @return int
     */
    public function detectOffset($where, $order, $id) {
        // Prepare WHERE and ORDER clauses
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        // Offset variable
        Indi::db()->query('SET @o=0;');

        // Random temporary table name. We should ensure that there will be no table with such name
        $tmpTableName = 'offset' . rand(2000, 8000);

        // We are using a temporary table to place data into it, and the get of offset
        Indi::db()->query($sql = '
            CREATE TEMPORARY TABLE `' . $tmpTableName . '`
            SELECT @o:=@o+1 AS `offset`, `id`="' . $id . '" AS `found`
            FROM `' . $this->_name .'`'
                . ($where ? ' WHERE ' . $where : '')
                . ($order ? ' ORDER BY ' . $order : '')
        );

        // Get the offset
        $offset = Indi::db()->query('
            SELECT `offset`
            FROM `' . $tmpTableName . '`
            WHERE `found` = "1"'
        )->fetchColumn(0);

        // Unset offset variable
        Indi::db()->query('SET @o=null;');

        // Truncate temporary table
        Indi::db()->query('DROP TABLE `' . $tmpTableName . '`');

        // Return
        return $offset;
    }

    /**
     * Provide readonly access to _evalFields property
     *
     * @return array
     */
    public function getEvalFields() {
        return $this->_evalFields;
    }

    /**
     * Return incremented by 1 maximum value of `move` column within current database table
     *
     * @return int
     */
    public function getNextMove() {
        return $this->fetchRow('`move` != "0"', '`move` DESC')->move + 1;
    }

    /**
     * Return row of certain field, related to current entity, if $name argument is specified
     * and contains only one field name. If $names argument contains comma-separated field names,
     * function will return a Indi_Db_Table_Rowset object, containing all found fields
     * or return all fields within current model. Second argument - $format, is applied only if $names argument
     * is empty|null. If $format = 'cols', array of field aliases will be returned, else if $format = 'rowset',
     * fields info will be returned as Indi_Db_Table_Rowset object
     *
     * @param string $names
     * @param string $format rowset|cols
     * @return Indi_Db_Table_Row|Indi_Db_Table_Rowset
     */
    public function fields($names = '', $format = 'rowset') {
        // If $name argument was not given
        if ($names == '') {

            // If $format argument == 'rowset' - return all fields as Indi_Db_Table_Rowset object
            if ($format == 'rowset')
                return Indi::model('Field')->createRowset(array(
                    'original' => array_values($this->_fields)
                ));

            // Else if $format == 'cols' - return all fields aliases as array
            else if ($format == 'cols')
                return array_keys($this->_fields);

            // Else if $name argument is presented, and it contains only one field name
        } else if (!preg_match('/,/', $names)){

            // Return certain field as Indi_Db_Table_Row object, if found
            if (array_key_exists($names, $this->_fields)) {
                return Indi::model('Field')->createRow(array(
                    'original' => $this->_fields[$names]
                ));
            }

            // Else if $name argument contains several field names
        } else {

            // Convert field names list to array
            $nameA = explode(',', $names);

            // Declare $found array
            $found = array();

            // Fore each field name
            foreach ($nameA as $nameI) {

                // Remove whitespaces
                $nameI = trim($nameI);

                // If field was found, append it to $found array
                if (array_key_exists($nameI, $this->_fields)) {
                    $found[$nameI] = $this->_fields[$nameI];
                }
            }

            // If $format argument is set to 'rowset'
            if ($format == 'rowset')

                // Return all found fields as Indi_Db_Table_Rowset object
                return Indi::model('Field')->createRowset(array(
                    'original' => array_values($found)
                ));

            // Else if $format is set to 'cols', array if field aliases will be returned
            else if ($format == 'cols') return array_keys($found);

        }
    }

    /**
     * Return array containing some basic info about model. Currently it is only database table name
     *
     * @return array
     */
    public function toArray() {
        $array['tableName'] = $this->_name;
        return $array;
    }

    /**
     * Return an array consisting of all values of a single $column column from the result set
     *
     * @param $column
     * @param null|string|array $where
     * @param null|string|array $order
     * @param null|int $count
     * @param null|int $page
     * @param null|int $offset
     * @return array
     */
    public function fetchColumn($column, $where = null, $order = null, $count = null, $page = null, $offset = null) {

        // Build WHERE and ORDER clauses
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        // Build LIMIT clause
        if ($count !== null || $page !== null) {
            $offset = (is_null($page) ? ($count ? 0 : $page) : $count * ($page - 1)) + ($offset ? $offset : 0);
            if ($offset < 0) {
                $count -= abs($offset);
                $offset = 0;
            }
            $limit = $offset . ($count ? ',' : '') . $count;

        } else {
            $limit = false;
        }

        // Build the query
        $sql = 'SELECT `' . $column . '` FROM `' . $this->_name . '`'
            . ($where ? ' WHERE ' . $where : '')
            . ($order ? ' ORDER BY ' . $order : '')
            . ($limit ? ' LIMIT ' . $limit : '');

        // Fetch and return result
        return $data = Indi::db()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Return the name of database table, related to current model
     *
     * @return string
     */
    public function name(){
        return $this->_name;
    }

    /**
     * Fetches one row in an object of type Indi_Db_Table_Row,
     * or returns null if no row matches the specified criteria.
     *
     * @param null|array|string $where
     * @param null|array|string $order
     * @param null|int $offset
     * @return null|Indi_Db_Table_Row object
     */
    public function fetchRow($where = null, $order = null, $offset = null) {
        // Build WHERE and ORDER clauses
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        // Build query, fetch row and return it as an Indi_Db_Table_Row object
        if ($data = Indi::db()->query(
            'SELECT * FROM `' . $this->_name . '`' .
                ($where ? ' WHERE ' . $where : '') .
                ($order ? ' ORDER BY ' . $order : '') .
                ($offset ? ' LIMIT ' . $offset . ',1' : '')
        )->fetch()) {

            // Release memory
            unset($where, $order, $offset);

            // Prepare data for Indi_Db_Table_Row object construction
            $constructData = array(
                'table'    => $this->_name,
                'original' => $data,
            );

            // Release memory
            unset($data);

            // Load class if need
            if (!class_exists($this->_rowClass)) {
                require_once 'Indi/Loader.php';
                Indi_Loader::loadClass($this->_rowClass);
            }

            // Construct and return Indi_Db_Table_Row object
            return new $this->_rowClass($constructData);
        }

        // NULL return
        return null;
    }

    /**
     * Create empty row
     *
     * @param array $input
     * @return Indi_Db_Table_Row object
     */
    public function createRow($input = array()) {

        // Prepare data for construction
        $constructData = array(
            'table'   => $this->_name,
            'original'     => is_array($input['original']) ? $input['original'] : array(),
            'modified' => is_array($input['modified']) ? $input['modified'] : array()
        );

        // Get row class name
        $rowClass = $this->rowClass();

        // Load row class if need
        if (!class_exists($rowClass)) {
            require_once 'Indi/Loader.php';
            Indi_Loader::loadClass($rowClass);
        }

        // Construct and return Indi_Db_Table_Row object
        return new $rowClass($constructData);
    }

    /**
     * Create Indi_Db_Table_Rowset object with some data, if passed
     *
     * @param array $input
     * @return mixed
     */
    public function createRowset($input = array()) {

        // Prepare data for Indi_Db_Table_Rowset object construction
        $data = array(
            'table'   => $this->_name,
            'original'     => is_array($input['original']) ? $input['original'] : array(),
            'rowClass' => $this->_rowClass,
            'found'=> isset($input['found'])
                ? $input['found']
                : (is_array($input['original']) ? count($input['original']) : 0)
        );

        // Construct and return Indi_Db_Table_Rowset object
        return new $this->_rowsetClass($data);
    }

    /**
     * Returns row class name
     *
     * @return string
     */
    public function rowClass()
    {
        return $this->_rowClass;
    }

    /**
     * Returns rowset class name
     *
     * @return string
     */
    public function rowsetClass()
    {
        return $this->_rowsetClass;
    }

    /**
     * Delete all rows from current database table, that match given WHERE clause
     *
     * @param $where
     * @return int Number of affected rows
     * @throws Exception
     */
    public function delete($where) {
        // Basic SQL expression
        $sql = 'DELETE FROM `' . $this->_name . '`';

        // If $where argument is specified, append it as string to basic SQL expression
        if ($where) {

            // Get WHERE clause as string
            if (is_array($where) && count($where)) $where = implode(' AND ', $where);

            // Append WHERE clause to basic expression
            $sql .= ' WHERE ' . $where;

            // Execute the query
            return Indi::db()->query($sql);

            // Otherwise throw an exception, to avoid deleting all database table's rows
        } else {
            throw new Exception('No WHERE clause');
        }
    }

    /**
     * Return tree column name
     *
     * @return string
     */
    public function treeColumn()
    {
        return $this->_treeColumn;
    }

    /**
     * Return id of entity, that current model is representing
     *
     * @return string
     */
    public function id()
    {
        return $this->_id;
    }

    /**
     * Inserts new row into db table
     *
     * @param array $data
     * @return string
     */
    public function insert($data) {

        // Get existing fields
        $fieldRs = $this->fields();

        // Build the first part of sql expression
        $sql = 'INSERT INTO `' . $this->_name . '` SET ';

        // Declare array for sql SET statements
        $setA = array();

        // Foreach field within existing fields
        foreach ($fieldRs as $fieldR) {

            // We will insert values for fields, that are actually exist in database table structure
            if ($fieldR->columnTypeId) {

                // If current field alias is one of keys within data to be inserted,
                // and if data's value for that field alias is not null
                if (!is_null($data[$fieldR->alias])) {

                    // We append value with related field alias to $set array
                    $setA[] = '`' . $fieldR->alias . '` = "' . str_replace('"', '\"', $data[$fieldR->alias]) .'"';

                    // Else if column type is TEXT, we use field's default value as value for insertion
                } else if ($fieldR->foreign('columnTypeId')->type == 'TEXT')
                    $setA[] = '`' . $fieldR->alias . '` = "' . str_replace('"', '\"', $fieldR->defaultValue) .'"';

            }
        }

        // Append imploded values from $set array to sql query, or append `id` = NULL expression, if no items in $set
        $sql .= count($setA) ? implode(', ', $setA) : '`id` = NULL';

        // Run the query
        Indi::db()->query($sql);

        // Return the id of inserted row
        return Indi::db()->getPDO()->lastInsertId();
    }

    /**
     * Update one or more db table columns within rows matching WHERE clause, specified by $where param
     *
     * @param array $data
     * @param string $where
     * @return int
     * @throws Exception
     */
    public function update(array $data, $where = '') {

        // Check if $data array is not empty
        if (count($data)) {

            // Get existing fields
            $fieldRs = $this->fields();

            // Build the first part of sql expression
            $sql = 'UPDATE `' . $this->_name . '` SET ';

            // Declare array for sql SET statements
            $setA = array();

            // Foreach field within existing fields
            foreach ($fieldRs as $fieldR) {

                // We will update values for fields, that are actually exist in database table structure
                if ($fieldR->columnTypeId) {

                    // If current field alias is one of keys within data to be updated,
                    if (array_key_exists($fieldR->alias, $data))

                        // We append value with related field alias to $set array
                        $setA[] = '`' . $fieldR->alias . '` = "' . str_replace('"', '\"', $data[$fieldR->alias]) .'"';
                }
            }

            // Append comma-imploded items of $setA array to sql query
            $sql .= implode(', ', $setA);

            // If $where argument was specified
            if ($where) {

                // Append it to sql query
                if (is_array($where) && count($where)) $where = implode(' AND ', $where);
                $sql .= ' WHERE ' . $where;
            }

            // Execute query and return number of affected rows
            return Indi::db()->query($sql);
        }
    }
}
<?php
class Indi_Db_Table_Beautiful extends Indi_Db_Table_Abstract{

    /**
     * Fetches all rows.
     *
     * @param string|array $where            OPTIONAL An SQL WHERE clause.
     * @param string|array $order            OPTIONAL An SQL ORDER clause.
     * @param int          $count            OPTIONAL An SQL LIMIT count.
     * @param int          $page             OPTIONAL page number
     * @return Indi_Db_Table_Rowset
     */
    public function fetchAll($where = null, $order = null, $count = null, $page = null, $offset = null) {
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);

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

        $sql = 'SELECT ' . ($limit ? $calcFoundRows : '') . '* FROM `' . $this->_name . '`'
            . ($where ? ' WHERE ' . $where : '')
            . ($order ? ' ORDER BY ' . $order : '')
            . ($limit ? ' LIMIT ' . $limit : '');

        $data = self::$_defaultDb->query($sql)->fetchAll();
        $data = array(
            'table'   => $this,
            'data'     => $data,
            'rowClass' => $this->_rowClass,
            'foundRows'=> $limit ? current(self::$_defaultDb->query('SELECT FOUND_ROWS()')->fetch()) : count($data),
            'page' => $page
        );

        return new $this->_rowsetClass($data);
    }

    /**
     * Get rowset as tree
     *
     * @param null $where
     * @param null $order
     * @param null $count
     * @param null $page
     * @param int $parentId
     * @return Indi_Db_Table_Rowset object
     */
    public function fetchTree($where = null, $order = null, $count = null, $page = null, $parentId = 0, $selected = 0, $keyword = null) {
        // Get raw tree
        $tree = $this->fetchRawTree($order, $where);

        // If we have WHERE clause, we extract values from $tree handled with keys 'tree', 'foundRows' and 'disabledA'
        if ($where) {
            extract($tree);

        // Else we just set $foundRows
        } else {
            $foundRows = count($tree);
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
            $foundRows = $foundRs->foundRows;
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
            if ($selected && $foundRows > Indi_Db_Table_Row_Beautiful::$comboOptionsVisibleCount){

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

            // Adjust points with shift
            //$end -= $shiftUp;
            //$start -= $shiftUp;

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
            $assocDataI['indent'] = $indents[$data[$i]['id']];
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
        // instead of using $row->{$row->getTable()->treeColumn} expression. Also, this will be useful in javascript, where
        // we have no ability to use such an expressions.
        foreach ($data as $id => $props) $data[$id]['_system']['parentId'] = $props[$this->treeColumn];

        // Setup rowset info
        $data = array (
            'table' => $this,
            'data' => array_values($data),
            'rowClass' => $this->_rowClass,
            'foundRows' => $foundRows,
            'page' => $page,
            'treeColumn' => $this->treeColumn
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
        $tree = $this->_db->query($query)->fetchAll();
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
            $foundA = $this->getAdapter()->query('SELECT `id` FROM `' . $this->_name . '` WHERE ' . $where)->fetchAll();
            foreach ($foundA as $foundI) {
                $primary[$foundI['id']] = true;
                unset($foundI);
            }

            // Get found rows
            $foundRows = count($primary);

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
            return array('tree' => $tmp, 'foundRows' => $foundRows, 'disabledA' => $disabled);
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
        $existing = $this->getFields();

        // Check if `title` column already exists
        if (in_array('title', $existing)) {
            $this->titleColumn = 'title';

        // Check if `_title` column already exists
        } else if (in_array('_title', $existing)) {
            $this->titleColumn = '_title';

            // Initialize newly created column with value
            /*$rs = $this->fetchAll();
            foreach ($rs as $r) {
                $r->_title = $r->getTitle();
                $r->save();
                unset($r);
            }*/

        // If not
        } else {
            // Create it
            $fieldR = Misc::loadModel('Field')->createRow();
            $fieldR->entityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $this->_name . '"')->id;
            $fieldR->title = 'System title';
            $fieldR->alias = '_title';
            $fieldR->storeRelationAbility = 'none';
            $fieldR->columnTypeId = 1;
            $fieldR->elementId = 22;
            $fieldR->save();

            // Initialize newly created column with value
            $rs = $this->fetchAll();
            foreach ($rs as $r) {
                $r->_title = $r->getTitle();
                $r->save();
                unset($r);
            }
            $this->titleColumn = '_title';
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
        $this->getAdapter()->query('SET @o=0;');

        // Random temporary table name. We should ensure that there will be no table with such name
        $tmpTableName = 'offset' . rand(2000, 8000);

        // We are using a temporary table to place data into it, and the get of offset
        $this->getAdapter()->query($sql = '
            CREATE TEMPORARY TABLE `' . $tmpTableName . '`
            SELECT @o:=@o+1 AS `offset`, `id`="' . $id . '" AS `found`
            FROM `' . $this->info('name') .'`'
             . ($where ? ' WHERE ' . $where : '')
             . ($order ? ' ORDER BY ' . $order : '')
        );

        // Get the offset
        $offset = $this->getAdapter()->query('
            SELECT `offset`
            FROM `' . $tmpTableName . '`
            WHERE `found` = "1"'
        )->fetchColumn(0);

        // Unset offset variable
        $this->getAdapter()->query('SET @o=null;');

        // Truncate temporary table
        $this->getAdapter()->query('DROP TABLE `' . $tmpTableName . '`');

        // Return
        return $offset;
    }
}
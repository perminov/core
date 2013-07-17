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
    public function fetchAll($where = null, $order = null, $count = null, $page = null)
    {
        if (is_array($where) && count($where)) $where = implode(' AND ', $where);
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        if ($count !== null || $page !== null) {
            $limit = (is_null($page) ? ($count ? '0' : $page) : $count * ($page - 1)) . ($count ? ',' : '') . $count;

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
    public function fetchTree($where = null, $order = null, $count = null, $page = null, $parentId = 0, $selected = 0)
    {
        // Get raw tree
        $tree = $this->fetchRawTree($order, $parentId);

        /*$this->getAdapter()->query(
            'UPDATE `' . $this->_name . '` SET `level` = `move`'
        );

        i($tree);
        $i = 1;
        foreach ($tree as $id => $branch) {
            $this->getAdapter()->query(
                'UPDATE `' . $this->_name . '` SET `move` = "' . $i . '" WHERE `id` = "' . $id . '"'
            );
            $i++;
        }*/
        // If where was a WHERE clause we have different behaviour
        if ($where) {
            // Fetch rows that match $where clause, ant set foundRows
            $foundRs = $this->fetchAll($where, $order, $count, $page);
            $foundRows = $foundRs->foundRows;
            $foundA = $foundRs->toArray();

            // We replace indexes with actual ids in $foundA array
            $tmp = array(); foreach ($foundA as $foundI) $tmp[$foundI['id']] = $foundI; $foundA = $tmp;

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
            if ($selected){
                // Get index of selected branch in raw tree
                $i = 0;
                foreach ($tree as $id => $info) {
                    if ($id == $selected)  {
                        $start = $i + ($page ? $page * $count: 0);
                        break;
                    }
                    $i++;
                }
            }

            // Set total results
            $foundRows = count($tree);

            // Get list of ids, related to current page of results, and remember indents
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
            foreach ($tree as $id => $info) {
                if ($i < $end) {
                    if ($i >= $start) {
                        $ids[] = $id;
                        $indents[$id] = Misc::indent($tree[$id][1]);
                    }
                    $i++;
                } else break;
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

        // Set 'disabled' system property for auxillary results
        if (is_array($disabledA)) foreach ($disabledA as $disabledI) $data[$disabledI]['_system']['disabled'] = true;

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
     * @return array
     */
    public function fetchRawTree($order = null) {
        // WHERE and ORDER clauses
        if (is_array($order) && count($order)) $order = implode(', ', $order);

        // Get tree column name
        $tc = $this->_name . 'Id';

        // Construct sql query
        $query = 'SELECT `id`, `' . $tc . '` FROM `' . $this->_name . '`';
        $query .= ($order ? ' ORDER BY ' . $order : '');

        // Get general tree data for whole table, but only `id` and `treeColumn` columns
        $tree = $this->_db->query($query)->fetchAll();
        $nested = array();
        foreach ($tree as $item) $nested[$item[$tc]][] = $item;
        $tree = $this->_append(0, array(), $nested, 0);

        // Then we get an associative array, where keys are ids, and values are arrays containing from parent ids and levels
        $return = array(); for ($i = 0; $i < count($tree); $i++) $return[$tree[$i]['id']] = array($tree[$i][$tc], $tree[$i]['level']);

        // Return array
        return $return;
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
            $data[] = $item;
            if ($recursive) $data = $this->_append($item['id'], $data, $nested, $level + 1);
        }
        return $data;
    }

}
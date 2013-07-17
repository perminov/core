<?php
/**
 * This is a temporary class used to separate good-looking php-code from bad-looking in Indi/Db/Table/Row.php
 * Good looking mean that it have proper coding style, doc blocks and other stuff
 * This class will be renamed to Indi/Db/Table/Row.php after all methods in current Indi/Db/Table/Row.php will become
 * good looking
 */
class Indi_Db_Table_Row_Beautiful extends Indi_Db_Table_Row_Abstract{

    /**
     * Default count of options that will be fetched
     *
     * @var int
     */
    public $comboOptionsVisibleCount = 20;

    /**
     * Store regular expression for checks of email addresses validity
     *
     * @var string
     */
    public $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
	
    /**
     * Saves row into database table. But.
     * Preliminary checks if row has a `move` field in it's structure and if row is not an existing row yet
     * (but is going to be inserted), and if so - autoset value for `move` column after row save
     *
     * @return int affected rows|last_insert_id
     */
    public function save() {
        if (!$this->_original['id'] && array_key_exists('move', $this->_original) && !$this->move) $orderAutoSet = true;
        $return = parent::save();
        if ($orderAutoSet) {
            $this->move = $this->id;
            parent::save();
        }
        return $return;
    }

    /**
     * Provide Move up/Move down actions for row within the needed area of rows
     *
     * @param string $direction (up|down)
     * @param string $within
     * @param string $condition
     */
    public function move($direction = 'up', $within = '', $condition = null) {
        // Check direction validity
        if (in_array($direction, array('up', 'down'))) {

            // Array of WHERE clause items
            $where = array();

            // Adding conditions required to match the needed scope, there changeRow should be searched
            if (!is_array($within) && $within) $within = explode(',', $within);
            // Append tree-column to $within array, if such column exists
            if (array_key_exists($this->getTable()->info('name') . 'Id', $this->_original)) $within[] = $this->getTable()->info('name') . 'Id';

            for ($i = 0; $i < count($within); $i++) $where[] = '`' . trim($within[$i]) . '` = "' . $this->{trim($within[$i])} . '"';

            // Adding custom condition
            if (is_array($condition) && count($condition)) $where = array_merge($where, $condition);
            else if ($condition) $where[] = $condition;

            // Nearest neighbour clauses
            $where[] = '`move` ' . ($direction == 'up' ? '<' : '>') . ' "' . $this->move . '"';
            $order = 'move ' . ($direction == 'up' ? 'DE' : 'A') . 'SC';

            // Find
            if ($changeRow = $this->getTable()->fetchRow($where, $order)) {
                // Backup `move` of current row
                $backup = $this->move;

                // We exchange values of `move` fields
                $this->move = $changeRow->move;
                $this->save(true);
                $changeRow->move = $backup;
                $changeRow->save(true);
            }
        }
    }

    /**
     * Fully deletion - including attached files and foreign key usages, if will be found
     *
     * @return int Number of deleted rows (1|0)
     */
    public function delete(){
        // Delete all files and images that have been attached to row
        $this->deleteUploadedFiles();

        // Delete other rows of entities, that have fields, related to entity of current row
        // This function also covers other situations, such as if entity of current row has a tree structure,
        // or row has dependent rowsets
        $this->deleteForeignKeysUsages();

        // Standard Indi_Db_Table_Row deletion
        return parent::delete();
    }

    public function getComboData($field, $page = null, $selected = null, $selectedTypeIsKeyword = false){
        $entityM = Misc::loadModel('Entity');
        $fieldM = Misc::loadModel('Field');
        $entityR = $entityM->fetchRow('`table` = "' . $this->_table->_name . '"');
        $fieldR = $fieldM->fetchRow('`entityId` = "' . $entityR->id . '" AND `alias` = "' . $field . '"');
        $relatedM = Entity::getInstance()->getModelById($fieldR->relation);

        $where = array();

        // Setup filter, as one of possible parts of WHERE clause
        if ($fieldR->filter) {
            if (preg_match('/(\$|::)/', $fieldR->filter)) {
                eval('$fieldR->filter = \'' . $fieldR->filter . '\';');
            }
            $where[] = $fieldR->filter;
        }
i($where);
        // Set ORDER clause for combo data
        if ($relatedM->fieldExists('move')) {
            $order = 'move';
        } else if ($relatedM->fieldExists('title')) {
            $order = 'title';
        } else {
            $order = null;
        }

        // If fetch-mode is 'keyword'
        if ($selectedTypeIsKeyword) {
            $keyword = $selected;

        // Else if fetch-mode is 'no-keyword'
        } else {

            // Get selected row
            $selectedR = $relatedM->fetchRow('`id` = "' . $selected . '"');

            // Setup title as start point (title can be title of selected row, or can be keyword)
            $keyword = str_replace('"','\"', $selectedR->title);
        }

        // If related entity has tree-structure
        if ($relatedM->treeColumn) {

            // If we go lower, page number should be incremented, so if passed page number
            // is 1, it will be 2, because actually results of page 1 were already fetched
            // and displayed at the stage of combo first initialization
            if ($page != null) {
                if(!$selected || $selectedTypeIsKeyword) $page++;

                // Page number is not null when we are paging, and this means that we are trying to fetch
                // more results that are upper or lower and start point for paging ($selected) was not changed.
                // So we mark that foundRows property of rowset should be unset, as in combo.js 'page-top-reached'
                // attribute is set depending on 'found' property existence in response json
                $unsetFoundRows = true;
            }

            // Fetch results
            if ($selectedTypeIsKeyword) {
                $where[] = '`title` LIKE "' . $keyword . '%"';
                $dataRs = $relatedM->fetchTree($where, $order, $this->comboOptionsVisibleCount, $page, 0);
            } else {
                $dataRs = $relatedM->fetchTree($where, $order, $this->comboOptionsVisibleCount, $page, 0, $selected);
            }

            // Unset found rows to prevent disabling of paging up
            if ($unsetFoundRows) unset($dataRs->foundRows);

            // Return result
            return $dataRs;

        // Otherwise
        } else {

            // If we selected option is set, or if we have keyword that results should match, special logic will run
            if ($selected) {

                // We do a backup for WHERE clause, because it's backup version
                // will be used to calc foundRows property in case if $selectedTypeIsKeyword = false
                $whereBackup = $where;

                // Get WHERE clause for options fetch
                $where = $where ? explode(' AND ', $where) : array();
                if ($selectedTypeIsKeyword) {
                    $order = 'POSITION("' . $config['find']. '" IN `title`) = 1 DESC, TRIM(SUBSTR(`title`, 1)) ASC';
                    $where[] = '`title` LIKE "' . $keyword . '%"';
                } else {
                    $where[] = '`title` '. (is_null($page) || $page > 0 ? '>=' : '<').' "' . $keyword . '"';
                }
                $where = implode(' AND ', $where);
                $where = preg_replace('/^ AND /', '', $where);

                // Get foundRows WHERE clause
                $foundRowsWhere = $selectedTypeIsKeyword ? $where : $whereBackup;
                $foundRowsWhere = $foundRowsWhere ? 'WHERE ' . $foundRowsWhere : '';

                // Get number of total found rows
                $foundRows = $this->getTable()->getAdapter()->query(
                    'SELECT COUNT(`id`) FROM `' . $relatedM->info('name') . '`' . $foundRowsWhere
                )->fetchColumn(0);

                // Get results
                if (!is_null($page)) {
                    // If we go lower, page number should be incremented, so if passed page number
                    // is 1, it will be 2, because actually results of page 1 were already fetched
                    // and displayed at the stage of combo first initialization
                    if ($page > 0) {
                        $page++;

                    // Otherwise, if we go upper, we should make page number positive.
                    // Also we should adjust ORDER clause to make it DESC
                    } else {
                        $page = abs($page);
                        $order = '`' . $order . '` DESC';

                        // We remember the fact of getting upper page results, because after results is fetched,
                        // we will revert them
                        $upper = true;
                    }

                }
                $dataRs = $relatedM->fetchAll($where, $order, $this->comboOptionsVisibleCount, $page);

                // We set number of total found rows only if passed page number is null, so that means that
                // we are doing a search of first page of results by a keyword, that just has been recently changed
                // so at this time we need to get total number of results that match given keyword
                if (is_null($page)) {
                    $dataRs->foundRows = $foundRows;
                } else {
                    unset($dataRs->foundRows);
                }

                // Reverse results if we were getting upper page results
                if ($upper) $dataRs->reverse();

                // Return needed page of results
                return $dataRs;

            // If we don't have neither initially selected options, nor keyword
            } else {

                // If user try to get results of upper page, empty result set should be returned
                if ($page < 0) {
                    return $this->getTable()->createRowset(array());

                // Increment page, as at stage of combo initialization passed page number was 0,
                // and after first try to get lower page results passed page number is 1, that actually
                // means that if we don't increment such page number, returned results for lower page
                // will be same as initial results got at combo initialization and that is a not correct
                // way.
                } else {
                    return $relatedM->fetchAll(null, $order, $this->comboOptionsVisibleCount, $page + 1);
                }
            }
        }
    }
}
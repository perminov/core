<?php
/**
 * This is a temporary class used to separate good-looking php-code from bad-looking in Indi/Db/Table/Row.php
 * Good looking mean that it have proper coding style, doc blocks and other stuff
 * This class will be renamed to Indi/Db/Table/Row.php after all methods in current Indi/Db/Table/Row.php will become
 * good looking
 */
class Indi_Db_Table_Row_Beautiful extends Indi_Db_Table_Row_Abstract{

    /**
     * Count of options that will be fetched. It's 300 by default - hundred-rounded number of countries in the world
     *
     * @var int
     */
    public static $comboOptionsVisibleCount = 300;

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

        // Setup $orderAutoSet flag
        if (!$this->_original['id'] && array_key_exists('move', $this->_original) && !$this->move) $orderAutoSet = true;

        // Setup `_title` property if need
        if (array_key_exists('_title', $this->_original)) $this->_title = $this->getTitle();

        // Save
        $return = parent::save();

        // Auto set `move` if need
        if ($orderAutoSet) {
            $this->move = $this->id;
            parent::save();
        }

        // Return
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

    public function getComboData($field, $page = null, $selected = null, $selectedTypeIsKeyword = false, $satellite = null){
        // Basic info
        $entityM = Misc::loadModel('Entity');
        $fieldM = Misc::loadModel('Field');
        $entityR = $entityM->fetchRow('`table` = "' . $this->_table->_name . '"');
        $fieldR = $fieldM->fetchRow('`entityId` = "' . $entityR->id . '" AND `alias` = "' . $field . '"');
        $fieldColumnTypeR = $fieldR->getForeignRowByForeignKey('columnTypeId');
        $relatedM = Entity::getInstance()->getModelById($fieldR->relation);
        $params = $fieldR->getParams();

        // Array for WHERE clause
        $where = array();

        // Setup filter, as one of possible parts of WHERE clause
        if ($fieldR->filter) {
            if (preg_match('/(\$|::)/', $fieldR->filter)) {
                eval('$fieldR->filter = \'' . $fieldR->filter . '\';');
            }
            $where[] = $fieldR->filter;
        }

        // If current field column type is ENUM or SET
        if (preg_match('/ENUM|SET/', $fieldColumnTypeR->type)) {
            $where[] = '`fieldId` = "' . $fieldR->id . '"';
            $dataRs = $relatedM->fetchAll($where, 'move');

            // We should mark rowset as related to field, that has a ENUM or SET column type
            // because values of property `alias` should be used as options keys, instead of values of property `id`
            $dataRs->enumset = true;
            return $dataRs;
        }

        // Setup filter by satellite
        if ($fieldR->satellite) {

            // Get satellite field row
            $satelliteR = $fieldR->getForeignRowByForeignKey('satellite');

            // If we have no satellite value passed as a param, we get it from related row property by default
            if (is_null($satellite)) $satellite = $this->{$satelliteR->alias};

            // If dependency type is not 'Variable entity'
            if ($fieldR->dependency != 'e') {

                // Example of situation, that is covered by use of `alternative` logic
                // 1. Indi Engine have tables `entity`, `field`, `element`, `possibleElementParam`
                // 2. If somewhere in entity structure i have a field that is using, for example html-editor,
                //    i want to be able to set some params to this html-editor, such as height, width and etc.
                // 3. html-editor is a row in `element` table, it has, for example id = 13
                // 4. All of possible html-editor params - are stored in `possibleElementParam` table, and are linked
                //    to html-editor by column `elementId` with value = 13
                // 5. I want to set width=500 for one html-editor, and width=600 to another html-editor
                // 6. Both 'one' and 'another' html-editor are rows in `field` table, and can be both linked to same
                //    entity, or to different entities, does not matter
                // 7. For being able to do action from point 5, i want to go 'Entities > Some test entity >
                //    Fields > Some html-editor field > Params > Create'
                // 8. On 'Create' screen there is a form with fieds:
                //    1. Entity (dropdown list)
                //    2. Field  (dropdown list)
                //    3. Param  (dropdown list)
                //    4. Value  (textarea)
                // 9. 'Entity' field is a satellite for 'Field' field, and if 'Entity' field value is changed,
                //    options in 'Field' dropdown list should be refreshed
                // 10. 'Field' field is a satellite for 'Param' field, and if 'Field' field value is changed,
                //    options in 'Param' dropdown list should be refreshed
                // 11. SQL Query for refreshing 'Field' options list, mentioned in point 9 will look like
                //     SELECT * FROM `field` WHERE `entityId` = "x"
                // 12. SQL Query for refreshing 'Field' options list, mentioned in point 10 will look like
                //     SELECT * FROM `possibleElementParam` WHERE `fieldId` = "y"
                // 13. The problem is that table `possibleElementParam` has no `fieldId` column, and that is why
                //      here is `alternative` logic is used. Result of `alternative` logic is that:
                //      1. SQL query will look like
                //         SELECT * FROM `possibleElementParam` WHERE `elementId` = "z"
                //      2. "z" - will be a value of `elementId` column of a selected row in 'Field' dropdown
                if ($fieldR->alternative) {

                    // If we have satellite value passed as a param, we set it as value for $this->{$satelliteR->alias},
                    // because getForeignRowByForeignKey() menthod use internal row property value, that store a foreign key,
                    // and do not use any external values
                    if (!is_null($satellite)) $this->{$satelliteR->alias} = $satellite;
                    $rowLinkedToSatellite = $this->getForeignRowByForeignKey($satelliteR->alias);
                    $where[] = 'FIND_IN_SET("' . $rowLinkedToSatellite->{$fieldR->alternative} . '", `' . $fieldR->alternative . '`)';

                // If we had used a column name (field alias) for satellite, that cannot be used in WHERE clause,
                // we use it's alias instead. Example:
                // 1. Current row is stored in a table `similar` with columns `countryId`, 'cityId', `similarCountryId`,
                //    `similarCityId`
                // 2. After value was changed in combo, linked to `similarCountryId` we want to fetch related cities
                //    for `similarCityId` combo, but if we would use standard logic, sql query for fetching would look like:
                //    SELECT * FROM `city` WHERE `similarCountryId` = "12345". The problem is that table `city` have no
                //    `similarCountryId` column, it has only `countryId` column.
                // 3. So, implemented solution allow to replace column name `similarCountryId` with `countryId` in that sql query,
                //    and instead of "12345" will be passed selected value in combo, linked to `similarCountryId`, not to
                //    `countryId` so the result will be exactly as we need
                } else if ($satelliteR->satellitealias) {
                    $where[] = 'FIND_IN_SET("' . $satellite . '", `' . $satelliteR->satellitealias . '`)';

                // Standard logic
                } else {
                    $where[] = 'FIND_IN_SET("' . $satellite . '", `' . $satelliteR->alias . '`)';
                }

            // If dependency type is 'Variable entity' we replace $relatedM object with calculated model
            } else if ($fieldR->dependency == 'e' && $satellite) {
                $relatedM = $entityM->getModelById($satellite);
            }
        }

        // If we havу no related model - this happen if we have 'varibale entity' satellite dependency type
        // and current satelite value is not defined - we return empty rowset
        if (!$relatedM) return new Indi_Db_Table_Rowset(array());

        // Get title column
        $titleColumn = $relatedM->titleColumn();

        // Set ORDER clause for combo data
        if ($relatedM->fieldExists('move')) {
            $order = 'move';
        } else {
            $order = $titleColumn;
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
                if(!$selected || $selectedTypeIsKeyword || func_get_arg(4)) $page++;

                // Page number is not null when we are paging, and this means that we are trying to fetch
                // more results that are upper or lower and start point for paging ($selected) was not changed.
                // So we mark that foundRows property of rowset should be unset, as in combo.js 'page-top-reached'
                // attribute is set depending on 'found' property existence in response json
                $unsetFoundRows = true;
            }

            // Fetch results
            if ($selectedTypeIsKeyword) {
                $dataRs = $relatedM->fetchTree($where, $order, self::$comboOptionsVisibleCount, $page, 0, null, $keyword);
            } else if (func_num_args() < 5 || is_null(func_get_arg(4))) {
                $dataRs = $relatedM->fetchTree($where, $order, self::$comboOptionsVisibleCount, $page, 0, $selected);
            } else {
                $dataRs = $relatedM->fetchTree($where, $order, self::$comboOptionsVisibleCount, $page, 0, null, null);
            }

            // Unset found rows to prevent disabling of paging up
            if ($unsetFoundRows) unset($dataRs->foundRows);

        // Otherwise
        } else {

            // If we selected option is set, or if we have keyword that results should match, special logic will run
            if ($selected) {

                // We do a backup for WHERE clause, because it's backup version
                // will be used to calc foundRows property in case if $selectedTypeIsKeyword = false
                $whereBackup = $where;

                // Get WHERE clause for options fetch
                if ($selectedTypeIsKeyword) {
                    $order = 'TRIM(SUBSTR(`' . $titleColumn . '`, 1)) ASC';
                    $where[] = '`' . $titleColumn . '` LIKE "' . $keyword . '%"';

                // We should get results started from selected value only if we have no $satellite argument passed
                } else if (func_num_args() < 5 || is_null(func_get_arg(4))) {

                    // If we are sorting results by `move` column, results start point should be = value of `move`
                    // property of $selectedR
                    if ($order == 'move') {
                        $where[] = '`move` '. (is_null($page) || $page > 0 ? '>=' : '<').' "' . $selectedR->move . '"';

                    // Else start point for resutlts will be set as value of `title` or `_title` property of $selectedR
                    } else {
                        $where[] = '`' . $titleColumn . '` '. (is_null($page) || $page > 0 ? '>=' : '<').' "' . $keyword . '"';
                    }

                    // We set this flag to true, because the fact that we are in the body of current 'else if' operator
                    // mean that:
                    // 1. we have selected value,
                    // 2. selected value is not a keyword,
                    // 3. $satellite logic is not used,
                    // 4. first option of final results, fetched by current function (getComboData) - wil be option
                    //    related to selected value
                    // So, we remember this fact, because if $foundRows will be not greater than self::$comboOptionsVisibleCount
                    // there will be no need for results set to be started from selected value, and what is why this
                    $resultsShouldBeStartedFromSelectedValue = true;
                }

                // Get foundRows WHERE clause
                $foundRowsWhere = $selectedTypeIsKeyword ? $where : $whereBackup;
                $foundRowsWhere = $foundRowsWhere ? 'WHERE ' . implode(' AND ', $foundRowsWhere) : '';

                // Get number of total found rows
                $foundRows = $this->getTable()->getAdapter()->query(
                    'SELECT COUNT(`id`) FROM `' . $relatedM->info('name') . '`' . $foundRowsWhere
                )->fetchColumn(0);

                // If results should be started from selected value but total found rows number if not too great
                // we will not use selected value as start point for results, because there will be a sutiation
                // that PgUp or PgDn should be pressed to view all available options in combo, instead of being
                // available all initially
                if ($resultsShouldBeStartedFromSelectedValue && $foundRows <= self::$comboOptionsVisibleCount) {
                    array_pop($where);
                }

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

                $dataRs = $relatedM->fetchAll($where, $order, self::$comboOptionsVisibleCount, $page);

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

            // If we don't have neither initially selected options, nor keyword
            } else {

                // If user try to get results of upper page, empty result set should be returned
                if ($page < 0) {
                    $dataRs = $this->getTable()->createRowset(array());

                // Increment page, as at stage of combo initialization passed page number was 0,
                // and after first try to get lower page results passed page number is 1, that actually
                // means that if we don't increment such page number, returned results for lower page
                // will be same as initial results got at combo initialization and that is a not correct
                // way.
                } else {
                    $dataRs = $relatedM->fetchAll($where, $order, self::$comboOptionsVisibleCount, $page + 1);
                }
            }
        }

        // If results should be grouped (similar way as <optgroup></optgroup> do)
        if ($params['groupBy']) {

            // Get distinct values
            $distinctGroupByFieldValues = array();
            foreach ($dataRs as $dataR)
                if (!$distinctGroupByFieldValues[$dataR->{$params['groupBy']}])
                    $distinctGroupByFieldValues[$dataR->{$params['groupBy']}] = true;

            // Get group field
            $groupByFieldR = $fieldM->fetchRow('
                        `entityId` = "' . $entityM->fetchRow('`id` = "' . $fieldR->relation . '"')->id . '" AND
                        `alias` = "' . $params['groupBy'] . '"
                    ');

            // Get group field related entity model
            $groupByFieldEntityM = $entityM->getModelById($groupByFieldR->relation);

            // Get titles for optgroups
            $groupByOptions = array();
            if ($groupByFieldEntityM->info('name') == 'enumset') {
                $groupByRs = $groupByFieldEntityM->fetchAll('
                            `fieldId` = "' . $groupByFieldR->id . '" AND
                            FIND_IN_SET(`alias`, "' . implode(',', array_keys($distinctGroupByFieldValues)) . '")
                        ');
                foreach ($groupByRs as $groupByR) $groupByOptions[$groupByR->alias] = Misc::usubstr($groupByR->title, 50);
            } else {
                $groupByRs = $groupByFieldEntityM->fetchAll(
                    'FIND_IN_SET(`id`, "' . implode(',', array_keys($distinctGroupByFieldValues)) . '"'
                );
                foreach ($groupByRs as $groupByR) $groupByOptions[$groupByR->id] = Misc::usubstr($groupByR->title, 50);
            }

            $dataRs->optgroup = array('by' => $groupByFieldR->alias, 'groups' => $groupByOptions);
        }

        // If additional params should be passed as each option attributes, setup list of such params
        if ($params['optionAttrs']) {
            $dataRs->optionAttrs = explode(',', $params['optionAttrs']);
        }

        // Set `enumset` property as false, because without definition it will have null value while passing
        // to combo.js and and after deepObjCopy there - will have typeof == object, which is not actually boolean
        // and will cause problems in combo.js
        $dataRs->enumset = false;

        return $dataRs;
    }
}
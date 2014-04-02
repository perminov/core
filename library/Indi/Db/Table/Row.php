<?php
class Indi_Db_Table_Row implements ArrayAccess
{
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
     * Temporary data, used for assigning some values to the current row object under some keys,
     * but these key => value pairs will be never involved at SQL INSERT or UPDATE query executions
     *
     * @var array
     */
    protected $_temporary = array();

    /**
     * Rows, pulled for current row's foreign keys
     *
     * @var array
     */
    protected $_foreign = array();

    /**
     * Rowsets containing children for current row, but related to other models
     *
     * @var array
     */
    protected $_nested = array();

    /**
     * Table name of table, that current row is related to
     *
     * @var string
     */
    protected $_table = '';

    /**
     * Store info about errors, fired while a try to save current row
     *
     * @var array
     */
    protected $_mismatch = array();

    /**
     * Count of options that will be fetched. It's 300 by default - hundred-rounded number of countries in the world
     *
     * @var int
     */
    public static $comboOptionsVisibleCount = 300;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array()) {

        // Setup initial properties
        $this->_table = $config['table'];
        $this->_original = $config['original'];
        $this->_modified = is_array($config['modified']) ? $config['modified'] : array();
        $this->_system = is_array($config['system']) ? $config['system'] : array();
        $this->_temporary = is_array($config['temporary']) ? $config['temporary'] : array();
        $this->_foreign = is_array($config['foreign']) ? $config['foreign'] : array();
        $this->_nested = is_array($config['nested']) ? $config['nested'] : array();

        // Compile php expressions stored in allowed fields and assign results under separate keys in $this->_compiled
        foreach ($this->model()->getEvalFields() as $evalField) {
            Indi::$cmpTpl = $this->_original[$evalField]; eval(Indi::$cmpRun); $this->_compiled[$evalField] = Indi::$cmpOut;
        }
    }

    /**
     * Saves row into database table. But.
     * Preliminary checks if row has a `move` field in it's structure and if row is not an existing row yet
     * (but is going to be inserted), and if so - autoset value for `move` column after row save
     *
     * @return int affected rows|last_insert_id
     */
    public function save() {

        // Check conformance to all requirements
        if (count($this->mismatch(true))) return false;

        // Setup $orderAutoSet flag if need
        if (!$this->_original['id'] && array_key_exists('move', $this->_original) && !$this->move) $orderAutoSet = true;

        // If curren row is an existin row
        if ($this->_original['id']) {

            // Update it
            if ($affected = $this->model()->update($this->_modified, '`id` = "' . $this->_original['id'] . '"')) {

                // Merge $this->_original and $this->_modified arrays into $this->_original array
                $this->_original = (array) array_merge($this->_original, $this->_modified);

                // Empty $this->_modified and $this->_mismatch arrays
                $this->_modified = $this->_mismatch = array();
            }

            // Setup $return variable as a number of affected rows, e.g 1 or 0
            $return = $affected;

        // Else current row is a new row
        } else {

            // Execute the INSERT sql query, get LAST_INSERT_ID and assign it as current row id
            $this->_original['id'] = $this->model()->insert($this->_modified);

            // Merge $this->_original and $this->_modified arrays into $this->_original array
            $this->_original = (array) array_merge($this->_original, $this->_modified);

            // Empty $this->_modified and $this->_mismatch arrays
            $this->_modified = $this->_mismatch = array();

            // Setup $return variable as id of current (a moment ago inserted) row
            $return = $this->_original['id'];
        }

        // Auto set `move` if need
        if ($orderAutoSet) {

            // Set `move` property equal to current row id
            $this->move = $this->id;

            // Update row data
            $this->model()->update($this->_modified, '`id` = "' . $this->_original['id'] . '"');
            $this->_original = (array) array_merge($this->_original, $this->_modified);
            $this->_modified = array();
        }

        // Update cache if need
        if ($this->model()->useCache()) Indi_Cache::update($this->model()->name());

        // Return current row id (in case if it was a new row) or number of affected rows (1 or 0)
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
            if (array_key_exists($this->_table . 'Id', $this->_original)) $within[] = $this->_table . 'Id';

            for ($i = 0; $i < count($within); $i++) $where[] = '`' . trim($within[$i]) . '` = "' . $this->{trim($within[$i])} . '"';

            // Adding custom condition
            if (is_array($condition) && count($condition)) $where = array_merge($where, $condition);
            else if ($condition) $where[] = $condition;

            // Nearest neighbour clauses
            $where[] = '`move` ' . ($direction == 'up' ? '<' : '>') . ' "' . $this->move . '"';
            $order = 'move ' . ($direction == 'up' ? 'DE' : 'A') . 'SC';

            // Find
            if ($changeRow = $this->model()->fetchRow($where, $order)) {

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
    public function delete() {

        // Delete all files (images, etc) that have been attached to row
        $this->deleteUploadedFiles();

        // Delete other rows of entities, that have fields, related to entity of current row
        // This function also covers other situations, such as if entity of current row has a tree structure,
        // or row has dependent rowsets
        $this->deleteForeignKeysUsages();

        // Standard deletion
        return $this->model()->delete('`id` = "' . $this->_original['id'] . '"');
    }

    /**
     * Get the data for use in all control element, that deal with foreign keys
     *
     * @param $field
     * @param null $page
     * @param null $selected
     * @param bool $selectedTypeIsKeyword
     * @param null $satellite
     * @param null $where
     * @param bool $noSatellite
     * @param null $fieldR
     * @param null $order
     * @param string $dir
     * @param null $offset
     * @return Indi_Db_Table_Rowset|mixed
     */
    public function getComboData($field, $page = null, $selected = null, $selectedTypeIsKeyword = false,
                                 $satellite = null, $where = null, $noSatellite = false, $fieldR = null,
                                 $order = null, $dir = 'ASC', $offset = null) {

        // Basic info
        $entityM = Indi::model('Entity');
        $fieldM = Indi::model('Field');
        $entityR = $entityM->fetchRow('`table` = "' . $this->_table . '"');
        $fieldR = $fieldR ? $fieldR : $fieldM->fetchRow('`entityId` = "' . $entityR->id . '" AND `alias` = "' . $field . '"');
        $fieldColumnTypeR = $fieldR->foreign('columnTypeId');
        $relatedM = Indi::model($fieldR->relation);
        $params = $fieldR->getParams();

        // Array for WHERE clauses
        $where = $where ? (is_array($where) ? $where : array($where)): array();

        // Setup filter, as one of possible parts of WHERE clause
        if ($fieldR->filter) $where[] = $fieldR->filter;

        // Compile filters if they contain php-expressions
        for($i = 0; $i < count($where); $i++) {
            Indi::$cmpTpl = $where[$i]; eval(Indi::$cmpRun); $where[$i] = Indi::$cmpOut;
        }

        // If current field column type is ENUM or SET
        if (preg_match('/ENUM|SET/', $fieldColumnTypeR->type)) {
            $where[] = '`fieldId` = "' . $fieldR->id . '"';
            $dataRs = $relatedM->fetchAll($where, '`move`');

            // We should mark rowset as related to field, that has a ENUM or SET column type
            // because values of property `alias` should be used as options keys, instead of values of property `id`
            $dataRs->enumset = true;

            // If current field store relation ability is 'many' - we setup selected as rowset object
            if ($fieldR->storeRelationAbility == 'many')
                $dataRs->selected = $dataRs->select($selected, 'alias');

            // Return combo data
            return $dataRs;

        // Else if current field column type is BOOLEAN - combo is used as an alternative for checkbox control
        } else if ($fieldColumnTypeR->type == 'BOOLEAN') {

            // Prepare the data
            $dataRs = Indi::model('Enumset')->createRowset(
                array(
                    'data' => array(
                        array('alias' => '0', 'title' => 'Нет'),
                        array('alias' => '1', 'title' => 'Да')
                    )
                )
            );

            $dataRs->enumset = true;
            return $dataRs;
        }

        // Setup filter by satellite
        if ($fieldR->satellite && $noSatellite != true) {

            // Get satellite field row
            $satelliteR = $fieldR->foreign('satellite');

            // If we have no satellite value passed as a param, we get it from related row property
            // or from satellite-field default value
            if (is_null($satellite)) {
                if (strlen($this->{$satelliteR->alias})) {
                    $satellite = $this->{$satelliteR->alias};
                } else {
                    $satellite = $satelliteR->defaultValue;
                }
            }

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
                    // because foreign() menthod use internal row property value, that store a foreign key,
                    // and do not use any external values
                    if (!is_null($satellite)) $this->{$satelliteR->alias} = $satellite;
                    $rowLinkedToSatellite = $this->foreign($satelliteR->alias);
                    if ($satelliteR->satellitealias) {
                        $where[] = 'FIND_IN_SET("' . $rowLinkedToSatellite->{$fieldR->alternative} . '", `' . $satelliteR->satellitealias . '`)';
                    } else {
                        $where[] = 'FIND_IN_SET("' . $rowLinkedToSatellite->{$fieldR->alternative} . '", `' . $fieldR->alternative . '`)';
                    }

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
                $relatedM = Indi::model($satellite);
            }
        }

        // If we havу no related model - this happen if we have 'varibale entity' satellite dependency type
        // and current satelite value is not defined - we return empty rowset
        if (!$relatedM) return new Indi_Db_Table_Rowset(array());

        // Get title column
        $titleColumn = $params['titleColumn'] ? $params['titleColumn'] : $relatedM->titleColumn();

        // Set ORDER clause for combo data
        if (is_null($order)) {
            if ($relatedM->fields('move')) {
                $order = 'move';
            } else {
                $order = $titleColumn;
            }

            // If $order is not null, but is an empty string, we set is as 'id' for results being fetched in the order of
            // their physical appearance in database table, however, regarding $dir (ASC or DESC) param.
        } else if (!strlen($order)) {
            $order = 'id';
        }

        // Here and below we will be always checking if $order is not empty
        // because we can have situations, there order is not set at all and if so, we won't use ORDER clause
        // So, if order is empty, the results will be retrieved in the order of their physical placement in
        // their database table
        if (!preg_match('/\(/', $order)) $order = '`' . $order . '`';

        // If fetch-mode is 'keyword'
        if ($selectedTypeIsKeyword) {
            $keyword = str_replace('"','\"', $selected);

            // Else if fetch-mode is 'no-keyword'
        } else {

            // Get selected row
            $selectedR = $relatedM->fetchRow('`id` = "' . $selected . '"');

            // Setup current value of a sorting field as start point
            if ($order && !preg_match('/\(/', $order)) {
                $keyword = str_replace('"','\"', $selectedR->{trim($order, '`')});
            }
        }

        // If related entity has tree-structure
        if ($relatedM->treeColumn()) {

            // If we go lower, page number should be incremented, so if passed page number
            // is 1, it will be 2, because actually results of page 1 were already fetched
            // and displayed at the stage of combo first initialization
            if ($page != null) {
                if(!$selected || $selectedTypeIsKeyword || func_get_arg(4)) $page++;

                // Page number is not null when we are paging, and this means that we are trying to fetch
                // more results that are upper or lower and start point for paging ($selected) was not changed.
                // So we mark that foundRows property of rowset should be unset, as in indi.combo.form.js 'page-top-reached'
                // attribute is set depending on 'found' property existence in response json
                $unsetFoundRows = true;
            }
            // Fetch results
            if ($selectedTypeIsKeyword) {
                $dataRs = $relatedM->fetchTree($where, $order, self::$comboOptionsVisibleCount, $page, 0, null, $keyword);
            } else {

                $order .= ' ' . ($dir == 'DESC' ? 'DESC' : 'ASC');

                if (is_null(func_get_arg(4))) {
                    $dataRs = $relatedM->fetchTree($where, $order, self::$comboOptionsVisibleCount, $page, 0, $selected);
                } else {
                    $dataRs = $relatedM->fetchTree($where, $order, self::$comboOptionsVisibleCount, $page, 0, null, null);
                }
            }


            // Unset found rows to prevent disabling of paging up
            if ($unsetFoundRows) $dataRs->found('unset');

            // Otherwise
        } else {

            // If we selected option is set, or if we have keyword that results should match, special logic will run
            if ($selected && ($fieldR->storeRelationAbility == 'one' || $selectedTypeIsKeyword)) {

                // We do a backup for WHERE clause, because it's backup version
                // will be used to calc foundRows property in case if $selectedTypeIsKeyword = false
                $whereBackup = $where;

                // Get WHERE clause for options fetch
                if ($selectedTypeIsKeyword) {
                    if (!preg_match('/\(/', $order)) {
                        //$order = 'TRIM(SUBSTR(`' . $titleColumn . '`, 1))';
                    }
                    // Check if keyword is a part of color value in format #rrggbb, and if so, we use RLIKE instead
                    // of LIKE, and prepare a special regular expression
                    if (preg_match('/^#[0-9a-fA-F]{0,6}$/', $keyword)) {
                        $rlike = '^[0-9]{3}' . $keyword . '[0-9a-fA-F]{' . (7 - mb_strlen($keyword, 'utf-8')) . '}$';
                        $where[] = '`' . $titleColumn . '` RLIKE "' . $rlike . '"';
                    } else {
                        $where[] = '`' . $titleColumn . '` LIKE "' . $keyword . '%"';
                    }

                    // We should get results started from selected value only if we have no $satellite argument passed
                } else if (is_null(func_get_arg(4))) {

                    // If $order is a name of a column, and not an SQL expression, we setup results start point as
                    // current row's column's value
                    if (!preg_match('/\(/', $order)) {
                        $where[] = $order . ' '. (is_null($page) || $page > 0 ? ($dir == 'DESC' ? '<=' : '>=') : ($dir == 'DESC' ? '>' : '<')).' "' . $keyword . '"';
                    }

                    // We set this flag to true, because the fact that we are in the body of current 'else if' operator
                    // mean that:
                    // 1. we have selected value,
                    // 2. selected value is not a keyword,
                    // 3. $satellite logic is not used,
                    // 4. first option of final results, fetched by current function (getComboData) - wil be option
                    //    related to selected value
                    // So, we remember this fact, because if $found will be not greater than self::$comboOptionsVisibleCount
                    // there will be no need for results set to be started from selected value, and what is why this
                    $resultsShouldBeStartedFromSelectedValue = true;
                }

                // Get foundRows WHERE clause
                $foundRowsWhere = $selectedTypeIsKeyword ? $where : $whereBackup;
                $foundRowsWhere = $foundRowsWhere ? 'WHERE ' . implode(' AND ', $foundRowsWhere) : '';

                // Get number of total found rows
                $found = Indi::db()->query(
                    'SELECT COUNT(`id`) FROM `' . $relatedM->name() . '`' . $foundRowsWhere
                )->fetchColumn(0);

                // If results should be started from selected value but total found rows number if not too great
                // we will not use selected value as start point for results, because there will be a sutiation
                // that PgUp or PgDn should be pressed to view all available options in combo, instead of being
                // available all initially
                if ($resultsShouldBeStartedFromSelectedValue && $found <= self::$comboOptionsVisibleCount) {
                    array_pop($where);
                }

                // Get results
                if (!is_null($page)) {
                    // If we go lower, page number should be incremented, so if passed page number
                    // is 1, it will be 2, because actually results of page 1 were already fetched
                    // and displayed at the stage of combo first initialization
                    if ($page > 0) {
                        $page++;

                        $order .= ' ' . ($dir == 'DESC' ? 'DESC' : 'ASC');

                        // Else if we go upper, but
                    } else if ($offset) {
                        $page++;
                        $order .= ' ' . ($dir == 'DESC' ? 'DESC' : 'ASC');

                        // Otherwise, if we go upper, we should make page number positive.
                        // Also we should adjust ORDER clause to make it DESC
                    } else {
                        $page = abs($page);
                        $order .= ' ' . ($dir == 'DESC' ? 'ASC' : 'DESC');

                        // We remember the fact of getting upper page results, because after results is fetched,
                        // we will revert them
                        $upper = true;
                    }

                } else {

                    $order .= ' ' . ($dir == 'DESC' ? 'DESC' : 'ASC');
                }

                $dataRs = $relatedM->fetchAll($where, $order, self::$comboOptionsVisibleCount, $page, $offset);

                // We set number of total found rows only if passed page number is null, so that means that
                // we are doing a search of first page of results by a keyword, that just has been recently changed
                // so at this time we need to get total number of results that match given keyword
                if (is_null($page)) {
                    $dataRs->found($found);
                } else {
                    $dataRs->found('unset');
                }

                // Reverse results if we were getting upper page results
                if ($upper) $dataRs->reverse();

                // If we don't have neither initially selected options, nor keyword
            } else {

                // If user try to get results of upper page, empty result set should be returned
                if ($page < 0) {
                    $dataRs = $this->model()->createRowset(array());

                // Increment page, as at stage of combo initialization passed page number was 0,
                // and after first try to get lower page results passed page number is 1, that actually
                // means that if we don't increment such page number, returned results for lower page
                // will be same as initial results got at combo initialization and that is a not correct
                // way.
                } else {

                    $order .= ' ' . ($dir == 'DESC' ? 'DESC' : 'ASC');

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
            $groupByFieldEntityM = Indi::model($groupByFieldR->relation);

            // Get titles for optgroups
            $groupByOptions = array();
            if ($groupByFieldEntityM->name() == 'enumset') {
                $groupByRs = $groupByFieldEntityM->fetchAll('
                            `fieldId` = "' . $groupByFieldR->id . '" AND
                            FIND_IN_SET(`alias`, "' . implode(',', array_keys($distinctGroupByFieldValues)) . '")
                        ');
                foreach ($groupByRs as $groupByR) $groupByOptions[$groupByR->alias] = Misc::usubstr($groupByR->title, 50);
            } else {
                $groupByRs = $groupByFieldEntityM->fetchAll(
                    'FIND_IN_SET(`id`, "' . implode(',', array_keys($distinctGroupByFieldValues)) . '")'
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
        // to indi.combo.form.js and and after Indi.copy there - will have typeof == object, which is not actually boolean
        // and will cause problems in indi.combo.form.js
        $dataRs->enumset = false;

        if ($fieldR->storeRelationAbility == 'many') {
            if ($selected) {
                // Convert list of selected ids into array
                $selected = explode(',', $selected);

                // Get array of ids of already fetched rows
                $allFetchedIds = array(); foreach ($dataRs as $dataR) $allFetchedIds[] = $dataR->id;

                // Check if some of selected rows are already presented in $dataRs
                $selectedThatArePresentedInCurrentDataRs = array_intersect($selected, $allFetchedIds);

                // Array for selected rows
                $data = array();

                // If some of selected rows are already presented in $dataRs, we pick them into $data array
                if (count($selectedThatArePresentedInCurrentDataRs))
                    foreach ($dataRs as $dataR)
                        if (in_array($dataR->id, $selectedThatArePresentedInCurrentDataRs))
                            $data[] = $dataR;

                // If some of selected rows are not presented in $dataRs, we do additional fetch to retrieve
                // them from database and append these rows to $data array
                if(count($selectedThatShouldBeAdditionallyFetched = array_diff($selected, $allFetchedIds))) {
                    $data = array_merge($data, $relatedM->fetchAll('
                        FIND_IN_SET(`id`, "' . implode(',', $selectedThatShouldBeAdditionallyFetched) . '")
                    ')->rows());
                }

                $dataRs->selected = $relatedM->createRowset(array('rows' => $data));
            } else {
                $dataRs->selected = $relatedM->createRowset(array('rows' => array()));
            }
        }

        return $dataRs;
    }

    /**
     * Build and return a <span/> element with css class and styles definitions, that will represent a color value
     * for each combo option, in case if combo options have color specification. This function was created for using in
     * optionTemplate param within combos because if combo is simultaneously dealing with color and with optionTemplate
     * param, javascript in indi.combo.form.js file will not create a color boxes to represent color-options,
     * because optionTemplate param assumes, that height of each combo option may be different with default height,
     * so default color box size may not match look and feel of options, builded with optionTemplate param usage.
     * So this function provides a possibility to define custom size for color box
     *
     * @param $colorField
     * @param string $size
     * @return string
     */
    public function colorBox($colorField, $size = '14x9') {
        list($width, $height) = explode('x', $size);
        if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $this->$colorField, $matches)) {
            $style = array('background: #' . $matches[1]);
            if (strlen($width)) $style[] = 'width: ' . $width . 'px';
            if (strlen($height)) $style[] = 'height: ' . $height . 'px';
            return '<span class="i-combo-color-box" style="' . implode('; ', $style) . '"></span> ';
        } else {
            return '';
        }
    }

    /**
     * Strips hue value from color in format 'xxx#rrggbb', where xxx - is hue value
     *
     * @param $colorField
     * @return string
     */
    public function colorHex($colorField) {
        if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $this->$colorField, $matches)) {
            return '#' . $matches[1];
        } else {
            return $this->$colorField;
        }
    }

    /**
     * Gets the foreign row by foreign key name, using it's current value
     *
     * @param string $key The  name of foreign key
     * @param bool $refresh If specified, cached foreign row will be refreshed
     * @return Indi_Db_Table_Row|Indi_Db_Table_Rowset|null
     * @throws Exception
     */
    public function foreign($key = '', $refresh = false) {

        // If $key argument contains more than one key name - we setup rows for all keys
        if (preg_match('/,/',$key)) {
            $keyA = explode(',', $key);
            foreach ($keyA as $keyI) $this->foreign(trim($keyI));

            // Return current row
            return $this;

        } else if (!$key) {
            return $this->_foreign;
        }

        // If $refresh argument is an object, we interpret it as a foreign row, and assign it directly
        if (is_string($key) && is_object($refresh)) return $this->_foreign[$key] = $refresh;

        // If foreign row, got by foreign key, was got already got earlier, and no refresh should be done - return it
        if (array_key_exists($key, $this->_foreign) && !$refresh) {
            return $this->_foreign[$key];

        // Else
        } else {

            // If field, representing foreign key - is exist within current entity
            if ($fieldR = $this->model()->fields($key)) {

                // If field do not store foreign keys - throw exception
                if ($fieldR->storeRelationAbility == 'none' || ($fieldR->relation == 0 && $fieldR->dependency != 'e')) {
                    throw new Exception('Field with alias `' . $key . '` within entity with table name `' . $this->_table .'` is not a foreign key');

                    // Else if field is able to store single key, or able to store multiple, but current key's value isn't empty
                } else if ($fieldR->storeRelationAbility == 'one' || strlen($this->$key)) {

                    // Determine a model, for foreign row to be got from. If field dependency is 'variable entity',
                    // then model is a value of satellite field. Otherwise model is field's `relation` property
                    $model = $fieldR->dependency == 'e'
                        ? $this->{$this->foreign('satellite')->alias}
                        : $fieldR->relation;

                    // Determine a fetch method
                    $methodType = $fieldR->storeRelationAbility == 'many' ? 'All' : 'Row';

                    // Declare array for WHERE clause
                    $where = array();

                    // If field is related to enumset entity, we should append an additional WHERE clause,
                    // that will outline the `fieldId` value, because in this case current row store aliases
                    // of rows from `enumset` table instead of ids, and aliases are not unique within that table.
                    if (Indi::model($fieldR->relation)->name() == 'enumset') {
                        $where[] = '`fieldId` = "' . $fieldR->id . '"';
                        $col = 'alias';
                    } else {
                        $col = 'id';
                    }

                    // Finish building WHERE clause
                    $where[] = '`' . $col . '` ' .
                        ($fieldR->storeRelationAbility == 'many'
                            ? 'IN(' . $this->$key . ')'
                            : '= "' . $this->$key . '"');

                    // Fetch foreign row/rows
                    $foreign = Indi::model($model)->{'fetch' . $methodType}($where);
                }

            // Else there is no such a field within current entity - throw an exception
            } else {
                throw new Exception('Field with alias `' . $key . '` does not exists within entity with table name `' . $this->_table .'`');
            }

            // Save foreign row within a current row under key name, and return it
            return $this->_foreign[$key] = $foreign;
        }
    }

    /**
     * Return a model, that current row is related to
     *
     * @return mixed
     */
    public function model() {
        return Indi::model($this->_table);
    }

    /**
     * Return a database table name, that current row is dealing with
     *
     * @return mixed
     */
    public function table() {
        return $this->_table;
    }

    /**
     * Provide Toggle On/Off action
     *
     * @throws Exception
     */
    public function toggle() {

        // If `toggle` column exists
        if ($this->model()->fields('toggle')) {

            // Do the toggle
            $this->toggle = $this->toggle == 'y' ? 'n' : 'y';
            $this->save();

            // Else throw exception
        } else throw new Exception('Column `toggle` does not exist');
    }

    /**
     * Delete all usages of current row
     */
    public function deleteForeignKeysUsages() {

        // Declare entities array
        $entities = array();

        // Get all fields in whole database, which are containing keys related to this entity
        $fieldRs = Indi::model('Field')->fetchAll('`relation` = "' . $this->model()->id() . '"');
        foreach ($fieldRs as $fieldR) $entities[$fieldR->entityId]['fields'][] = $fieldR;

        // Get auxillary deletion info within each entity
        foreach ($entities as $eid => $data) {

            // Load model
            $model = Indi::model($eid);

            // Foreach field within current model
            foreach ($data['fields'] as $field) {
                // We should check that column - which will be used in WHERE clause for retrieving a dependent rowset -
                // still exists. We need to perform this check because this column may have already been deleted, if
                // it was dependent of other column that was deleted.
                if ($model->fields($field->alias)) {

                    // We delete rows there $this->id in at least one field, which ->storeRelationAbility = 'one'
                    if ($field->storeRelationAbility == 'one') {
                        $model->fetchAll('`' . $field->alias . '` = "' . $this->id . '"')->delete();

                        // If storeRelationAbility = 'many', we do not delete rows, but we delete
                        // mentions of $this->id from comma-separated sets of keys
                    } else if ($field->storeRelationAbility == 'many') {
                        $rs = $model->fetchAll('FIND_IN_SET(' . $this->id . ', `' . $field->alias . '`)');
                        foreach ($rs as $r) {
                            $set = explode(',', $r->{$field->alias});
                            $found = array_search($this->id, $set);
                            if ($found !== false) unset($set[$found]);
                            $r->{$field->alias} = implode(',', $set);
                            $r->save(true);
                        }
                    }
                }
            }
        }
    }

    /**
     * Delete all files (images, etc) that have been attached to row. Names of all uploaded files,
     * are constructed by the following pattern
     * <upload path>/<entity|table|model name>/<row id>_<file upload field alias>.<file extension>
     * or
     * <general upload path>/<entity|table|model name>/<row id>_<file upload field alias>,<resized copy name>.<file extension>
     *  (in case if uploaded file is an image, and resized copy autocreation was set up)
     *
     * This function get all parts of the pattern, build it, and finds all files that match this pattern
     * with glob() php function usage, so uploaded files names and/or paths and/or extensions are not stored in db,
     *
     * @param string $name The alias of field, that is File Upload field (Aliases of such fields are used in the
     *                     process of filename construction for uploaded files to saved under)
     * @throws Exception
     */
    public function deleteUploadedFiles($name = '') {

        // Absolute upload path in filesystem
        $abs = DOC . STD . '/' . Indi::ini()->upload->path . '/' . $this->_table . '/';

        // Array for filenames that should be deleted
        $files = array();

        // We delete all files in case if there is no aim to delete only specified image copies
        if (!$name){
            $nonamed = glob($abs . $this->id . '.*');
            $named = glob($abs . $this->id . ',*');
            if (is_array($nonamed)) $files = array_merge($nonamed, $files);
            if (is_array($named)) $files = array_merge($named, $files);
        }

        // All resized copies are to be deleted too
        $resized = glob($abs . $this->id . '_' . $name . '*.*');
        if (is_array($resized)) $files = array_merge($resized, $files);

        // Foreach file - delete it from server
        for ($j = 0; $j < count($files); $j++) {
            try {
                unlink($files[$j]);
            } catch (Exception $e) {
                throw new Exception();
            }
        }
    }

    /**
     * Get the absolute path to a file, that was attached to current row by uploading within field with $alias alias.
     * If $copy argument is given, function will return a path to a resized copy of file - of course if uploaded file
     * was an image. If file was not found - return false;
     *
     * @param string $alias
     * @param string $copy
     * @return bool|string
     */
    public function abs($alias, $copy = '') {
        // Get the name of the directory, relative to document root,
        // and where all files related model of current row are located
        $src =  STD . '/' . Indi::ini()->upload->path . '/' . $this->_table . '/';

        // Build the filename pattern for using in glob() php function
        $pat = DOC . $src . $this->id . ($alias ? '_' . $alias : '') . ($copy ? ',' . $copy : '') . '.' ;

        // Get the files, matching $pat pattern
        $files = glob($pat . '*');

        // If no files found, return false
        if(count($files) == 0) return false;

        // Else return absolute path to first found file
        return $files[0];
    }

    /**
     * Get the relative ( - relative to document root ) filename of the uploaded file, that was attached to current row
     * by uploading within field with $alias alias. If $copy argument is given, function will return a path
     * to a resized copy of file - of course if uploaded file was an image. If file was not found - will return false.
     *
     * @param $alias
     * @param string $copy
     * @return string|null
     */
    public function src($alias, $copy = '') {
        // Get the filename with absolute path
        if ($abs = $this->abs($alias, $copy))

            // Else return path to first found file, relative to document root
            return str_replace(DOC, '', $abs);
    }

    /**
     * Create an return an <embed> tag with 'src' attribute, pointing to uploaded file
     * of type application/x-shockwave-flash, if it exists, or return false otherwise
     *
     * @param $alias Alias of field of 'File' type
     * @param string $attr Additional attributes list for <embed> tag
     * @return bool|string
     */
    public function swf($alias, $attr = '') {

        // If uploaded file exists, get it src
        if ($src = $this->src($alias))

            // Return <embed> tag with found src
            return '<embed src="' . $src .'" border="0"' . ($attr ? $attr : '') . '/>';

        // Return false otherwise
        return false;
    }

    /**
     * Build and return an <img> tag, representing an uploaded image
     *
     * @param null $alias Alias of field, image was uploaded using by
     * @param null $copy Name of image resized copy, if resized copy should be displayed instead of original image
     * @param string $attr Attributes list for <img> tag
     * @param bool $noCache Append image last modification time to 'src' attribute
     * @param bool $size Include real dimensions info as 'real-width' and 'real-height' attributes within <img> tag
     * @return bool|string Built <img> tag, of false, if image file does not exists
     */
    public function img($alias = null, $copy = null, $attr = '', $noCache = true, $size = true) {

        // If image file exists
        if ($abs = $this->abs($alias, $copy)) {

            // Start building <img> tag
            $img = '<img';

            // Get image filename, relative to $_SERVER['DOCUMENT_ROOT']
            $src = str_replace(DOC, '', $abs);

            // If $noCache argument is true, we append file modification time to 'src' attribute
            if ($noCache) $src .= '?' . filemtime($abs);

            // Append 'src' attribute to <img> tag
            $img .= ' src="' . $src . '"';

            // If $size argument is true, we should mention real image dimensions as additional attributes
            if ($size) {
                list($w, $h) = getimagesize($abs);
                $img .= ' real-width="' . $w . '" real-height="' . $h . '"';
            }

            // If $attr argument is specified, we append it to <img> tag
            if ($attr) $img .= ' ' .$attr;

            // If $attr argument do not contain 'alt' attribute, we append it, but with empty value
            if (!preg_match('/alt="/', $attr)) $img .= ' alt=""';

            // Close <img> tag and return it
            return $img . '/>';
        }

        // Return false
        return false;
    }
    /**
     * Fetch the rowset, nested to current row, assing that rowset within $this->_nested array under certain key,
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

        // Id $fetch argument is object, we interpret it as nested data, so we assign it directly
        if (is_object($fetch)) return $this->_nested[$table] = $fetch;

        // Determine the nested rowset identifier. If $alias argument is not null, we will assume that needed rowset
        // is or should be stored under $alias key within $this->_nested array, or under $table key otherwise.
        // This is useful in cases when we need to deal with nested rowsets, got from same database table, but
        // with different fetch params, such as WHERE, ORDER, LIMIT clauses, etc.
        $key = $alias ? $alias : $table;

        // If needed nested rowset is already exists within $this->_nested array, and it shouldn't be refreshed
        if (array_key_exists($key, $this->_nested) && !$fresh) {

            // If $fetch argument is 'unset', we do unset nested data, stored under $key key within $this->_nested
            // Here we use $fetch argument, instead of $fresh agrument, for more friendly unsetting usage, e.g
            // $rs->nested('table', 'unset') instead of $rs->nested('table', null, null, null, 'unset')
            if ($fetch == 'unset') {

                // Unset nested data
                unset($this->_nested[$key]);

                // Return row itself
                return $this;

            // Else we return it
            } else return $this->_nested[$key];

        // Otherwise we fetch it, assign it under $key key within $this->_nested array and return it
        } else {

            // Determine the field, that is a connector between current row and nested rowset
            $connector = $field ? $field : $this->_table . 'Id';

            // If $fetch argument is array
            if (is_array($fetch)) {

                // Define the allowed keys within $fetch array
                $params = array('where', 'order', 'count', 'page', 'offset');

                // Unset all keys within $fetch array, that are not allowed
                foreach ($fetch as $k => $v) if (!in_array($k, $params)) unset($fetch[$k]);

                // Extract allowed keys with their values from $fetch array
                extract($fetch);
            }

            // Convert $where to array
            $where = isset($where) && is_array($where) ? $where : (strlen($where) ? array($where) : array());

            // If connector field store relation ability is multiple
            if (Indi::model($table)->fields($connector)->storeRelationAbility == 'many')

                // We use FIND_IN_SET sql expression for prepending $where array
                array_unshift($where, 'FIND_IN_SET("' . $this->id . '", `' . $connector . '`)');

            // Else if connector field store relation ability is single
            else if (Indi::model($table)->fields($connector)->storeRelationAbility == 'one')

                // We use '=' sql expression for prepending $where array
                array_unshift($where, '`' . $connector . '` = "' . $this->id . '"');

            // Else an Exception will be thrown, as connector field do not exists or don't have store relation ability
            else throw new Exception();

            // Fetch nested rowset, assign it under $key key within $this->_nested array and return that rowset
            return $this->_nested[$key] = Indi::model($table)->fetchAll($where, $order, $count, $page, $offset);
        }
    }

    /**
     * Returns the column/value data as an array.
     * If $type param is set to current (by default), the returned array will contain original data
     * with overrided values for keys of $this->_modified array
     *
     * @param string $type current|original|modified|temporary
     * @param bool $deep
     * @return array
     */
    public function toArray($type = 'current', $deep = true) {
        if ($type == 'current') {

            // Merge _original, _modified, _compiled and _temporary array of properties
            $array = (array) array_merge($this->_original, $this->_modified, $this->_compiled, $this->_temporary);

            // Append _system array as separate array within returning array, under '_system' key
            if (count($this->_system)) $array['_system'] = $this->_system;

        } else if ($type == 'original') {
            $array = (array) $this->_original;
        } else if ($type == 'modified') {
            $array = (array) $this->_modified;
        } else if ($type == 'temporary') {
            $array = (array) $this->_temporary;
        }

        if ($deep) {
            if (count($this->_foreign))
                foreach ($this->_foreign as $alias => $row)
                    if (is_object($row) && $row instanceof Indi_Db_Table_Row)
                        $array['_foreign'][$alias] = $row->toArray($type, $deep);

            if (count($this->_nested))
                foreach ($this->_nested as $alias => $rowset)
                    if (is_object($rowset) && $rowset instanceof Indi_Db_Table_Rowset)
                        $array['_nested'][$alias] = $rowset->toArray($deep);

        }

        return $array;
    }

    /**
     * Test existence of row field
     *
     * @param  string  $columnName   The column key.
     * @return boolean
     */
    public function __isset($columnName) {
        return array_key_exists($columnName, $this->_original);
    }

    /**
     * Retrieve row field value
     *
     * @param  string $columnName The user-specified column name.
     * @return string             The corresponding column value.
     */
    public function __get($columnName) {
        if ($columnName == 'title' && !$this->__isset($columnName)) {
            return $this->__isset('_title') ? $this->_title : 'No title';

        } else if ($columnName == 'foreign') {
            return $this->foreign();
        }

        // We trying to find the key's ($columnName) value at first in $this->_modified array,
        // then in $this->_original array, and then in $this->_temporary array, and return
        // once value was found
        if (array_key_exists($columnName, $this->_modified)) return $this->_modified[$columnName];
        else if (array_key_exists($columnName, $this->_original)) return $this->_original[$columnName];
        else if ($this->_temporary[$columnName]) return $this->_temporary[$columnName];
    }

    /**
     * Set row field value, by creating an item of $this->_modified array, in case if
     * value is different from value of $this->_original at same key ($columnName)
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     */
    public function __set($columnName, $value) {

        // If $columnName is exists as one of keys within $this->_original array
        if (array_key_exists($columnName, $this->_original)) {

            // If this value is not equal to value, already existing in $this->_original array under same
            // key ($columnName), we put this value in a $this->_modified array
            if ($this->_original[$columnName] !== $value)
                $this->_modified[$columnName] = $value;

            // Else we unset value, stored in $this->_modified array under $columnName key, because the fact
            // that we are here mean value is now the exact same as it was originally, so we need to unset
            // info about it's modification, as it is actually no more modified
            else unset($this->_modified[$columnName]);

            // Else we put this value in $this->_temporary array
        } else $this->_temporary[$columnName] = $value;
    }

    /**
     * Strips all tags from $html argument, except tags mentioned in $tags argument as a comma-separated list,
     * then strip event attributes from these tags, and after that return result
     *
     * @static
     * @param $html
     * @param string $tags
     * @return string
     */
    public static function safeHtml($html, $tags = 'font,span') {

        // Strip all tags, except tags, mentioned in $tags argument
        $html = strip_tags($html, '<' . preg_replace('/,/', '><', $tags) . '>');

        // Strip event attributes, and return the result
        return self::safeAttrs($html);
    }

    /**
     * Strip event attributes from tags, that are exist in $html argument, and return the result
     *
     * @static
     * @param $html
     * @return mixed
     */
    public static function safeAttrs($html) {

        // Declare a callback function for usage within preg_replace_callback() php function
        if (!function_exists('safeAttrsCallback')) {
            function safeAttrsCallback($m) {
                $m[2] = preg_replace('/\s+on[a-zA-Z0-9]+\s*=\s*"[^">]+"/', '', $m[2]);
                $m[2] = preg_replace("/\s+on[a-zA-Z0-9]+\s*=\s*'[^'>]+'/", '', $m[2]);
                return $m[1] . $m[2] . $m[5];
            }
        }

        // Replace double and single quotes that are prepended with a backslash, with their special charachers
        $html = preg_replace('/\\\\"/', '&quot;', $html); $html = preg_replace("/\\\\'/", '&#039;', $html);

        // Strip event attributes, using a callback function
        $html = preg_replace_callback('/(<[a-zA-Z0-9]+)((\s+[a-zA-Z0-9]+\s*=\s*("|\')[^\4>]+\4)*)\s*(\/?>)/', 'safeAttrsCallback', $html);

        // Restore double and single quotes that were prepended with a backslash
        $html = preg_replace('/&quot;/', '\"', $html); $html = preg_replace('/&#039;/', "\'", $html);

        // Return result
        return $html;
    }

    /**
     * If $check argument is set to false or not given - return the stack of errors,
     * appeared while try to save current row. Otherwise, if $check argument is set to true,
     * do a check for $this->_modifed values conformance to all possible requirements, e.g.
     * control element requirements, mysql column type requirements and additional/user-defined requirements
     *
     * @param bool $check
     * @return array
     */
    public function mismatch($check = false) {

        // If $check argument is set to false, return $this->_mismatch stack, else reset $this->_mismatch array
        if ($check == false) return $this->_mismatch; else $this->_mismatch = array();

        // Declare an array, containing aliases of control elements, that can deal with array values
        $arrayAllowed = array('multicheck', 'time', 'datetime');

        // For each $modified field
        foreach ($this->_modified as $column => $value) {

            // Get the field
            $fieldR = $this->model()->fields($column);

            // Get the control element
            $elementR = $fieldR->foreign('elementId');

            // Get the control element
            $entityR = $fieldR->foreign('relation');

            // If $value is an object - push a message to $this->_mismatch stack,
            // stop dealing with the current column's value and continue with the next
            if (is_object($value)) {

                // Push a error to errors stack
                $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_CANT_BE_OBJECT, $fieldR->title);

                // Jump to checking the next column's value
                continue;
            }

            // If $value is an array, but current field's control element do not deal with arrays
            // - push a message to $this->_mismatch stack, stop dealing with the current column's
            // value and continue with the next
            if (is_array($value) && !in_array($elementR->alias, $arrayAllowed)) {

                // Push a error to errors stack
                $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_CANT_BE_ARRAY, $fieldR->title);

                // Jump to checking the next column's value
                continue;
            }

            // If element is 'string' or 'text'
            if (preg_match('/^string|textarea$/', $elementR->alias)) {

                // If field is in list of eval fields, and current field's value contains php expressions
                if (in_array($fieldR->alias, $this->model()->getEvalFields()) && preg_match(Indi::rex('phpsplit'), $value)) {

                    // Split value by php expressions
                    $chunk = preg_split(Indi::rex('phpsplit'), $value, -1, PREG_SPLIT_DELIM_CAPTURE);

                    // Declare a variable for filtered value
                    $value = '';

                    // For each chunk
                    for ($i = 0; $i < count($chunk); $i++) {

                        // If chunk is a part of php expression - append that php expression to filtered value
                        if ($chunk[$i] == '<?') {
                            $php = $chunk[$i] . $chunk[$i+1] . $chunk[$i+2];
                            $value .= $php;
                            $i += 2;

                        // Else if chunk is not a php expression - make it safe and append to filtered value
                        } else  $value .= self::safeHtml($chunk[$i]);
                    }

                    // Else field is not in list of eval fields, make it's value safe by stripping restricted html tags,
                    // and by stripping event attributes from allowed tags
                } else $value = self::safeHtml($value);

            // If element is 'move'
            } else if ($elementR->alias == 'move') {

                // If $value is not a decimal
                if (!preg_match(Indi::rex('int11'), $value)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_INT11, $value, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

            // If element is 'radio', or element is 'combo' and field store relation ability is 'one'
            } else if ($elementR->alias == 'radio' || ($elementR->alias == 'combo' && $fieldR->storeRelationAbility == 'one')) {

                // If field deals with values from 'enumset' table
                if ($entityR->table == 'enumset') {

                    // Get the possible field values
                    $possible = $fieldR->nested('enumset')->column('alias');

                    // If $value is not a one of possible values
                    if (!in_array($value, $possible)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_IS_NOT_ALLOWED, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // Else if field deals with foreign keys of other tables
                } else {

                    // If $value is not a decimal
                    if (!preg_match(Indi::rex('int11'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_INT11, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }
                }

            // If element is 'multicheck' or element is 'combo' and field relation ability is 'many'
            } else if ($elementR->alias == 'multicheck' || ($elementR->alias == 'combo' && $fieldR->storeRelationAbility == 'many')) {

                // Implode the values list by comma
                if (is_array($value)) $value = implode(',', $value);

                // Trim the ',' from value
                $value = trim($value, ',');

                // If value is not empty
                if (strlen($value)) {

                    // Get the values array
                    $valueA = explode(',', $value);

                    // If field deals with values from 'enumset' table
                    if ($entityR->table == 'enumset') {

                        // Get the possible field values
                        $possible = $fieldR->nested('enumset')->column('alias');

                        // If at least one of values is not one of possible values
                        if (count($impossible = array_diff($valueA, $possible))) {

                            // Push a error to errors stack
                            $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_CONTAINS_UNALLOWED_ITEMS,
                                $fieldR->title, implode('","', $impossible)
                            );

                            // Jump to checking the next column's value
                            continue;
                        }

                    // Else if field deals with foreign keys of other tables
                    } else {

                        // If $value is not a list of non-zero decimals
                        if (!preg_match(Indi::rex('int11list'), $value)) {

                            // Push a error to errors stack
                            $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_LIST_OF_NON_ZERO_DECIMALS,
                                $value, $fieldR->title);

                            // Jump to checking the next column's value
                            continue;
                        }
                    }
                }

            // If element is 'check'
            } else if ($elementR->alias == 'check') {

                // If $value is not '1' or '0'
                if (!preg_match(Indi::rex('bool'), $value)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_BOOLEAN, $value, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

            // If element is 'color'
            } else if ($elementR->alias == 'color') {

                // If $value is not a color in format #rrggbb or in format hue#rrggbb
                if (!preg_match(Indi::rex('rgb'), $value) && !preg_match(Indi::rex('hrgb'), $value)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_COLOR, $value, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;

                    // Else if $value is a color in format #rrggbb, e.g without hue
                } else if (preg_match(Indi::rex('rgb'), $value)) {

                    // Prepend color with it's hue number
                    $value = Misc::rgbPrependHue($value);
                }

            // If element is 'calendar'
            } else if ($elementR->alias == 'calendar') {

                // If $value is not a date in format YYYY-MM-DD
                if (!preg_match(Indi::rex('date'), $value)) {

                    // Set $mismatch flag to true
                    $mismatch = true;

                    // If $value is a zero-date, e.g '0000-00-00', '00/00/0000', etc
                    if (preg_match(Indi::rex('zerodate'), $value)) {

                        // Set $mismatch flag to false and set value as '0000-00-00'
                        $mismatch = false; $value = '0000-00-00';

                    // Else if $value is a non-zero date, and field has a 'displayFormat' param
                    } else if ($fieldR->params['displayFormat']) {

                        // Try to get a unix-timestamp of a date stored in $value variable
                        $utime = strtotime($value);

                        // If date, builded from $utime and formatted according to 'displayFormat' param
                        // is equal to initial value of $value variable - this will mean that date, stored
                        // in $value is a valid date, so we
                        if (date($fieldR->params['displayFormat'], $utime) == $value) {

                            // Set $mismatch flag to false
                            $mismatch = false;

                            // And setup $value as date got from $utime timestamp and formatted by 'Y-m-d' format
                            $value = date('Y-m-d', $utime);
                        }
                    }

                    // If $mismatch flag is set to true
                    if ($mismatch) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DATE, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }
                }

                // If $value is not '0000-00-00'
                if ($value != '0000-00-00') {

                    // Extract year, month and day from value
                    list($year, $month, $day) = explode('-', $value);

                    // If date is not a valid date
                    if (!checkdate($month, $day, $year)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_VALID_DATE, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }
                }

            // If element is 'html' - no checks
            } else if ($elementR->alias == 'html') {

            // If element is 'upload' - no checks
            } else if ($elementR->alias == 'upload') {

            // If element is 'time'
            } else if ($elementR->alias == 'time') {

                // If $value is not an array, we convert it to array, containing hours, minutes and seconds
                // values under corresponding keys, by splitting $value by ':' sign
                if (!is_array($value)) {

                    // Make a copy of $value and redefine $value as array
                    $time = $value; $value = array();

                    // Extract hours, minutes and seconds
                    list($value['hours'], $value['minutes'], $value['seconds']) = explode(':', $time);
                }

                // If $value is an array - get the imploded value, assuming that array version of values contains
                // hours, minutes and seconds under corresponding keys within that array
                $time = implode(':', array(
                    str_pad($value['hours'], 2, '0', STR_PAD_LEFT),
                    str_pad($value['minutes'], 2, '0', STR_PAD_LEFT),
                    str_pad($value['seconds'], 2, '0', STR_PAD_LEFT)
                ));

                // If $value is not a time in format HH:MM:SS
                if (!preg_match(Indi::rex('time'), $time)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_TIME, $time, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

                // If any of hours, minutes or seconds values exceeds their possible
                if ($value['hours'] > 23 || $value['minutes'] > 59 || $value['seconds'] > 59) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_VALID_TIME, $time, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

                // Assign a value
                $value = $time;

            // If element is 'number'
            } else if ($elementR->alias == 'number') {

                // If $value is not a decimal
                if (!preg_match(Indi::rex('int11'), $value)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_INT11, $value, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

            // If element is 'datetime'
            } else if ($elementR->alias == 'datetime') {

                // If $value is not an array, we convert it to array, containing date, time, year, month, day,
                // hours, minutes and seconds values under corresponding keys in $value array,
                if (!is_array($value)) {

                    // Make a copy of $value and redefine $value as array
                    $datetime = $value; $value = array();

                    list($value['date'], $value['time']) = explode(' ', $datetime);
                    list($value['year'], $value['month'], $value['day']) = explode('-', $value['date']);
                    list($value['hours'], $value['minutes'], $value['seconds']) = explode(':', $value['time']);

                // Else if $value is already an array, we assume that it already have 'date', 'hours', 'minutes'
                // and 'seconds' keys, so we only explode value under 'date' key to setup values for keys 'year',
                // 'month' and 'day' separately
                } else {

                    // Extract year, month and day from date
                    list($value['year'], $value['month'], $value['day']) = explode('-', $value['date']);
                }

                // If $value is not a date in format YYYY-MM-DD
                if (!preg_match(Indi::rex('date'), $value['date'])) {

                    // Set $mismatch flag to true
                    $mismatch = true;

                    // If $value is a zero-date, e.g '0000-00-00', '00/00/0000', etc
                    if (preg_match(Indi::rex('zerodate'), $value['date'])) {

                        // Set $mismatch flag to false and set value as '0000-00-00'
                        $mismatch = false; $value['date'] = '0000-00-00';

                        // Else if $value is a non-zero date, and field has a 'displayFormat' param
                    } else if ($fieldR->params['displayDateFormat']) {

                        // Try to get a unix-timestamp of a date stored in $value variable
                        $utime = strtotime($value['date']);

                        // If date, builded from $utime and formatted according to 'displayFormat' param
                        // is equal to initial value of $value variable - this will mean that date, stored
                        // in $value is a valid date, so we
                        if (date($fieldR->params['displayDateFormat'], $utime) == $value['date']) {

                            // Set $mismatch flag to false
                            $mismatch = false;

                            // And setup $value as date got from $utime timestamp and formatted by 'Y-m-d' format
                            $value['date'] = date('Y-m-d', $utime);
                        }
                    }

                    // If $mismatch flag is set to true
                    if ($mismatch) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_DATE, $value['date'], $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }
                }

                // If $value['date'] is not '0000-00-00'
                if ($value['date'] != '0000-00-00') {

                    // If date is not a valid date
                    if (!checkdate($value['month'], $value['day'], $value['year'])) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_VALID_DATE,
                            $value['date'], $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }
                }

                // If $value is an array - get the imploded value, assuming that array version of values contains
                // hours, minutes and seconds under corresponding keys within that array
                $time = implode(':', array(
                    str_pad($value['hours'], 2, '0', STR_PAD_LEFT),
                    str_pad($value['minutes'], 2, '0', STR_PAD_LEFT),
                    str_pad($value['seconds'], 2, '0', STR_PAD_LEFT)
                ));

                // If $value is not a time in format HH:MM:SS
                if (!preg_match(Indi::rex('time'), $time)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_TIME, $time, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

                // If any of hours, minutes or seconds values exceeds their possible
                if ($value['hours'] > 23 || $value['minutes'] > 59 || $value['seconds'] > 59) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_VALID_TIME, $time, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

                // Assign a value
                $value = $value['date'] . ' ' . $time;

            // If element is 'hidden'
            } else if ($elementR->alias == 'hidden') {

                // Get the column type
                $columnTypeR = $fieldR->foreign('columnTypeId');

                // If column type is 'VARCHAR(255)'
                if ($columnTypeR->type == 'VARCHAR(255)') {

                    // Make the value safer
                    $value = self::safeHtml($value);

                // If column type is 'INT(11)'
                } else if ($columnTypeR->type == 'INT(11)') {

                    // If $value is not a decimal, or more than 11-digit decimal
                    if (!preg_match(Indi::rex('int11'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_INT11, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // If column type is 'TEXT' - no checks
                } else if ($columnTypeR->type == 'TEXT') {


                // If column type is 'DOUBLE(7,2)'
                } else if ($columnTypeR->type == 'DOUBLE(7,2)') {

                    // If $value is not a DOUBLE(7,2)
                    if (!preg_match(Indi::rex('double72'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DOUBLE72, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // If column type is 'DATE'
                } else if ($columnTypeR->type == 'DATE') {

                    // If $value is not a date in format YYYY-MM-DD
                    if (!preg_match(Indi::rex('date'), $value)) {

                        // Set $mismatch flag to true
                        $mismatch = true;

                        // If $value is a zero-date, e.g '0000-00-00', '00/00/0000', etc
                        if (preg_match(Indi::rex('zerodate'), $value)) {

                            // Set $mismatch flag to false and set value as '0000-00-00'
                            $mismatch = false; $value = '0000-00-00';
                        }

                        // If $mismatch flag is set to true
                        if ($mismatch) {

                            // Push a error to errors stack
                            $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DATE, $value, $fieldR->title);

                            // Jump to checking the next column's value
                            continue;
                        }
                    }

                    // Extract year, month and day from date
                    list($year, $month, $day) = explode('-', $value);

                    // If $value is not a valid date
                    if ($value != '0000-00-00' && !checkdate($month, $day, $year)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_VALID_DATE, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // If column type is 'YEAR'
                } else if ($columnTypeR->type == 'YEAR') {

                    // If $value is not a YEAR
                    if (!preg_match(Indi::rex('year'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_YEAR, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // If column type is 'TIME'
                } else if ($columnTypeR->type == 'TIME') {

                    // If $value is not an array, we convert it to array, containing hours, minutes and seconds
                    // values under corresponding keys, by splitting $value by ':' sign
                    if (!is_array($value)) {

                        // Make a copy of $value and redefine $value as array
                        $time = $value; $value = array();

                        // Extract hours, minutes and seconds
                        list($value['hours'], $value['minutes'], $value['seconds']) = explode(':', $time);
                    }

                    // If $value is an array - get the imploded value, assuming that array version of values contains
                    // hours, minutes and seconds under corresponding keys within that array
                    $time = implode(':', array(
                        str_pad($value['hours'], 2, '0', STR_PAD_LEFT),
                        str_pad($value['minutes'], 2, '0', STR_PAD_LEFT),
                        str_pad($value['seconds'], 2, '0', STR_PAD_LEFT)
                    ));

                    // If $value is not a time in format HH:MM:SS
                    if (!preg_match(Indi::rex('time'), $time)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_TIME, $time, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                    // If any of hours, minutes or seconds values exceeds their possible
                    if ($value['hours'] > 23 || $value['minutes'] > 59 || $value['seconds'] > 59) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_VALID_TIME, $time, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                    // Assign the value
                    $value = $time;

                // If column type is 'DATETIME'
                } else if ($columnTypeR->type == 'DATETIME') {

                    // Make a copy of $value and redefine $value as array
                    $datetime = $value; $value = array();

                    // Convert $value to array, containing date, time, year, month, day,
                    // hours, minutes and seconds values under corresponding keys in $value array,
                    list($value['date'], $value['time']) = explode(' ', $datetime);
                    list($value['year'], $value['month'], $value['day']) = explode('-', $value['date']);
                    list($value['hours'], $value['minutes'], $value['seconds']) = explode(':', $value['time']);

                    // If $value is not a date in format YYYY-MM-DD
                    if (!preg_match(Indi::rex('date'), $value['date'])) {

                        // Set $mismatch flag to true
                        $mismatch = true;

                        // If $value is a zero-date, e.g '0000-00-00', '00/00/0000', etc
                        if (preg_match(Indi::rex('zerodate'), $value['date'])) {

                            // Set $mismatch flag to false and set value as '0000-00-00'
                            $mismatch = false; $value['date'] = '0000-00-00';
                        }

                        // If $mismatch flag is set to true
                        if ($mismatch) {

                            // Push a error to errors stack
                            $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_DATE,
                                $value['date'], $fieldR->title);

                            // Jump to checking the next column's value
                            continue;
                        }
                    }

                    // If $value['date'] is not '0000-00-00'
                    if ($value['date'] != '0000-00-00') {

                        // If date is not a valid date
                        if (!checkdate($value['month'], $value['day'], $value['year'])) {

                            // Push a error to errors stack
                            $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_VALID_DATE,
                                $value['date'], $fieldR->title);

                            // Jump to checking the next column's value
                            continue;
                        }
                    }

                    // If $value is an array - get the imploded value, assuming that array version of values contains
                    // hours, minutes and seconds under corresponding keys within that array
                    $time = implode(':', array(
                        str_pad($value['hours'], 2, '0', STR_PAD_LEFT),
                        str_pad($value['minutes'], 2, '0', STR_PAD_LEFT),
                        str_pad($value['seconds'], 2, '0', STR_PAD_LEFT)
                    ));

                    // If $value is not a time in format HH:MM:SS
                    if (!preg_match(Indi::rex('time'), $time)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_TIME, $time, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                    // If any of hours, minutes or seconds values exceeds their possible
                    if ($value['hours'] > 23 || $value['minutes'] > 59 || $value['seconds'] > 59) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_CONTAIN_VALID_TIME, $time, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                    // Assign a value
                    $value = $value['date'] . ' ' . $time;

                // If column type is 'ENUM'
                } else if ($columnTypeR->type == 'ENUM') {

                    // Get the possible field values
                    $possible = $fieldR->nested('enumset')->column('alias');

                    // If $value is not a one of possible values
                    if (!in_array($value, $possible)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_IS_NOT_ALLOWED, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // If column type is 'SET'
                } else if ($columnTypeR->type == 'SET') {

                    // Trim the ',' from value
                    $value = trim($value, ',');

                    // If value is not empty
                    if (strlen($value)) {

                        // Get the values array
                        $valueA = explode(',', $value);

                        // If field deals with values from 'enumset' table
                        if ($entityR->table == 'enumset') {

                            // Get the possible field values
                            $possible = $fieldR->nested('enumset')->column('alias');

                            // If at least one of values is not one of possible values
                            if (count($impossible = array_diff($valueA, $possible))) {

                                // Push a error to errors stack
                                $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_CONTAINS_UNALLOWED_ITEMS,
                                    $fieldR->title, implode('","', $impossible)
                                );

                                // Jump to checking the next column's value
                                continue;
                            }
                        }
                    }

                // If column type is 'BOOLEAN'
                } else if ($columnTypeR->type == 'BOOLEAN') {

                    // If $value is not '1' or '0'
                    if (!preg_match(Indi::rex('bool'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_BOOLEAN, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // If column type is 'VARCHAR(10)'
                } else if ($columnTypeR->type == 'VARCHAR(10)') {

                    // If $value is not a color in format #rrggbb or in format hue#rrggbb
                    if (!preg_match(Indi::rex('rgb'), $value) && !preg_match(Indi::rex('hrgb'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_COLOR, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;

                    // Else if $value is a color in format #rrggbb, e.g without hue
                    } else if (preg_match(Indi::rex('rgb'), $value)) {

                        // Prepend color with it's hue number
                        $value = Misc::rgbPrependHue($value);
                    }
                }
            }

            // Re-assign the value to column
            $this->$column = $value;
        }

        // If current model has a tree-column, and current row is not an existing new row, and tree column value was
        // modified, and it is going to be same as current row id
        if ($this->model()->treeColumn() && $this->id && $this->_modified[$this->model()->treeColumn()] == $this->id) {

            // Define a shortcut for tree column field alias
            $treeColumn = $this->model()->treeColumn();

            // Get the tree column field
            $fieldR = $this->model()->fields($treeColumn);

            // Push a error to errors stack
            $this->_mismatch[$treeColumn] = sprintf(I_ROWSAVE_ERROR_VALUE_TREECOLUMN_INVALID, $fieldR->title);

        }
//        i($this);

        // Return array of errors
        return $this->_mismatch;
    }

    /**
     * This function sets of gets a value of $this->_temporary array by a given key (argument #1)
     * using a given value (argument # 2)

     * @return mixed
     */
    public function original() {
        if (func_num_args() == 0) return $this->_original;
        else if (func_num_args() == 1) return $this->_original[func_get_arg(0)];
        else return $this->_original[func_get_arg(0)] = func_get_arg(1);
    }

    /**
     * This function sets of gets a value of $this->_temporary array by a given key (argument #1)
     * using a given value (argument # 2)
     *
     * @return mixed
     */
    public function temporary() {
        if (func_num_args() == 0) return $this->_temporary;
        else if (func_num_args() == 1) return $this->_temporary[func_get_arg(0)];
        else return $this->_temporary[func_get_arg(0)] = func_get_arg(1);
    }

    /**
     * Forces value setting for a given key at $this->_modified array,
     * without 'same-value' check. Actually this function was created
     * to deal with cases, when we need to skip prepending a hue number
     * to #RRGGBB color, because we need to display color value without hue number in forms.
     *
     * @return mixed
     */
    public function modified() {
        if (func_num_args() == 0) return $this->_modified;
        else if (func_num_args() == 1) return $this->_modified[func_get_arg(0)];
        else return $this->_modified[func_get_arg(0)] = func_get_arg(1);
    }

    /**
     * This function sets of gets a value of $this->_system array by a given key (argument #1)
     * using a given value (argument # 2)
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
     * @return mixed
     */
    public function compiled() {
        if (func_num_args() == 0) return $this->_compiled;
        else if (func_num_args() == 1) return $this->_compiled[func_get_arg(0)];
        else return $this->_compiled[func_get_arg(0)] = func_get_arg(1);
    }

    /**
     * Proxy to __isset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset) {
        return $this->__isset($offset);
    }

    /**
     * Proxy to __get
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return mixed|string
     */
    public function offsetGet($offset) {
        return $this->__get($offset);
    }

    /**
     * Proxy to __set
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        $this->__set($offset, $value);
    }

    /**
     * Proxy to __unset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset) {
        return $this->__unset($offset);
    }
}
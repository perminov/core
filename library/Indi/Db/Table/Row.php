<?php
class Indi_Db_Table_Row implements ArrayAccess
{
    /**
     * Table name of table, that current row is related to
     *
     * @var string
     */
    protected $_table = '';

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
     * Array of names of the fields, that were affected by the last ->save() call
     *
     * @var array
     */
    protected $_affected = array();

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
     * Array containing meta information about uploaded files
     *
     * @var array
     */
    protected $_files = array();

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
     * Used to store data, required for rendering the UI for current row's properties.
     * Usage: $row->view('someRowProperty', array('someparam1' => 'somevalue1')), assuming that 'someRowProperty'
     * is a field, that need to have some additional params for being properly displayed in the UI
     *
     * @var array
     */
    protected $_view = array();

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array()) {

        // Setup initial properties
        $this->_init($config);

        // Compile php expressions stored in allowed fields and assign results under separate keys in $this->_compiled
        foreach ($this->model()->getEvalFields() as $evalField) {
            if (strlen($this->_original[$evalField])) {
                Indi::$cmpTpl = $this->_original[$evalField]; eval(Indi::$cmpRun); $this->_compiled[$evalField] = Indi::cmpOut();
            }
        }
    }

    /**
     * Setup initial properties
     *
     * @param array $config
     */
    protected function _init(array $config = array()) {
        $this->_table = $config['table'];
        $this->_original = $this->fixTypes($config['original']);
        $this->_modified = is_array($config['modified']) ? $config['modified'] : array();
        $this->_system = is_array($config['system']) ? $config['system'] : array();
        $this->_temporary = is_array($config['temporary']) ? $config['temporary'] : array();
        $this->_foreign = is_array($config['foreign']) ? $config['foreign'] : array();
        $this->_nested = is_array($config['nested']) ? $config['nested'] : array();
    }

    /**
     * Fix types of data, got from PDO
     *
     * @param array $data
     * @return array
     */
    public final function fixTypes(array $data) {

        // Foreach prop check
        foreach ($data as $k => $v) {

            // If prop's value is a string, containing integer value - force value type to be integer, not string
            if (preg_match(Indi::rex('int11'), $v)) $data[$k] = (int) $v;

            // Else if prop's value is a string, containing decimal value - force value type to be float, not string
            else if (preg_match(Indi::rex('decimal112'), $v)) $data[$k] = (float) $v;
        }

        // Return
        return $data;
    }

    /**
     * Get the title of current row
     *
     * @return string
     */
    public function title() {

        return $this->{$this->model()->titleColumn()};
    }

    /**
     * Update current row title in case if title is dependent from some foreign key data.
     * After that, function also updates all titles, that are dependent on current row title
     *
     * @param Field_Row $titleFieldR
     */
    public function titleUpdate(Field_Row $titleFieldR) {

        // If field, used as title field - is storing single foreign key
        if ($titleFieldR->storeRelationAbility == 'one') {

            // If foreign row can be successfully got by that foreign key
            if ($this->foreign($titleFieldR->alias))

                // Set current row's title as value, got by title() call on foreign row
                $this->title = $this->foreign($titleFieldR->alias)->title();

        // Else if field, that is used as title field - is storing multiple foreign keys
        } else if ($titleFieldR->storeRelationAbility == 'many') {

            // Declare $titleA array
            $titleA = array();

            // Foreach foreign row within foreign rowset, got by multiple foreign keys
            foreach ($this->foreign($titleFieldR->alias) as $foreignR)

                // Append the result of title() method call in foreign row to $titleA array
                $titleA[] = $foreignR->title();

            // Setup current row's title as values of $titleA array, imploded by comma
            $this->title = implode(', ', $titleA);
        }

        // Update title
        if (preg_match('/^one|many$/', $titleFieldR->storeRelationAbility)) {
            $this->model()->update(
                array('title' => ($this->title = mb_substr($this->title, 0, 255, 'utf-8'))),
                '`id` = "' . $this->id . '"'
            );
        }

        // Update dependent titles
        $this->titleUsagesUpdate();
    }

    /**
     * Flush a special-formatted json error message, in case if current row has mismatches
     *
     * @param bool $check If this param is omitted or `false` (by default) - function will look at existing mismatches.
     *                    Otherwise, it will run a distinct mismatch check-up, and then behave on results
     */
    public function mflush($check = false) {

        // Check conformance to all requirements / Ensure that there are no mismatches
        if (count($this->mismatch($check))) {

            // Rollback changes
            Indi::db()->rollback();

            // Build an array, containing mismatches explanations
            $mismatch = array(
                'entity' => array(
                    'title' => $this->model()->title(),
                    'entry' => $this->id
                ),
                'errors' => $this->_mismatch,
                'trace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1)
            );

            // Log this error if logging of 'jerror's is turned On
            if (Indi::logging('mflush')) Indi::log('mflush', $mismatch);

            // Flush mismatch
            jflush(false, array('mismatch' => $mismatch));
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

        // If current row has any mismatches - flush that mismatches
        $this->mflush(true);

        // Backup original and modified data
        $original = $this->_original; $modified = $this->_modified;

        // Setup $orderAutoSet flag if need
        if (!$this->_original['id'] && array_key_exists('move', $this->_original) && !$this->move) $orderAutoSet = true;

        // If current row is an existing row
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

            // Set up a $new flag, indicating that this will be a new row
            $new = true;

            // Do some needed operations that are required to be done right before row insertion into a database table
            $this->onBeforeInsert();

            // Execute the INSERT sql query, get LAST_INSERT_ID and assign it as current row id
            $this->_original['id'] = $this->model()->insert($this->_modified);

            // Merge $this->_original and $this->_modified arrays into $this->_original array
            $this->_original = (array) array_merge($this->_original, $this->_modified);

            // Empty $this->_modified and $this->_mismatch arrays
            $this->_modified = $this->_mismatch = array();

            // Setup $return variable as id of current (a moment ago inserted) row
            $return = $this->_original['id'];
        }

        // Provide a changelog recording, if configured
        $this->changeLog($original);

        // Auto set `move` if need
        if ($orderAutoSet) {

            // Set `move` property equal to current row id
            $this->move = $this->id;

            // Update row data
            $this->model()->update($this->_modified, '`id` = "' . $this->_original['id'] . '"');
            $this->_original = (array) array_merge($this->_original, $this->_modified);
            $this->_modified = array();
        }

        // If current entity has a non-zero `titleFieldId` property
        if ($titleFieldR = $this->model()->titleField()) {

            // If value of field, that is used as title-field - was modified
            if (in_array($titleFieldR->alias, array_keys($modified))) {

                // Update title
                $this->titleUpdate($titleFieldR);
            }

        // Else if current entity has an empty/zero `titleFieldId` property, but current row was an already existing row
        // and entity's database table column, that is however used as a title-column - was modified
        } else if ($original['id'] && in_array($this->model()->titleColumn(), array_keys($modified))) {

            // Search and update usages
            $this->titleUsagesUpdate();
        }

        // Update cache if need
        if (Indi::ini('db')->cache && $this->model()->useCache()) Indi_Cache::update($this->model()->table());

        // Adjust file-upload fields contents according to meta info, existing in $this->_files for such fields
        $this->files(true);

        // Do some needed operations that are required to be done right after row was inserted/updated
        if ($new) $this->onInsert(); else $this->onUpdate();

        // Return current row id (in case if it was a new row) or number of affected rows (1 or 0)
        return $return;
    }

    /**
     * This function is called right before 'return ...' statement within Indi_Db_Table_Row::save() body.
     * It can be useful in cases when we need to do something once where was an entry inserted in database table
     */
    public function onInsert() {

    }

    /**
     * This function is called right before '$this->model()->insert(..)' statement within Indi_Db_Table_Row::save() body.
     * It can be useful in cases when we need to do something before where will be an entry inserted in database table
     */
    public function onBeforeInsert() {

    }

    /**
     * This function is called right before 'return ...' statement within Indi_Db_Table_Row::save() body.
     * It can be useful in cases when we need to do something once where was an entry updated in database table
     */
    public function onUpdate() {

    }

    /**
     * This function is called right before 'return ...' statement within Indi_Db_Table_Row::delete() body.
     * It can be useful in cases when we need to do something once where was an entry deleted from database table
     */
    public function onDelete() {

    }

    /**
     * Update titles of all rows, that use current row for building title
     */
    public function titleUsagesUpdate() {

        // Get the model-usages info as entityId and titleFieldAlias
        $usageA = Indi::db()->query('
            SELECT `e`.`id` AS `entityId`, `e`.`table`, `f`.`alias` AS `titleFieldAlias`
            FROM `entity` `e`, `field` `f`
            WHERE `f`.`relation` = "' . $this->model()->id() . '" AND `e`.`titleFieldId` = `f`.`id`
        ')->fetchAll();

        // Foreach model usage
        foreach ($usageA as $usageI) {

            // Get the model
            $model = Indi::model($usageI['entityId']);

            // Get the field
            $titleFieldR = $model->fields($usageI['titleFieldAlias']);

            // Build WHERE clause
            $where = $titleFieldR->storeRelationAbility == 'one'
                ? '`' . $titleFieldR->alias .'` = "' . $this->id . '"'
                : 'FIND_IN_SET("' . $this->id . '", `' . $titleFieldR->alias . '`)';

            // Get the rows, that use current row for building their titles
            $rs = $model->fetchAll($where);

            // Setup foreign data, for it to be fetched within a single request
            // to database server, instead of multiple request for each row within rowset
            $rs->foreign($titleFieldR->alias);

            // Foreach row -  update it's title
            foreach ($rs as $r) $r->titleUpdate($titleFieldR);
        }
    }

    /**
     * Provide Move up/Move down actions for row within the needed area of rows
     *
     * @param string $direction (up|down)
     * @param string $within
     * @return bool
     */
    public function move($direction = 'up', $within = '') {

        // Check direction validity
        if (in_array($direction, array('up', 'down'))) {

            // Setup initial WHERE clause, for being able to detect the scope of rows, that order should be changed within
            $where = is_array($within) ? $within : (strlen($within) ? array($within): array());

            // Apend additional part to WHERE clause, in case if current entity - is a tree-like entity
            if ($this->model()->treeColumn())
                $where[] = '`' . $this->model()->treeColumn() . '` = "' . $this->{$this->model()->treeColumn()} . '"';

            // Append nearest-neighbour WHERE clause part, for finding the row,
            // that current row should exchange value of `move` property with
            $where[] = '`move` ' . ($direction == 'up' ? '<' : '>') . ' "' . $this->move . '"';

            // Setup ORDER clause
            $order = 'move ' . ($direction == 'up' ? 'DE' : 'A') . 'SC';

            // Find row, that will be used for `move` property value exchange
            if ($changeRow = $this->model()->fetchRow($where, $order)) {

                // Backup `move` of current row
                $backup = $this->move;

                // We exchange values of `move` fields
                $this->move = $changeRow->move;
                $this->save();
                $changeRow->move = $backup;
                $changeRow->save();

                // If `move` property of current row an $changeRow row was successfully exchanged -
                // return boolean true as an indicator of success
                if (!$this->mismatch() && !$changeRow->mismatch()) return true;
            }
        }
    }

    /**
     * Fully deletion - including attached files and foreign key usages, if will be found
     *
     * @return int Number of deleted rows (1|0)
     */
    public function delete() {

        // Delete other rows of entities, that have fields, related to entity of current row
        // This function also covers other situations, such as if entity of current row has a tree structure,
        // or row has dependent rowsets
        $this->deleteUsages();

        // Standard deletion
        $return = $this->model()->delete('`id` = "' . $this->_original['id'] . '"');

        // Delete all files (images, etc) that have been attached to row
        $this->deleteFiles();

        // Delete all files/folder uploaded/created while using CKFinder
        $this->deleteCKFinderFiles();

        // Delete all `changeLog` entries, related to current entry
        $this->deleteChangeLog();

        // Do some custom things
        $this->onDelete();

        // Unset `id` prop
        $this->id = null;

        // Return
        return $return;
    }

    /**
     * Delete all `changeLog` entries, related to current entry
     */
    public function deleteChangeLog() {

        // If `id` prop is null/zero/false/empty - return
        if (!$this->id) return;

        // If `ChangeLog` model does not exist - return
        if (!$changeLogM = Indi::model('ChangeLog', true)) return;

        // Find and delete related `changeLog` entries
        $changeLogM->fetchAll(array(
            '`entityId` = "' . $this->model()->id() . '"',
            '`key` = "' . $this->id . '"'
        ))->delete();
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
     * @param null $consistence
     * @return Indi_Db_Table_Rowset_Combo
     */
    public function getComboData($field, $page = null, $selected = null, $selectedTypeIsKeyword = false,
                                 $satellite = null, $where = null, $noSatellite = false, $fieldR = null,
                                 $order = null, $dir = 'ASC', $offset = null, $consistence = null, $multiSelect = null) {

        // Basic info
        $fieldM = Indi::model('Field');
        $fieldR = $fieldR ? $fieldR : Indi::model($this->_table)->fields($field);
        $fieldColumnTypeR = $fieldR->foreign('columnTypeId');
        if ($fieldR->relation) $relatedM = Indi::model($fieldR->relation);

        // Array for WHERE clauses
        $where = $where ? (is_array($where) ? $where : array($where)): array();

        // Setup filter, as one of possible parts of WHERE clause
        if ($fieldR->filter) $where[] = $fieldR->filter;

        // Compile filters if they contain php-expressions
        for($i = 0; $i < count($where); $i++) {
            Indi::$cmpTpl = $where[$i]; eval(Indi::$cmpRun); $where[$i] = Indi::cmpOut();
        }

        // If $multiSelect argument is not given - detect it automatically
        if ($multiSelect === null) $multiSelect = $fieldR->storeRelationAbility == 'many';

        // If current field column type is ENUM or SET
        if (preg_match('/ENUM|SET/', $fieldColumnTypeR->type)) {

            // Use existing enumset data, already nested for current field, instead of additional db fetch
            $dataRs = $fieldR->nested('enumset');

            // If $consistence argument is given, and it's an array, we assume it's an explicit definition of
            // a number of combo data items, that should ONLY be displayed. ONLY here mean combo items will be
            // exact as in $consistence array, not less and not greater. This feature is used for rowset filters,
            // and is a part of a number of tricks, that provide the availability of filter-combo data-options only
            // for data-options, that will have at least one matching row within rowset, in case of their selection
            // as a part of a rowset search criteria.
            if (is_array($consistence)) $dataRs = $dataRs->select($consistence, 'alias');

            // We should mark rowset as related to field, that has a ENUM or SET column type
            // because values of property `alias` should be used as options keys, instead of values of property `id`
            $dataRs->enumset = true;

            // If current field store relation ability is 'many' - we setup selected as rowset object
            if ($multiSelect) $dataRs->selected = $dataRs->select($selected, 'alias');

            // Return combo data
            return $dataRs;

        // Else if current field column type is BOOLEAN - combo is used as an alternative for checkbox control
        } else if ($fieldColumnTypeR->type == 'BOOLEAN') {

            // Prepare the data
            $dataRs = Indi::model('Enumset')->createRowset(
                array(
                    'data' => array(
                        array('alias' => '0', 'title' => I_NO),
                        array('alias' => '1', 'title' => I_YES)
                    )
                )
            );

            // If $consistence argument is given, and it's an array, we assume it's an explicit definition of
            // a number of combo data items, that should ONLY be displayed. ONLY here mean combo items will be
            // exact as in $consistence array, not less and not greater. This feature is used for rowset filters,
            // and is a part of a number of tricks, that provide the availability of filter-combo data-options only
            // for data-options, that will have at least one matching row within rowset, in case of their selection
            // as a part of a rowset search criteria.
            if (is_array($consistence)) $dataRs = $dataRs->select($consistence, 'alias');

            // Setup `enumset` prop as `true`
            $dataRs->enumset = true;

            // Return
            return $dataRs;

        // Else if combo data is being prepared for an usual (non-boolean and non-enumset) combo
        } else

            // If $consistence argument is given, and it's an array, we assume it's an explicit definition of
            // a number of combo data items, that should ONLY be displayed. ONLY here mean combo items will be
            // exact as in $consistence array, not less and not greater. This feature is used for rowset filters,
            // and is a part of a number of tricks, that provide the availability of filter-combo data-options only
            // for data-options, that will have at least one matching row within rowset, in case of their selection
            // as a part of a rowset search criteria. The $consistence array is being taken into consideration even
            // if it constains no elements ( - zero-length array), in this case
            if (is_string($consistence) && strlen($consistence)) $where[] = $consistence;

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
                    $satellite = $satelliteR->compiled('defaultValue');
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
                    $v = $rowLinkedToSatellite->{$fieldR->alternative};
                    $c = $satelliteR->satellitealias ? $satelliteR->satellitealias : $fieldR->alternative;
                    $alternativeR = Indi::model($satelliteR->relation)->fields($fieldR->alternative);
                    $where[] = in('many', array($satelliteR->storeRelationAbility, $alternativeR->storeRelationAbility))
                        && preg_match('/,/', $v)
                        ? 'CONCAT(",", `' . $c . '`, ",") REGEXP ",(' . implode('|', explode(',', $v)) . '),"'
                        : 'FIND_IN_SET("' . $v . '", `' . $c . '`)';

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

                    $where[] = $satelliteR->storeRelationAbility == 'many' && preg_match('/,/', $satellite)
                        ? 'CONCAT(",", `' . $satelliteR->satellitealias . '`, ",") REGEXP ",(' . implode('|', explode(',', $satellite)) . '),"'
                        : 'FIND_IN_SET("' . $satellite . '", `' . $satelliteR->satellitealias . '`)';

                // Standard logic
                } else {

                    $where[] = $satelliteR->storeRelationAbility == 'many' && preg_match('/,/', $satellite)
                        ? 'CONCAT(",", `' . $satelliteR->alias . '`, ",") REGEXP ",(' . implode('|', explode(',', $satellite)) . '),"'
                        : 'FIND_IN_SET("' . $satellite . '", `' . $satelliteR->alias . '`)';
                }

            // If dependency type is 'Variable entity' we replace $relatedM object with calculated model
            } else if ($fieldR->dependency == 'e' && $satellite) {
                $relatedM = Indi::model($satellite);
            }
        }

        // If we have no related model - this happen if we have 'varibale entity' satellite dependency type
        // and current satellite value is not defined - we return empty rowset
        if (!$relatedM) return new Indi_Db_Table_Rowset(array('titleColumn' => 'title', 'rowClass' => __CLASS__));

        // Get title column
        $titleColumn = $fieldR->params['titleColumn'] ?: $relatedM->titleColumn();

        // Set ORDER clause for combo data
        if (is_null($order)) {
            if ($relatedM->comboDataOrder) {
                $order = $relatedM->comboDataOrder;
                if (!func_get_arg(9) && $relatedM->comboDataOrderDirection)
                    $dir = $relatedM->comboDataOrderDirection;
            } else if ($relatedM->fields('move') && $relatedM->treeColumn()) {
                $order = 'move';
            } else {
                $order = $titleColumn;
            }

            // If $order is not null, but is an empty string, we set is as 'id' for results being fetched in the order of
            // their physical appearance in database table, however, regarding $dir (ASC or DESC) param.
        } else if (!is_array($order) && !strlen($order)) {
            $order = 'id';
        }

        // Here and below we will be always checking if $order is not empty
        // because we can have situations, there order is not set at all and if so, we won't use ORDER clause
        // So, if order is empty, the results will be retrieved in the order of their physical placement in
        // their database table
        if (!is_array($order) && !preg_match('/\(/', $order)) $order = '`' . $order . '`';

        // If fetch-mode is 'keyword'
        if ($selectedTypeIsKeyword) {
            //$keyword = str_replace('"','\"', $selected);
            $keyword = $selected;

        // Else if fetch-mode is 'no-keyword'
        } else {

            // Get selected row
            $selectedR = $relatedM->fetchRow('`id` = "' . $selected . '"');

            // Setup current value of a sorting field as start point
            if (!is_array($order) && $order && !preg_match('/\(/', $order)) {
                //$keyword = str_replace('"','\"', $selectedR->{trim($order, '`')});
                $keyword = $selectedR->{trim($order, '`')};
            }
        }

        // Alternate WHERE
        if (Indi::admin()->alternate && !$fieldR->ignoreAlternate
            && $alternateField = $relatedM->fields(Indi::admin()->alternate . 'Id'))
            $where[] = $alternateField->storeRelationAbility == 'many'
                ? 'FIND_IN_SET("' . Indi::admin()->id . '", `' . $alternateField->alias . '`)'
                : '`' . $alternateField->alias . '` = "' . Indi::admin()->id .'"';

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

                if (!is_array($order)) $order .= ' ' . ($dir == 'DESC' ? 'DESC' : 'ASC');

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
            if ($selected && (($fieldR->storeRelationAbility == 'one' && !$multiSelect) || $selectedTypeIsKeyword)) {

                // We do a backup for WHERE clause, because it's backup version
                // will be used to calc foundRows property in case if $selectedTypeIsKeyword = false
                $whereBackup = $where;

                // Get WHERE clause for options fetch
                if ($selectedTypeIsKeyword) {

                    // Check if keyword is a part of color value in format #rrggbb, and if so, we use RLIKE instead
                    // of LIKE, and prepare a special regular expression
                    if (preg_match('/^#[0-9a-fA-F]{0,6}$/', $keyword)) {
                        $rlike = '^[0-9]{3}' . $keyword . '[0-9a-fA-F]{' . (7 - mb_strlen($keyword, 'utf-8')) . '}$';
                        $where['lookup'] = '`' . $titleColumn . '` RLIKE "' . $rlike . '"';

                    // Else
                    } else $where['lookup'] = ($keyword2 = str_replace('"', '\"', Indi::kl($keyword)))
                        ? '(`' . $titleColumn . '` LIKE "%' . str_replace('"', '\"', $keyword) . '%" OR `' . $titleColumn . '` LIKE "%' . $keyword2 . '%")'
                        : '`' . $titleColumn . '` LIKE "%' . str_replace('"', '\"', $keyword) . '%"';

                // We should get results started from selected value only if we have no $satellite argument passed
                } else if (is_null(func_get_arg(4))) {

                    // If $order is a name of a column, and not an SQL expression, we setup results start point as
                    // current row's column's value
                    if (!preg_match('/\(/', $order)) {
                        $where['lookup'] = $order . ' '. (is_null($page) || $page > 0 ? ($dir == 'DESC' ? '<=' : '>=') : ($dir == 'DESC' ? '>' : '<')).' "' . str_replace('"', '\"', $keyword) . '"';
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
                $foundRowsWhere = im($selectedTypeIsKeyword ? $where : $whereBackup, ' AND ');

                // Adjust WHERE clause so it surely match existing value
                if (is_null(func_get_arg(4))) $this->comboDataExistingValueWHERE($foundRowsWhere, $fieldR, $consistence);

                //
                $foundRowsWhere = $foundRowsWhere ? 'WHERE ' . $foundRowsWhere : '';

                // Get number of total found rows
                $found = Indi::db()->query(
                    'SELECT COUNT(`id`) FROM `' . $relatedM->table() . '`' . $foundRowsWhere
                )->fetchColumn(0);

                // If results should be started from selected value but total found rows number if not too great
                // we will not use selected value as start point for results, because there will be a sutiation
                // that PgUp or PgDn should be pressed to view all available options in combo, instead of being
                // available all initially
                if ($resultsShouldBeStartedFromSelectedValue && $found <= self::$comboOptionsVisibleCount) {
                    unset($where['lookup']);
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

                // Else
                } else {

                    // Append order direction
                    $order .= ' ' . ($dir == 'DESC' ? 'DESC' : 'ASC');

                    // Adjust WHERE clause so it surely match existing value
                    if (!$selectedTypeIsKeyword && is_null(func_get_arg(4))) $this->comboDataExistingValueWHERE($where, $fieldR, $consistence);
                }

                // Fetch raw combo data
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

                    // Adjust WHERE clause so it surely match consistence values
                    if (is_null($page) && !$selectedTypeIsKeyword && is_null(func_get_arg(4))) 
                        $this->comboDataExistingValueWHERE($where, $fieldR, $consistence);

                    // Fetch raw combo data
                    $dataRs = $relatedM->fetchAll($where, $order, self::$comboOptionsVisibleCount, $page + 1);
                }
            }
        }

        // If results should be grouped (similar way as <optgroup></optgroup> do)
        if ($fieldR->params['groupBy']) {

            // Get distinct values
            $distinctGroupByFieldValues = array();
            foreach ($dataRs as $dataR)
                if (!$distinctGroupByFieldValues[$dataR->{$fieldR->params['groupBy']}])
                    $distinctGroupByFieldValues[$dataR->{$fieldR->params['groupBy']}] = true;

            // Get group field
            $groupByFieldR = $fieldM->fetchRow(array(
                '`entityId` = "' . $fieldR->relation . '"',
                '`alias` = "' . $fieldR->params['groupBy'] . '"'
            ));

            // Get group field related entity model
            $groupByFieldEntityM = Indi::model($groupByFieldR->relation);

            // Get titles for optgroups
            $groupByOptions = array();
            if ($groupByFieldEntityM->table() == 'enumset') {

                $groupByRs = $groupByFieldEntityM->fetchAll(array(
                    '`fieldId` = "' . $groupByFieldR->id . '"',
                    'FIND_IN_SET(`alias`, "' . implode(',', array_keys($distinctGroupByFieldValues)) . '")'
                ));

                $keyProperty = 'alias';
            } else {

                $groupByRs = $groupByFieldEntityM->fetchAll(
                    'FIND_IN_SET(`id`, "' . implode(',', array_keys($distinctGroupByFieldValues)) . '")'
                );
                $keyProperty = 'id';
            }

            $titleColumn = $groupByFieldEntityM->titleColumn();

            foreach ($groupByRs as $groupByR) {

                // Here we are trying to detect, does $o->title have tag with color definition, for example
                // <span style="color: red">Some option title</span> or <font color=lime>Some option title</font>, etc.
                // We should do that because such tags existance may cause a dom errors while performing usubstr()
                $info = Indi_View_Helper_Admin_FormCombo::detectColor(array(
                    'title' => $groupByR->$titleColumn,
                    'value' => $groupByR->$keyProperty
                ));

                // Reset $system array
                $system = array();

                // If color was detected as a box, append $system['boxColor'] property
                if ($info['box']) $system['boxColor'] = $info['color'];

                // If non-box color was detected - setup a 'color' property
                if ($info['style']) $system['color'] = $info['color'];

                // Setup primary option data
                $groupByOptions[$groupByR->$keyProperty] = array('title' => usubstr($info['title'], 50), 'system' => $system);
            }

            $dataRs->optgroup = array('by' => $groupByFieldR->alias, 'groups' => $groupByOptions);
        }

        // If additional params should be passed as each option attributes, setup list of such params
        if ($fieldR->params['optionAttrs']) {
            $dataRs->optionAttrs = explode(',', $fieldR->params['optionAttrs']);
        }

        // Set `enumset` property as false, because without definition it will have null value while passing
        // to indi.combo.form.js and and after Indi.copy there - will have typeof == object, which is not actually boolean
        // and will cause problems in indi.combo.form.js
        $dataRs->enumset = false;

        if ($fieldR->storeRelationAbility == 'many' || $multiSelect) {
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
                if (count($selectedThatShouldBeAdditionallyFetched = array_diff($selected, $allFetchedIds))) {
                    $data = array_merge($data, $relatedM->fetchAll('
                        FIND_IN_SET(`id`, "' . implode(',', $selectedThatShouldBeAdditionallyFetched) . '")
                    ')->rows());
                }

                // Create unsorted rowset
                $unsortedRs = $relatedM->createRowset(array('rows' => $data));

                // Build array containing rows, that are ordered within that array
                // in the same order as their ids in $selected comma-separated string
                foreach (ar($selected) as $id) if ($row = $unsortedRs->select($id)->at(0)) $sorted[] = $row;

                // Unset $unsorted
                unset($unsortedRs);

                // Setup `selected` property as a *_Rowset object, containing properly ordered rows
                $dataRs->selected = $relatedM->createRowset(array('rows' => $sorted));

                // Unset $sorted
                unset($sorted);

            // Else
            } else {
                $dataRs->selected = $relatedM->createRowset(array('rows' => array()));
            }
        }

        // Setup combo data rowset title column
        $dataRs->titleColumn = $titleColumn;

        // If foreign data should be fetched
        if ($fieldR->params['foreign']) $dataRs->foreign($fieldR->params['foreign']);

        // Return combo data rowset
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

        // If $key argument is an array, it mean that key argument contains info about not only multiple foreign rows
        // to be fetched, but also info about sub-nested rowsets and sub-foreign rows that should be fetched too
        if (is_array($key)) {

            // Create a rowset with current row as a single row within that rowset
            $rowset = Indi::trail()->model->createRowset(array('rows' => array($this)));

            // Fetch all required data using rowset's foreign() method instead of row's foreign() method
            $rowset->foreign($key);

            // Pick the one existing row from rowset and unset rowset to release RAM
            $row = $rowset->rewind()->current(); unset($rowset);

            // Get the _foreign property of picked row and use it as value for $this's _foreign property
            $this->_foreign = $row->foreign();

            // Unset picked row to release RAM
            unset($row);

            // Return current row itself, but now with properly updated _foreign property
            return $this;
        }

        // If $key argument contains more than one key name - we setup rows for all keys
        if (preg_match('/,/',$key)) {

            // Explode keys by comma
            $keyA = explode(',', $key);

            // Fetch foreign data for each key separately
            foreach ($keyA as $keyI) {

                // If $refresh arg is boolean true, or if value, stored under $keyI was modified
                // set up $refresh_ flag as boolean true
                $refresh_ = $refresh ?: array_key_exists(trim($keyI), $this->_modified);

                // Fetch foreign data
                $this->foreign(trim($keyI), $refresh_);
            }

            // Return current row
            return $this;

        } else if (!$key) {
            return $this->_foreign;
        }

        // If $refresh argument is an object, we interpret it as a foreign row, and assign it directly
        if (is_string($key) && is_object($refresh)) return $this->_foreign[$key] = $refresh;

        // If $refresh arg is boolean true, or if value, stored under $key was modified
        // set up $refresh_ flag as boolean true
        $refresh_ = $refresh ?: array_key_exists(trim($key), $this->_modified);

        // If foreign row, got by foreign key, was got already got earlier, and no refresh should be done - return it
        if (array_key_exists($key, $this->_foreign) && !$refresh_) {
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
                        ? $this->{$fieldR->foreign('satellite')->alias}
                        : $fieldR->relation;

                    // Determine a fetch method
                    $methodType = $fieldR->storeRelationAbility == 'many' ? 'All' : 'Row';

                    // Declare array for WHERE clause
                    $where = array();

                    // If field is related to enumset entity, we should append an additional WHERE clause,
                    // that will outline the `fieldId` value, because in this case current row store aliases
                    // of rows from `enumset` table instead of ids, and aliases are not unique within that table.
                    if (Indi::model($model)->table() == 'enumset') {
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
                    $foreign = Indi::model($model)->{'fetch' . $methodType}(
                        $where,
                        $fieldR->storeRelationAbility == 'many'
                            ? 'FIND_IN_SET(`' . $col . '`, "' . $this->$key . '")'
                            : null
                    );
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
     * @return Indi_Db_Table
     */
    public function model() {
        return Indi::model($this->_table);
    }

    /**
     * Return a database table name, that current row is dealing with
     *
     * @return string
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
     * Mark for deletion
     */
    public function m4d() {

        // If `m4d` field does not exist - flush an error message
        if (!$this->model()->fields('m4d')) jflush(false, sprintf(I_ROWM4D_NO_SUCH_FIELD, $this->model()->title()));

        // Do mark
        $this->m4d = 1;

        // Save
        $this->save();
    }

    /**
     * Delete all usages of current row
     */
    public function deleteUsages() {

        // Declare entities array
        $entities = array();

        // Get all fields in whole database, which are containing keys related to this entity
        $fieldRs = Indi::model('Field')->fetchAll('`relation` = "' . $this->model()->id() . '"');
        foreach ($fieldRs as $fieldR) $entities[$fieldR->entityId]['fields'][] = $fieldR;

        // Get auxiliary deletion info within each entity
        foreach ($entities as $eid => $data) {

            // Load model
            $model = Indi::model($eid);

            // Foreach field within current model
            foreach ($data['fields'] as $field) {
                // We should check that column - which will be used in WHERE clause for retrieving a dependent rowset -
                // still exists. We need to perform this check because this column may have already been deleted, if
                // it was dependent of other column that was deleted.
                if ($model->fields($field->alias) && $field->columnTypeId && Indi::db()->query(
                    'SHOW COLUMNS FROM `' . $model->table(). '` LIKE "' . $field->alias . '"'
                )->fetchColumn()) {

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
     * <upload path>/<entity|table|model name>/<row id>_<file upload field alias>,<resized copy name>.<file extension>
     *  (in case if uploaded file is an image, and resized copy autocreation was set up)
     *
     * This function get all parts of the pattern, build it, and finds all files that match this pattern
     * with glob() php function usage, so uploaded files names and/or paths and/or extensions are not stored in db,
     *
     * @param string $field The alias of field, that is File Upload field (Aliases of such fields are used in the
     *                     process of filename construction for uploaded files to saved under)
     * @throws Exception
     */
    public function deleteFiles($field = '') {

        // If upload dir does not exist - return
        if (($dir = $this->model()->dir('exists')) === false) return;

        // If $field argument is not given
        if (!$field) {

            // We assume that all files, uploaded using all (not certain) file upload fields should be deleted,
            // so we get the array of aliases of file upload fields within entity, that current row is related to
            $alias = array();
            foreach ($this->model()->fields() as $fieldR)
                if ($fieldR->foreign('elementId')->alias == 'upload')
                    $alias[] = $fieldR->alias;

            // If no 'upload' fields found - return
            if (!$alias) return;

            // Use that file upload fields aliases list to build a part of a pattern for use in php glob() function
            $field = '{' . im($alias) . '}';
        }

        // If value of $field variable is still empty - return
        if (!$field) return;

        // Get all of the possible files, uploaded using that field, and all their versions
        $fileA = glob($dir . $this->id . '_' . $field . '[.,]*', GLOB_BRACE);

        // Delete them
        foreach ($fileA as $fileI) @unlink($fileI);
    }


    /**
     * Delete all of the files/folders uploaded/created as a result of CKFinder usage. Actually,
     * this function can do a deletion only in one case - if entity, that current row is related to
     * - is involved in 'alternate-cms-users' feature. This feature assumes, that any row, related to
     * such an entity/model - is representing a separate user account, that have ability to sign in into the
     * Indi Engine system interface, and therefore may have access to CKFinder
     *
     * @return mixed
     */
    public function deleteCKFinderFiles () {

        // If CKFinder upload dir (special dir for current row instance) does not exist - return
        if (($dir = $this->model()->dir('exists', $this->id)) === false) return;

        // Delete recursively all the contents - folder and files
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }

        // Remove the directory itself
        rmdir($dir);
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

        // Here were omit STD's one or more dir levels at the ending, in case if
        // Indi::ini('upload')->path is having one or more '../' at the beginning
        $std = STD; $uph = Indi::ini('upload')->path;
        if (preg_match(':^(\.\./)+:', $uph, $m)) {
            $uph = preg_replace(':^(\.\./)+:', '', $uph);
            $lup = count(explode('/', rtrim($m[0], '/')));
            for ($i = 0; $i < $lup; $i++) $std = preg_replace(':/[a-zA-Z0-9_\-]+$:', '', $std);
        }

        // Get the name of the directory, relative to document root,
        // and where all files related model of current row are located
        $src =  $std . '/' . $uph . '/' . $this->_table . '/';

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
     * @param bool $dc Whether or not to append modification timestamp, for disabling browser cache
     * @param bool $std Whether or not to prepend returned value with STD
     * @return string|null
     */
    public function src($alias, $copy = '', $dc = false, $std = false) {

        // Get the filename with absolute path
        if ($abs = preg_match('/^([A-Z]:|\/)/', $alias) ? $alias : $this->abs($alias, $copy)) {

            // Here were omit STD's one or more dir levels at the ending, in case if
            // Indi::ini('upload')->path is having one or more '../' at the beginning
            $std_ = STD;
            if (preg_match(':^(\.\./)+:', Indi::ini('upload')->path, $m)) {
                $lup = count(explode('/', rtrim($m[0], '/')));
                for ($i = 0; $i < $lup; $i++) $std_ = preg_replace(':/[a-zA-Z0-9_\-]+$:', '', $std_);
            }

            // Return path, relative to document root
            return str_replace(DOC . ($std ? '' : $std_), '', $abs) . ($dc ? '?' . filemtime($abs) : '');
        }
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
            $src = str_replace(DOC . STD, '', $abs);

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
     * @param string $table A table, where rowset will be fetched from
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
                $params = array('where', 'order', 'count', 'page', 'offset', 'foreign', 'nested');

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

            // Fetch nested rowset, assign it under $key key within $this->_nested array
            $this->_nested[$key] = Indi::model($table)->fetchAll($where, $order, $count, $page, $offset);

            // Setup foreign data for nested rowset, if need
            if ($foreign) $this->_nested[$key]->foreign($foreign);

            // Setup nested data for nested rowset, if need
            if (is_string($nested)) $this->_nested[$key]->nested($nested);
            else if (is_array($nested))
                foreach ($nested as $args)
                    $this->_nested[$key]->nested($args[0], $args[1], $args[2], $args[3], $args[4]);

            // Return nested rowset
            return $this->_nested[$key];
        }
    }

    /**
     * Returns the column/value data as an array.
     * If $type param is set to current (by default), the returned array will contain original data
     * with overrided values for keys of $this->_modified array
     *
     * @param string $type current|original|modified|temporary
     * @param bool $deep
     * @param null $purp
     * @return array
     */
    public function toArray($type = 'current', $deep = true, $purp = null) {
        if ($type == 'current') {

            // Merge _original, _modified, _compiled and _temporary array of properties
            $array = (array) array_merge($this->_original, $this->_modified, $purp == 'form' ? array() : $this->_compiled, $this->_temporary);

            // Setup filefields values
            foreach ($this->model()->getFileFields() as $fileField) $array[$fileField] = $this->$fileField;

            // Append _system array as separate array within returning array, under '_system' key
            if (count($this->_system)) $array['_system'] = $this->_system;

            // Append _view array as separate array within returning array, under '_view' key
            if (count($this->_view)) $array['_view'] = $this->_view;

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

        // We trying to find the key's ($columnName) value at first in $this->_modified array,
        // then in $this->_original array, and then in $this->_temporary array, and return
        // once value was found
        if (array_key_exists($columnName, $this->_modified)) return $this->_modified[$columnName];
        else if (array_key_exists($columnName, $this->_original)) return $this->_original[$columnName];
        else if ($this->_temporary[$columnName]) return $this->_temporary[$columnName];
        else if ($fieldR = $this->model()->fields($columnName)) if ($fieldR->foreign('elementId')->alias == 'upload')
            return $this->src($columnName);
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
     * @param string $allowedTags
     * @return string
     */
    public static function safeHtml($html, $allowedTags = '') {

        // Build list of allowed tags, using tags, passed with $allowedTags arg and default tags
        $allowedS = im(array_unique(array_merge(ar('font,span,br'), ar(strtolower($allowedTags)))));

        // Strip all tags, except tags, mentioned in $tags argument
        $html = strip_tags($html, '<' . preg_replace('/,/', '><', $allowedS) . '>');

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

        // Restore double and single quotes
        $html = preg_replace('/&quot;/', '"', $html); $html = preg_replace('/&#039;/', "'", $html);

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
     * @param null|string $message
     * @return array
     */
    public function mismatch($check = false, $message = null) {

        // If $check argument is boolean
        if (is_bool($check)) {

            // If $check argument is set to false, return $this->_mismatch stack, else reset $this->_mismatch array
            if ($check == false) return $this->_mismatch; else $this->_mismatch = array();

        // Else if $check argument is not boolean, and, additionally, $message argument was given
        } else if (func_num_args() == 2) {

            // If $message argument was given, and it is strict null
            if ($message === null) {

                // Delete the item, stored under $check key from $this->_mismatch array
                unset($this->_mismatch[$check]);

                // Return array of all remaining mismatches
                return $this->_mismatch;

            // Else we explicitly setup $message as an item within $this->_mismatch array, under $check key
            } else return $this->_mismatch[$check] = $message;

        // Else we assume that $check argument is field name, so the mismatch for especially that field will be returned
        } else return $this->_mismatch[$check];

        // Return array of errors
        return $this->scratchy() ?: $this->validate();
    }

    /**
     * Custom validation function, to be overridden in child classes if need
     *
     * @return array
     */
    public function validate() {
        return $this->_mismatch;
    }

    /**
     * Validate all modified fields to ensure all of them have values, convenient with their datatypes,
     * collect their errors in $this->_mismatch array, with field names as keys and return it
     *
     * @return array
     */
    public function scratchy() {

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
                        } else  $value .= self::safeHtml($chunk[$i], $fieldR->params['allowedTags']);
                    }

                // Else field is not in list of eval fields, make it's value safe by stripping restricted html tags,
                // and by stripping event attributes from allowed tags
                } else $value = self::safeHtml($value, $fieldR->params['allowedTags']);

            // If element is 'move'
            } else if ($elementR->alias == 'move') {

                // If $value is not a decimal
                if (!preg_match(Indi::rex('int11'), $value)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_INT11, $value, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

            // If element is 'price'
            } else if ($elementR->alias == 'price') {

                // Round the value to 2 digits after floating point
                if (is_numeric($value)) $value = price($value);

                // If $value is not a decimal
                if (!preg_match(Indi::rex('decimal112'), $value)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DECIMAL112, $value, $fieldR->title);

                    // Jump to checking the next column's value
                    continue;
                }

                // If element is 'decimal143'
            } else if ($elementR->alias == 'decimal143') {

                // Round the value to 2 digits after floating point
                if (is_numeric($value)) $value = decimal($value, 3);

                // If $value is not a decimal
                if (!preg_match(Indi::rex('decimal143'), $value)) {

                    // Push a error to errors stack
                    $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DECIMAL143, $value, $fieldR->title);

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
                    $value = hrgb($value);
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

                        // If date, built from $utime and formatted according to 'displayFormat' param
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
                if (!preg_match(Indi::rex('int11lz'), $value)) {

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

                            // Renew year, month and day values
                            list($value['year'], $value['month'], $value['day']) = explode('-', $value['date']);
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

                // If column type is 'DECIMAL(11,2)'
                } else if ($columnTypeR->type == 'DECIMAL(11,2)') {

                    // If $value is not a decimal, or more than 11-digit decimal
                    if (!preg_match(Indi::rex('decimal112'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DECIMAL112, $value, $fieldR->title);

                        // Jump to checking the next column's value
                        continue;
                    }

                // If column type is 'DECIMAL(14,3)'
                } else if ($columnTypeR->type == 'DECIMAL(14,3)') {

                    // If $value is not a decimal, or more than 11-digit decimal
                    if (!preg_match(Indi::rex('decimal143'), $value)) {

                        // Push a error to errors stack
                        $this->_mismatch[$column] = sprintf(I_ROWSAVE_ERROR_VALUE_SHOULD_BE_DECIMAL143, $value, $fieldR->title);

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
                        $value = hrgb($value);
                    }
                }
            }

            // Re-assign the value to column
            $this->$column = $value;
        }

        // Get tree-column name
        $tc = $this->model()->treeColumn();

        // If current model has a tree-column, and current row is an existing row and tree column value was modified
        if ($tc && $this->id && ($parentId_new = $this->_modified[$tc])) {

            // Get the tree column field row object
            $fieldR = $this->model()->fields($tc);

            // If tree-column's value it is going to be same as current row id
            if ($parentId_new == $this->id) {

                // Push a error to errors stack
                $this->_mismatch[$tc] = sprintf(I_ROWSAVE_ERROR_VALUE_TREECOLUMN_INVALID_SELF, $fieldR->title);

            // Else if there is actually no parent row got by such a parent id
            } else if (!$parentR = $this->foreign($tc)) {

                // Push a error to errors stack
                $this->_mismatch[$tc] = sprintf(I_ROWSAVE_ERROR_VALUE_TREECOLUMN_INVALID_404, $parentId_new, $fieldR->title);

            // Else if parent row, got by given parent id, has a non-zero parent row id (mean non-zero grandparent row id for current row)
            } else if ($parentR->$tc) {

                // Backup $parentR
                $_parentR = $parentR;

                // Here we ensure that id, that we gonna set up as parent-row id for a current row - is not equal
                // to current row id, and, even more, ensure that ids of all parent-row's ancestor rows are not
                // equal to current row id too
                do {

                    // If ancestor row id is equal to current row id
                    if ($parentR->$tc == $this->id) {

                        // Push a error to errors stack
                        $this->_mismatch[$tc] = sprintf(I_ROWSAVE_ERROR_VALUE_TREECOLUMN_INVALID_CHILD, $_parentR->title(), $fieldR->title, $this->title());

                        // Break the loop
                        break;

                    // Else get the upper-level ancestor
                    } else $parentR = $parentR->foreign($tc);

                } while ($parentR->$tc);
            }
        }

        // If current row relates to an account-model - do additional validation
        $this->_ifRole();

        // Return found mismatches
        return $this->_mismatch;
    }

    /**
     * Check if current entry's model is attached to a role, and if so - check that username (`email` prop) is unique
     *
     * @return mixed
     */
    protected function _ifRole() {

        // If current model is not used within any access role - return
        if (!$this->model()->hasRole()) return;

        // If current entry already has a mismatch-message for 'email' field - return
        if ($this->_mismatch['email']) return;

        // Strip unsafe characters
        $this->email = preg_replace('/[^0-9a-zA-Z\.-_@]/', '', $this->email);

        // If `email` prop became empty
        if (!$this->email) {

            // Setup mismatch message
            $this->_mismatch['email'] = sprintf(I_ADMIN_ROWSAVE_LOGIN_REQUIRED, $this->field('email')->title);

            // Return
            return;
        }

        // For each account model
        foreach (Indi_Db::role() as $entityId) {

            // Model shortcut
            $m = Indi::model($entityId);

            // Try to find an account with such a username, and if found
            if ($m->fetchRow(array(
                '`email` = "' . $this->email . '"',
                $m->id() == $this->model()->id() ? '`id` != "' . $this->id . '"' : 'TRUE'
            ))) {

                // Setup a mismatch message
                $this->_mismatch['email'] = sprintf(
                    I_ADMIN_ROWSAVE_LOGIN_OCCUPIED, $this->email, $this->field('email')->title);

                // Stop searching
                break;
            }
        }
    }

    /**
     * This function sets of gets a value of $this->_temporary array by a given key (argument #1)
     * using a given value (argument # 2)

     * @return mixed
     */
    public function original() {
        if (func_num_args() == 0) return $this->_original;
        else if (func_num_args() == 1) return is_array(func_get_arg(0)) ? $this->_original = func_get_arg(0) : $this->_original[func_get_arg(0)];
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
        else if (func_num_args() == 1) return is_array(func_get_arg(0)) ? $this->_modified = func_get_arg(0) : $this->_modified[func_get_arg(0)];
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
     * This function sets of gets a value of $this->_system array by a given key (argument #1)
     * using a given value (argument # 2)
     *
     * @return mixed
     */
    public function view() {
        if (func_num_args() == 1) {
            return $this->_view[func_get_arg(0)];
        } else if (func_num_args() == 2) {
            $this->_view[func_get_arg(0)] = func_get_arg(1);
            return $this;
        } else {
            return $this->_view;
        }
    }

    /**
     * Return results of certain field value compilation
     *
     * @return mixed
     */
    public function compiled() {

        // If no arguments passed - the whole array, containing compiled values will be returned
        if (func_num_args() == 0) return $this->_compiled;

        // Else if one argument is given
        else if (func_num_args() == 1) {

            // Assume it is a alias of a field, that is having value that should be compiled
            $evalField = func_get_arg(0);

            // If there is already exist a value for that field within $this->_compiled array
            if (array_key_exists($evalField, $this->_compiled)) {

                // Return that compiled value
                return $this->_compiled[$evalField];

            // Else if field original value is not empty, and field is within
            // list of fields that are allowed for being compiled
            } else if (strlen($this->_original[$evalField]) && $this->model()->getEvalFields($evalField)) {

                // Check if field original value contains php expressions, and if so
                if (preg_match(Indi::rex('php'), $this->_original[$evalField])) {

                    // Compile that value
                    Indi::$cmpTpl = $this->_original[$evalField]; eval(Indi::$cmpRun);

                    // Save compilation result under $evalField key within $this->_compiled array, and return that result
                    return $this->_compiled[$evalField] = Indi::cmpOut();

                // Else return already existing value
                } else return $this->_compiled[$evalField] = $this->_original[$evalField];
            }

        // Else if two arguments passed, we assume they are key and value, and there
        // should be explicit setup performed, so we do it
        } else return $this->_compiled[func_get_arg(0)] = func_get_arg(1);
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

    /**
     * Do all maintenance, related to file-uploads, e.g upload/replace/delete files and make copies if need
     *
     * @param array|bool $fields
     * @return mixed
     */
    public function files($fields = array()) {

        // If $fields argument is a string - convert it to array by exploding by comma
        if (is_string($fields)) $fields = explode(',', $fields);

        // If there is no file upload fields that should be taken into attention - exit
        if (is_array($fields) && !count($fields)) return;

        // If value, got by $this->model()->dir() call, is not a directory name
        if ((is_array($fields) ?: $this->_files) && !Indi::rexm('dir', $dir = $this->model()->dir())) {

            // Assume it is a error message, and put it under '#model' key within $this->_mismatch property
            $this->_mismatch['#model'] = $dir;

            // Exit
            $this->mflush(false);
        }
        
        // If $fields arguments is a boolean and is true we assume that there is already exists file-fields
        // content modification info, that was set up earlier, so now we should apply file-upload fields contents
        // modifications, according to that info
        if ($fields === true) foreach ($this->_files as $field => $meta) {

            // If $meta is an array, we assume it contains values (`name`, `tmp_name`, etc),
            // picked from $_FILES variable, especially for a certain file-upload field
            if (is_array($meta)) {

                // Get the extension of the uploaded file
                $ext = preg_replace('/.*\.([^\.]+)$/', '$1', $meta['name']);

                // Delete all of the possible files, uploaded using that field, and all their versions
                $this->deleteFiles($field);

                // Build the full filename into $dst variable
                $dst = $dir . $this->id . '_' . $field . '.' . strtolower($ext);

                // Move uploaded file to $dst destination, or copy, if move_uploaded_file() call failed
                if (!move_uploaded_file($meta['tmp_name'], $dst)) copy($meta['tmp_name'], $dst);

                // If uploaded file is an image in formats gif, jpg or png
                if (preg_match('/^gif|jpe?g|png$/i', $ext)) {

                    // Check if there should be copies created for that image
                    $resizeRs = Indi::model('Resize')->fetchAll('`fieldId` = "' . $this->model()->fields($field)->id . '"');

                    // If should - create thmem
                    foreach ($resizeRs as $resizeR) $this->resize($field, $resizeR, $dst);
                }

                // Remove meta info for current file-upload field
                unset ($this->_files[$field]);

            // If file, uploaded using $field field, should be deleted
            } else if ($meta == 'd') {

                // Get the file, and all it's versions
                $fileA = glob($dir . $this->id . '_' . $field . '[.,]*');

                // Delete them
                foreach ($fileA as $fileI) @unlink($fileI);

                // Remove meta info for current file-upload field
                unset ($this->_files[$field]);

            // If url was detected in $_POST data under key, assotiated with file-upload field
            } else if (preg_match(Indi::rex('url'), $meta)) {

                // Load that file by a given url
                $this->wget($meta, $field);

                // Remove meta info for current file-upload field
                unset ($this->_files[$field]);
            }

        // For each file upload field alias within $fields list
        } else foreach ($fields as $field) {

            // If there was a file uploaded a moment ago, we should move it to certain place
            if (Indi::post($field) == 'm') {

                // Get the meta information
                $meta = Indi::files($field);

                // If meta information contains a error
                if ($meta['error']) {

                    // Setup an appropriate error message
                    if ($meta['error'] === UPLOAD_ERR_INI_SIZE) $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_INI_SIZE, $field);
                    else if ($meta['error'] === UPLOAD_ERR_FORM_SIZE) $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_FORM_SIZE, $field);
                    else if ($meta['error'] === UPLOAD_ERR_PARTIAL) $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_PARTIAL, $field);
                    else if ($meta['error'] === UPLOAD_ERR_NO_FILE) $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_NO_FILE, $field);
                    else if ($meta['error'] === UPLOAD_ERR_NO_TMP_DIR) $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_NO_TMP_DIR, $field);
                    else if ($meta['error'] === UPLOAD_ERR_CANT_WRITE) $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_CANT_WRITE, $field);
                    else if ($meta['error'] === UPLOAD_ERR_EXTENSION) $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_EXTENSION, $field);
                    else $this->_mismatch[$field] = sprintf(I_UPLOAD_ERR_UNKNOWN, $field);

                    // Stop current iteration and goto next, if current was not the last
                    continue;
                }

                // Assign $meta under $field key within $this->_files array
                $this->_files[$field] = $meta;

            // If file, uploaded using $field field, should be deleted
            } else if (Indi::post($field) == 'd') {

                // Assign $meta under $field key within $this->_files array
                $this->_files[$field] = Indi::post($field);

            // If url was detected in $_POST data under key, assotiated with file-upload field
            } else if (preg_match(Indi::rex('url'), Indi::post($field))) {

                // Assign $meta under $field key within $this->_files array
                $this->_files[$field] = Indi::post($field);
            }
        }
        
        // Flush existing/collected/current mismatches
        $this->mflush(false);
    }

    /**
     * Create the resized copy of an image, uploaded using $field field, according to info stored in $resizeR argument
     *
     * @param string $field Alias of field, that image was uploaded using by
     * @param Resize_Row $resizeR
     * @param null $src Original image full path. If this argument is not set - it will be calculated.
     * @return mixed
     */
    public function resize($field, Resize_Row $resizeR, $src = null) {

        // If no $src argument given
        if (!$src) {

            // Get the directory name
            $dir = DOC . STD . '/' . Indi::ini()->upload->path . '/' . $this->_table . '/';

            // If directory does not exist - return
            if (!is_dir($dir)) return;

            // Get the original uploaded file full filename
            list($src) = glob($dir . $this->id . '_' . $field . '.*');

            // If filename was not found - return
            if (!$src) return;
        }

        // Get the extension of the original uploaded file
        $ext = preg_replace('/.*\.([^\.]+)$/', '$1', $src);

        // If original uploaded file is not an image in format gif, jpeg or png - return
        if (!preg_match('/^gif|jpe?g|png$/i', $ext)) return;

        // Get the absolute filename of image's copy
        $dst = preg_replace('~(\.' . $ext . ')$~', ',' .$resizeR->alias . '$1', $src);

        // If copy's proportions setting is 'o' - e.g. 'original'
        if ($resizeR->proportions == 'o') {

            // We just make a copy of the image and do no size adjustments
            copy($src, $dst);

        // Else
        } else {

            // Try to create a new Imagick object, and stop function execution, if imagick object creation failed
            try { $imagick = new Imagick($src); } catch (Exception $e) {return;}

            // Get width and height
            $width = $resizeR->masterDimensionValue;
            $height = $resizeR->slaveDimensionValue;

            // If copy's proportions setting is 'c' - e.g. 'crop'
            if ($resizeR->proportions == 'c') {

                // This is a specialization of the cropImage method. At a high level, this method will
                // create a thumbnail of a given image, with the thumbnail sized at ($width, $height).
                // If the thumbnail does not match the aspect ratio of the source image, this is the
                // method to use. The thumbnail will capture the entire image on the shorter edge of
                // the source image (ie, vertical size on a landscape image). Then the thumbnail will
                // be scaled down to meet your target height, while preserving the aspect ratio.
                // Extra horizontal space that does not fit within the target $width will be cropped
                // off evenly left and right. As a result, the thumbnail is usually a good representation
                // of the source image.
                $imagick->cropThumbnailImage($width, $height);

            // Else create a non-cropped thumbnail
            } else {

                // If slave dimension should be limited
                if ($resizeR->slaveDimensionLimitation) {

                    // Create a thumbnail
                    $imagick->thumbnailImage($width, $height, true);

                // Else if slave dimension should not be limited
                } else {

                    // Set it as 0
                    if ($resizeR->masterDimensionAlias == 'width') $height = 0; else $width = 0;

                    // Create a thumbnail
                    $imagick->thumbnailImage($width, $height, false);
                }
            }

            // Remove the canvas
            if ($ext == 'gif') $imagick->setImagePage(0, 0, 0, 0);

            // Save the copy
            $imagick->writeImage($dst);

            // Free memory
            $imagick->destroy();
        }
    }

    /**
     * Get a remote file by it's url, and assign it to a certain $field field within current row,
     * as if it was manually uploaded by user. If any error occured - return boolean false, or boolean true otherwise
     *
     * @param $url
     * @param $field
     * @return mixed
     */
    public function wget($url, $field) {

        // If value, got by $this->model()->dir() call, is not a directory name
        if (!Indi::rexm('dir', $dir = $this->model()->dir())) {

            // Assume it is a error message, and put it under $field key within $this->_mismatch property
            $this->_mismatch[$field] = $dir;

            // Exit
            return false;
        }

        // Delete all of the possible files, previously uploaded using that field, and all their versions
        $this->deleteFiles($field);

        // Get the extension of the uploaded file
        preg_match('/[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-]+\/[^?#]*\.([a-zA-Z0-9+]+)$/', $url, $m); $uext = $m[1];

        // Try to detect remote file props using cURL request
        $p = Indi::probe($url); $size = $p['size']; $mime = $p['mime']; $cext = $p['ext'];

        // If simple extension detection failed - use one that curl detected
        $ext = $uext ? $uext : $cext;

        // If no size, or zero-size detected
        if (!$size) {

            // Setup an error to $this->_mismatch array, under $field key
            $this->_mismatch[$field] = sprintf(I_WGET_ERR_ZEROSIZE, $field);

            // Exit
            return false;
        }

        // Check is $url's host name is same as $_SERVER['HTTP_HOST']
        $purl = parse_url($url); $isOwnUrl = $purl['host'] == $_SERVER['HTTP_HOST'] || !$purl['host'];

        // If no extension was got from the given url
        if (!$ext || ($isOwnUrl && !$uext)) {

            // If $url's hostname is same as $_SERVER['HTTP_HOST']
            if ($isOwnUrl) {

                // If hostname is not specified within $url, prepend $url with self hostname and PRE constant
                if (!$purl['host']) $url = 'http://' . $_SERVER['HTTP_HOST'] . PRE . $url;

                // Get request headers, and declare $hrdS variable for collecting strigified headers list
                $hdrA = apache_request_headers(); $hdrS = '';

                // Unset headers, that may (for some unknown-by-me reasons) cause freeze execution
                unset($hdrA['Connection'], $hdrA['Content-Length'], $hdrA['Content-length'], $hdrA['Accept-Encoding']);

                // Build headers list
                foreach ($hdrA as $n => $v) $hdrS .= $n . ': ' . $v . "\r\n";

                // Prepare context options
                $opt = array('http'=> array('method'=> 'GET', 'header'=> $hdrS));

                // Create context, for passing as a third argument within file_get_contents() call
                $ctx = stream_context_create($opt);

                // Write session data and suspend session, so session file, containing serialized session data
                // will be temporarily unlocked, to prevent caused-by-lockness execution freeze
                session_write_close();
            }

            // Get the contents from url, and if some error occured then
            ob_start(); $raw = file_get_contents($url, false, $ctx); if ($error = ob_get_clean()) {

                // Resume session
                if ($isOwnUrl) session_start();

                // Save that error to $this->_mismatch array, under $field key
                $this->_mismatch[$field] = $error;

                // Exit
                return false;
            }

            // Resume session
            if ($isOwnUrl) session_start();

            // Create the temporary file, and place the url contents to it
            $tmp = tempnam(sys_get_temp_dir(), "indi-wget");
            $fp = fopen($tmp, 'wb'); fwrite($fp, $raw); fclose($fp);

            // If no extension yet detected
            if (!$ext) {

                // Get the mime type
                $fi = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($fi, $tmp);
                finfo_close($fi);

                // Get the extension
                $ext = Indi::ext($mime);
            }
        }

        // Build the full filename into $dst variable
        $dst = $dir . $this->id . '_' . $field . '.' . $ext;

        // Copy the remote file
        copy($tmp ? $tmp : $url, $dst);

        // Delete the temporary file
        if ($tmp) @unlink($tmp);

        // If uploaded file is an image in formats gif, jpg or png
        if (preg_match('/^gif|jpe?g|png$/i', $ext)) {

            // Check if there should be copies created for that image
            $resizeRs = Indi::model('Resize')->fetchAll('`fieldId` = "' . $this->model()->fields($field)->id . '"');

            // If should - create thmem
            foreach ($resizeRs as $resizeR) $this->resize($field, $resizeR, $dst);
        }

        // Return boolean true
        return true;
    }

    /**
     * Get all direct descedants (incapsulated in Indi_Db_Table_Rowset object), found in $source rowset especially for
     * current row, and attach these to $this->_nested property, under the '$this->model()->treeColumn()' key, so they
     * will be accessible by the same way as per $this->nested() ordinary usage. After that, function calls itself for
     * each item within nested items, so function is acting recursively
     *
     *
     * @param Indi_Db_Table_Rowset $source
     */
    public function nestDescedants(Indi_Db_Table_Rowset $source) {

        // Find and attach direct descedants of current row to $this->_nested property
        $this->nested($this->_table, $source->select($this->id, $this->model()->treeColumn()));

        // Do quite the same for each direct descedant
        foreach ($this->nested($this->_table) as $nestedR) $nestedR->nestDescedants($source);
    }

    /**
     * Get some basic info about uploaded file
     *
     * @param $field
     * @return array
     */
    public function file($field) {

        // If given field alias - is not an alias of one of filefields - return
        if (!$this->model()->getFileFields($field)) return;

        // If there is currently no file uploaded - return
        if (!($abs = $this->abs($field))) return;

        // Get the extension
        $ext = array_pop(explode('.', $abs));

        // Setup basic file info
        $file = array(
            'mtime' => filemtime($abs),
            'size' => $size = filesize($abs),
            'src' => $this->src($abs),
            'ext' => $ext,
            'mime' => Indi::mime($abs),
            'text' => $text = strtoupper($ext) . ' » ' . size2str($size),
            'href' => $href = PRE . '/auxiliary/download/id/' . $this->id . '/field/' . $this->model()->fields($field)->id . '/',
            'link' => '<a href="' . $href . '">' . $text . '</a>'
        );

        // Get more info, using getimagesize/getflashsize functions
        if (array_shift(explode('/', $file['mime'])) == 'image') $more = getimagesize($abs);
        else if ($ext == 'swf') $more = getflashsize($abs);

        // If more info was successfully got, append some of it to main info
        if ($more) {
            $file['width'] = $more[0];
            $file['height'] = $more[1];
        }

        // Here were omit STD's one or more dir levels at the ending, in case if
        // Indi::ini('upload')->path is having one or more '../' at the beginning
        $std_ = STD;
        if (preg_match(':^(\.\./)+:', Indi::ini('upload')->path, $m)) {
            $lup = count(explode('/', rtrim($m[0], '/')));
            for ($i = 0; $i < $lup; $i++) $std_ = preg_replace(':/[a-zA-Z0-9_\-]+$:', '', $std_);
            $file['std'] = $std_;
        }

        // Return
        return (object) $file;
    }

    /**
     * Assign values to row properties in batch mode, but only for properties,
     * which names are not starting with underscope ('_') sign,
     * as this properties starting with that sign is used for internal features,
     * so they are not allowed for assign and will be ignored if faced.
     *
     * Example: $row->assign(array('prop1' => 'val1', 'prop2' => 'val2'));
     * is equal to: $row->prop1 = 'val1'; $row->prop2 = 'val2';
     *
     * @param array $assign
     * @return Indi_Db_Table_Row
     */
    public function assign(array $assign) {

        // Assign props in batch mode, but ignore ones starting with underscope
        foreach ($assign as $k => $v) if (!preg_match('/^_/', trim($k))) $this->{trim($k)} = $v;

        // Return row itself
        return $this;
    }

    /**
     * Return Field_Row object for a given field/property within current row
     *
     * @param $alias
     * @return Field_Row|Indi_Db_Table_Rowset
     */
    public function field($alias) {
        return $this->model()->fields($alias);
    }

    /**
     * Provide the ability for changelog
     *
     * @param $original
     * @return mixed
     */
    public function changeLog($original) {

        // Get changelog config
        $cfg = $this->model()->changeLog();

        // Get the state of modified fields, that they were in at the moment before current row was saved
        $affected = array_diff_assoc($this->_original, $original);

        // Set up `_affected` prop, so it to contain affected field names
        $this->_affected = array_keys($affected);

        // Unset fields, that should not be involved in logging
        if ($cfg['ignore']) foreach(ar($cfg['ignore']) as $ignore) unset($affected[$ignore]);

        // If no changes logging is not enabled, or current row was a new row, or wasn't, but had no modified properties - return
        if (!$cfg['toggle'] || !$original['id'] || !count($affected)) return;

        // Get the id of current entity/model
        $entityId = $this->model()->id();

        // Get the foreign key names
        $foreignA = $this->model()->fields()->select('one,many', 'storeRelationAbility')->column('alias');

        // Get the list of foreign keys, that had modified values
        $affectedForeignA = array_intersect($foreignA, array_keys($affected));

        // Setup $was object as a clone of $this object, but at a state
        // that it had before it was saved, and even before it was modified
        $was = clone $this; $was->original($original);

        // Setup foreign data for $was object
        $was->foreign(implode(',', $affectedForeignA));

        // Setup $now object as a clone of $this object, at it's current state
        $now = clone $this; $now->foreign(implode(',', $affectedForeignA));

        // Get the storage model
        $storageM = Indi::model('ChangeLog');

        // Get the rowset of modified fields
        $affectedFieldRs = Indi::model($entityId)->fields()->select(array_keys($affected), 'alias');

        // Foreach modified field within the modified fields rowset
        foreach ($affectedFieldRs as $affectedFieldR) {

            // Create the changelog entry object
            $storageR = $storageM->createRow();

            // Setup a link to current row
            $storageR->entityId = $entityId;
            $storageR->key = $this->id;

            // Setup a field, that was modified
            $storageR->fieldId = $affectedFieldR->id;

            // If modified field is a foreign key
            if (array_key_exists($affectedFieldR->alias, $was->foreign())) {

                // If modified field's foreign data was a rowset object
                if ($was->foreign($affectedFieldR->alias) instanceof Indi_Db_Table_Rowset) {

                    // Declare the array that will contain comma-imploded titles of all rows
                    // within modified field's foreign data rowset
                    $implodedWas = array();

                    // Fulfil that array
                    foreach ($was->foreign($affectedFieldR->alias) as $r) $implodedWas[] = $r->title();

                    // Convert that array to comma-separated string
                    $storageR->was = implode(', ', $implodedWas);

                // Else if modified field's foreign data was a row object
                } else if ($was->foreign($affectedFieldR->alias) instanceof Indi_Db_Table_Row) {

                    // Get that row's title
                    $storageR->was = $was->foreign($affectedFieldR->alias)->title();

                }

            // Else if modified field is not a foreign key
            } else {

                // Get it's value as is
                $storageR->was = $was->{$affectedFieldR->alias};
            }

            // If modified field is a foreign key
            if (array_key_exists($affectedFieldR->alias, $now->foreign())) {

                // If modified field's foreign data was a rowset object
                if ($now->foreign($affectedFieldR->alias) instanceof Indi_Db_Table_Rowset) {

                    // Declare the array that will contain comma-imploded titles of all rows
                    // within modified field's foreign data rowset
                    $implodedNow = array();

                    // Fulfil that array
                    foreach ($now->foreign($affectedFieldR->alias) as $r) $implodedNow[] = $r->title();

                    // Convert that array to comma-separated string
                    $storageR->now = implode(', ', $implodedNow);

                // Else if modified field's foreign data was a row object
                } else if ($now->foreign($affectedFieldR->alias) instanceof Indi_Db_Table_Row) {

                    // Get that row's title
                    $storageR->now = $now->foreign($affectedFieldR->alias)->title();
                }

            // Else if modified field is not a foreign key
            } else {

                // Get it's value as is
                $storageR->now = $now->{$affectedFieldR->alias};
            }

            // Setup other properties
            $storageR->datetime = date('Y-m-d H:i:s');
            $storageR->changerType = Indi::model(Indi::admin()->alternate ? Indi::admin()->alternate : 'Admin')->id();
            $storageR->profileId = Indi::admin()->profileId;
            $storageR->changerId = Indi::admin()->id;
            $storageR->save();
        }
    }

    /**
     * Determine whether field's value was changed from zero-value to non-zero-value
     *
     * @param $field
     * @return bool
     */
    public function fieldIsUnzeroed($field) {
        return array_key_exists($field, $this->_modified)
            && (
                $this->_original[$field] == $this->field($field)->zeroValue()
                || (
                    in_array($field, $this->model()->getEvalFields())
                    && preg_match(Indi::rex('phpsplit'), $this->_original[$field])
                )
            ) && $this->_modified[$field] != $this->field($field)->zeroValue();
    }

    /**
     * Determine whether field's value was changed from non-zero-value to zero-value
     *
     * @param $field
     * @return bool
     */
    public function fieldIsZeroed($field) {
        return array_key_exists($field, $this->_modified)
            && $this->_original[$field] != $this->field($field)->zeroValue()
            && $this->_modified[$field] == $this->field($field)->zeroValue();
    }

    /**
     * Detect whether or not row's field currently has a zero-value
     *
     * @param $field
     * @param $version
     * @return bool
     */
    public function fieldIsZero($field, $version = null) {
        if ($version == 'original') return $this->_original[$field] == $this->field($field)->zeroValue();
        else if ($version == 'modified') return $this->_modified[$field] == $this->field($field)->zeroValue();
        else return $this->$field == $this->field($field)->zeroValue();
    }

    /**
     * Detect whether or not row's field currently has a non-zero-value
     *
     * @param $field
     * @param $version
     * @return bool
     */
    public function fieldIsNonZero($field, $version = null) {
        if ($version == 'original') return $this->_original[$field] != $this->field($field)->zeroValue();
        else if ($version == 'modified') return $this->_modified[$field] != $this->field($field)->zeroValue();
        else return $this->$field != $this->field($field)->zeroValue();
    }

    /**
     * If some of the row's prop values are CKEditor-field values, we shoudl check whether they contain '<img>'
     * and other tags having STD injections at the beginning of 'src' or other same-aim html attributes,
     * and if found - trim it, for avoid problems while possible move from STD to non-STD, or other-STD directories
     *
     * @return Indi_Db_Table_Row
     */
    public function trimSTDfromCKEvalues() {

        // Collect aliases of all CKEditor-fields
        $ckeFieldA = array();
        foreach ($this->model()->fields() as $fieldR)
            if ($fieldR->foreign('elementId')->alias == 'html')
                $ckeFieldA[] = $fieldR->alias;

        // Get the aliases of fields, that are CKEditor-fields
        $ckePropA = array_intersect(array_keys($this->_original), $ckeFieldA);

        // Left-trim the {STD . '/www'} from the values of 'href' and 'src' attributes
        foreach ($ckePropA as $ckePropI)
            $this->$ckePropI = preg_replace(':(\s*(src|href)\s*=\s*[\'"])' . STD . '/www/:', '$1/', $this->$ckePropI);

        // Return
        return $this;
    }

    /**
     * Alias for $this->delta() method
     *
     * @param $prop
     * @return mixed
     */
    public function moDelta($prop) {
        return $this->delta($prop);
    }

    /**
     * Get the difference between modified and original values for a given property.
     * This method is for use with only properties, that have numeric values
     *
     * @param $prop
     * @return mixed
     */
    public function delta($prop) {
        return array_key_exists($prop, $this->_modified) ? $this->_modified[$prop] - $this->_original[$prop] : 0;
    }

    /**
     * This function assumes that $prop - is the name of the property, that contains date in a some format,
     * so function convert it into timestamp and then convert it back to date, but in a custom format, provided
     * by $format argument. Output format is 'Y-m-d' by default
     *
     * @param $prop
     * @param string $format
     * @param string $ldate
     * @return string
     */
    public function date($prop, $format = 'Y-m-d', $ldate = '') {

        // If $ldate arg is given
        if ($ldate) {

            // Get localized date
            $date = ldate(Indi::date2strftime($format), $this->$prop);

            // Force Russian-style month name endings
            foreach (array('ь' => 'я', 'т' => 'та', 'й' => 'я') as $s => $r) {
                $date = preg_replace('/' . $s . '\b/u', $r, $date);
                $date = preg_replace('/' . $s . '(\s)/u', $r . '$1', $date);
                $date = preg_replace('/' . $s . '$/u', $r, $date);
            }

            // Force Russian-style weekday name endings, suitable for version, spelling-compatible for question 'When?'
            if (is_string($ldate) && in('weekday', ar($ldate)))
                foreach (array('а' => 'у') as $s => $r) {
                    $date = preg_replace('/' . $s . '\b/u', $r, $date);
                    $date = preg_replace('/' . $s . '(\s)/u', $r . '$1', $date);
                    $date = preg_replace('/' . $s . '$/u', $r, $date);
                }

        // Else use ordinary approach
        } else $date = date($format, strtotime($this->$prop));

        // Return
        return $date;
    }

    /**
     * Format localized date, according to current locale, set by setlocale() call
     * The key thing is that date()-compatible format can be used, rather than strftime()-compatible format
     *
     * @param $prop
     * @param $format
     * @return string
     */
    public function ldate($prop, $format) {
        return $this->date($prop, $format, true);
    }

    /**
     * Return number-formatted value of $this->prop
     *
     * @param $prop
     * @param null|int $precision
     * @param bool $color
     * @return bool|string
     */
    public function number($prop, $precision = null, $color = false) {

        // If $prop arg is an alias of an existing field
        if ($fieldR = $this->field($prop)) {

            // If $precision arg is not given, or given incorrect
            if (func_num_args() == 1 || !Indi::rexm('int11', $precision)) {

                // If existing field's column type is DECIMAL(XXX,Y)
                if (preg_match('/^DECIMAL\([0-9]+,([0-9]+)\)$/', $fieldR->foreign('columnTypeId')->type, $mColumnType)) {

                    // Set $precision as Y
                    $precision = (int) $mColumnType[1];

                // Else set $precision as 0
                } else $precision = 0;

            // Else set $precision as 0
            } else $precision = 0;

        // Else if $prop is a temporary prop
        } else if (array_key_exists($prop, $this->_temporary)) {

            // If $precision arg is not given, or given incorrect
            if (func_num_args() == 1 || !Indi::rexm('int11', $precision)) $precision = 0;

        // Else if $prop can't be used as an identifier of any prop
        } else return false;

        // Return formatted value of $this->$prop
        $formatted = decimal($this->$prop, $precision, true);

        // If $color flag is `true`
        if ($color) {

            // Possible colors
            $colorA = array(-1 => 'red', 0 => 'black', 1 => 'green');

            // Wrap formatted number into a <SPAN> with color definition
            return '<span style="color: ' . $colorA[sign($this->$prop)] . '">' . $formatted . '</span>';

        // Else just return formatted value
        } else return $formatted;
    }

    /**
     * Calls the parent class's same function, passing same arguments.
     * This is similar to ExtJs's callParent() function, except that agruments are
     * FORCED to be passed (in extjs, if you call this.callParent() - no arguments would be passed,
     * unless you use this.callParent(arguments) expression instead)
     */
    public function callParent() {

        // Get call info from backtrace
        $call = array_pop(array_slice(debug_backtrace(), 1, 1));

        // Make the call
        return call_user_func_array(get_parent_class($call['class']) . '::' . $call['function'], func_num_args() ? func_get_args() : $call['args']);
    }
    
    /**
     * Retrieve width and height from the getimagesize/getflashsize call, for an image or swf file
     * linked to a curent row's $alias field, incapsulated within an instance of stdClass object
     *
     * @param $alias
     * @param string $copy
     * @return stdClass
     */
    public function dim($alias, $copy = '') {

        // If image file exists
        if ($abs = $this->abs($alias, $copy)) {

            // Get the native result of getimagesize/getflashsize call
            $dim = (preg_match('/\.swf$/', $abs) ? getflashsize($abs) : getimagesize($abs));
            
            // Return 
            return (object) array('width' => $dim[0], 'height' => $dim[1]);
        }
    }

    /**
     * Reset row props modifications. If row had any foreign data, that data will be re-fetched to ensure it rely
     * on original values of foreign keys rather than modified values of foreign keys
     *
     * @param bool $clone
     * @return bool|Indi_Db_Table_Row
     */
    public function reset($clone = false) {

        // Backup modifications
        $modified = $this->_modified;

        // Backup foreign data
        $foreign = $this->_foreign;

        // Get modified foreign key names
        $mfkeyA = array_intersect(array_keys($modified), array_keys($foreign));

        // Reset modifications
        $this->_modified = array();

        // Remove foreign data for modified foreign keys
        foreach ($mfkeyA as $mfkeyI) unset($this->_foreign[$mfkeyI]);

        // If $clone arg is `true`
        if ($clone) {

            // Create the clone
            $clone = clone $this;

            // Set up clone's own foreign data, but only for certain foreign keys
            if ($mfkeyA) $clone->foreign(im($mfkeyA));

            // Get modifications back
            $this->_modified = $modified;

            // Get foreign data
            $this->_foreign = $foreign;

        // Else
        } else {

            // Renew own foreign data, for it to rely on original values rather than modified values
            if ($mfkeyA) $this->foreign(im($mfkeyA));
        }

        // Return
        return $clone ? $clone : $this;
    }

    /**
     * Getter function for `_affected` prop. If $prop arg is given, then function
     * will indicate whether or not prop having $prop as it alias is in the list
     * of affected props
     *
     * @param null|string $prop
     * @return array|bool
     */
    public function affected($prop = null) {

        // If $prop arg is given
        if (func_num_args()) {

            // If $prop arg is an array, or contains comma, we assume that $prop arg is a list of prop names
            if (is_array($prop) || preg_match('/,/', $prop)) {

                // So we try to detect if any of props within that list was affected
                foreach (ar($prop) as $propI) if (in($propI, $this->_affected)) return true;

                // If detection failed - return false
                return false;

            // Else if single prop name is given as $prop arg - detect whether or not it is in the list of affected props
            } else return in($prop, $this->_affected);
        }

        // Return array of affected props
        return $this->_affected;
    }

    /**
     *
     *
     * @param $fields
     * @return mixed
     */
    public function toGridData($fields) {

        // Render grid data
        $data = $this->model()->createRowset(array('rows' => array($this)))->toGridData($fields);

        // Return
        return array_shift($data);
    }

    /**
     * Assing values for props, responsible for storing info about
     * the user who initially created current entry
     *
     * @param string $prefix
     */
    public function author($prefix = 'author') {
        if (Indi::admin()) {
            $this->{$prefix . 'Type'} = Indi::admin()->model()->id();
            $this->{$prefix . 'Id'} = Indi::admin()->id;
        } else {
            $this->{$prefix . 'Type'} = Indi::me('aid');
            $this->{$prefix . 'Id'} = Indi::me('id');
        }
    }

    /**
     * Adjust given $where arg so it surely match existing value
     *
     * @param $where
     * @param $fieldR
     * @return mixed
     */
    protected function comboDataExistingValueWHERE(&$where, $fieldR, $consistence = null) {

        // If current entry is not yet exist - return
        if (!$this->id && !$consistence) return;

        // If $where arg is an empty array - return
        if (is_array($where) && !count($where)) return;

        // If $where arg is an empty string - return
        if (is_string($where) && !strlen($where)) return;

        // Build alternative WHERE clauses,
        // that will surely provide current value presence within fetched combo data
        $or = array(
            'one' => '`id` = "' . $this->{$fieldR->alias} . '"',
            'many' => '`id` IN (' . $this->{$fieldR->alias} . ')'
        );

        // If $fieldR's `storeRelationAbility` prop's value is not one oth the keys within $or array - return
        if ((!$this->{$fieldR->alias} || !$or[$fieldR->storeRelationAbility]) && !$consistence) return;

        // Implode $where
        if (is_array($where)) $where = im($where, ' AND ');

        // Append alternative
        $where = im(array('(' . $where . ')', $consistence ? '(' . $consistence . ')' : $or[$fieldR->storeRelationAbility]), ' OR ');
    }

    /**
     * Append $value to the list of comma-separated values, stored as a string value in $this->$prop
     *
     * @param $prop
     * @param $value
     * @param bool $unique
     * @return mixed
     */
    public function push($prop, $value, $unique = true) {

        // Convert $value to string
        $value .= '';

        // Convert $this->$prop to string
        $this->$prop .= '';

        // If $value is not an empty string
        if (strlen($value)) {

            // If $this->$prop is currently not an empty string, append $value followed by comma
            if (strlen($this->$prop)) {

                // If $unique is `true`, make sure $this->$prop will contain only distinct values
                if (!$unique || !in($value, $this->$prop)) $this->$prop .= ',' . $value;
            }

            // Else setup $this->$prop with $value
            else $this->$prop = $value;
        }

        // Return
        return $this->$prop;
    }

    /**
     * Drop $value from the comma-separated list, stored in $this->$prop
     * NOTE: $value can also be comma-separated list too
     *
     * @param $prop
     * @param $value
     * @return mixed
     */
    public function drop($prop, $value) {

        // Convert $value to string
        $value .= '';

        // Convert $this->$prop to string
        $this->$prop .= '';

        // If $value and $this->$prop are not empty strings
        if (strlen($value) && strlen($this->$prop)) {

            // If $unique is `true`, make sure $this->$prop will contain only distinct values
            $this->$prop = im(un($this->$prop, $value));
        }

        // Return
        return $this->$prop;
    }

    /**
     * This function is for compiling prop default values within *_Row instance context
     *
     * @param $prop
     */
    public function compileDefaultValue($prop) {
        if (strlen($this->_original[$prop])) {
            Indi::$cmpTpl = $this->_original[$prop]; eval(Indi::$cmpRun); $this->$prop = Indi::cmpOut();
        }
    }

    /**
     * Detect whether or not
     * 1. Entry has at least one modified prop ($prop arg is not given)
     * 2. ANY of entry's props, mentioned in given $prop arg (the comma-separated list) was modified
     *
     * @param $propS
     * @return bool|int
     */
    public function isModified($propS = null) {

        // If $propS arg is notgiven/null/zero/false/empty - return count of modified props
        if (func_num_args() == 0 || !$propS) return count($this->_modified);

        // Detect if at least one prop in the $propS list is modified
        foreach (ar($propS) as $propI) if (array_key_exists($propI, $this->_modified)) return true;

        // Return false
        return false;
    }
}
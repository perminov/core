<?php
class Indi_Controller {

    /**
     * Constructor
     */
    public function __construct() {

        // Create an Indi_View instance
		$view = class_exists('Project_View') ? new Project_View : new Indi_View();

        // Get the script path
        $spath = Indi::ini('view')->scriptPath;

        // Get the module path
        $mpath = Indi::uri('module') == 'front' ? '' : '/' . Indi::uri('module');

        // Get the module helper path prefix
        $mhpp = Indi::uri('module') == 'front' ? '' : '/' . ucfirst(Indi::uri('module'));

        // Get the module helper class prefix
        $mhcp = Indi::uri('module') == 'front' ? '' : ucfirst(Indi::uri('module')) . '_';

        // Add script paths for major core part and for front core part
        $view->addScriptPath(DOC . STD . '/core/' . $spath . $mpath);
        $view->addScriptPath(DOC . STD . '/coref/' . $spath . $mpath);

        // Add script path for certain/current project
        if (is_dir(DOC . STD . '/www/' . $spath)) $view->addScriptPath(DOC . STD . '/www/' . $spath . $mpath);

        // Add helper paths for major core part and for front core part
        $view->addHelperPath(DOC . STD . '/core/library/Indi/View/Helper' . $mhpp, 'Indi_View_Helper_' . $mhcp);
        $view->addHelperPath(DOC . STD . '/coref/library/Indi/View/Helper' . $mhpp, 'Indi_View_Helper_' . $mhcp);

        // Add helper paths for certain/current project
        if (is_dir(DOC . STD . '/www/library'))
            $view->addHelperPath(DOC . STD . '/www/library/Project/View/Helper' . $mhpp, 'Project_View_Helper_'. $mhcp);

        // Put view object into the registry
		Indi::registry('view', $view);
	}

    /**
     * Dispatch the request
     */
    public function dispatch() {

        // Setup the Content-Type header
        header('Content-Type: text/html; charset=utf-8');

        // Do the pre-dispatch maintenance
        $this->preDispatch();

        // Here we provide an ability for operations, required for
        // a certain item to be performed, instead actual action call
        if (preg_match('/^[A-Za-z_][A-Za-z_0-9]*$/', Indi::uri()->consider)) {

            // Call the function, that will do these operations
            $this->{Indi::uri()->action . 'ActionI' . ucfirst(Indi::uri()->consider)}(Indi::post());

            // Force to stop the execution. Usually, execution won't reach this line, as in most cases
            // jflush() is called earlier than here at this line
            jflush(false, 'No consider');
        }

        // Here we provide an ability for a combo options data to be fetched instead of actual action call
        if (preg_match('/^[A-Za-z_][A-Za-z_0-9]*$/', Indi::uri()->odata)) {

            // Fetch the combo options data
            $this->{Indi::uri()->action . 'ActionOdata'}(Indi::uri()->odata, Indi::post());

            // Force to stop the execution. Usually, execution won't reach this line, as in most cases
            // execution is stopped earlier than here at this line
            jflush(false, 'No odata');
        }

        // Call the desired action method
        $this->{Indi::uri()->action . 'Action'}();

        // Do the post-dispatch maintenance
        $this->postDispatch();
    }

    /**
     * Empty method
     */
    public function preDispatch() {

    }

    /**
     * Empty method
     */
    public function postDispatch() {

    }

    /**
     * Getter
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property) {
        if (preg_match('/^row(set|)$/i', $property)) return Indi::trail()->$property;
    }

    /**
     * Setter
     *
     * @param string $property
     * @param $value
     */
    public function __set($property, $value) {
        if (preg_match('/^row(set|)$/i', $property)) Indi::trail()->$property = $value;
    }

    /**
     * Checker
     *
     * @param string $property
     * @return bool
     */
    public function __isset($property) {
        if (preg_match('/^row(set|)$/i', $property)) return isset(Indi::trail()->$property);
    }


    /**
     * Does nothing. Declared for possibility to adjust primary WHERE clause
     *
     * @param $where
     * @return mixed
     */
    public function adjustPrimaryWHERE($where) {
        return $where;
    }

    /**
     * Builds the ORDER clause
     *
     * todo: Сделать для variable entity и storeRelationАbility=many
     *
     * @param $finalWHERE
     * @param string $json
     * @return null|string
     */
    public function finalORDER($finalWHERE, $json = '') {
        // If no sorting params provided - ORDER clause won't be built
        if (!$json) return null;

        // Extract column name and direction from json param
        list($column, $direction) = array_values(current(json_decode($json, 1)));

        // If no sorting is needed - return null
        if (!$column) return null;

        // Find a field, that column is linked to
        foreach (Indi::trail()->fields as $fieldR) if ($fieldR->alias == $column) break;

        // If there is no grid field with such a name, return null
        if ($fieldR->alias !== $column) return null;

        // If no direction - set as ASC by default
        if (!preg_match('/^ASC|DESC$/', $direction)) $direction = 'ASC';

        // Setup a foreign rows for $fieldR's foreign keys
        $fieldR->foreign('columnTypeId');

        // If this is a simple column
        if ($fieldR->storeRelationAbility == 'none') {

            // If sorting column type is BOOLEAN (use for Checkbox control element only)
            if ($fieldR->foreign('columnTypeId')->type == 'BOOLEAN') {

                // Provide an approriate SQL expression, that will handle different titles for 1 and 0 possible column
                // values, depending on current language
                return (Indi::ini('view')->lang == 'en'
                    ? 'IF(`' . $column . '`, "' . I_YES .'", "' . I_NO . '") '
                    : 'IF(`' . $column . '`, "' . I_NO .'", "' . I_YES . '") ') . $direction;

                // Else build the simplest ORDER clause
            } else {
                return '`' . $column . '` ' . $direction;
            }

            // Else if column is storing single foreign keys
        } else if ($fieldR->storeRelationAbility == 'one') {

            // If column is of type ENUM
            if ($fieldR->foreign('columnTypeId')->type == 'ENUM') {

                // Get a list of comma-imploded aliases, ordered by their titles
                $set = Indi::db()->query($sql = '

                    SELECT GROUP_CONCAT(`alias` ORDER BY `title`)
                    FROM `enumset`
                    WHERE `fieldId` = "' . $fieldR->id . '"

                ')->fetchColumn(0);

                // Build the order clause, using FIND_IN_SET function
                return 'FIND_IN_SET(`' . $column . '`, "' . $set . '") ' . $direction;

                // If column is of type (BIG|SMALL|MEDIUM|)INT
            } else if (preg_match('/INT/', $fieldR->foreign('columnTypeId')->type)) {

                // If column's field have no satellite, or have, but dependency type is not 'Variable entity'
                if (!$fieldR->satellite || $fieldR->dependency != 'e') {

                    // Get the possible foreign keys
                    $setA = Indi::db()->query('
                        SELECT DISTINCT `' . $column . '` AS `id`
                        FROM `' . Indi::trail()->model->table() . '`
                        ' . ($finalWHERE ? 'WHERE ' . $finalWHERE : '') . '
                    ')->fetchAll(PDO::FETCH_COLUMN);

                    // If at least one key was found
                    if (count($setA)) {

                        // Setup a proper order of elements in $setA array, depending on their titles
                        $setA = Indi::order($fieldR->relation, $setA);

                        // Build the order clause, using FIND_IN_SET function
                        return 'FIND_IN_SET(`' . $column . '`, "' . implode(',', $setA) . '") ' . $direction;

                        // Otherwise there will be no ORDER clause
                    } else {
                        return null;
                    }
                }
            }
        }
    }

    /**
     * Builds and returns a stack of WHERE clauses, that are representing grid's filters usage
     *
     * @param $FROM string table/model/entity name. Current model will be used by default
     * @return array
     */
    public function filtersWHERE($FROM = '') {

        // Setup model, that should have fields, mentioned as filtering params names
        $model = $FROM ? Indi::model($FROM) : Indi::trail()->model;

        // Defined an array for collecting data, that may be used in the process of building an excel spreadsheet
        $excelA = array();

        // Clauses stack
        $where = array();

        // If we have no 'search' param in query string, there is nothing to do here
        if (Indi::get()->search) {

            // Decode 'search' param from json to an associative array
            $search = json_decode(Indi::get()->search, true);

            // Foreach passed filter pair (alias => value)
            foreach ($search as $searchOnField) {

                // Get the filter's alias (same as entity field's and db table column's name) and value
                $filterSearchFieldAlias = key($searchOnField);
                $filterSearchFieldValue = current($searchOnField);

                // Get a field row object, that is related to current filter field alias. We need to do it because there
                // can be a case then filter field alias can be not the same as any field's alias - if filter is working
                // in range-mode. This can only happen for filters, that are linked to fields, that have column types:
                // DATE, INT (- just INT, no foreign keys, only simple numbers) and DATETIME. Actally there is one more
                // column type VARCHAR(10) that is used for color fields (xxx#rrggbb, with xxx - hue value of color), but
                // this does not matter, as color type of filters does not affect passed filter alias, so it's the same
                // as field alias and corresponding db table column name
                $found = null;
                foreach ($model->fields() as $fieldR)
                    if ($fieldR->alias == preg_replace('/-(lte|gte)$/','',$filterSearchFieldAlias))
                        $found = $fieldR;

                // Pick the current filter field title to $excelA
                if (array_key_exists($found->alias, $excelA) == false) {

                    // Get filter `alt` property
                    if (Indi::trail()->filters instanceof Indi_Db_Table_Rowset)
                        $alt = Indi::trail()->filters->select($found->id, 'fieldId')->current()->alt;

                    // Set excel filter mention title
                    $excelA[$found->alias] = array('title' => $alt ? $alt : $found->title);
                }

                // If field is not storing foreign keys
                if ($found->storeRelationAbility == 'none') {

                    // If $found field's control element is 'Color'
                    if ($found->elementId == 11) {

                        // Get the hue range borders
                        list($hueFrom, $hueTo) = $filterSearchFieldValue;

                        // Build a WHERE clause for that hue range borders. If $hueTo > $hueFrom, use BETWEEN clause,
                        // else if $hueTo < $hueFrom, use '>=' and '<=' clauses, or else if $hueTo = $hueFrom, use '='
                        // clause
                        if ($hueTo > $hueFrom) {
                            $where[] = 'SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) BETWEEN "' . str_pad($hueFrom, 3, '0', STR_PAD_LEFT) . '" AND "' . str_pad($hueTo, 3, '0', STR_PAD_LEFT) . '"';
                        } else if ($hueTo < $hueFrom) {
                            $where[] = '(SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) >= "' . str_pad($hueFrom, 3, '0', STR_PAD_LEFT) . '" OR SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) <= "' . str_pad($hueTo, 3, '0', STR_PAD_LEFT) . '")';
                        } else {
                            $where[] = 'SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) = "' . str_pad($hueFrom, 3, '0', STR_PAD_LEFT) . '"';
                        }

                        // Pick the current filter value and field type to $excelA
                        $excelA[$found->alias]['type'] = 'color';
                        $excelA[$found->alias]['value'] = array($hueFrom, $hueTo);
                        $excelA[$found->alias]['offset'] = $searchOnField['_xlsLabelWidth'];

                        // Else if $found field's control element is 'Check' or 'Combo', we use '=' clause
                    } else if ($found->elementId == 9 || $found->elementId == 23) {
                        $where[] = '`' . $filterSearchFieldAlias . '` = "' . $filterSearchFieldValue . '"';

                        // Pick the current filter value to $excelA
                        $excelA[$found->alias]['value'] = $filterSearchFieldValue ? I_ACTION_INDEX_FILTER_TOOLBAR_CHECK_YES : I_ACTION_INDEX_FILTER_TOOLBAR_CHECK_NO;

                        // Else if $found field's control element is 'String', we use 'LIKE "%xxx%"' clause
                    } else if ($found->elementId == 1) {
                        $where[] = '`' . $filterSearchFieldAlias . '` LIKE "%' . $filterSearchFieldValue . '%"';

                        // Pick the current filter value to $excelA
                        $excelA[$found->alias]['value'] = $filterSearchFieldValue;

                        // Else if $found field's control element are 'Number', 'Date' or  'Datetime'
                    } else if (preg_match('/^18|12|19$/', $found->elementId)) {

                        // Detect the type of filter value - bottom or top, in 'range' terms mean
                        // greater-or-equal or less-or-equal
                        preg_match('/([a-zA-Z0-9_\-]+)-(lte|gte)$/', $filterSearchFieldAlias, $matches);

                        // Currently both filter for Date and Datetime fields looks exactly the same, despite on Datetime
                        // fields have time in addition to date. But here, in php-code we need to handle these filter types
                        // with small differences, that are 1) trim up to 10 characters 2) ' 00:00:00' should be appended
                        // to filter that is associated with Datetime field to provide a valid WHERE clause
                        if (preg_match('/^12|19$/', $found->elementId))
                            $filterSearchFieldValue = substr($filterSearchFieldValue, 0, 10);

                        // Pick the current filter value and field type to $excelA
                        $excelA[$found->alias]['type'] = $found->elementId == 18 ? 'number' : 'date';
                        $excelA[$found->alias]['value'][$matches[2]] = $filterSearchFieldValue;

                        // If we deal with DATETIME column, append a time postfix for a proper comparison
                        if ($found->elementId == 19)
                            $filterSearchFieldValue .= preg_match('/gte$/', $filterSearchFieldAlias) ? ' 00:00:00' : ' 23:59:59';

                        // Use a '>=' or '<=' clause, according to specified range border's type
                        $where[] = '`' . $matches[1] . '` ' . ($matches[2] == 'gte' ? '>' : '<') . '= "' . $filterSearchFieldValue . '"';

                        // If $found field's column type is TEXT ( - control elements 'Text' and 'HTML')
                    } else if ($found->columnTypeId == 4) {

                        // Use 'MATCH AGAINST' clause
                        $where[] = 'MATCH(`' . $filterSearchFieldAlias . '`) AGAINST("' . $filterSearchFieldValue .
                            '*" IN BOOLEAN MODE)';

                        // Pick the current filter value and field type to $excelA
                        $excelA[$found->alias]['value'] = $filterSearchFieldValue;
                    }

                    // Else if $found field is able to store only one foreign key, use '=' clause
                } else if ($found->storeRelationAbility == 'one') {
                    $where[] = '`' . $filterSearchFieldAlias . '` = "' . $filterSearchFieldValue . '"';

                    // Pick the current filter value and fieldId (if foreign table name is 'enumset')
                    // or foreign table name, to $excelA
                    $excelA[$found->alias]['value'] = $filterSearchFieldValue;
                    if ($found->relation == 6) {
                        $excelA[$found->alias]['fieldId'] = $found->id;
                    } else {
                        $excelA[$found->alias]['table'] = $found->relation;
                    }

                    // Else if $found field is able to store many foreign keys, use FIND_IN_SET clause
                } else if ($found->storeRelationAbility == 'many') {

                    // Declare array for FIND_IN_SET clauses
                    $fisA = array();

                    // If $filterSearchFieldValue is a non-empty string, convert it to array
                    if (is_string($filterSearchFieldValue) && strlen($filterSearchFieldValue))
                        $filterSearchFieldValue = explode(',', $filterSearchFieldValue);

                    // Fill that array
                    foreach ($filterSearchFieldValue as $filterSearchFieldValueItem)
                        $fisA[] = 'FIND_IN_SET("' . $filterSearchFieldValueItem . '", `' . $filterSearchFieldAlias . '`)';

                    // Implode array of FIND_IN_SET clauses with AND, and enclose by round brackets
                    $where[] = '(' . implode(' AND ', $fisA) . ')';

                    // Pick the current filter value and fieldId (if foreign table name is 'enumset')
                    // or foreign table name, to $excelA
                    $excelA[$found->alias]['value'] = $filterSearchFieldValue;
                    if ($found->relation == 6) {
                        $excelA[$found->alias]['fieldId'] = $found->id;
                    } else {
                        $excelA[$found->alias]['table'] = $found->relation;
                    }
                }
            }
        }

        // If the purpose of current request is to build an excel spreadsheet -
        // setup filters usage information in $this->_excelA property
        if (Indi::uri()->excel) $this->_excelA = $excelA;

        // Return WHERE clause
        return $where;
    }

    /**
     * Prepare arguments for $this->_odata() function call, and call that function for fetching filter-combo options data.
     * This function handles all cases, related to combo options data fetch, such as
     * page-by-page appending/prepending, combo-keyword lookup, fetch satellited data (for example fetch cities for second
     * combo when country was selected in first combo), and all this for filter combos
     *
     * @param string $for A name of field, that combo data should be fetched for
     * @param array $post Request params, required to make a proper fetch (page number, keyword, value of satellite)
     */
    public function indexActionOdata($for, $post) {

        // Get the field
        $field = Indi::trail()->model->fields($for);

        // Find a filter, representing the given field, and get it's WHERE clause
        $where = ($filter = Indi::trail()->filters->select($field->id, 'fieldId')->at(0)) ? $filter->filter : null;

        // Setup a row
        $this->row = Indi::trail()->filtersSharedRow;

        // Prepare and flush json-encoded combo options data
        $this->_odata($for, $post, $field, $where);
    }

    /**
     * Prepare arguments for $this->_odata() function call, and call that function for fetching combo options data.
     * This function handles all cases, related to combo options data fetch, such as
     * page-by-page appending/prepending, combo-keyword lookup, fetch satellited data (for example fetch cities for second
     * combo when country was selected in first combo), and all this for form and sibling combos
     *
     * @param string $for A name of field, that combo data should be fetched for
     * @param array $post Request params, required to make a proper fetch (page number, keyword, value of satellite)
     */
    public function formActionOdata($for, $post) {

        // If options data is for sibling combo
        if ($for == 'sibling') {

            // Create pseudo field for sibling combo
            $field = Indi_View_Helper_Admin_SiblingCombo::createPseudoFieldR(
                $for, Indi::trail()->section->entityId, Indi::trail()->scope->WHERE);
            $this->row->$for = Indi::uri()->id;
            $order = Indi::trail()->scope->ORDER;
            $dir = array_pop(explode(' ', $order));
            $order = trim(preg_replace('/ASC|DESC/', '', $order), ' `');
            if (preg_match('/\(/', $order)) $offset = Indi::uri()->aix - 1;

            // Else if options data is for combo, associated with a existing form field
        } else $field = Indi::trail()->model->fields($for);

        // Prepare and flush json-encoded combo options data
        $this->_odata($for, $post, $field, null, $order, $dir, $offset);
    }

    /**
     * Fetch combo options data. This function handles all cases, related to combo options data fetch, such as
     * page-by-page appending/prepending, combo-keyword lookup, fetch satellited data (for example fetch cities for second
     * combo when country was selected in first combo), and all this for form, filter and sibling combos
     *
     * @param string $for A name of field, that combo data should be fetched for
     * @param array $post Request params, required to make a proper fetch (page number, keyword, value of satellite)
     * @param Field_Row $field
     * @param string $where
     * @param string $order
     * @param string $dir
     * @param string $offset
     */
    protected function _odata($for, $post, $field, $where, $order = null, $dir = null, $offset = null) {

        // Get combo data rowset
        $comboDataRs = $post->keyword
            ? $this->row->getComboData(
                $for, $post->page, $post->keyword, true, $post->satellite, $where, false, $field, $order, $dir)
            : $this->row->getComboData(
                $for, $post->page, $this->row->$for, false, $post->satellite, $where, false, $field, $order, $dir, $offset);

        // Prepare combo options data
        $comboDataA = $comboDataRs->toComboData($field->params);

        $options = $comboDataA['options'];
        $titleMaxLength = $comboDataA['titleMaxLength'];

        $options = array('ids' => array_keys($options), 'data' => array_values($options));

        // Setup number of found rows
        if ($comboDataRs->found()) $options['found'] = $comboDataRs->found();

        // Setup tree flag
        if ($comboDataRs->model()->treeColumn()) $options['tree'] = true;

        // Setup groups for options
        if ($comboDataRs->optgroup) $options['optgroup'] = $comboDataRs->optgroup;

        // Setup additional attributes names list
        if ($comboDataRs->optionAttrs) $options['attrs'] = $comboDataRs->optionAttrs;

        // Setup `titleMaxLength` property
        $options['titleMaxLength'] = $titleMaxLength;

        // Flush
        jflush(true, $options);
    }

    /**
     * Calls the parent class's same function, passing same arguments.
     * This is similar to ExtJs's callParent() function, except that agruments are
     * FORCED to be passed (in extjs, if you call this.callParent() - no arguments would be passed,
     * unless you use this.callParent(arguments) expression instead)
     */
    function callParent() {

        // Get call info from backtrace
        $call = array_pop(array_slice(debug_backtrace(), 1, 1));

        // Make the call
        call_user_func_array(get_parent_class($call['class']) . '::' . $call['function'], $call['args']);
    }
}
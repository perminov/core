<?php
class Indi_Controller {

    /**
     * Constructor
     */
    public function __construct() {

        // Create an Indi_View instance
		$view = new Indi_View();

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

        // Call the desired action method
        eval('$this->' . Indi::uri()->action . 'Action();');

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
            if ($fieldR->foreign['columnTypeId']->type == 'BOOLEAN') {

                // Provide an approriate SQL expression, that will handle different titles for 1 and 0 possible column
                // values, depending on current language
                if (Indi::ini('view')->lang == 'en')
                    return 'IF(`' . $column . '`, "' . GRID_FILTER_CHECKBOX_YES .'", "' . GRID_FILTER_CHECKBOX_NO . '") '
                        . $direction;
                else
                    return 'IF(`' . $column . '`, "' . GRID_FILTER_CHECKBOX_NO .'", "' . GRID_FILTER_CHECKBOX_YES . '") '
                        . $direction;

                // Else build the simplest ORDER clause
            } else {
                return '`' . $column . '` ' . $direction;
            }

            // Else if column is storing single foreign keys
        } else if ($fieldR->storeRelationAbility == 'one') {

            // If column is of type ENUM
            if ($fieldR->foreign('columnTypeId')->type == 'ENUM') {

                // Get a list of comma-imploded aliases, ordered by their titles
                $set = $this->db->query($sql = '

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
                        FROM `' . Indi::trail()->model->name() . '`
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
}
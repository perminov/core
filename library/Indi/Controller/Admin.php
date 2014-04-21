<?php
class Indi_Controller_Admin extends Indi_Controller{

    /**
     * Array of section ids, starting from current section and up to the top.
     *
     * @var array
     */
    private $_routeA = array();

    /**
     * Init all general cms features
     */
    public function preDispatch() {

        // Set current language
        @include_once(DOC . STD . '/core/application/lang/admin/' . Indi::ini('view')->lang . '.php');
        @include_once(DOC . STD . '/www/application/lang/admin/' . Indi::ini('view')->lang . '.php');

        // Perform authentication
        $this->auth();

        // If we are in some section, mean not in just '/admin/', but at least in '/admin/somesection/'
        if (Indi::trail(true) && Indi::trail()->model) {

            // If action is 'index'
            if (Indi::uri('action') == 'index') {

                // Get the primary WHERE clause
                $primaryWHERE = $this->primaryWHERE();

                // Set info (about primary hash and row index), related to parent section, if these params are
                // passed within the uri
                $this->setScopeUpper($primaryWHERE);

                // If a rowset should be fetched
                if (Indi::uri()->json || Indi::uri()->excel) {

                    // Get final WHERE clause, that will implode primaryWHERE, filterWHERE and keywordWHERE
                    $finalWHERE = $this->finalWHERE($primaryWHERE);

                    // Get final ORDER clause, built regarding column name and sorting direction
                    $finalORDER = $this->finalORDER($finalWHERE, $this->get['sort']);

                    // Get the rowset, fetched using WHERE and ORDER clauses, and with built LIMIT clause,
                    // constructed with usage of $this->get('limit') and $this->get('page') params
                    $this->rowset = Indi::trail()->model->{
                    'fetch'. (Indi::trail()->model->treeColumn() ? 'Tree' : 'All')
                    }($finalWHERE, $finalORDER,
                        Indi::uri()->excel ? null : (int) Indi::get('limit'),
                        Indi::uri()->excel ? null : (int) Indi::get('page'));

                    // Save rowset properties, to be able to use them later in Sibling-navigation feature, and be
                    // able to restore the state of panel, that is representing the rowset at cms interface.
                    // State of the panel includes: filtering and search params, sorting params
                    $this->setScope($primaryWHERE, $this->get['search'], Indi::uri()->keyword, $this->get['sort'],
                        $this->get['page'], $this->rowset->found(), $finalWHERE, $finalORDER);
                }

            // Else if where is some another action
            } else {

                // Setup a primary hash and row index for current trail item
                Indi::trail()->section->primaryHash = Indi::uri()->ph;
                Indi::trail()->section->rowIndex = Indi::uri()->aix;

                // Setup current row
                $this->row = &Indi::trail()->row;

                // If we are here for just check of row availability, do it
                if (Indi::uri()->check) die($this->checkRowIsInScope());
            }

            // Set scope hashes for each item within trail, starting from item, related to current section, and up to the top
            Indi::trail(true)->setItemScopeHashes(Indi::uri()->ph, Indi::uri()->aix, Indi::uri()->action == 'index');
        }
    }

    /**
     * Provide default downAction (Move down) for Admin Sections controllers
     *
     * @param string $where
     */
    public function downAction($where = null) {
        $this->move('down', $where);
    }

    /**
     * Provide default upAction (Move up) for Admin Sections controllers
     *
     * @param string $where
     */
    public function upAction($where = null) {
        $this->move('up', $where);
    }

    /**
     * Gets $within param and call $row->move() method with that param.
     * This was created just for use in in $controller->downAction() and $controller->upAction()
     *
     * @param $direction
     * @param null $where
     */
    public function move($direction, $where = null) {

        // Get the scope of rows to move within
        if (Indi::trail(1)->section->parentSectionConnector) {
            $within = Indi::trail(1)->section->foreign('parentSectionConnector')->alias;
        } else if (Indi::trail(1)->row){
            $within = Indi::trail(1)->section->foreign('entityId')->table . 'Id';
        }

        // Move
        $this->row->move($direction, $within, Indi::trail(1)->section->filter);

        // Redirect
        $this->redirect();
    }

    /**
     * Provide delete action
     */
    public function deleteAction($redirect = true) {
        $this->preDelete();
        $this->row->delete();
        $this->postDelete();
        if ($redirect) $this->redirect();
    }

    /**
     * Provide form action
     */
    public function formAction() {

        // If 'combo' uri param is set
        if (Indi::uri()->combo) {

            // If 'sibling' uri param is set
            if (Indi::uri()->sibling) {

                // Create pseudo field for sibling combo
                $field = Indi_View_Helper_Admin_SiblingCombo::createPseudoFieldR(
                    $this->post['field'],
                    Indi::trail()->section->entityId,
                    $this->view->getScope('WHERE', null, Indi::uri()->section, Indi::uri()->ph)
                );
                $this->row->{$this->post['field']} = Indi::uri()->id;

                $order = $this->view->getScope('ORDER');
                $dir = array_pop(explode(' ', $order));
                $order = trim(preg_replace('/ASC|DESC/', '', $order), ' `');
                if (preg_match('/\(/', $order)) $offset = Indi::uri()->aix - 1;

            } else {
                $field = Indi::trail()->model->fields($this->post['field']);
            }

            if (Indi::uri()->filter) {
                foreach(Indi::trail()->filters as $filterR) {
                    if ($filterR->fieldId == $field->id) {
                        $where = $filterR->filter;
                        break;
                    }
                }
            }

            // Get options
            if ($this->post['keyword']) {
                $comboDataRs = $this->row->getComboData($this->post['field'], $this->post['page'], $this->post['keyword'],
                    true, $this->post['satellite'], $where, false, $field, $order, $dir);
            } else {
                $comboDataRs = $this->row->getComboData($this->post['field'], $this->post['page'],
                    $this->row->{$this->post['field']}, false, $this->post['satellite'], $where, false, $field, $order,
                    $dir, $offset);
            }


            // Options array
            $options = array();

            // Get title column
            $titleColumn = $field->params['titleColumn'] ? $field->params['titleColumn'] : 'title';

            // If 'optgroup' param is used
            if ($comboDataRs->optgroup) {
                $by = $comboDataRs->optgroup['by'];
            }

            // Detect key property for options
            $keyProperty = $comboDataRs->enumset ? 'alias' : 'id';

            $titleMaxLength = 0;

            foreach ($comboDataRs as $o) {
                $system = $o->system();
                if ($by) $system = array_merge($system, array('group' => $o->$by));
                $options[$o->$keyProperty] = array('title' => usubstr($o->$titleColumn, 50), 'system' => $system);

                // Deal with optionTemplate param, if specified
                if ($field->params['optionTemplate']) {
                    Indi::$cmpTpl = $field->params['optionTemplate']; eval(Indi::$cmpRun); $options[$o->$keyProperty]['option'] = Indi::$cmpOut;
                }

                // Deal with optionAttrs, if specified.
                if ($comboDataRs->optionAttrs) {
                    for ($i = 0; $i < count($comboDataRs->optionAttrs); $i++) {
                        $options[$o->$keyProperty]['attrs'][$comboDataRs->optionAttrs[$i]] = $o->{$comboDataRs->optionAttrs[$i]};
                    }
                }

                // Update maximum option title length, if it exceeds previous maximum
                $noHtmlSpecialChars = preg_replace('/&[a-z]*;/', ' ',$options[$o->$keyProperty]['title']);
                if (mb_strlen($noHtmlSpecialChars, 'utf-8') > $titleMaxLength)
                    $titleMaxLength = mb_strlen($noHtmlSpecialChars, 'utf-8');

            }
            $options = array('ids' => array_keys($options), 'data' => array_values($options));

            // Setup number of found rows
            if ($comboDataRs->found()) $options['found'] = $comboDataRs->found();

            // Setup tree flag
            if ($comboDataRs->model()->treeColumn()) $options['tree'] = true;

            // Setup groups for options
            if ($comboDataRs->optgroup) $options['optgroup'] = $comboDataRs->optgroup;

            // Setup additional attributes names list
            if ($comboDataRs->optionAttrs) $options['attrs'] = $comboDataRs->optionAttrs;

            $options['titleMaxLength'] = $titleMaxLength;

            // Output
            die(json_encode($options));

        } else if (is_array(Indi::trail(1)->disabledFields['save']) && count(Indi::trail(1)->disabledFields['save'])) {

            $fieldIdA = array();
            foreach (Indi::trail(1)->fields as $fieldR)
                if (in_array($fieldR->alias, Indi::trail(1)->disabledFields['save']))
                    $fieldIdA[$fieldR->id] = $fieldR->alias;

            $disabledFieldRs = Indi::model('DisabledField')->fetchAll(array(
                '`sectionId` = "' . Indi::trail(1)->section->id . '"',
                '`fieldId` IN (' . implode(',', array_keys($fieldIdA)) . ')'
            ));
            foreach ($disabledFieldRs as $disabledFieldR) {
                if (strlen($disabledFieldR->defaultValue)) {
                    Indi::$cmpTpl = $disabledFieldR->defaultValue; eval(Indi::$cmpRun); $disabledFieldR->defaultValue = Indi::$cmpOut;
                    $this->row->{$fieldIdA[$disabledFieldR->fieldId]} = $disabledFieldR->defaultValue;
                }
            }
        }
    }

    /**
     * Remember current rowset properties SQL - WHERE, ORDER, LIMIT clauses - to be able to apply these properties in cases:
     *
     * 1. We were in one section, made some search by filters and/or keyword, did sorting by some column, went to some page.
     *    After that we went to another section, and then decide to return to the first. So, by this function, system will be
     *    able to retrieve first section's params from $_SESSION, and display the grid in the exact same way as it was when
     *    we had left it.
     * 2. We were in one section, made some search by filters and/or keyword, did sorting by some column, went to some page and
     *    clicked 'Details' on some row on that page, so the details form was displayed. So, this function is one of providing
     *    an ability to navigate/jump to current row's siblings - go to prev/next rows. Foк example if we have a States section,
     *    and we go to it, and types 'Ala' in fast-keyword-search field, so the corresponding results were displayed. Then, if we
     *    go to 'Alabama' form screen, there will be buttons titled "Prev" and "Next". For example, by clicking Next, the Alaska's
     *    editing form will be displayed instead of Alabama's. But there will be certainly no 'Ohio'.
     *
     * Actually, the only one param is stored as SQL-string - $primary param. This params includes all parts of WHERE clause, that
     * was used to retrieve a current rowset, but except parts, related to filters/keyword search usage. There parts are
     * stored as JSON-string, because it is much more easier to get last used filters's values from JSON rather than SQL.
     *
     * Function creates a hash-key (md5 from $primary param) to place the array of scope params under this key in $_SESSION
     *
     * $order param is stored in JSON format too, because it will be passed to Ext.grid
     *
     * P.S. getScope() function is defined in Indi_View class
     *
     * @param $primary array
     * @param $filters JSON
     * @param $keyword urlencoded string
     * @param $order JSON
     * @param $page int
     */
    public function setScope($primary, $filters, $keyword, $order, $page, $found, $WHERE, $ORDER) {

        // Get $primary as string
        $primary = count($primary) ? implode(' AND ', $primary) : null;

        // Get a scope hash
        $primaryHash = substr(md5($primary), 0, 10);

        // Remember all scope params in $_SESSION under a hash
        $_SESSION['indi']['admin'][Indi::uri()->section][$primaryHash] = array(
            'primary' => $primary,
            'filters' => $filters,
            'keyword' => $keyword,
            'order' => $order,
            'page' => $page,
            'found' => $found,
            'WHERE' => $WHERE,
            'ORDER' => $ORDER,
            'hash' => $primaryHash,
            'upperHash' => $_SESSION['indi']['admin'][Indi::uri()->section][$primaryHash]['upperHash'],
            'upperAix' => $_SESSION['indi']['admin'][Indi::uri()->section][$primaryHash]['upperAix']
        );

        //i($_SESSION['indi']['admin'][Indi::uri()->section]);
    }

    public function setScopeUpper($primary) {

        // Get $primary as string
        $primary = count($primary) ? implode(' AND ', $primary) : null;

        // Get a scope hash
        $primaryHash = substr(md5($primary), 0, 10);

        // Set the hash to be available at the stage then grid (or section's other panel) is rendered, but it's
        // store is not yet loaded
        $_SESSION['indi']['admin'][Indi::uri()->section][$primaryHash]['hash'] = $primaryHash;

        // Remember hash of upper scope same place in $_SESSION where local scope params will be set
        if (Indi::uri()->ph)
            $_SESSION['indi']['admin'][Indi::uri()->section][$primaryHash]['upperHash'] = Indi::uri()->ph;

        if (Indi::uri()->aix)
            $_SESSION['indi']['admin'][Indi::uri()->section][$primaryHash]['upperAix'] = Indi::uri()->aix;
    }

    /**
     * Builds an array of WHERE clauses, that will be imploded with AND, and used to determine a possible border limits
     * of scope of rows that section will be dealing with, and that are allowed for section to deal with them.
     * While building, it handles:
     *
     * 1. Childs-by-parent logic
     * 2. Alternates logic
     * 3. Custom additional adjustments (adjustments of WHERE clauses stack)
     *
     * After an array is built, function calcs a hash for imploded array, and assigns that hash as a temporary property
     * of current section, for hash to be accessbile within View object.
     *
     * primaryWHERE = parentWHERE + alternates handling, adjusted with adjustPrimaryWHERE()
     *
     * @return array
     */
    public function primaryWHERE() {

        // Define an array for WHERE clauses
        $where = array();

        // Append a childs-by-parent clause to primaryWHERE stack
        if (Indi::uri('action') == 'index' && Indi::trail(1)->section->sectionId
            && $parentWHERE = $this->parentWHERE()) $where[] = $parentWHERE;

        // If a special section's primary filter was defined, add it to primary WHERE clauses stack
        if (strlen(Indi::trail()->section->compiled('filter'))) {
            $where[] = Indi::trail()->section->compiled('filter');
        }

        // Owner control. There can be a situation when some cms users are not stored in 'admin' db table - these users
        // called 'alternates'. Example: we have 'Experts' cms section (rows are fetched from 'expert' db table) and
        // public (mean non-cms) area logic allow any visitor to ask a questions to certain expert. So, if we want to
        // provide an ability for experts to answer these questions, and if we do not want to create a number of special
        // web-pages in public area that will handle all related things, we can provide a cms access for experts instead.
        // So we can create a 'Questions' section within cms, and if `question` table will contain `expertId` column
        // (it will contain - we will create it for that purpose) - the only questions, addressed to curently logged-in
        // expert will be available for view and answer.
        if ($alternateWHERE = $this->alternateWHERE()) $where[] =  $alternateWHERE;

        // Adjust primary WHERE clauses stack - apply some custom adjustments
        $where = $this->adjustPrimaryWHERE($where);

        if (Indi::uri('action') == 'index') {

            // Get a string version of WHERE stack
            $whereS = count($where) ? implode(' AND ', $where) : null;

            // Set a hash
            Indi::trail()->section->primaryHash = substr(md5($whereS), 0, 10);
        }

        // Return primary WHERE clauses stack
        return $where;
    }

    public function alternateWHERE($trailStepsUp = 0) {
        if ($_SESSION['admin']['alternate'] && Indi::trail($trailStepsUp)->model->fields($_SESSION['admin']['alternate'] . 'Id'))
            return '`' . $_SESSION['admin']['alternate'] . 'Id` = "' . $_SESSION['admin']['id'] . '"';
    }

    /**
     * Function return a sql-string containing a WHERE clause, that do especially provide an ability to deal with
     * childs-by-parent logic, mean that if current section have parent section, we should fetch only records,
     * related to parent row, for example if we want to see cities, we must define in WHAT country these cities
     * are located
     *
     * @return string|null
     */
    public function parentWHERE() {

        // We check if a non-standard parent connector field name should be used to fetch childs
        // For example, if we have 'Countries' section (displayed rows a fetched from 'country' db table)
        // and 'Cities' section (displayed rows a fetched from 'city' db table) and 'city' table have a column
        // where country identifier of each city is specified, but this column is not named (for some reason)
        // as 'countryId', and we need it to have some another name - so in that cases we use parentSectionConnector
        // logic.
        $connectorAlias = Indi::trail()->section->parentSectionConnector
            ? Indi::trail()->section->foreign('parentSectionConnector')->alias
            : Indi::trail(1)->model->name() . 'Id';

        // Get the connector value
        $connectorValue = Indi::uri('action') == 'index'
            ? Indi::uri('id')
            : $_SESSION['indi']['admin']['trail']['parentId'][Indi::trail(1)->section->id];

        // Return clause
        return Indi::trail()->model->fields($connectorAlias)->storeRelationAbility == 'many'
            ? 'FIND_IN_SET("' . $connectorValue . '" IN `' . $connectorAlias . '`)'
            : '`' . $connectorAlias . '` = "' . $connectorValue . '"';
    }

    /**
     * Builds a SQL string from an array of clauses, imploded with OR. String will be enclosed by round brackets, e.g.
     * '(`column1` LIKE "%keyword%" OR `column2` LIKE "%keyword%" OR `columnN` LIKE "%keyword%")'. Result string will
     * not contain seach clauses for columns, that are involved in building of set of another kind of WHERE clauses -
     * related to grid filters
     *
     * @param $keyword
     * @return string
     */
    public function keywordWHERE($keyword = '') {

        // If $keyword param is not passed we pick Indi::uri()->keyword as $keyword
        if (strlen($keyword) == 0) $keyword = Indi::uri()->keyword;

        // If keyword is empty, nothing to do here
        if (strlen($keyword) == 0) return;

        // Convert quotes and perform an urldecode
        $keyword = str_replace('"','&quot;', strip_tags(urldecode($keyword)));

        // Clauses stack
        $where = array();

        // Set up info about column types to be available within each grid field
        Indi::trail()->gridFields->foreign('columnTypeId');

        // Exclusions array - we will be not trying to find a keyword in columns, that will be involved in search process
        // in $this->filtersWHERE() function, so one column can be used to find either selected-grid-filter-value or keyword,
        // not both at the same time
        $exclude = array();
        if ($this->get['search']) {
            $search = json_decode($this->get['search'], true);
            foreach ($search as $searchOnField) $exclude[] = key($searchOnField);
        }

        // Build WHERE clause for each db table column, that is presented in section's grid
        foreach (Indi::trail()->gridFields as $fieldR) {

            // Check that grid field's alias (same as db tabe column name) is not in exclusions
            if (!in_array($fieldR->alias, $exclude)) {

                // If column does not store foreign keys
                if ($fieldR->relation == 0) {

                    // If column store boolean values
                    if (preg_match('/BOOLEAN/', $fieldR->foreign['columnTypeId']['type'])) {
                        $where[] = 'IF(`' . $fieldR->alias . '`, "' . GRID_FILTER_CHECKBOX_YES . '", "' .
                            GRID_FILTER_CHECKBOX_NO . '") LIKE "%' . $keyword . '%"';

                        // Otherwise handle keyword search on other non-relation column types
                    } else {

                        // Setup an array with several column types and possible characters sets for each type.
                        $reg = array(
                            'YEAR' => '[0-9]', 'DATE' => '[0-9\-]', 'DATETIME' => '[0-9\- :]',
                            'TIME' => '[0-9:]', 'INT' => '[0-9]', 'DOUBLE' => '[0-9\.]'
                        );

                        // We check if db table column type is presented within a keys of $reg array, and if so, we check
                        // if $keyword consists from characters, that are within a column's type's allowed character set.
                        // If yes, we add a keyword clause for that column in a stack. We need to do these two checks
                        // because otherwise, for example if we will be trying to find keyword 'Привет' in column that have
                        // type DATE - it will cause a mysql collation error
                        if (preg_match(
                            '/(' . implode('|', array_keys($reg)) . ')/',
                            $fieldR->foreign['columnTypeId']['type'], $matches
                        )) {
                            if (preg_match('/^' . $reg[$matches[1]] . '$/', $keyword)) {
                                $where[] = '`' . $fieldR->alias . '` LIKE "%' . $keyword . '%"';
                            }

                            // If column's type is CHAR|VARCHAR|TEXT - all is quite simple
                        } else  {
                            $where[] = '`' . $fieldR->alias . '` LIKE "%' . $keyword . '%"';
                        }
                    }

                    // If column store foreign keys from `enumset` table
                } else if ($fieldR->relation == 6) {

                    // Find `enumset` keys (mean `alias`-es), that have `title`-s, that match keyword
                    $relatedRs = Indi::model('Enumset')->fetchAll(
                        '`fieldId` = "' . $fieldR->id . '" AND `title` LIKE "%' . $keyword . '%"'
                    );
                    $idA = array(); foreach ($relatedRs as $relatedR) $idA[] = $relatedR->alias;

                    // If at least one key was found, append a clause
                    if (count($idA))
                        $where[] = 'FIND_IN_SET(`' . $fieldR->alias . '`, "' . implode(',', $idA) . '")';

                    // If column store foreign keys, but not from `enumset` table
                } else {

                    // If column does not have a satellite (dependency='u'), or have but dependency type is set to 'c'
                    // (- mean childs-by-parent logic)
                    if (preg_match('/c|u/',$fieldR->dependency)) {

                        // Load related model
                        $relatedM = Indi::model($fieldR->relation);

                        // Find matched foreign rows, collect their ids, and add a clause
                        $relatedRs = $relatedM->fetchAll('`title` LIKE "%' . $keyword . '%"');
                        $idA = array(); foreach ($relatedRs as $relatedR) $idA[] = $relatedR->id;
                        if (count($idA))
                            $where[] = 'FIND_IN_SET(`' . $fieldR->alias . '`, "' . implode(',', $idA) . '")';

                        // Else if dependency=e - mean 'Variable entity'. Will be implemented later
                    } else {

                    }
                }
            }
        }

        // Return clauses, imploded by OR, if clauses count > 0
        return count($where) ? '(' . implode(' OR ', $where) . ')' : null;
    }

    /**
     * Build and return a final WHERE clause, that will be passed to fetchAll() method, for fetching section's main
     * rowset. Function use a $primaryWHERE, merge it with $this->filtersWHERE() and append to it $this->keywordWHERE()
     * if return values of these function are not null
     *
     * @param $primaryWHERE
     * @return null|string
     */
    public function finalWHERE($primaryWHERE, $customWHERE = null) {

        // If there was a primaryHash passed instead of $primaryWHERE param - then we extract all scope params from
        if (is_string($primaryWHERE) && preg_match('/^[0-9a-zA-Z]{10}$/', $primaryWHERE)) {

            // Get the scope
            $scope = $this->view->getScope(null, null, Indi::uri()->section, $primaryWHERE);

            // Prepare $primaryWHERE
            $primaryWHERE = $scope['primary'] ? array($scope['primary']) : array();

            // Prepare search data for $this->filtersWHERE()
            $this->get['search'] = $scope['filters'];

            // Prepare search data for $this->keywordWHERE()
            Indi::uri()->keyword = urlencode($scope['keyword']);

            // Prepare sort params for $this->finalORDER()
            $this->get['sort'] = $scope['order'];
        }

        // Final WHERE stack
        $finalWHERE = $primaryWHERE;

        // Get a WHERE stack of clauses, related to filters search and merge it with $primaryWHERE
        if (count($filtersWHERE = $this->filtersWHERE())) $finalWHERE = array_merge($finalWHERE, $filtersWHERE);

        // Get a WHERE clause, related to keyword search and append it to $primaryWHERE
        if ($keywordWHERE = $this->keywordWHERE()) $finalWHERE[] = $keywordWHERE;

        // Prepend a custom WHERE clause
        if (is_array($customWHERE) && count($customWHERE)) {
            $finalWHERE = array_merge($finalWHERE, $customWHERE);
        } else if ($customWHERE) {
            $finalWHERE[] = $customWHERE;
        }

        // Return imploded $finalWHERE, or null is there is no items in $finalWHERE stack
        return count($finalWHERE) ? implode(' AND ', $finalWHERE) : null;
    }

    /**
     * Builds and returns a stack of WHERE clauses, that are representing grid's filters usage
     *
     * @return array
     */
    public function filtersWHERE() {

        // Defined an array for collecting data, that may be used in the process of building an excel spreadsheet
        $excelA = array();

        // Clauses stack
        $where = array();

        // If we have no 'search' param in query string, there is nothing to do here
        if ($this->get['search']) {

            // Decode 'search' param from json to an associative array
            $search = json_decode($this->get['search'], true);

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
                foreach (Indi::trail()->fields as $fieldR)
                    if ($fieldR->alias == preg_replace('/-(lte|gte)$/','',$filterSearchFieldAlias))
                        $found = $fieldR;

                // Pick the current filter field title to $excelA
                if (array_key_exists($found->alias, $excelA) == false)
                    $excelA[$found->alias] = array('title' => $found->title);

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

                    // Fill that array
                    foreach ($filterSearchFieldValue as $filterSearchFieldValueItem) {
                        $fisA[] = 'FIND_IN_SET("' . $filterSearchFieldValueItem . '", `' . $filterSearchFieldAlias . '`)';
                    }

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
//            i($where);
        }
        if (Indi::uri()->excel) $this->excelA = $excelA;
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
        foreach (Indi::trail()->gridFields as $fieldR) if ($fieldR->alias == $column) break;

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
            if ($fieldR->foreign['columnTypeId']->type == 'ENUM') {

                // Get a list of comma-imploded aliases, ordered by their titles
                $set = $this->db->query($sql = '

                    SELECT GROUP_CONCAT(`alias` ORDER BY `title`)
                    FROM `enumset`
                    WHERE `fieldId` = "' . $fieldR->id . '"

                ')->fetchColumn(0);

                // Build the order clause, using FIND_IN_SET function
                return 'FIND_IN_SET(`' . $column . '`, "' . $set . '") ' . $direction;

                // If column is of type (BIG|SMALL|MEDIUM|)INT
            } else if (preg_match('/INT/', $fieldR->foreign['columnTypeId']->type)) {

                // If column's field have no satellite, or have, but dependency type is not 'Variable entity'
                if (!$fieldR->satellite || $fieldR->dependency != 'e') {

                    // Get the possible foreign keys
                    $setA = $this->db->query('
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

    public function checkRowIsInScope(){
        // Get scope params
        $scope = $this->view->getScope(null, null, Indi::uri()->section, Indi::uri()->ph);

        if (Indi::uri()->aix && !Indi::uri()->id) {
            $R = Indi::trail()->model->fetchRow($scope['WHERE'], $scope['ORDER'], Indi::uri()->aix - 1);
            return $R ? $R->id : null;

        } else if (Indi::uri()->id){
            // Prepare WHERE clause
            $where  = '`id` = "' . Indi::uri()->id . '"';
            if ($scope['WHERE'])  $where .= ' AND ' . $scope['WHERE'];

            // Check that row exists
            $R = Indi::trail()->model->fetchRow($where);

            // Get the offest, if needed
            if ($this->post['forceOffsetDetection'] && $R) {
                return Indi::trail()->model->detectOffset($scope['WHERE'], $scope['ORDER'], $R->id);

                // Or just return the id, as an ensurement, that such row exists
            } else {
                return $R ? $R->id : null;
            }
        }
    }

    /**
     * Provide a download of a excel spreadsheet
     *
     * @param $data
     */
    public function excel($data){

        /** Include path **/
        ini_set('include_path', ini_get('include_path').';../Classes/');

        /** PHPExcel */
        include 'PHPExcel.php';

        /** PHPExcel_Writer_Excel2007 */
        include 'PHPExcel/Writer/Excel2007.php';

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator($_SESSION['admin']['title']);
        $objPHPExcel->getProperties()->setLastModifiedBy($_SESSION['admin']['title']);
        $objPHPExcel->getProperties()->setTitle(Indi::trail()->section->title);

        // Set active sheet by index
        $objPHPExcel->setActiveSheetIndex(0);

        // Get the columns, that need to be presented in a spreadsheet
        $columnA = json_decode($this->get['columns'], true);

        // Setup a row index, which is data rows starting from
        $currentRowIndex = 1;

        // Calculate last row index
        $lastRowIndex =
            1 /* bread crumbs row*/ +
                1 /* row with total number of results found */ +
                (is_array($this->excelA) && count($this->excelA) ? count($this->excelA) + 1 : 0) /* filters count */ +
                (bool) (Indi::uri()->keyword || (is_array($this->excelA) && count($this->excelA) > 1)) +
                1 /* data header row */+
                count($data);

        // Set default row height
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15.75);

        // Apply general styles for all spreadsheet
        foreach ($columnA as $n => $columnI) {

            // Get column letter
            $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

            // Apply styles for all rows within current column (font and alignment)
            $objPHPExcel->getActiveSheet()
                ->getStyle($columnL . '1:' . $columnL . $lastRowIndex)
                ->applyFromArray(array(
                    'font' => array(
                        'size' => 8,
                        'name' => 'Tahoma',
                        'color' => array(
                            'rgb' => '04408C'
                        )
                    ),
                    'alignment' => array(
                        'vertical' => 'center'
                    )
                )
            );
        }

        // Capture last column letter(s)
        $lastColumnLetter = $columnL;

        // Merge all cell at first row, and place as bread crumbs will be placed here
        $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $lastColumnLetter . '1');

        // Write bread crumbs, where current spreadsheet was got from
        $crumbA = Indi::trail(true)->toString(false);

        // Defined a PHPExcel_RichText object
        $objRichText = new PHPExcel_RichText();

        // For each crumb
        for ($i = 0; $i < count($crumbA); $i++) {

            // Set font name, size and color
            $objSelfStyled = $objRichText->createTextRun(strip_tags($crumbA[$i]));
            $objSelfStyled->getFont()->setName('Tahoma')->setSize('9')->getColor()->setRGB('04408C');

            // Check if crubs contains html-code
            if (mb_strlen($crumbA[$i], 'utf-8') != mb_strlen(strip_tags($crumbA[$i]), 'utf-8')) {

                // Set italic if detected
                if (preg_match('/<\/i>/', $crumbA[$i])) $objSelfStyled->getFont()->setItalic(true);

                // Set color if detected
                if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $crumbA[$i], $c))

                    // If we find a hex equivalent for found color definition (if it's not already in hex format)
                    if ($hex = Indi::hexColor($c[1]))

                        // We set font color
                        $objSelfStyled->getFont()->getColor()->setRGB(ltrim($hex, '#'));

            }

            // Append separator
            if ($i < count($crumbA) -1) {
                $objSelfStyled = $objRichText->createTextRun(' » ');
                $objSelfStyled->getFont()->setName('Tahoma')->setSize('9');
                $objSelfStyled->getFont()->getColor()->setRGB('04408C');
            }
        }

        // Write prepared rich text object to first row
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', $objRichText);

        // Here we set row height, because OpenOffice Writer (unlike Microsoft Excel)
        // ignores previously set default height definition
        $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

        // Increment current row index as we need to keep it actual after each new row added to the spreadsheet
        $currentRowIndex++;

        // Set total number of $data items
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $currentRowIndex, I_TOTAL . ': ' . count($data));
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);
        $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);
        $currentRowIndex++;

        // If filters were used
        if (is_array($this->excelA) && count($this->excelA)) {

            // We shift current row index to provide a empty row for visual separation bread crubms row and filters rows
            $currentRowIndex++;

            // Info about filters was prepared to $this->filtersWHERE() method, as an array of used filters
            // For each used filter:
            foreach ($this->excelA as $alias => $excelI) {

                // Create rich text object
                $objRichText = new PHPExcel_RichText();

                // Merge all cell within current row
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);

                // Write a filter title and setup a font name, size and color for it
                $objSelfStyled = $objRichText->createTextRun($excelI['title'] . ' -» ');
                $objSelfStyled->getFont()->setName('Tahoma')->setSize('8')->getColor()->setRGB('04408C');

                // If filter type is 'date' (or 'datetime'. There is no difference at this case)
                if ($excelI['type'] == 'date') {

                    // Get the format
                    foreach (Indi::trail()->fields as $fieldR) {
                        if ($fieldR->alias == $alias) {
                            $format = $fieldR->params['display' . ($fieldR->elementId == 12 ? '' : 'Date') . 'Format'];
                        }
                    }

                    // If start point for date range specified
                    if (isset($excelI['value']['gte'])) {

                        // Write the 'from ' string before actual filter date
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_DATE_FROM . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Deal with date converstion
                        if (preg_match(Indi::rex('date'), $excelI['value']['gte'])) {
                            if ($excelI['value']['gte'] == '0000-00-00' && $format == 'd.m.Y') {
                                $excelI['value']['gte'] = '00.00.0000';
                            } else if ($excelI['value']['gte'] != '0000-00-00'){
                                $excelI['value']['gte'] = date($format, strtotime($excelI['value']['gte']));
                                if ($excelI['value']['gte'] == '30.11.-0001') $excelI['value']['gte'] = '00.00.0000';
                            }
                        }

                        // Write the converted date
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['gte'] . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }

                    // If end point for date range specified
                    if (isset($excelI['value']['lte'])) {

                        // Write the 'until ' string before actual filter date
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_DATE_TO . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Deal with date converstion
                        if (preg_match(Indi::rex('date'), $excelI['value']['lte'])) {
                            if ($excelI['value']['lte'] == '0000-00-00' && $format == 'd.m.Y') {
                                $excelI['value']['lte'] = '00.00.0000';
                            } else if ($excelI['value']['gte'] != '0000-00-00'){
                                $excelI['value']['lte'] = date($format, strtotime($excelI['value']['lte']));
                                if ($excelI['value']['lte'] == '30.11.-0001') $excelI['value']['lte'] = '00.00.0000';
                            }
                        }

                        // Write the converted date
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['lte'] . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }

                    // If filter type is 'number'
                } else if ($excelI['type'] == 'number') {

                    // If start point for number range specified
                    if (isset($excelI['value']['gte'])) {

                        // Write the 'from ' string before actual filter value
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_FROM . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Write the actual filter start point value
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['gte'] . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }

                    // If start point for number range specified
                    if (isset($excelI['value']['lte'])) {

                        // Write the 'to ' string before actual filter value
                        $objSelfStyled = $objRichText->createTextRun(I_ACTION_INDEX_FILTER_TOOLBAR_NUMBER_TO . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('04408C');

                        // Write the actual filter end point value
                        $objSelfStyled = $objRichText->createTextRun($excelI['value']['lte'] . ' ');
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                    }


                    // If filter type is 'number'
                } else if ($excelI['type'] == 'color') {

                    // Create the GD canvas image for hue background and thumbs to be placed there
                    $canvasIm = imagecreatetruecolor(197, 15);
                    imagecolortransparent($canvasIm, imagecolorallocate($canvasIm, 0, 0, 0));

                    // Pick hue bg and place it on canvas
                    $hueFn = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/i/admin/i-color-slider-bg.png';
                    $hueIm = imagecreatefrompng($hueFn);
                    imagecopy($canvasIm, $hueIm, 7, 2, 0, 0, 183, 11);

                    // Pick first thumb and place it on canvas
                    $firstThumbFn = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/i/admin/i-color-slider-thumb-first.png';
                    $firstThumbIm = imagecreatefrompng($firstThumbFn);
                    imagecopy($canvasIm, $firstThumbIm, floor(183/360*$excelI['value'][0]), 0, 0, 0, 15, 15);

                    // Pick last thumb and place it on canvas
                    $firstThumbFn = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/i/admin/i-color-slider-thumb-last.png';
                    $firstThumbIm = imagecreatefrompng($firstThumbFn);
                    imagecopy($canvasIm, $firstThumbIm, floor(183/360*$excelI['value'][1]), 0, 0, 0, 15, 15);

                    //  Add the GD image to a spreadsheet
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setCoordinates('A' . $currentRowIndex);
                    $objDrawing->setImageResource($canvasIm);
                    $objDrawing->setHeight(11)->setWidth(183)->setOffsetY(4)->setOffsetX($excelI['offset'] + 5);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                    // If filter type is combo, with foreign table relation
                } else if ($excelI['table']) {

                    // Prepare the array of keys
                    if (!is_array($excelI['value'])) $excelI['value'] = array($excelI['value']);

                    // Fetch rows by keys
                    $rs = Indi::model($excelI['table'])->fetchAll('`id` IN (' . implode(',', $excelI['value']) . ')')->toArray();

                    // Foreach row
                    for ($i = 0; $i < count($rs); $i++) {

                        // Set default color
                        $color = '7EAAE2';

                        // Check if row title contains color definition
                        if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $rs[$i]['title'], $c)) {

                            // If we find a hex equivalent for found color definition (if it's not already in hex format)
                            if ($hex = Indi::hexColor($c[1])) {

                                // Strip html from row title
                                $rs[$i]['title'] = strip_tags($rs[$i]['title']);

                                // Capture color, for being available to setup as a font color
                                $color = ltrim($hex, '#');
                            }
                        }

                        // Write row title
                        $objSelfStyled = $objRichText->createTextRun($rs[$i]['title']);
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB($color);

                        // Write separator, if needed
                        if ($i < count($rs) - 1) {
                            $objSelfStyled = $objRichText->createTextRun(', ');
                            $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                            $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                        }
                    }

                    // If filter type if also combo, but keys are from 'enumset' table
                } else if ($excelI['fieldId']) {

                    // Prepare the array of keys
                    if (!is_array($excelI['value'])) $excelI['value'] = array($excelI['value']);

                    // Fetch rows by keys
                    $rs = Indi::model('Enumset')->fetchAll(array(
                        '`fieldId` = "' . $excelI['fieldId'] . '"',
                        '`alias` IN ("' . implode('","', $excelI['value']) . '")'
                    ))->toArray();

                    // Foreach row
                    for ($i = 0; $i < count($rs); $i++) {

                        // Set default color
                        $color = '7EAAE2';

                        // Check if row title contains color definition
                        if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $rs[$i]['title'], $c)) {

                            // If we find a hex equivalent for found color definition (if it's not already in hex format)
                            if ($hex = Indi::hexColor($c[1])) {

                                // Strip html from row title
                                $rs[$i]['title'] = strip_tags($rs[$i]['title']);

                                // Capture color, for being available to setup as a font color
                                $color = ltrim($hex, '#');
                            }
                        }

                        // Write row title
                        $objSelfStyled = $objRichText->createTextRun($rs[$i]['title']);
                        $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                        $objSelfStyled->getFont()->getColor()->setRGB($color);

                        // Write separator, if needed
                        if ($i < count($rs) - 1) {
                            $objSelfStyled = $objRichText->createTextRun(', ');
                            $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                            $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                        }
                    }

                    // Else if filter type is 'text' or 'html' (simple text search)
                } else {

                    // Write the filter value
                    $objSelfStyled = $objRichText->createTextRun($excelI['value']);
                    $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
                    $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');
                }

                // Set rich text object as a cell value
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $currentRowIndex, $objRichText);

                // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
                $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

                // Increment current row index as we need to keep it actual after each new row added to the spreadsheet
                $currentRowIndex++;
            }
        }

        // Append row with keyword, if keyword search was used
        if (Indi::uri()->keyword) {

            // Setup new rich text object for keyword search usage mention
            $objRichText = new PHPExcel_RichText();

            // Merge current row sells and set alignment as 'right'
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRowIndex . ':' . $lastColumnLetter . $currentRowIndex);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRowIndex)->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            // Write the keyword search method title, with a separator
            $objSelfStyled = $objRichText->createTextRun('Искать -» ');
            $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
            $objSelfStyled->getFont()->getColor()->setRGB('04408C');

            // Write used keyword
            $objSelfStyled = $objRichText->createTextRun(urldecode(Indi::uri()->keyword));
            $objSelfStyled->getFont()->setName('Tahoma')->setSize('8');
            $objSelfStyled->getFont()->getColor()->setRGB('7EAAE2');

            // Set rich text object as cell value
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $currentRowIndex, $objRichText);

            // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
            $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

            // Increment current row index as we need to keep it actual after each new row added to the spreadsheet
            $currentRowIndex++;


            // If no keyword search was used, but number of filters, involved in search is more than 1
            // we provide an empty row, as a separator between filters mentions and found data
        } else if (is_array($this->excelA) && count($this->excelA) > 1) {

            // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
            $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

            // Increment current row index
            $currentRowIndex++;
        }

        // Get the order column alias
        $orderColumnAlias = @array_shift(json_decode($this->get['sort']))->property;

        // For each column
        foreach ($columnA as $n => $columnI) {

            // Get column letter
            $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

            // Setup column width
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnL)->setWidth(ceil($columnI['width']/8.43));

            // Replace &nbsp;
            $columnI['title'] = str_replace('&nbsp;', ' ', $columnI['title']);

            // Write header title of a certain column to a header cell
            $objPHPExcel->getActiveSheet()->SetCellValue($columnL . $currentRowIndex, $columnI['title']);

            if ($columnI['dataIndex'] == $orderColumnAlias) {
                // /library/extjs4/resources/themes/images/default/grid/sort_asc.gif

                // Create the GD canvas image for hue background and thumbs to be placed there
                $canvasIm = imagecreatetruecolor(13, 5);
                imagecolortransparent($canvasIm, imagecolorallocate($canvasIm, 0, 0, 0));

                // Pick hue bg and place it on canvas
                $iconFn = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . STD . '/core/library/extjs4/resources/themes/images/default/grid/sort_' . $columnI['sortState'] . '.gif';
                $iconIm = imagecreatefromgif($iconFn);
                imagecopy($canvasIm, $iconIm, 0, 0, 0, 0, 13, 5);

                //  Add the GD image to a spreadsheet
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setCoordinates($columnL . $currentRowIndex);
                $objDrawing->setImageResource($canvasIm);
                $objDrawing->setWidth(13)->setHeight(5)->setOffsetY(10)->setOffsetX($columnI['titleWidth'] + 6);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            }

            // Apply header row style
            $objPHPExcel->getActiveSheet()
                ->getStyle($columnL . $currentRowIndex)
                ->applyFromArray(
                array(
                    'borders' => array(
                        'right' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => ($columnI['dataIndex'] == $orderColumnAlias ? 'aaccf6':'c5c5c5')
                            )
                        ),
                        'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => ($columnI['dataIndex'] == $orderColumnAlias ? 'BDD5F1':'d5d5d5')
                            )
                        ),
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'rgb' => ($columnI['dataIndex'] == $orderColumnAlias ? 'A7C7EE':'c5c5c5')
                            )
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 90,
                        'startcolor' => array(
                            'rgb' => ($columnI['dataIndex'] == $orderColumnAlias ? 'ebf3fd' : 'F9F9F9'),
                        ),
                        'endcolor' => array(
                            'rgb' => ($columnI['dataIndex'] == $orderColumnAlias ? 'd9e8fb' : 'E3E4E6'),
                        ),
                    )
                )
            );

            // Apply align for all rows within current column, except header rows
            $objPHPExcel->getActiveSheet()
                ->getStyle($columnL . ($currentRowIndex + 1) . ':' . $columnL . $lastRowIndex)
                ->applyFromArray(array(
                    'alignment' => array(
                        'horizontal' => $columnI['align']
                    )
                )
            );
        }

        // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
        $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);
        $currentRowIndex++;

        // We remember a current row index at this moment, because it is the index which data rows are starting from
        $dataStartAtRowIndex = $currentRowIndex;

        // Foreach item in $data array
        for ($i = 0; $i < count($data); $i++) {

            // Here we set row height, because OpenOffice Writer (unlike Excel) ignores previously setted default height
            $objPHPExcel->getActiveSheet()->getRowDimension($currentRowIndex)->setRowHeight(15.75);

            // Foreach column
            foreach ($columnA as $n => $columnI) {

                // Convert the column index to excel column letter
                $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

                // Get the value
                $value = $data[$i][$columnI['dataIndex']];

                // If cell value contain a .i-color-box item, we replaced it with same-looking GD image box
                if (preg_match('/<span class="i-color-box" style="[^"]*background: #([0-9A-Fa-f]{6});">/', $value, $c)) {

                    // Create the GD image
                    $gdImage = @imagecreatetruecolor(14, 11) or die('Cannot Initialize new GD image stream');
                    imagefill($gdImage, 0, 0, imagecolorallocate(
                            $gdImage, hexdec(substr($c[1], 0, 2)), hexdec(substr($c[1], 2, 2)), hexdec(substr($c[1], 4, 2)))
                    );

                    if (preg_match('/<span class="i-color-box" style="[^"]*margin-left: ([0-9]+)px/', $value, $o)) {
                        $additionalOffsetX = $o[1] + 3;
                    }

                    //  Add the image to a worksheet
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setCoordinates($columnL . $currentRowIndex);
                    $objDrawing->setImageResource($gdImage);
                    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight(11);
                    $objDrawing->setWidth(14);
                    $objDrawing->setOffsetY(5)->setOffsetX(5 + $additionalOffsetX);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                    // Replace .i-color-box item from value, and prepend it with 6 spaces to provide an indent,
                    // because gd image will override cell value otherwise
                    $value = str_pad('', 6, ' ') . strip_tags($value);

                    // Else if cell value contain a color definition within 'color' attribute,
                    // or as a 'color: xxxxxxxx' expression within 'style' attribute, we extract that color definition
                } else if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $value, $c)) {

                    // If we find a hex equivalent for found color definition (if it's not already in hex format)
                    if ($hex = Indi::hexColor($c[1]))

                        // Set cell's color
                        $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)
                            ->getFont()->getColor()->setRGB(ltrim($hex, '#'));
                }

                // If cell contains an 'a' tag with href attribute, we set a hyperlink to current cell
                if (preg_match('/<a/', $value) && preg_match('/href="([^"]+)"/', $value, $a)) {


                    // If there is no protocol definition within href, we set it as 'http://'
                    $protocol = preg_match('/:\/\//', $a[1]) ? '' : 'http://';

                    // If href start with a '/', it means that there is no hostname specified, so we define default
                    $server = preg_match('/^\/[^\/]{0,1}/', $a[1]) ? $_SERVER['HTTP_HOST'] : '';

                    // Prepend href with protocol and hostname
                    $a[1] = $protocol . $server . $a[1];

                    // Set cell value as hyperlink
                    $objPHPExcel->getActiveSheet()->getCell($columnL . $currentRowIndex)->getHyperlink()->setUrl($a[1]);
                    $objPHPExcel->getActiveSheet()->getStyle($columnL . $currentRowIndex)->getFont()->setUnderline(true);
                }

                // Strip html content from $value
                $value = strip_tags($value);

                // Replace some special characters definitions to its actual symbols
                $value = str_replace(
                    array('&nbsp;','&laquo;','&raquo;','&mdash;','&quot;','&lt;','&gt;'),
                    array(' ','«','»','—','"','<','>'), $value);

                // Set right and bottom border, because cell fill will hide default Excel's ot OpenOffice Write's cell borders
                $objPHPExcel->getActiveSheet()
                    ->getStyle($columnL . $currentRowIndex)
                    ->applyFromArray(
                    array(
                        'borders' => array(
                            'right' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array(
                                    'rgb' => 'D0D7E5'
                                )
                            ),
                            'bottom' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array(
                                    'rgb' => 'D0D7E5'
                                )
                            )
                        )
                    )
                );

                // Set cell value
                $objPHPExcel->getActiveSheet()->SetCellValue($columnL . $currentRowIndex, $value);

                // Set odd-even rows background difference
                if ($i%2) {
                    $objPHPExcel->getActiveSheet()
                        ->getStyle($columnL . $currentRowIndex)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FAFAFA');
                }
            }

            // Increment current row index;
            $currentRowIndex++;
        }

        // Apply right border for the most right column
        $objPHPExcel->getActiveSheet()
            ->getStyle($lastColumnLetter . $dataStartAtRowIndex . ':' . $lastColumnLetter . $lastRowIndex)
            ->applyFromArray(array(
                'borders' => array(
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => 'c5c5c5'
                        )
                    )
                )
            )
        );

        // Apply last row style (bottom border)
        $objPHPExcel->getActiveSheet()
            ->getStyle('A' . (count($data) + $dataStartAtRowIndex - 1) . ':' . $columnL . (count($data) + $dataStartAtRowIndex - 1))
            ->applyFromArray(
            array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => 'c5c5c5'
                        )
                    )
                )
            )
        );

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle(Indi::trail()->section->title);

        // Freeze header
        $objPHPExcel->getActiveSheet()->freezePane('A' . ($dataStartAtRowIndex));

        // Output
        $file = Indi::trail()->section->title . '.xlsx';
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) $file = iconv('utf-8', 'windows-1251', $file);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        die();
    }

    /**
     * Try to find user data in certain place (database table), identified by $place argument
     *
     * @param $username
     * @param $password
     * @param string $place
     * @param null $profileId
     * @param array $level1ToggledSectionIdA
     * @return array|mixed
     */
    protected function _findSigninUserData($username, $password, $place = 'admin', $profileId = null,
                                           $level1ToggledSectionIdA = array()) {

        $profileId = Indi::model($place)->fields('profileId') ? '`a`.`profileId`' : '"' . $profileId . '"';
        $adminToggle = Indi::model($place)->fields('toggle') ? '`a`.`toggle` = "y"' : '1';
        return Indi::db()->query('
            SELECT
                `a`.*,
                `a`.`password` = "' . $password . '"
                    OR `a`.`password` = PASSWORD("' . $password . '")
                    OR `a`.`password` = OLD_PASSWORD("' . $password . '")
                        AS `passwordOk`,
                '. $adminToggle . ' AS `adminToggle`,
                `p`.`toggle` = "y" AS `profileToggle`,
                `p`.`title` AS `profileTitle`,
                COUNT(`sa`.`sectionId`) > 0 AS `atLeastOneSectionAccessible`
            FROM `' . $place . '` `a`
                LEFT JOIN `profile` `p` ON (`p`.`id` = ' . $profileId . ')
                LEFT JOIN `section2action` `sa` ON (
                    FIND_IN_SET(' . $profileId . ', `sa`.`profileIds`)
                    AND `sa`.`actionId` = "1"
                    AND `sa`.`toggle` = "y"
                    AND FIND_IN_SET(`sa`.`sectionId`, "' . implode(',', $level1ToggledSectionIdA) . '")
                )
            WHERE `a`.`email` = "' . $username . '"
            LIMIT 1
        ')->fetch();
    }

    /**
     * Perform first level of authentication - check that:
     * 1. User exists
     * 2. Password is ok
     * 3. User is not disabled
     * 4. User have a type, that is not disabled
     * 5. There is at least one toggled 'On' section at level 1, that user have access to
     *
     * @param $username
     * @param $password
     * @return array|mixed|string
     */
    private function _authLevel1($username, $password) {

        // Get array of most top toggled 'On' sections ids
        $level0ToggledOnSectionIdA = Indi::db()->query('
            SELECT `id`
            FROM `section`
            WHERE `sectionId` = "0" AND `toggle` = "y"
        ')->fetchAll(PDO::FETCH_COLUMN);

        // Get array of level 1 toggled 'On' sections ids.
        $level1ToggledOnSectionIdA = Indi::db()->query('
            SELECT `id`
            FROM `section`
            WHERE FIND_IN_SET(`sectionId`, "' . implode(',', $level0ToggledOnSectionIdA) . '") AND `toggle` = "y"
        ')->fetchAll(PDO::FETCH_COLUMN);

        // Try to find user data in primary place - `admin` table
        $data = $this->_findSigninUserData($username, $password, 'admin', null, $level1ToggledOnSectionIdA);

        // If not found
        if (!$data) {

            // Get the list of other possible places, there user with given credentials can be found
            $profile2tableA = Indi::db()->query('
                SELECT `e`.`table`, `p`.`id` AS `profileId`
                FROM `entity` `e`, `profile` `p`
                WHERE `p`.`entityId` != "0"
                    AND `p`.`entityId` = `e`.`id`
            ')->fetchAll();

            // Foreach possible place - try to find
            foreach ($profile2tableA as $profile2tableI)
                if ($data = $this->_findSigninUserData($username, $password, $profile2tableI['table'],
                    $profile2tableI['profileId'], $level1ToggledOnSectionIdA))
                    break;

            // If found - assign some additional info to found data
            if ($profile2tableI) {
                $data['alternate'] = $profile2tableI['table'];
                $data['profileId'] = $profile2tableI['profileId'];
            }
        }

        // Set approriate error messages if:
        // 1. User data is still not found
        if (!$data) $error = I_LOGIN_ERROR_NO_SUCH_ACCOUNT;

        // 2. Given password is wrong
        else if (!$data['passwordOk']) $error = I_LOGIN_ERROR_WRONG_PASSWORD;

        // 3. User's signin ability is turned off
        else if (!$data['adminToggle']) $error = I_LOGIN_ERROR_ACCOUNT_IS_OFF;

        // 4. User's profile is turned off (So all users with such profile are unable to signin)
        else if (!$data['profileToggle']) $error = I_LOGIN_ERROR_PROFILE_IS_OFF;

        // 5. User have no accessbile sections
        else if (!$data['atLeastOneSectionAccessible']) $error = I_LOGIN_ERROR_NO_ACCESSIBLE_SECTIONS;

        return $error ? $error : $data;
    }

    /**
     * Check is user has access to perform $action action within $section section. Checks include:
     * 1. Section exists
     * 2. Section is switched on
     * 3. Action exists
     * 4. Action is switched on
     * 5. Action exists within section
     * 6. Action is switched in within section
     * 7. User has access to action within section
     * 8. Section is a child of a section that is switched on, and all parents up to the top are switched on too
     *
     * @param $section
     * @param $action
     * @return array|mixed|string
     */
    private function _authLevel2($section, $action) {
        $data = Indi::db()->query('
            SELECT
                `s`.`id`,
                `s`.`toggle` = "y" AS `sectionToggle`,
                `a`.`id` > 0 AS `actionExists`,
                `a`.`toggle` = "y" AS `actionToggle`,
                `sa`.`id` > 0 AS `section2actionExists`,
                `sa`.`toggle` = "y" AS `section2actionToggle`,
                FIND_IN_SET(' . $_SESSION['admin']['profileId'] . ', `sa`.`profileIds`) > 0 AS `granted`,
                `s`.`sectionId` as `sectionId`
            FROM `section` `s`
               LEFT JOIN `action` `a` ON (`a`.`alias` = "' . $action . '")
               LEFT JOIN `section2action` `sa` ON (`sa`.`actionId` = `a`.`id` AND `sa`.`sectionId` = `s`.`id`)
            WHERE 1
                AND `s`.`alias` = "' . $section . '"
                AND `s`.`sectionId` != "0"
        ')->fetch();

        // Set approriate error messages if:
        // 1. Section was not found
        if (!$data) $error = I_ACCESS_ERROR_NO_SUCH_SECTION;

        // 2. Section is switched off
        else if (!$data['sectionToggle']) $error = I_ACCESS_ERROR_SECTION_IS_OFF;

        // 3. Action does not exist at all
        else if (!$data['actionExists']) $error = I_ACCESS_ERROR_NO_SUCH_ACTION;

        // 4. Action is switched off
        else if (!$data['actionToggle']) $error = I_ACCESS_ERROR_ACTION_IS_OFF;

        // 5. Action does not exits in that section
        else if (!$data['section2actionExists']) $error = I_ACCESS_ERROR_NO_SUCH_ACTION_IN_SUCH_SECTION;

        // 6. Action is switched off in that section
        else if (!$data['section2actionToggle']) $error = I_ACCESS_ERROR_ACTION_IS_OFF_IN_SUCH_SECTION;

        // 7. User have no rights on that action in that section
        else if (!$data['granted']) $error = I_ACCESS_ERROR_ACTION_IS_NOT_ACCESSIBLE;

        // 8. One of parent sections for current section - is switched off
        else {

            // Start fulfil section id stack
            $this->_routeA = array($data['id'], $data['sectionId']);

            // Setup initial id of parent section
            $parent = array('sectionId' => $data['sectionId']);

            // Navigate through parent sections up to the root
            while ($parent = Indi::db()->query('
                SELECT `sectionId`, `toggle` FROM `section` WHERE `id` = "' . $parent['sectionId'] . '" LIMIT 1
            ')->fetch()) {

                // If any of parent sections if switched off - setup an error and break the loop
                if ($parent['toggle'] == 'n') {
                    $error = I_ACCESS_ERROR_ONE_OF_PARENT_SECTIONS_IS_OFF;
                    break;

                    // Else push new item in $this->_routeA stack
                } else if ($parent['sectionId']) $this->_routeA[] = $parent['sectionId'];

                // Else stop loop, as $parent['sectionId'] = 0, so there is no sense to find a section with such an `id`
                else break;
            }
        }

        // If $error was set - return error, or return $data otherwise
        return $error ? $error : $data;
    }

    /**
     * Perform authentication by 3 levels
     */
    public function auth() {

        // If visitor is a visitor, e.g. he has not signed in yet
        if (!$_SESSION['admin']) {

            // If he is trying to do that
            if (Indi::post()->enter && Indi::uri('section') == 'index' && Indi::uri('action') == 'index') {

                // If no username given
                if (!Indi::post()->username) $data = I_LOGIN_ERROR_ENTER_YOUR_USERNAME;

                // Else if no password given
                else if (!Indi::post()->password) $data = I_LOGIN_ERROR_ENTER_YOUR_PASSWORD;

                // Else try to find user's data
                else $data = $this->_authLevel1(Indi::post()->username, Indi::post()->password);

                // If $data is not an array, e.g some error there, output it as json with that error
                if (!is_array($data)) die(json_encode(array('error' => $data)));

                // Else start a session for user and report that singin was ok
                $allowedA = array('id', 'title', 'email', 'password', 'profileId', 'profileTitle', 'alternate');
                foreach ($allowedA as $allowedI) $_SESSION['admin'][$allowedI] = $data[$allowedI];
                die(json_encode(array('ok' => '1')));
            }

            // If user was thrown out from the system, assing a throwOutMsg to $this->view object, for this message
            // to be available for picking up and usage as Ext.MessageBox message, as a reason of throw out
            if ($_SESSION['indi']['throwOutMsg']) {
                $this->view->throwOutMsg = $_SESSION['indi']['throwOutMsg'];
                unset($_SESSION['indi']['throwOutMsg']);
            }

            // Render login page
            $out = $this->view->render('login.php');

            // Do paths replacements, if current project runs within webroot subdirectory
            if (STD) {
                $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . STD . '/', $out);
                $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
                $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            }

            // Flush the login page
            die($out);

            // Else if user is already signed in, and is trying to perform some action in some section
        } else {

            // Do the first level access check
            $data = $this->_authLevel1($_SESSION['admin']['email'], $_SESSION['admin']['password']);

            // If $data is not an array, e.g some error there, output it as json with that error
            if (!is_array($data)) {

                // Change the error message to it's version for case then user was thrown out
                if ($data == I_LOGIN_ERROR_NO_SUCH_ACCOUNT) $data = I_THROW_OUT_ACCOUNT_DELETED;
                else if ($data == I_LOGIN_ERROR_WRONG_PASSWORD) $data = I_THROW_OUT_PASSWORD_CHANGED;
                else if ($data == I_LOGIN_ERROR_ACCOUNT_IS_OFF) $data = I_THROW_OUT_ACCOUNT_IS_OFF;
                else if ($data == I_LOGIN_ERROR_PROFILE_IS_OFF) $data = I_THROW_OUT_PROFILE_IS_OFF;
                else if ($data == I_LOGIN_ERROR_NO_ACCESSIBLE_SECTIONS) $data = I_THROW_OUT_NO_ACCESSIBLE_SECTIONS;

                // Save error message in session, for ability to display it in message box
                $_SESSION['indi']['throwOutMsg'] = $data;

                // Logout
                if (Indi::uri()->section == 'index') die(header('Location: ' . PRE . '/logout/'));
                else if (!Indi::uri()->json) die('<script>top.window.location="' . PRE .'/logout/"</script>');
                else die(json_encode(array('trowOutMsg' => $data)));

                // Else if current section is 'index', e.g we are in the root of interface
            } else if (Indi::uri()->section != 'index') {

                // Do the second level access check
                $data = $this->_authLevel2(Indi::uri()->section, Indi::uri()->action);

                // If $data is not an array, e.g some error there, output it as json with that error
                if (!is_array($data)) die($data);

                // Else go further and perform last auth check, within Indi_Trail_Admin::__construct()
                else Indi::trail($this->_routeA)->authLevel3($this);
            }
        }

        // If current request had a only aim to check access - report that all is ok
        if (Indi::get('check')) die('ok');
    }

    /**
     * Provide default index action
     */
    public function indexAction() {

        // If data should be got as json or excel
        if (Indi::uri('json') || Indi::uri('excel')) {

            // Adjust rowset, before using it as a basement of grid data
            $this->adjustGridDataRowset();

            // Build the grid data, based on current rowset
            $data = $this->rowset->toGridData(Indi::trail());

            // Adjust grid data
            $this->adjustGridData($data);

            // If data is needed as json for extjs grid store - we convert $data to json with a proper format and flush it
            if (Indi::uri('json')) die(json_encode(array('totalCount' => $this->rowset->found(), 'blocks' => $data)));

            // Else if data is gonna be used in the excel spreadsheet building process, pass it to a special function
            if (Indi::uri('excel')) $this->excel($data);
        }
    }

    /**
     * Adjust rowset, before using it as a basement of grid data. This function is empty here, but may be useful in
     * some situations
     */
    function adjustGridDataRowset() {

    }

    /**
     * Adjust data, that was already prepared for usage in grid. This function is for ability to post-adjustments
     *
     * @param array $data This param is passed by reference
     */
    function adjustGridData(&$data) {

    }

    /**
     * Render the output. If $return argument is true, builded output will be returned instead of flushing into the
     * browser
     *
     * @param bool $return
     * @return mixed|string|void
     */
    public function postDispatch($return = false) {

        // If we are in a root of interface, build the general layout
        if (Indi::uri('section') == 'index' && Indi::uri('action') == 'index') {

            // Setup the left menu
            $this->view->menu = Section::menu();

            // Setup info about current logged in cms user
            $this->view->admin = $_SESSION['admin']['title'] . ' [' . $_SESSION['admin']['profileTitle']  . ']';

            // Render the layout
            $out = $this->view->render('index.php');

            // Else, if we are doing something in a certain section
        } else {

            // Setup a row object to be available within view engine
            if (Indi::trail()->row) $this->view->row = Indi::trail()->row;

            // Render the contents
            $out = $this->view->renderContent();
        }

        // Strip '/admin' from $out, if cms-only mode is enabled
        if (COM) $out = preg_replace('/("|\')\/admin/', '$1', $out);

        // Make a src|href replacements, if project is running in a subfolder of document root
        if (STD) {
            $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<form[^>]+)(action)=("|\')\//', '$1$2=$3' . STD . '/', $out);
        }

        // If $return argument is true, return builded data, or flush it otherwise
        if ($return) return $out; else die($out);
    }

    /**
     * Redirect from non-index action to index action, with taking in attention such things as
     * 1. Parentness
     * 2. Primary hash
     * 3. Row index
     * Or redirect to custom location, if $location argument is given
     *
     * @param string $location
     */
    public function redirect($location = ''){

        // If $location argument is empty
        if (!$location) {

            // Setup parentness
            if (Indi::trail(1)->row) $id = Indi::trail(1)->row->id;

            // Get scope data
            if (Indi::uri()->ph) $scope = $_SESSION['indi']['admin'][Indi::uri('section')][Indi::uri()->ph];

            // Build uri location for redirect
            $location = Indi::trail()->section->href  . '/' .
                ($id ? 'index/id/' . $id . '/' : ($scope ? 'index/' : '')) .
                ($scope['upperHash'] ? 'ph/' . $scope['upperHash'] . '/aix/' . $scope['upperAix'] . '/' : '');

        }

        // Redirect
        die('<script>window.parent.Indi.load("' . $location . '");</script>');
    }

    /**
     * Toggle current row, and redirect back
     */
    public function toggleAction() {

        // Toggle
        Indi::trail()->row->toggle();

        // Redirect
        $this->redirect();
    }

    /**
     * Do custom things before saveAction() call
     */
    public function postSave() {

    }

    /**
     * Do custom things after saveAction() call
     */
    public function preSave() {

    }

    /**
     * Do custom things before deleteAction() call
     */
    public function preDelete() {

    }

    /**
     * Do custom things after deleteAction() call
     */
    public function postDelete() {

    }

    /**
     * Save form data
     *
     * @param bool $redirect
     */
    public function saveAction($redirect = true) {

        // Do pre-save operations
        $this->preSave();

        // Get array of aliases of fields, that are actually represented in database table
        $possibleA = Indi::trail()->model->fields(null, 'columns');

        // Pick values from Indi::post()
        foreach ($possibleA as $possibleI) $data[$possibleI] = Indi::post($possibleI);

        // Unset 'move' key from data, because 'move' is a system field, and it's value will be set up automatically
        unset($data['move']);

        // If current cms user is an alternate, and if there is corresponding field within current entity structure
        if ($_SESSION['admin']['alternate'] && in_array($_SESSION['admin']['alternate'] . 'Id', $possibleA))

            // Force setup of that field value as id of current cms user
            $data[$_SESSION['admin']['alternate'] . 'Id'] = $_SESSION['admin']['id'];

        // If there was disabled fields defined for current section, we check if default value was additionally set up
        // and if so - assign that default value under that disabled field alias in $data array, or, if default value
        // was not set - drop corresponding key from $data array
        foreach (Indi::trail()->disabledFields as $disabledFieldR)
            foreach (Indi::trail()->fields as $fieldR)
                if ($fieldR->id == $disabledFieldR->fieldId)
                    if (!strlen($disabledFieldR->defaultValue)) unset($data[$fieldR->alias]);
                    else $data[$fieldR->alias] = $disabledFieldR->compiled('defaultValue');

        // Update current row properties with values from $data array
        foreach ($data as $field => $value) $this->row->$field = $value;

        // Get the list of ids of fields, that are disabled
        $disabledA = Indi::trail()->disabledFields->column('fieldId');

        // Get the aliases of fields, that are file upload fields, and that are not disabled,
        // and are to be some changes applied on
        $filefields = array();
        foreach (Indi::trail()->fields as $fieldR)
            if ($fieldR->foreign('elementId')->alias == 'upload' && !in_array($fieldR->id, $disabledA)
                && preg_match('/^m|d$/', Indi::post($fieldR->alias))) $filefields[] = $fieldR->alias;

        // Perform the whole set of file upload maintenance
        $this->row->files($filefields);

        // Save the row
        $this->row->save();

        // Do post-save operations
        $this->postSave();

        // If 'redirect-url' param exists within post data
        if ($location = Indi::post('redirect-url')) {

            // Chech if $url contains primary hash value
            if (preg_match('#/ph/([0-9a-f]+)/#', $location, $matches)) {

                // Remember the fact that save button was toggled on
                $_SESSION['indi']['admin'][Indi::uri()->section][$matches[1]]['toggledSave'] = true;

                // If it was a new row, that we've just saved
                if (!Indi::uri()->id) {

                    // Increment 'found' scope param
                    $_SESSION['indi']['admin'][Indi::uri()->section][$matches[1]]['found']++;

                    // Replace the null id with id of newly created row
                    $location = str_replace('null', Indi::trail()->row->id, $location);
                }

                // Replace the null id with id of newly created row
            } else if (!Indi::uri()->id)  $location = str_replace('null', Indi::trail()->row->id, $location);
        }

        // Redirect
        if ($redirect) $this->redirect($location);
    }
}
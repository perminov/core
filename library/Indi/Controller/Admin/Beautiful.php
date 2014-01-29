<?php
class Indi_Controller_Admin_Beautiful extends Indi_Controller{
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
        if ($this->trail->getItem()->section->parentSectionConnector) {
            $within = $this->trail->getItem()->section->getForeignRowByForeignKey('parentSectionConnector')->alias;
        } else if ($this->trail->getItem(1)->row){
            $within = $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id';
        }
        // Move
        $this->row->move($direction, $within, $this->trail->getItem()->section->filter);
        $this->postMove();
        $this->redirectToIndex();
    }

    /**
     * Provide delete action
     *
     */
    public function deleteAction($redirect = true) {
        $this->preDelete();
        $this->row->delete();
        $this->postDelete();
        if ($redirect) $this->redirectToIndex();
    }

    public function formAction() {
        if ($this->params['combo']) {

            // Get field
            if ($this->params['sibling']) {
                $field = Indi_View_Helper_Admin_SiblingCombo::createPseudoFieldR(
                    $this->post['field'],
                    $this->trail->getItem()->section->entityId,
                    $this->view->getScope('WHERE', null, $this->params['section'], $this->params['ph'])
                );
                $this->row->{$this->post['field']} = $this->params['id'];

                $this->view->trail = $this->trail;

                $order = $this->view->getScope('ORDER');
                $dir = array_pop(explode(' ', $order));
                $order = trim(preg_replace('/ASC|DESC/', '', $order), ' `');
                if (preg_match('/\(/', $order)) $offset = $this->params['aix'] - 1;

            } else {
                $field = $this->trail->getItem()->getFieldByAlias($this->post['field']);
            }

            if ($this->params['filter']) {
                foreach($this->trail->getItem()->filters as $filterR) {
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

            // Get params
            $params = $field->getParams();

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
                $options[$o->$keyProperty] = array('title' => Misc::usubstr($o->title, 50), 'system' => $system);

                // Deal with optionTemplate param, if specified
                if ($params['optionTemplate']) {
                    Indi::$cmpTpl = $params['optionTemplate']; eval(Indi::$cmpRun); $options[$o->$keyProperty]['option'] = Indi::$cmpOut;
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
            if ($comboDataRs->foundRows) $options['found'] = $comboDataRs->foundRows;

            // Setup tree flag
            if ($comboDataRs->getTable()->treeColumn) $options['tree'] = true;

            // Setup groups for options
            if ($comboDataRs->optgroup) $options['optgroup'] = $comboDataRs->optgroup;

            // Setup additional attributes names list
            if ($comboDataRs->optionAttrs) $options['attrs'] = $comboDataRs->optionAttrs;

            $options['titleMaxLength'] = $titleMaxLength;

            // Output
            die(json_encode($options));
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
        $_SESSION['indi']['admin'][$this->params['section']][$primaryHash] = array(
            'primary' => $primary,
            'filters' => $filters,
            'keyword' => $keyword,
            'order' => $order,
            'page' => $page,
            'found' => $found,
            'WHERE' => $WHERE,
            'ORDER' => $ORDER,
            'hash' => $primaryHash,
            'upperHash' => $_SESSION['indi']['admin'][$this->params['section']][$primaryHash]['upperHash'],
            'upperAix' => $_SESSION['indi']['admin'][$this->params['section']][$primaryHash]['upperAix']
        );

        //i($_SESSION['indi']['admin'][$this->params['section']]);
    }

    public function setScopeUpper($primary) {
        // Get $primary as string
        $primary = count($primary) ? implode(' AND ', $primary) : null;

        // Get a scope hash
        $primaryHash = substr(md5($primary), 0, 10);

        // Set the hash to be available at the stage then grid (or section's other panel) is rendered, but it's
        // store is not yet loaded
        $_SESSION['indi']['admin'][$this->params['section']][$primaryHash]['hash'] = $primaryHash;

        // Remember hash of upper scope same place in $_SESSION where local scope params will be set
        if ($this->params['ph'])
            $_SESSION['indi']['admin'][$this->params['section']][$primaryHash]['upperHash'] = $this->params['ph'];

        if ($this->params['aix'])
            $_SESSION['indi']['admin'][$this->params['section']][$primaryHash]['upperAix'] = $this->params['aix'];
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
        if ($this->trail->getItem(1)->row && $parentWHERE = $this->parentWHERE()) $where[] = $parentWHERE;

        // If a special section's primary filter was defined, we compile it if it contains php expressions
        // and add it to primary WHERE clauses stack
        if ($this->trail->getItem()->section->filter) {
            Indi::$cmpTpl = $this->trail->getItem()->section->filter; eval(Indi::$cmpRun); $where[] = Indi::$cmpOut;
        }

        // Owner control. There can be a situation when some cms users are not stored in 'admin' db table - these users
        // called 'alternates'. Example: we have 'Experts' cms section (rows are fetched from 'expert' db table) and
        // public (mean non-cms) area logic allow any visitor to ask a questions to certain expert. So, if we want to
        // provide and ability for experts to answer these questions, and if we do not want to create a number of special
        // web-pages in public area that will handle all related things, we can provide a cms access for experts instead.
        // So we can create a 'Questions' section within cms, and if `question` table will contain `expertId` column
        // (it will contain - we will create it for that purpose) - the only questions, addressed to curently logged-in
        // expert will be available for view and answer.
        if ($this->admin['alternate'] && $this->trail->getItem()->model->fieldExists($this->admin['alternate'] . 'Id'))
            $where[] =  '`' . $this->admin['alternate'] . 'Id` = "' . $this->admin['id'] . '"';

        // Adjust primary WHERE clauses stack
        $where = $this->adjustPrimaryWHERE($where);

        // Get a string version of WHERE stack
        $whereS = count($where) ? implode(' AND ', $where) : null;

        // Set a hash
        $this->trail->items[count($this->trail->items)-1]->section->primaryHash = substr(md5($whereS), 0, 10);

        // Return primary WHERE clauses stack
        return $where;
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
        if ($this->trail->getItem()->section->parentSectionConnector) {
            $parentSectionConnectorAlias =$this->trail->getItem()->section->getForeignRowByForeignKey('parentSectionConnector')->alias;
            $clause = '`' . $parentSectionConnectorAlias . '` = "' . $this->trail->getItem(1)->row->id . '"';

        // Otherwise we use common, most used logic (e.g. SELECT * FROM `city` WHERE `countryId` = "<country id>")
        } else {
            $clause = '`' . $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id` = "' . $this->trail->getItem(1)->row->id . '"';
        }

        // Return clause
        return $clause;
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

        // If $keyword param is not passed we pick $this->params['keyword'] as $keyword
        if (strlen($keyword) == 0) $keyword = $this->params['keyword'];

        // If keyword is empty, nothing to do here
        if (strlen($keyword) == 0) return;

        // Convert quotes and perform an urldecode
        $keyword = str_replace('"','&quot;', strip_tags(urldecode($keyword)));

        // Clauses stack
        $where = array();

        // Set up info about column types to be available within each grid field
        $this->trail->getItem()->gridFields->setForeignRowsByForeignKeys('columnTypeId');

        // Exclusions array - we will be not trying to find a keyword in columns, that will be involved in search process
        // in $this->filtersWHERE() function, so one column can be used to find either selected-grid-filter-value or keyword,
        // not both at the same time
        $exclude = array();
        if ($this->get['search']) {
            $search = json_decode($this->get['search'], true);
            foreach ($search as $searchOnField) $exclude[] = key($searchOnField);
        }

        // Build WHERE clause for each db table column, that is presented in section's grid
        foreach ($this->trail->getItem()->gridFields as $fieldR) {

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
                    $relatedRs = Misc::loadModel('Enumset')->fetchAll(
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
                        $relatedM = Entity::getInstance()->getModelById($fieldR->relation);

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
            $scope = $this->view->getScope(null, null, $this->params['section'], $primaryWHERE);

            // Prepare $primaryWHERE
            $primaryWHERE = $scope['primary'] ? array($scope['primary']) : array();

            // Prepare search data for $this->filtersWHERE()
            $this->get['search'] = $scope['filters'];

            // Prepare search data for $this->keywordWHERE()
            $this->params['keyword'] = urlencode($scope['keyword']);

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
                foreach ($this->trail->getItem()->fields as $fieldR)
                    if ($fieldR->alias == preg_replace('/-(lte|gte)$/','',$filterSearchFieldAlias))
                        $found = $fieldR;

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

                    // Else if $found field's control element is 'Check', we use '=' clause
                    } else if ($found->elementId == 9) {
                        $where[] = '`' . $filterSearchFieldAlias . '` = "' . $filterSearchFieldValue . '"';

                    // Else if $found field's control element is 'String', we use 'LIKE "%xxx%"' clause
                    } else if ($found->elementId == 1) {
                        $where[] = '`' . $filterSearchFieldAlias . '` LIKE "%' . $filterSearchFieldValue . '%"';

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

                    // If $found field's column type is BOOLEAN ( - can be handled with control elements 'Check' and 'Combo')
                    } else if ($found->columnTypeId == 12) {
                        // Use '=' clause
                        $where[] = '`' . $filterSearchFieldAlias . '` ="' . $filterSearchFieldValue . '"';
                    }

                // Else if $found field is able to store only one foreign key, use '=' clause
                } else if ($found->storeRelationAbility == 'one') {
                    $where[] = '`' . $filterSearchFieldAlias . '` = "' . $filterSearchFieldValue . '"';

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
                }
            }
//            i($where);
        }
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
        foreach ($this->trail->getItem()->gridFields as $fieldR) if ($fieldR->alias == $column) break;

        // If there is no grid field with such a name, return null
        if ($fieldR->alias !== $column) return null;

        // If no direction - set as ASC by default
        if (!preg_match('/^ASC|DESC$/', $direction)) $direction = 'ASC';

        // Setup a foreign rows for $fieldR's foreign keys
        $fieldR->setForeignRowsByForeignKeys('columnTypeId');

        // If this is a simple column
        if ($fieldR->storeRelationAbility == 'none') {

            // If sorting column type is BOOLEAN (use for Checkbox control element only)
            if ($fieldR->foreign['columnTypeId']->type == 'BOOLEAN') {

                // Provide an approriate SQL expression, that will handle different titles for 1 and 0 possible column
                // values, depending on current language
                if ($GLOBALS['lang'] == 'en')
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
                        FROM `' . $this->trail->getItem()->model->info('name') . '`
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
        $scope = $this->view->getScope(null, null, $this->params['section'], $this->params['ph']);

        if ($this->params['aix'] && !$this->params['id']) {
            $R = $this->trail->getItem()->model->fetchRow($scope['WHERE'], $scope['ORDER'], $this->params['aix'] - 1);
            return $R ? $R->id : null;

        } else if ($this->params['id']){
            // Prepare WHERE clause
            $where  = '`id` = "' . $this->params['id'] . '"';
            if ($scope['WHERE'])  $where .= ' AND ' . $scope['WHERE'];

            // Check that row exists
            $R = $this->trail->getItem()->model->fetchRow($where);

            // Get the offest, if needed
            if ($this->post['forceOffsetDetection'] && $R) {
                return $this->trail->getItem()->model->detectOffset($scope['WHERE'], $scope['ORDER'], $R->id);

            // Or just return the id, as an ensurement, that such row exists
            } else {
                return $R ? $R->id : null;
            }
        }
    }

    public function xls(){

        /** Include path **/
        ini_set('include_path', ini_get('include_path').';../Classes/');

        /** PHPExcel */
        include 'PHPExcel.php';

        /** PHPExcel_Writer_Excel2007 */
        include 'PHPExcel/Writer/Excel2007.php';

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        /*$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
        $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");*/

        // Set active sheet by index
        $objPHPExcel->setActiveSheetIndex(0);

        // Get the columns, that need to be presented in a spreadsheet
        $columnA = json_decode($this->get['columns'], true);

        // Get the data
        $data = $this->prepareJsonDataForIndexAction(false);

        // Set columns cells values as column titles
        foreach ($columnA as $n => $columnI) {

            // Get column letter
            $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

            // Setup autosize detect for column
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnL)->setWidth(ceil($columnI['width']/8.43));

            // Write header title of a certain column to a header cell
            $objPHPExcel->getActiveSheet()->SetCellValue($columnL . '1', $columnI['title']);

            // Apply styles for all rows within current column (font and right border)
            $objPHPExcel->getActiveSheet()->getStyle($columnL . '1:' . $columnL . (count($data) + 1))->applyFromArray(array(
                'font' => array(
                    'size' => 8,
                    'name' => 'Tahoma',
                    'color' => array(
                        'argb' => 'FF04408C'
                    )
                ),
                'borders' => array(
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                ),
                'alignment' => array(
                    'vertical' => 'center'
                )
            ));

            // Apply align for all rows within current column, except header rows
            $objPHPExcel->getActiveSheet()->getStyle($columnL . '2:' . $columnL . (count($data) + 1))->applyFromArray(array(
                'alignment' => array(
                    'horizontal' => $columnI['align']
                )
            ));
        }

        // Apply header row style
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $columnL . '1')->applyFromArray(array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFF9F9F9',
                ),
                'endcolor' => array(
                    'argb' => 'FFE3E4E6',
                ),
            ),
        ));

        // Foreach item in $data array
        for ($i = 0; $i < count($data); $i++) {

            // Foreach column within needed columns
            foreach ($columnA as $n => $columnI) {

                // Convert the column index to excel column letter
                $columnL = PHPExcel_Cell::stringFromColumnIndex($n);

                // Get the value
                $value = $data[$i][$columnI['dataIndex']];

                // If cell value contain a .i-color-box item, we replaced it with same-looking GD image box
                if (preg_match('/<span class="i-color-box" style="background: #([0-9A-Fa-f]{6});"><\/span>/', $value, $c)) {

                    // Create the GD image
                    $gdImage = @imagecreatetruecolor(14, 11) or die('Cannot Initialize new GD image stream');
                    imagefill($gdImage, 0, 0, imagecolorallocate(
                        $gdImage, hexdec(substr($c[1], 0, 2)), hexdec(substr($c[1], 2, 2)), hexdec(substr($c[1], 4, 2)))
                    );

                    //  Add the image to a worksheet
                    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawing->setCoordinates($columnL . ($i + 2));
                    $objDrawing->setImageResource($gdImage);
                    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight(11);
                    $objDrawing->setWidth(14);
                    $objDrawing->setOffsetY(4)->setOffsetX(3);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                    // Replace .i-color-box item from value, and prepend it with 6 spaces to provide an indent,
                    // because gd image will override cell value otherwise
                    $value = str_pad('', 6, ' ') . strip_tags($value);

                // Else if cell value contain a color definition within 'color' attribute,
                // or as a 'color: xxxxxxxx' expression within 'style' attribute, we extract that color definition
                } else if (preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $value, $c)) {

                    // If we find a hex equivalent for found color definition (if it's not already in hex format)
                    if ($hex = Indi::hexColor($c[1])) {

                        // Strip html value
                        $value = strip_tags($value);

                        // Set cell's color
                        $objPHPExcel->getActiveSheet()->getStyle($columnL . ($i + 2))
                            ->getFont()->getColor()->setARGB('FF' . ltrim($hex, '#'));
                    }
                }

                // Set cell value
                $objPHPExcel->getActiveSheet()->SetCellValue($columnL . ($i + 2), $value);
            }
        }

        // Apply last row style (bottom border)
        $objPHPExcel->getActiveSheet()->getStyle('A' . (count($data) + 1) . ':' . $columnL . (count($data) + 1))->applyFromArray($rowStyle = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        ));

        // Apply autofilter
        // $objPHPExcel->getActiveSheet()->setAutoFilter('A1:' . $columnL . (count($data) + 1));

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle($this->trail->getItem()->section->title);

        $objPHPExcel->getActiveSheet()->freezePane('A2');

        // Output
        $file = urlencode($this->trail->getItem()->section->title) . '.xlsx';
        $file = str_replace('+', '%20', $file);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=' . $file);
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        die();
    }
}
<?php
class Indi_Controller_Admin extends Indi_Controller_Admin_Beautiful{
    public $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
    public $datePattern = "/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/";

    public $condition = '';

    /**
     * Store Indi_Auth object
     *
     * @var Indi_Auth
     */
    public $auth;

    /**
     * Store info about current logged in admin
     *
     * @var object
     */
    public $admin;

    /**
     * Init all general cms features
     *
     */
    public function preDispatch()
    {
        // Set current language
        $config = Indi::registry('config');
        @include_once($_SERVER['DOCUMENT_ROOT'] . STD . '/core/application/lang/admin/' . $config['view']->lang . '.php');
        @include_once($_SERVER['DOCUMENT_ROOT'] . STD . '/www/application/lang/admin/' . $config['view']->lang . '.php');
        $GLOBALS['lang'] = $config['view']->lang;

        // Perform authentication
        Indi_Auth::getInstance()->auth($this);

        // set up all trail info
        $sectionAlias = $this->controller;

        // set up info for pagination
        if (isset($this->get['limit'])) {
            $this->limit = $this->get['limit'];
            $this->start = $this->get['start'];
            if (is_numeric($this->limit) && is_numeric($this->start)) {
                $this->page = $_SESSION['admin']['indexParams'][$sectionAlias]['page'] = ($this->start/$this->limit)+1;
            } else {
                $this->page = 1;
            }
        }

        $section = Misc::loadModel('Section');

        // set up all trail info
        $this->trail = new Indi_Trail_Admin($this->controller, $this->identifier, $this->action, null, $this->params, $this->authComponent);

        // set up current section and foreign rows, assotiated with
        $this->section = $section->fetchRow('`alias` = "' . $this->controller . '"');

        if ($this->section) {

            // set up row of entity, assotiated with current section, if action != 'index'
            $this->row = $this->trail->getItem()->row;

            // set up rowset
            if ($this->trail->getItem()->model) {

                if ($this->action == 'index') {

                    $primaryWHERE = $this->primaryWHERE();

                    $this->setScopeUpper($primaryWHERE);

                    if ($this->params['json']) {
                        // Get final WHERE clause, that will implode primaryWHERE, filterWHERE and keywordWHERE
                        $finalWHERE = $this->finalWHERE($primaryWHERE);

                        // Get final ORDER clause, built regarding column name and sorting direction
                        $finalORDER = $this->finalORDER($finalWHERE, $this->get['sort']);

                        // Get the rowset, fetched using WHERE and ORDER clauses, and with built LIMIT clause,
                        // constructed with usage of $this->limit and $this->page params
                        $this->rowset = $this->trail->getItem()->model->{
                            'fetch'. ($this->trail->getItem()->model->treeColumn ? 'Tree' : 'All')
                        }($finalWHERE, $finalORDER,
                            $this->params['xls'] ? null : $this->limit,
                            $this->params['xls'] ? null : $this->page);

                        // Save rowset properties, to be able to use them later in Sibling-navigation feature, and be
                        // able to restore the state of panel, that is representing the rowset at cms interface.
                        // State of the panel includes: filtering and search params, sorting params
                        $this->setScope($primaryWHERE, $this->get['search'], $this->params['keyword'], $this->get['sort'],
                            $this->get['page'], $this->rowset->foundRows, $finalWHERE, $finalORDER);
                    }
                } else {

                    $this->trail->items[count($this->trail->items) - 1]->section->primaryHash = $this->params['ph'];
                    $this->trail->items[count($this->trail->items) - 1]->section->rowIndex = $this->params['aix'];

                    if ($this->params['check']) die($this->checkRowIsInScope());
                }

                $this->trail->setItemScopeHashes($this->params['ph'], $this->params['aix'], $this->params['action'] == 'index');
            }
        }
    }

    /**
     * Provide default toggle on/off action for cms controllers
     *
     */
    public function toggleAction()
    {
        $this->preToggle();
        $this->row->toggle();
        $this->postToggle();
        $this->redirectToIndex();
    }

    public function preToggle(){
    }
    public function postToggle(){
    }

    /**
     * New version of default save function, at this time
     * it'll get entity structure for taking in attention
     * and not just POST data as it was before
     *
     * @param bool $redirect
     */
    public function saveAction($redirect = true) {
        $this->preSave();
        /**
         * ��� ����� ������� ����� �� ������
         * 1. ������������� ��� ����� ���������� �� ����������� �������� � ������, ���� ��� ���� � ���������, �� ����������� � POST-�
         * 2. ��� ����� � ���������, ��������������� ������������ ������, �������� ������������ �������, �������� ���������, �����, ������,
         *    �����, ����, ����, ���������
         * 3. ���� �������� �����������, �� ��������� ����� ������ �� ����������� ���������� ��� ����� ����
         * 4. ������������� ������� �����������, ���� ��������� ����� ������
         * 5. ������������� ����������� ������� � ������ �����
         */

        $fields = $this->trail->getItem()->fields->setForeignRowsByForeignKeys('elementId');
        $model = $this->trail->getItem()->model;
        $table = $model->info('name');
        $sql = array();
        $set = array();
        $data = array();
        $sql[] = ($this->identifier ? 'UPDATE' : 'INSERT INTO') . ' `' . $table . '` SET';
        $treeColumn = $this->trail->getItem()->treeKeyName;
        foreach ($fields as $field) {
            if (isset($this->post[$field->alias])) {
                $value = $this->post[$field->alias];
//				if ( ! is_array($this->post[$field['alias']])) {
//					$value = str_replace('"','\"', $this->post[$field['alias']]);
//					$value = preg_replace('/\\{2,}"/', '\\"', $value);
//				} else {
//					$value = $this->post[$field['alias']];
//				}
            } else {
                $value = '';
            }
            if (!in_array($field->alias, $this->trail->getItem()->disabledFields['save']))
            i($field->foreign['elementId']);
            switch ($field->foreign['elementId']['alias']) {
                case 'string':
                case 'html':
                case 'check':
                case 'upload':
                    break;
                case 'price':
                    $value = $this->post[$field->alias]['integer'] . '.' . $this->post[$field->alias]['decimal'];
                    break;
                case 'color':
                    $value = preg_match('/^#[a-fA-F0-9]{6}$/', trim($value)) ? trim($value) : '#ffffff';
                    $value = Misc::rgbPrependHue($value);
                    break;
                case 'calendar':
                    $params = $field->getParams();
                    if ($params['displayFormat']) {
                        if ($params['displayFormat'] == 'd.m.Y' && $value == '00.00.0000') {
                            $value = '0000-00-00';
                        } else {
                            $value = date('Y-m-d', strtotime($value));
                        }
                    }
                    $value = preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', trim($value)) ? trim($value) : '0000-00-00';
                    break;
                case 'dimension':
                    $value = preg_match('/^[0-9]+$/', trim($value)) ? trim($value) : '0';
                    break;
                case 'time':
                    foreach ($this->post[$field->alias] as $p => $v) {
                        $this->post[$field->alias][$p] = preg_match('/^[0-9]{2}$/', trim($v)) ? trim($v) : '00';
                    }
                    $value = implode(':', array_values($this->post[$field->alias]));
                    break;
                case 'datetime':
                    if (is_array($this->post[$field->alias])) {
                        $params = $field->getParams();
                        if ($params['displayDateFormat']) {
                            if ($params['displayDateFormat'] == 'd.m.Y' && $this->post[$field->alias]['date'] == '00.00.0000') {
                                $this->post[$field->alias]['date'] = '0000-00-00';
                            } else {
                                $this->post[$field->alias]['date'] = date('Y-m-d', strtotime($this->post[$field->alias]['date']));
                            }
                        }
                        $value = preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', trim($this->post[$field->alias]['date'])) ? trim($this->post[$field->alias]['date']) : '0000-00-00';
                        unset($this->post[$field->alias]['date']);
                        foreach ($this->post[$field->alias] as $p => $v) {
                            $this->post[$field->alias][$p] = preg_match('/^[0-9]{2}$/', trim($v)) ? trim($v) : '00';
                        }
                        $value .= ' ' . implode(':', array_values($this->post[$field->alias]));
                    } else {
                        $value = preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', trim($this->post[$field->alias])) ? trim($this->post[$field->alias]) : '0000-00-00 00:00:00';
                    }
                    break;
                case 'multicheck':
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    } else {
                        $value = '';
                    }
                    break;
                case 'multiselect':
                    if (!is_array($value) || (count($value) <= 1 && ! array_key_exists(-1, $value))) {
                        $value = '';
                    } else {
                        unset($value[-1]);
                        $value = implode(',', $value);
                    }
                    break;
                case 'move':
                    if (!$this->identifier) {
                        $value = $model->getLastPosition();
                    } else {
                        $value = $this->trail->getItem()->row->move;
                    }
                case 'hidden':
                    if ($field->alias == 'move') {
                        if (!$this->identifier) {
                            $value = $model->getLastPosition();
                        } else {
                            $value = $this->trail->getItem()->row->move;
                        }
                    }
                    break;
                default:
                    break;
            }
            if ($field['columnTypeId'] != 0) {
                // prevent self parentness for row
                if ($this->identifier && $field->alias == $treeColumn && $value == $this->identifier){
                    $value = $this->trail->getItem()->row->$treeColumn;
                }
                // ���������, �� ��������� �� ���� � ������ �����������
                if (!in_array($field->alias, $this->trail->getItem()->disabledFields['save'])) {
                    $set[] = $field->alias . ' = "' . $value . '"';
                    $data[$field->alias] = $value;
                } else if (!$this->trail->getItem()->row->{$field->alias}){
                    $fieldId = $this->trail->getItem()->getFieldByAlias($field->alias)->id;
                    $sectionId = $this->trail->getItem()->section->id;
                    $disabledField = Misc::loadModel('DisabledField')->fetchRow('`sectionId` = "' . $sectionId . '" AND `fieldId` = "' . $fieldId . '"');
                    if (strlen($disabledField->defaultValue)) {
                        $value = $disabledField->defaultValue;
						Indi::$cmpTpl = $value; eval(Indi::$cmpRun); $value = Indi::$cmpOut;
                        $set[] = $field->alias . ' = "' . $value . '"';
                        $data[$field->alias] = $value;
                    }
                } else {
                    $value = $this->trail->getItem()->row->{$field->alias};
                    $set[] = $field->alias . ' = "' . $value . '"';
                    $data[$field->alias] = $value;
                }
                // ��������� ���������
                if ($this->admin['alternate'] && $field->alias == $this->admin['alternate'] . 'Id') {
                    $value = $this->admin['id'];
                    $set[] = $field->alias . ' = "' . $value . '"';
                    $data[$field->alias] = $value;
                }
            }
        }
        $set = implode(', ', $set);  $sql[] = $set; if ($this->identifier) $sql[] =  'WHERE `id` = "' . $this->identifier . '"'; $sql = implode(' ', $sql);
        //$this->db->query($sql);
//		d($this->trail->getItem()->disabledFields);
        try {
            if ($this->identifier) {
                $row = $this->trail->getItem()->model->fetchRow('`id` = "' . $this->identifier . '"');
                foreach ($data as $f => $v) $row->$f = $v;
                $row->save();
                //$this->trail->getItem()->model->update($data, '`id` = "' . $this->identifier . '"');
            } else {
                $row = $this->trail->getItem()->model->createRow($data);
                $row->save();
                $this->identifier = $row->id;
                //$this->identifier = $this->trail->getItem()->model->insert($data);
            }

            Indi_Image::deleteEntityImagesIfChecked();
            Indi_Image::uploadEntityImagesIfBrowsed(null, null, $this->requirements);

        } catch (Exception $e) {
            d($e);
            die();
        }

        $this->updateCacheIfNeed();

        $this->postSave();
        if ($redirect) {
            if ($this->trail->getItem(1)->row) {
                $id = $this->trail->getItem(1)->row->id;
            }
            if ($this->post['redirect-url']) {
                $url = $this->post['redirect-url'];
                if (preg_match('/\/ph\/([0-9a-f]+)\//', $url, $matches)) {
                    $_SESSION['indi']['admin'][$this->params['section']][$matches[1]]['toggledSave'] = true;
                    if (!$this->params['id']) {
						$_SESSION['indi']['admin'][$this->params['section']][$matches[1]]['found']++;
                        $this->post['redirect-url'] = str_replace('null', $this->identifier, $this->post['redirect-url']);
					}
                } else {
                    if (!$this->params['id']) $this->post['redirect-url'] = str_replace('null', $this->identifier, $this->post['redirect-url']);
                }
				die('<script>top.window.Indi.load("' . $this->post['redirect-url'] . '")</script>');
            } else {
                $url = STD . (COM ? '' : '/' . $this->module) . '/' . $this->section->alias . '/'
                    . ($id ? 'index/id/' . $id . '/' : '');
            }
            $this->_redirect($url);
        }
    }

    /**
     * Provide default index action
     *
     */
    public function indexAction()
    {
        if ($this->params['json']) {

            if ($this->params['xls']) {
                $this->xls();
            } else {
                die($this->prepareJsonDataForIndexAction());
            }
        }
    }

    public function viewAction(){

    }
    public function prepareJsonDataForIndexAction($json = true){
        // set up raw grid data
        $data = $this->rowset->toArray();
        $this->doSomethingCustom();

        // set up indent in case of tree structure of entity
        for($i = 0; $i < count($data); $i++) {
            $data[$i]['title'] = $data[$i]['_system']['indent'] . $data[$i]['title'];
        }

        // get info about columns that wiil be presented in grid
        $gridFields = $this->trail->getItem()->gridFields->toArray();
        $gridFieldsAliases = array('id'); for ($i = 0; $i < count ($gridFields); $i++) $gridFieldsAliases[] = $gridFields[$i]['alias'];

        // get info about all columns that are exists at the present moment in $data
        $columns = count($data) ? array_keys($data[0]) : array();

        // unset columns in $data that will not be used in grid
        for ($i = 0; $i < count($data); $i++) {
            foreach ($columns as $column) {
                if (!in_array($column, $gridFieldsAliases) && $column != '_system') {
                    unset($data[$i][$column]);
                }
            }
        }
        $gridFieldsAliasesThatStoreBoolean = array();
        // get info about grid columns, that store relations and boolean values
        for ($i = 0; $i < count ($gridFields); $i++) {
            if ($gridFields[$i]['relation']) $gridFieldsThatStoreRelation[$gridFields[$i]['alias']] = $gridFields[$i]['relation'];
            if ($gridFields[$i]['elementId'] == 9) $gridFieldsAliasesThatStoreBoolean[] = $gridFields[$i]['alias'];
        }
        $columntype = Misc::loadModel('ColumnType');

        if (count($gridFieldsThatStoreRelation)) {
            // get info about grid columns, that store relations, and their columns have SET and ENUM types
            // we need this info because there will be another logic to get titles for them
            // at first, get ids of 'columntypes' db table rows there was specified in 'type' column that they have SET or ENUM types
            $irregularColumnTypesIds = $columntype->getImplodedIds('`type` LIKE "ENUM%" OR `type` LIKE "SET%"', true);

            $irregularGridFieldsThatStoreRelation = array();
            foreach($gridFields as $gridField){
                if(in_array($gridField['columnTypeId'], $irregularColumnTypesIds)) $irregularGridFieldsThatStoreRelation[$gridField['alias']] = $gridField['id'];
            }
            $keys = array();
            // get distinct values for grid columns, that store relations
            $gridFieldsAliasesThatStoreRelation = array_keys($gridFieldsThatStoreRelation);
            for ($i = 0; $i < count($data); $i++) {
                foreach ($gridFieldsAliasesThatStoreRelation as $alias) {
                    if (strlen($data[$i][$alias])) {
                        if (preg_match('/,/', $data[$i][$alias])) {
                            $multipleA = explode(',', $data[$i][$alias]);
                            foreach ($multipleA as $multipleI) {
                                if (@!in_array($multipleI, $keys[$alias])) {
                                    $keys[$alias][] = $multipleI;
                                }
                            }
                        } else {
                            if (@!in_array($data[$i][$alias], $keys[$alias])) {
                                $keys[$alias][] = $data[$i][$alias];
                            }
                        }
                    }
                }
            }

            $irregularGridFieldsAliasesThatStoreRelation = array_keys($irregularGridFieldsThatStoreRelation);
            // get custom titles for values of grid columns, that store relations
            foreach ($keys as $fieldAlias => $foreignKeyValues) {
                if (count($foreignKeyValues)) {

                    // get titles for ENUM and SET columns (we called them 'irregular')
                    if (in_array($fieldAlias, $irregularGridFieldsAliasesThatStoreRelation)) {
                        $condition  = '`alias` IN ("' . implode('","', $foreignKeyValues) . '")';
                        $condition .= ' AND `fieldId` = "' . $irregularGridFieldsThatStoreRelation[$fieldAlias] . '"';
                        $foreignRowset = Entity::getInstance()->getModelById($gridFieldsThatStoreRelation[$fieldAlias])->fetchAll($condition);
                        foreach ($foreignRowset as $foreignRow) $titles[$fieldAlias][$foreignRow->alias] = $foreignRow->getTitle();

                    // get title for other columns that store relations
                    } else {
                        $foreignRowset = Entity::getInstance()->getModelById($gridFieldsThatStoreRelation[$fieldAlias])->fetchAll('`id` IN (' . implode(',', $foreignKeyValues) . ')');
                        foreach ($foreignRowset as $foreignRow) {
                            $title = $foreignRow->getTitle();
                            if(preg_match('/^[0-9]{3}#[0-9a-fA-F]{6}$/',$title)) {
                                $color = substr($title, 4);
                                $title = '<span class="i-color-box" style="background: #' . $color . ';"></span>#'. $color;
                            }
                            $titles[$fieldAlias][$foreignRow->id] = $title;
                        }
                    }
                }
            }

            // apply up custom titles
            for ($i = 0; $i < count($data); $i++) {
                foreach ($gridFieldsAliasesThatStoreRelation as $alias) {
                    if (preg_match('/,/', $data[$i][$alias])) {
                        $multipleA = explode(',', $data[$i][$alias]);
                        $title = array();
                        foreach ($multipleA as $multipleI) {
                            $title[] = $titles[$alias][$multipleI];
                        }
                        if ($title) $data[$i][$alias] = implode(', ', $title);
                    } else {
                        $title = $titles[$alias][$data[$i][$alias]];
                        if ($title || $data[$i][$alias] == 0) $data[$i][$alias] = $title;
                    }
                }
            }
        }

        // add trailing zeros to column that have the 'DOUBLE' type
        $doubleColumns = $columntype->fetchAll('`type` LIKE "DOUBLE%"');
        foreach ($doubleColumns as $doubleColumn) {
            preg_match("/\(\d+,(\d+)\)/i", $doubleColumn->type, $matches);
            $pad = $matches[1];
            for ($i = 0; $i < count($gridFields); $i++) {
                if ($doubleColumn->id == $gridFields[$i]['columnTypeId']) {
                    for ($j = 0; $j < count ($data); $j++) {
                        $double = $data[$j][$gridFields[$i]['alias']];
                        if (count($parts = explode('.', $double))) {
                            $data[$j][$gridFields[$i]['alias']] = $parts[0] . '.' . str_pad($parts[1], $pad, '0', STR_PAD_RIGHT);
                        } else {
                            $data[$j][$gridFields[$i]['alias']] = $double . str_pad('.', $pad + 1, '0', STR_PAD_RIGHT);
                        }
                    }
                }
            }
        }

        // convert hue part from columns of type Color to color box
        $colorColumns = $columntype->fetchAll('`type` = "VARCHAR(10)"');
        foreach ($colorColumns as $colorColumn) {
            for ($i = 0; $i < count($gridFields); $i++) {
                if ($colorColumn->id == $gridFields[$i]['columnTypeId']) {
                    for ($j = 0; $j < count ($data); $j++) {
                        $color = substr($data[$j][$gridFields[$i]['alias']], 4);
                        $data[$j][$gridFields[$i]['alias']] = '<span class="i-color-box" style="background: #' . $color . ';"></span>#'. $color;
                    }
                }
            }
        }
//#3667f0
        // check if data at any column has color format and convert hue part to color box
        for ($i = 0; $i < count($gridFields); $i++) {
            for ($j = 0; $j < count ($data); $j++) {
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $data[$j][$gridFields[$i]['alias']], $matches)) {
                    $data[$j][$gridFields[$i]['alias']] = '<span class="i-color-box" style="background: #' . $matches[1] . ';"></span>#'. $matches[1];
                } else if (preg_match('/^<span class="i-color-box" style="background: ([#0-9a-zA-Z]{3,20});[^"]*"[^>]*>/', $data[$j][$gridFields[$i]['alias']], $matches)) {
                    $data[$j][$gridFields[$i]['alias']] = '<span class="i-color-box" style="background: ' . $matches[1] . ';"></span>'. strip_tags($data[$j][$gridFields[$i]['alias']]);
                }
            }
        }


        // get custom titles for values in grid that are foreign keys, but each can relate to different entity
        // at first we should  find such a columns and their satellites
        $satellitedFields = $this->trail->getItem()->model->getSatellitedFields($gridFieldsAliases);
        foreach ($satellitedFields as $field) {
            $fieldAlias = $field->alias;
            $satelliteAlias = $field->getForeignRowByForeignKey('satellite')->alias;
            $i = 0;
            foreach ($this->rowset as $row) {
                if ($fr = $row->getForeignRowByForeignKey($fieldAlias))	{
                    if ($fr instanceof Indi_Db_Table_Row) $data[$i][$fieldAlias] = $fr->getTitle();
                }
                $i++;
            }
        }

        // set grid titles by custom logic
        $this->setGridTitlesByCustomLogic($data);

        // apply up custom titles
        for ($i = 0; $i < count($data); $i++) {
            foreach ($gridFieldsAliasesThatStoreBoolean as $alias) {
                $data[$i][$alias] = $data[$i][$alias] ? GRID_FILTER_CHECKBOX_YES : GRID_FILTER_CHECKBOX_NO;
            }
        }

        // find date and datetime fields and apply display format, if specified
        foreach ($this->trail->getItem()->gridFields as $fieldR) {
            if ($fieldR->elementId == 12) {
                $params = $fieldR->getParams();
                if ($params['displayFormat']) {
                    for ($j = 0; $j < count ($data); $j++) {
                        if (preg_match($this->datePattern, $data[$j][$fieldR->alias])) {
                            if ($data[$j][$fieldR->alias] == '0000-00-00' && $params['displayFormat'] == 'd.m.Y') {
                                $data[$j][$fieldR->alias] = '00.00.0000';
                            } else if ($data[$j][$fieldR->alias] != '0000-00-00'){
                                $data[$j][$fieldR->alias] = date($params['displayFormat'], strtotime($data[$j][$fieldR->alias]));
                                if ($data[$j][$fieldR->alias] == '30.11.-0001') $data[$j][$fieldR->alias] = '00.00.0000';
                            }
                        }
                    }
                }
            } else if ($fieldR->elementId == 19) {
                $params = $fieldR->getParams();
                if ($params['displayDateFormat'] || $params['displayTimeFormat']) {
                    if (!$params['displayDateFormat']) $params['displayDateFormat'] = 'Y-m-d';
                    if (!$params['displayTimeFormat']) $params['displayTimeFormat'] = 'H:i:s';
                    for ($j = 0; $j < count ($data); $j++) {
                        if (preg_match('/^0000-00-00/', $data[$j][$fieldR->alias])
                            && $params['displayDateFormat'] == 'd.m.Y'
                            && preg_match('/00:00:00$/', $data[$j][$fieldR->alias])
                            && $params['displayTimeFormat'] == 'H:i') {
                            $data[$j][$fieldR->alias] = '00.00.0000 00:00';
                        } else if ($data[$j][$fieldR->alias] != ''){
                            $data[$j][$fieldR->alias] = date($params['displayDateFormat'] . ' ' . $params['displayTimeFormat'], strtotime($data[$j][$fieldR->alias]));
                        }

                    }
                }
            }
        }
        if ($json) {
            $jsonData = array("totalCount" => $this->rowset->foundRows, "blocks" => $data);
            return json_encode($jsonData);
        } else {
            return $data;
        }
    }

    function setGridTitlesByCustomLogic(&$data){}

    public function postSave(){
    }
    public function preSave(){
    }
    public function postDelete(){
    }
    public function preDelete(){
    }
    public function postMove(){
    }
    public function doSomethingCustom(){
    }
    public function redirectToIndex(){
        if ($this->trail->getItem(1)->row) {
            $id = $this->trail->getItem(1)->row->id;
        }
        if ($this->params['ph']) $scope = $_SESSION['indi']['admin'][$this->section->alias][$this->params['ph']];
        $this->_redirect(
            STD . '/' .
            (COM ? '' : $this->module . '/') .
            $this->section->alias  . '/' .
            ($id ? 'index/id/' . $id . '/' : ($scope ? 'index/' : '')) . 
            ($scope['upperHash'] ? 'ph/' . $scope['upperHash'] . '/aix/' . $scope['upperAix'] . '/' : '')
        );
    }
    public function postDispatch($return = false){
        // assign general template data
        $this->assign();
        if (!$this->section && $this->action == 'index') {
            $out = $this->view->render('index.php');
        } else {
            $out = $this->view->renderContent();
        }
        if (COM) {
            $out = preg_replace('/("|\')\/admin/', '$1', $out);
        };
        if (STD) {
            $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . STD . '/', $out);
            $out = preg_replace('/value: \'\/admin/', 'value: \'' . STD . '/admin', $out);
        }
        if ($return) {
            return $out;
        } else {
            die($out);
        }
    }
    /**
      * Assigns admin name, date, menu, trail and all
      * that used in admin area in general
      *
      */
    public function assign(){
        $this->view->assign('admin', $this->admin['title'] . ' [' . $this->admin['profile']  . ']');
        $this->view->assign('date', date('<b>l</b>, d.m.Y [H:i]'));
        $this->view->assign('menu', Indi_Auth::getInstance()->getMenu());
        $this->view->assign('get', $this->get);
        $this->view->assign('request', $this->params);

        $this->view->assign('trail', $this->trail);
        $this->view->assign('module', $this->module);
        $this->view->assign('section', $this->section);
        $this->view->assign('action', $this->action);
        $this->view->assign('entity', $this->section ? $this->section->getForeignRowByForeignKey('entityId') : null);
        if ($this->trail->getItem()->model) {
            $this->view->assign('structure', $this->trail->getItem()->model->getFields());
        }
        if ($this->trail->getItem()->actions) {
            $this->view->assign('actions', $this->trail->getItem()->actions);
        }
        if ($this->trail->getItem()->sections) {
            $this->view->assign('sections', $this->trail->getItem()->sections);
        }
        if ($this->row) {
            $this->view->assign('row', $this->row);
            foreach ($this->trail->getItem()->model->getFields() as $field) {
                if (!is_array($this->row->$field) && !is_null($this->row->$field)) {
                    $this->row->$field = stripslashes($this->row->$field);
                }
            }
        }
        if ($this->rowset) {
            foreach ($this->rowset as $row) {
                foreach ($this->trail->getItem()->model->getFields() as $field) {
                    if (is_string($row->$field)) {
                        $row->$field = stripslashes($row->$field);
                    }
                }
            }
            $this->view->assign('rowset', $this->rowset);
            $this->view->page = $this->page;
            $this->view->limit = $this->limit;
        }
        // adding request object to view
//        $this->view->assign('request', $this->getRequest());
        if ($this->only) {
            $this->view->assign('only',true);
        }
    }

    public function updateCacheIfNeed(){
        if (!Indi_Cache::$useCache) return;
        // Get table name
        $table = $this->trail->getItem()->model->info('name');

        if ($table == 'entity') {
            // Сколько было кэшей в списке
            $tablesName = Indi_Cache::fname('tables');
            require_once($tablesName);
            $was = $GLOBALS['cache']['tables'];
            // Сколько должно теперь быть
            $rs = $this->db->query('SELECT `table` FROM `entity` WHERE `useCache` = "1"')->fetchAll();
            foreach ($rs as $r) $now[] = $r['table'];
            // Генерим дополнительные файлы кэша, если нужно
            $add = array_diff($now, $was);
            foreach ($add as $new) Indi_Cache::update(ucfirst($new));
            // Удаляем старые файлы кэша, елси нужно
            $rem = array_diff($was, $now);
            foreach ($rem as $del) unlink(Indi_Cache::fname(ucfirst($del)));
            // Обновляем кэш списка кэшей
            $php = '<?php $GLOBALS["cache"]["tables"] = array("' . implode('","', $now) . '");';
            $fp = fopen($tablesName, 'w');
            fwrite($fp, $php);
            fclose($fp);
        }

        // Update cache if need
        if (Misc::loadModel('Entity')->fetchRow('`table` = "' . $table .'" AND `system` = "y"')->useCache) {
            Indi_Cache::update(get_class($this->trail->getItem()->model));
        }
    }
}
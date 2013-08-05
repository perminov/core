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
        // languages
        $config = Indi_Registry::get('config');
        @include_once($_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] . '/core/application/lang/admin/' . $config['view']->lang . '.php');
        @include_once($_SERVER['DOCUMENT_ROOT'] . $_SERVER['STD'] . '/www/application/lang/admin/' . $config['view']->lang . '.php');
        $GLOBALS['lang'] = $config['view']->lang;

        // perform authentication
        Indi_Auth::getInstance()->auth($this);

        // set up all trail info
        $sectionAlias = $this->controller;

        // set up info for pagination
        if (isset($this->get['limit'])) {
            $this->limit = $this->get['limit'];
            $this->start = $this->get['start'];
			$this->page = $_SESSION['admin']['indexParams'][$sectionAlias]['page'] = ($this->start/$this->limit)+1;
        }

        $section = Misc::loadModel('Section');

        // set up all trail info
        $this->trail = new Indi_Trail_Admin($this->controller, $this->identifier, $this->action, null, $this->params, $this->authComponent);

        // set up current section and foreign rows, assotiated with
        $this->section = $section->fetchRow('`alias` = "' . $this->controller . '"');

        if ($this->section) {

            $this->section->setForeignRowsByForeignKeysOld();

            // set up row of entity, assotiated with current section, if action != 'index'
            $this->row = $this->trail->getItem()->row;

            // determine if current entity has a tree structure
            if ($this->trail->getItem()->model) {
                $fields = $this->trail->getItem()->fields->toArray();
                foreach ($fields as $field) {
                    $entityRow = Entity::getInstance()->fetchRow('`id`="' . $field['entityId'] . '"');
                    if ($field['alias'] == $entityRow->table . 'Id' && $field['relation'] == $entityRow->id) {
                        $this->trail->getItem()->treeColumn = $field['alias'];
                        break;
                    }
                }
            }

            // set up rowset
            if ($this->trail->getItem()->model && $this->action == 'index') {
                // rowset is default ordered by `move` field, if not exists then `title` field,
                // if it also does not exist, so there will be no ordering.
                $order = $this->trail->getItem()->model->fieldExists('move') ? 'move' : $this->order;
                $order = $order ? $order : ($this->trail->getItem()->model->fieldExists('title') ? 'title' : null);

                // set up default sorting for json listing
                if(!$this->params['json']) {
                    $this->trail->getItem()->section->sorting = $order;
                }

                // if this section have parent section, we should fetch only records, related to parent row
                // for example if we want to see cities, we must define in WHAT country these cities are located
                if ($this->trail->getItem(1)->row && !$this->noFilterByParent) {
                    if (!is_null($this->specialParentCondition())) {
                        $condition[] = $this->specialParentCondition();
                    } else {
                        if ($this->trail->getItem()->section->parentSectionConnector) {
                            $parentSectionConnectorAlias =$this->trail->getItem()->section->getForeignRowByForeignKey('parentSectionConnector')->alias;
                            $condition[] = '`' . $parentSectionConnectorAlias . '` = "' . $this->trail->getItem(1)->row->id . '"';
                        } else {
                            $condition[] = '`' . $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id` = "' . $this->trail->getItem(1)->row->id . '"';
                        }
                    }
                }

                if ($this->rowsetCondition) $condition[] = $this->rowsetCondition;
                if ($this->trail->getItem()->section->filter) $condition[] = $this->trail->getItem()->section->filter;
                if ($this->rsc) $condition[] = $this->rsc;

                // owner control
                if($this->admin['alternate'] && $this->trail->getItem()->model->fieldExists($this->admin['alternate'] . 'Id')) $condition[] =  '`' . $this->admin['alternate'] . 'Id` = "' . $this->admin['id'] . '"';

                // grid filters search
                if ($this->get['search']) {
                    $search = json_decode($this->get['search'], true);
                    foreach ($search as $searchOnField) {
                        $filterSearchFieldAlias = key($searchOnField);
                        $filterSearchFieldValue = current($searchOnField);
                        $found = null;
                        foreach ($this->trail->getItem()->fields as $field) if ($field->alias == preg_replace('/-(lte|gte)$/','',$filterSearchFieldAlias)) $found = $field;
                        if ($found->relation || $found->elementId == 9 || $found->elementId == 11) {
                            if (is_array($filterSearchFieldValue)) {
                                if ($found->storeRelationAbility == 'one') {
                                    $condition[] = '`' . $filterSearchFieldAlias . '` IN ("' . implode('","', $filterSearchFieldValue) . '")';
                                } else if ($found->storeRelationAbility == 'many') {
                                    $findInSet = array();
                                    foreach ($filterSearchFieldValue as $filterSearchFieldValueItem) {
                                        $findInSet[] = 'FIND_IN_SET("' . $filterSearchFieldValueItem . '", `' . $filterSearchFieldAlias . '`)';
                                    }
                                    $condition[] = '(' . implode(' AND ', $findInSet) . ')';
                                } else if ($found->storeRelationAbility == 'none') {
                                    if ($found->elementId == 11) {
                                        list($hueFrom, $hueTo) = $filterSearchFieldValue;
                                        if ($hueTo > $hueFrom) {
                                            $condition[] = 'SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) BETWEEN "' . str_pad($hueFrom, 3, '0', STR_PAD_LEFT) . '" AND "' . str_pad($hueTo, 3, '0', STR_PAD_LEFT) . '"';
                                        } else if ($hueTo < $hueFrom) {
                                            $condition[] = '(SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) >= "' . str_pad($hueFrom, 3, '0', STR_PAD_LEFT) . '" OR SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) <= "' . str_pad($hueTo, 3, '0', STR_PAD_LEFT) . '")';
                                        } else {
                                            $condition[] = 'SUBSTRING(`' . $filterSearchFieldAlias . '`, 1, 3) = "' . str_pad($hueFrom, 3, '0', STR_PAD_LEFT) . '"';
                                        }
                                    }
                                }
                            } else {
                                if (in_array($found->storeRelationAbility, array('none', 'one'))) {
                                    $condition[] = '`' . $filterSearchFieldAlias . '` = "' . $filterSearchFieldValue . '"';
                                } else if ($found->storeRelationAbility == 'many') {
                                    $condition[] = 'FIND_IN_SET("' . $filterSearchFieldValue . '", `' . $filterSearchFieldAlias . '`)';
                                }
                            }
                        } else if ($found->elementId == 1) {
                            $condition[] = '`' . $filterSearchFieldAlias . '` LIKE "%' . $filterSearchFieldValue . '%"';
                        } else if (in_array($found->elementId, array(18,12,19))) {
                            preg_match('/([a-zA-Z0-9_\-]+)-(lte|gte)$/', $filterSearchFieldAlias, $matches);
                            if ($found->elementId == 12 || $found->elementId ==19) $filterSearchFieldValue = substr($filterSearchFieldValue, 0, 10);
                            if ($found->elementId == 19) $filterSearchFieldValue .= ' 00:00:00';
                            $condition[] = '`' . $matches[1] . '` ' . ($matches[2] == 'gte' ? '>' : '<') . '= "' . $filterSearchFieldValue . '"';
                        } else if ($found->columnTypeId == 4) {
                            $condition[] = 'MATCH(`' . $filterSearchFieldAlias . '`) AGAINST("' . $filterSearchFieldValue . '*" IN BOOLEAN MODE)';
                        }
                    }
                }
                // fast search
                $condition = $this->appendFastSearchConditionIfNeed($condition);

                $this->limit = $this->trail->getItem()->section->rowsOnPage;
                // set up sorting depend on ExtJS grid column click
                $condition = count($condition) ? implode(' AND ', $condition) : null;
                $condition = $this->modifyRowsetCondition($condition);
                $order = $this->getOrderForJsonRowset($condition, true);
                if($this->params['json']) {
                    $this->preIndexJson();
                    if ($this->trail->getItem()->model->treeColumn) {
                        $this->rowset = $this->trail->getItem()->model->fetchTree($condition, $order, $this->limit, $this->page);
                    } else {
                        $order = $this->getOrderForJsonRowset($condition);
                        $this->rowset = $this->trail->getItem()->model->fetchAll($condition, $order, $this->limit, $this->page);
                    }
                }
            }
        }
        //parent::preDispatch();
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

        $fields = $this->trail->getItem()->fields->setForeignRowsByForeignKeys('elementId')->toArray();
        $model = $this->trail->getItem()->model;
        $table = $model->info('name');
        $sql = array();
        $set = array();
        $data = array();
        $sql[] = ($this->identifier ? 'UPDATE' : 'INSERT INTO') . ' `' . $table . '` SET';
        $treeColumn = $this->trail->getItem()->treeKeyName;
        foreach ($fields as $field) {
            if (isset($this->post[$field['alias']])) {
                $value = $this->post[$field['alias']];
//				if ( ! is_array($this->post[$field['alias']])) {
//					$value = str_replace('"','\"', $this->post[$field['alias']]);
//					$value = preg_replace('/\\{2,}"/', '\\"', $value);
//				} else {
//					$value = $this->post[$field['alias']];
//				}
            } else {
                $value = '';
            }
            if (!in_array($field['alias'], $this->trail->getItem()->disabledFields))
            switch ($field['foreign']['elementId']['alias']) {
                case 'string':
                case 'html':
                case 'check':
                case 'upload':
                    break;
                case 'price':
                    $value = $this->post[$field['alias']]['integer'] . '.' . $this->post[$field['alias']]['decimal'];
                    break;
                case 'color':
                    $value = preg_match('/^#[a-fA-F0-9]{6}$/', trim($value)) ? trim($value) : '#ffffff';
                    $value = Misc::rgbPrependHue($value);
                    break;
                case 'calendar':
                    $value = preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', trim($value)) ? trim($value) : '0000-00-00';
                    break;
                case 'dimension':
                    $value = preg_match('/^[0-9]+$/', trim($value)) ? trim($value) : '0';
                    break;
                case 'time':
                    foreach ($this->post[$field['alias']] as $p => $v) {
                        $this->post[$field['alias']][$p] = preg_match('/^[0-9]{2}$/', trim($v)) ? trim($v) : '00';
                    }
                    $value = implode(':', array_values($this->post[$field['alias']]));
                    break;
                case 'datetime':
                    if (is_array($this->post[$field['alias']])) {
                        $value = preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', trim($this->post[$field['alias']]['date'])) ? trim($this->post[$field['alias']]['date']) : '0000-00-00';
                        unset($this->post[$field['alias']]['date']);
                        foreach ($this->post[$field['alias']] as $p => $v) {
                            $this->post[$field['alias']][$p] = preg_match('/^[0-9]{2}$/', trim($v)) ? trim($v) : '00';
                        }
                        $value .= ' ' . implode(':', array_values($this->post[$field['alias']]));
                    } else {
                        $value = preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', trim($this->post[$field['alias']])) ? trim($this->post[$field['alias']]) : '0000-00-00 00:00:00';
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
                default:
                    break;
            }
            if ($field['columnTypeId'] != 0) {
                // prevent self parentness for row
                if ($this->identifier && $field['alias'] == $treeColumn && $value == $this->identifier){
                    $value = $this->trail->getItem()->row->$treeColumn;
                }
                // ���������, �� ��������� �� ���� � ������ �����������
                if (!in_array($field['alias'], $this->trail->getItem()->disabledFields)) {
                    $set[] = $field['alias'] . ' = "' . $value . '"';
                    $data[$field['alias']] = $value;
                } else if (!$this->trail->getItem()->row->{$field['alias']}){
                    $fieldId = $this->trail->getItem()->getFieldByAlias($field['alias'])->id;
                    $sectionId = $this->trail->getItem()->section->id;
                    $disabledField = Misc::loadModel('DisabledField')->fetchRow('`sectionId` = "' . $sectionId . '" AND `fieldId` = "' . $fieldId . '"');
                    if (strlen($disabledField->defaultValue)) {
                        $value = $disabledField->defaultValue;
                        if (preg_match('/(\$|::)/', $value)) {
                            eval('$value = ' . $value . ';');
                        }
                        $set[] = $field['alias'] . ' = "' . $value . '"';
                        $data[$field['alias']] = $value;
                    }
                } else {
                    $value = $this->trail->getItem()->row->{$field['alias']};
                    $set[] = $field['alias'] . ' = "' . $value . '"';
                    $data[$field['alias']] = $value;
                }
                // ��������� ���������
                if ($this->admin['alternate'] && $field['alias'] == $this->admin['alternate'] . 'Id') {
                    $value = $this->admin['id'];
                    $set[] = $field['alias'] . ' = "' . $value . '"';
                    $data[$field['alias']] = $value;
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
                $this->identifier = $row->save();
                //$this->identifier = $this->trail->getItem()->model->insert($data);
            }

            Indi_Image::deleteEntityImagesIfChecked();
            Indi_Image::uploadEntityImagesIfBrowsed(null, null, $this->requirements);

        } catch (Exception $e) {
            d($e);
            die();
            throw new Exception('Cannot save into "' . $this->section->foreignRows->entityId->table . '" table');
        }

        $this->updateCacheIfNeed();

        $this->postSave();
        if ($redirect) {
            if ($this->trail->getItem(1)->row) {
                $id = $this->trail->getItem(1)->row->id;
            }
            $this->_redirect($_SERVER['STD'] . ($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));
        }
    }

    /**
     * Provide default index action
     *
     */
    public function indexAction()
    {
        if ($this->params['json']) {
            die($this->prepareJsonDataForIndexAction());
        }
    }

    /**
     * Provide default form action
     *
     */
    public function formAction()
    {
//		$this->post['field'] = 'defaultSort';
//		$this->post['satellite'] = 93;
        if ($this->params['json']) {
            $fields = $this->trail->getItem()->fields;
            for($i = 0; $i < $fields->count(); $i++) {
                if ($fields[$i]->alias == $this->post['field']) {
                    $elementAlias = Entity::getModelByTable('element')->fetchRow('`id` = "' . $fields[$i]->elementId . '"')->alias;
                    if ($fields[$i]->dependency == 'e') {
                        $entityId = $this->row->getEntityIdForVariableForeignKey($this->post['field'], $this->post['satellite']);
                        $this->trail->items[count($this->trail->items)-1]->fields[$i]->relation = $entityId;
                    } else if ($fields[$i]->dependency != 'u') {
                        for($j = 0; $j < $fields->count(); $j++) {
                            if ($fields[$i]->satellite == $fields[$j]->id) {
                                if ($fields[$i]->alternative) {
                                    $satelliteRow = Entity::getInstance()->getModelById($fields[$j]->relation)->fetchRow('`id` = "' .$this->post['satellite'] . '"');
                                    $fields[$j]->alias = $fields[$i]->alternative;
                                    $this->post['satellite'] = $satelliteRow->{$fields[$i]->alternative};
                                }
//								if (!$this->trail->items[count($this->trail->items)-1]->dropdownWhere[$fields[$i]->alias]) $this->trail->items[count($this->trail->items)-1]->dropdownWhere[$fields[$i]->alias] = array();
//                                $this->trail->items[count($this->trail->items)-1]->dropdownWhere[$fields[$i]->alias][] = 'FIND_IN_SET("' . $this->post['satellite'] . '", `' . ($fields[$j]->satellitealias ? $fields[$j]->satellitealias : $fields[$j]->alias) . '`)';
                                $this->trail->items[count($this->trail->items)-1]->dropdownWhere[$fields[$i]->alias] = 'FIND_IN_SET("' . $this->post['satellite'] . '", `' . ($fields[$j]->satellitealias ? $fields[$j]->satellitealias : $fields[$j]->alias) . '`)';
                            }
                        }
                    } else if ($fields[$i]->dependency == 'u') {
                    }
                }
            }
            $this->view->trail = $this->trail;
            $this->view->row = $this->row;
            switch ($elementAlias) {
                case 'select':
                    $element = $this->view->formSelect($this->post['field'], null, array('optionsOnly' => true));
                    break;
                case 'dselect':
                    $attribs = array(
                        'optionsOnly' => true,
                        'find' => str_replace('"','\"', $this->post['find']),
                        'value' => $this->post['value'],
                        'more' => $this->post['more'] == 'true' ? true : false,
                        'element' => 'dselect'
                    );
                    if (isset($this->post['noempty'])) $attribs['noempty'] = $this->post['noempty'];
                    if (isset($this->post['page'])) $attribs['page'] = $this->post['page'];
                    if (isset($this->post['up'])) $attribs['up'] = $this->post['up'];

                    $element = $this->view->formDselect($this->post['field'], null, $attribs);
                    break;
                case 'multicheck':
                    $element = $this->view->formMulticheck($this->post['field'], 1, array('optionsOnly' => true));
                    break;
            }
            die($element);
        } else if ($this->params['combo']){
            parent::formAction();
        }
    }

    public function viewAction()
    {

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
                    if ($data[$i][$alias] && @!in_array($data[$i][$alias], $keys[$alias])) $keys[$alias][] = $data[$i][$alias];
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
                                $title = '<span class="color-box" style="background: #' . $color . ';"></span>#'. $color;
                            }
                            $titles[$fieldAlias][$foreignRow->id] = $title;
                        }
                    }
                }
            }
            // set grid titles by custom logic
            $this->setGridTitlesByCustomLogic($data);

            // apply up custom titles
            for ($i = 0; $i < count($data); $i++) {
                foreach ($gridFieldsAliasesThatStoreRelation as $alias) {
                    $title = $titles[$alias][$data[$i][$alias]];
                    if ($title) $data[$i][$alias] = $title;
                }
            }
        }

        // apply up custom titles
        for ($i = 0; $i < count($data); $i++) {
            foreach ($gridFieldsAliasesThatStoreBoolean as $alias) {
                $data[$i][$alias] = $data[$i][$alias] ? GRID_FILTER_CHECKBOX_YES : GRID_FILTER_CHECKBOX_NO;
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
                        $data[$j][$gridFields[$i]['alias']] = '<span class="color-box" style="background: #' . $color . ';"></span>#'. $color;
                    }
                }
            }
        }
//#3667f0
        // check if data at any column has color format and convert hue part to color box
        for ($i = 0; $i < count($gridFields); $i++) {
            for ($j = 0; $j < count ($data); $j++) {
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $data[$j][$gridFields[$i]['alias']], $matches)) {
                    $data[$j][$gridFields[$i]['alias']] = '<span class="color-box" style="background: #' . $matches[1] . ';"></span>#'. $matches[1];
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
                    $data[$i][$fieldAlias] = $fr->getTitle();
                }
                $i++;
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

    public function getOrderForJsonRowset($condition = null){
        //$this->get['sort'] = ;
        if ($this->get['sort']) {
            $sort = current(json_decode($this->get['sort'], 1));
            $this->post['sort'] = $sort['property'];
            $this->post['dir'] = $sort['direction'];
            // Если не указано, по какому столбцу сорировать, не сортируем
            if (!$this->post['sort']) return;
        }
//		$this->post['sort'] = 'identifier';
//		$this->post['dir'] = 'ASC';
        // get info about columns that will be presented in grid
        $gridFields = $this->trail->getItem()->gridFields->toArray();

        // check if field (that is to be sorted by) store relation
        $entityId = false;
        for ($i = 0; $i < count($gridFields); $i++) {
            if($gridFields[$i]['alias'] == $this->post['sort']){//} && ($gridFields[$i]['relation'] || $gridFields[$i]['satellite'])) {
                $entityId = $gridFields[$i]['relation'];
                $fieldId  = $gridFields[$i]['id'];
                $satellite = $gridFields[$i]['satellite'];
                $columnTypeId = $gridFields[$i]['columnTypeId'];
                break;
            }
        }

        // get distinct entity ids that will be used to initialize models and retrieve rowsets
        if ($entityId) {
            // get distinct ids of foreign rows
            $info = $this->trail->getItem()->model->info();
            $query = 'SELECT DISTINCT `' . $this->post['sort'] . '` AS `id` FROM `' . $info['name'] . '` WHERE 1 ' . ($condition ? ' AND ' . $condition : '');
            $result = $this->db->query($query)->fetchAll();
            if (count($result)) {
                // get distinct foreign key values, to avoid twice or more calculating title for equal ids
                for ($i = 0; $i < count($result); $i++) $ids[] = $result[$i]['id'];

                // create temporary table
                $tmpTable = 'sorting';

                $query = 'DROP TABLE IF EXISTS `' . $tmpTable . '`;';
                $this->db->query($query);

                $query = 'CREATE TEMPORARY TABLE `' . $tmpTable . '` (`id` VARCHAR(255) NOT NULL, `title` VARCHAR(255)  COLLATE utf8_general_ci NOT NULL);';
                $this->db->query($query);

                $query = ' INSERT INTO `' . $tmpTable . '` ';
                if ($entityId == 6) {
                    $condition  = '`alias` IN ("' . implode('","', $ids) . '")';
                    $condition .= ' AND `fieldId` = "' . $fieldId . '"';
                    $query .= 'VALUES ';
                    $foreignRowset = Entity::getInstance()->getModelById($entityId)->fetchAll($condition);
                    foreach ($foreignRowset as $foreignRow) {
                        $values[] = '("' . $foreignRow->alias . '","' . str_replace('"', '&quote;', $foreignRow->getTitle()) . '")';
                    }
                    $query .= implode(',', $values) . ';';
                } else {
                    // in this block we investigate - have the 'getTitle' method been redeclared in child *_Row class, so if no - it
                    // mean that we can get titles for foreign keys  directly from 'title' column on corresponding foreign table
                    // and there is no need to preform any modifications on them before output in json format
                    // this shit is need it to avoid unneeded abuse to mysql server - to improve performance
                    $entity = Entity::getInstance()->fetchRow('`id` = "' . $entityId . '"')->toArray();
                    $info = Entity::getModelById($entityId)->info();
                    $modelsDirPath = trim($_SERVER['DOCUMENT_ROOT'] . '/www', '/') . '/application/';

                    // get filename of row class
                    $file = $backendModulePath . 'models/' . str_replace('_', '/', $info['rowClass']) . '.php';
                    if (file_exists($file)) $code = file_get_contents($file);

                    // if function 'getTitle' was not redeclared
                    if (!strpos($code, 'function getTitle(')) {
                        $foreignTableInfo = Entity::getModelById($entityId)->info();
                        $query .= 'SELECT `id`,`title` FROM `' . $foreignTableInfo['name'] . '` WHERE `id` IN (' . implode(',', $ids) . ');';
                    } else {
                        // prepare and put data into temporary table
                        $foreignRowset = Entity::getModelById($entityId)->fetchAll('`id` IN (' . implode(',', $ids) . ')');

                        $query .= 'VALUES ';
                        foreach ($foreignRowset as $foreignRow) {
                            $values[] = '(' . $foreignRow->id . ', "' . str_replace('"', '&quote;', $foreignRow->getTitle()) . '")';
                        }
                        $query .= implode(',', $values) . ';';
                    }
                }
                $this->db->query($query);

                // sort data in temporary table by 'title' field
                $result = $this->db->query('SELECT `id`,`title` FROM `' . $tmpTable . '` ORDER BY `title` ' . $this->post['dir'])->fetchAll();

                $query = 'DROP TABLE `' . $tmpTable . '`;';
                $this->db->query($query);

                // get array of ids
                $ids = array();
                for ($i = 0; $i < count($result); $i++) $ids[] = $result[$i]['id'];

                $order = 'POSITION(CONCAT("\'", ' . $this->post['sort'] . ', "\'") IN "\'' . implode("','", $ids) . '\'") ASC';
            } else $ids = array();

        // if column store foreign keys that are pointing to variable entties
        } else if ($satellite){
            $rowset = $this->trail->getItem()->model->fetchAll('1 ' . ($condition ? ' AND ' . $condition : ''));
            $tmp = array();
            foreach ($rowset as $row) {
                $tmp[] = array('id' => $row->id, 'title' => $row->getForeignRowByForeignKey($this->post['sort'])->getTitle());
            }
            if (count($tmp)) {
                // create temporary table
                $tmpTable = 'sorting';

                $query = 'DROP TABLE IF EXISTS `' . $tmpTable . '`;';
                $this->db->query($query);

                $query = 'CREATE TEMPORARY TABLE `' . $tmpTable . '` (`id` VARCHAR(255) NOT NULL, `title` VARCHAR(255) NOT NULL);';
                $this->db->query($query);

                $query = 'INSERT INTO `' . $tmpTable . '` ';
                $values = array();
                for ($i = 0; $i < count($tmp); $i ++) {
                    $values[] = '(' . $tmp[$i]['id'] . ', "' . str_replace('"', '&quote;', $tmp[$i]['title']) . '")';
                }
                $query .= 'VALUES ' . implode(', ', $values) . ';';
                $this->db->query($query);

                // sort data in temporary table by 'title' field
                $result = $this->db->query('SELECT `id`,`title` FROM `' . $tmpTable . '` ORDER BY `title` ' . $this->post['dir'])->fetchAll();

                $query = 'DROP TABLE `' . $tmpTable . '`;';
                $this->db->query($query);

                // get array of ids
                $ids = array();
                for ($i = 0; $i < count($result); $i++) $ids[] = $result[$i]['id'];

                $order = 'POSITION(CONCAT("\'", `id`, "\'") IN "\'' . implode("','", $ids) . '\'") ASC';
            }
        } else if (Misc::loadModel('ColumnType')->fetchRow('`id` = "' . $columnTypeId . '"')->type == 'BOOLEAN') {
            if ($GLOBALS['lang'] == 'en') {
                $order = 'IF(`' . $this->post['sort'] . '`, "' . GRID_FILTER_CHECKBOX_YES .'", "' . GRID_FILTER_CHECKBOX_NO . '") ' . $this->post['dir'];
            } else {
                $order = 'IF(`' . $this->post['sort'] . '`, "' . GRID_FILTER_CHECKBOX_NO .'", "' . GRID_FILTER_CHECKBOX_YES . '") ' . $this->post['dir'];
            }
        } else {
            $order = '`' . $this->post['sort'] . '` ' . $this->post['dir'];
        }
        $order = trim($order) ? $order : null;
        return $order;
    }

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
        $this->_redirect($_SERVER['STD'] . ($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));
    }
    public function postDispatch($return = false){
        // assign general template data
        $this->assign();
        if (!$this->section && $this->action == 'index') {
            $out = $this->view->render('index.php');
        } else {
            ob_start(); $this->view->renderContent(); $out = ob_get_clean();
        }
        if ($GLOBALS['cmsOnlyMode']) {
            $out = preg_replace('/("|\')\/admin/', '$1', $out);
        };
        if ($_SERVER['STD']) {
            $out = preg_replace('/(<link[^>]+)(href)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
            $out = preg_replace('/(<script[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
            $out = preg_replace('/(<img[^>]+)(src)=("|\')\//', '$1$2=$3' . $_SERVER['STD'] . '/', $out);
            $out = preg_replace('/value: \'\/admin/', 'value: \'' . $_SERVER['STD'] . '/admin', $out);
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
    public function assign()
    {
        $this->view->assign('admin', $this->admin['title'] . ' [' . $this->admin['profile']  . ']');
        $this->view->assign('date', date('<b>l</b>, d.m.Y [H:i]'));
        $this->view->assign('menu', Indi_Auth::getInstance()->getMenu());
        $this->view->assign('get', $this->get);
        $this->view->assign('request', $this->params);

        $this->view->assign('trail', $this->trail);
        $this->view->assign('module', $this->module);
        $this->view->assign('section', $this->section);
        $this->view->assign('action', $this->action);
        $this->view->assign('entity', $this->section->foreignRows->entityId);
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

    public function appendFastSearchConditionIfNeed($condition) {
        if (!$this->params['keyword']) return $condition;
        $this->params['keyword'] = str_replace('"','&quot;', strip_tags(urldecode($this->params['keyword'])));
        $keywordCondition = array();
        $this->trail->getItem()->gridFields->setForeignRowsByForeignKeys('columnTypeId');
        $exclude = array();
        if ($this->get['search']) {
            $search = json_decode($this->get['search'], true);
            foreach ($search as $searchOnField) $exclude[] = key($searchOnField);
        }
        foreach ($this->trail->getItem()->gridFields as $gridField) {
            if ($gridField->columnTypeId && !in_array($gridField->alias, $exclude)) {
                // Поиск по текстовым полям
                if (!$gridField->relation) {
                    $reg = array(
                        'YEAR' => '[0-9]', 'DATE' => '[0-9\-]', 'DATETIME' => '[0-9\- :]',
                        'TIME' => '[0-9:]', 'INT' => '[0-9]', 'DOUBLE' => '[0-9\.]'
                    );
                    if (preg_match('/(' . implode('|', array_keys($reg)) . ')/', $gridField->foreign['columnTypeId']['type'], $matches)) {
                        if (preg_match('/^' . $reg[$matches[1]] . '$/', $this->params['keyword'])) {
                            $keywordCondition[] = '`' . $gridField->alias . '` LIKE "%' . $this->params['keyword'] . '%"';
                        }
                    } else if (preg_match('/BOOLEAN/', $gridField->foreign['columnTypeId']['type'])) {
                        $keywordCondition[] = 'IF(`' . $gridField->alias . '`, "Да", "Нет") LIKE "%' . $this->params['keyword'] . '%"';
                    } else {
                        $keywordCondition[] = '`' . $gridField->alias . '` LIKE "%' . $this->params['keyword'] . '%"';
                    }
                // Поиск по полям типов ENUM и SET
                } else if ($gridField->relation == 6) {
                    $relativeValues = Misc::loadModel('Enumset')->fetchAll('`fieldId` = "' . $gridField->id . '" AND `title` LIKE "%' . $this->params['keyword'] . '%"');
                    $set = array(); foreach ($relativeValues as $relativeValue) $set[] = $relativeValue->alias;
                    if (count($set)) {
                        $keywordCondition[] = 'FIND_IN_SET(`' . $gridField->alias . '`, "' . implode(',', $set) . '")';
                    }
                // Поиск по остальным типам
                } else {
                    // Если поле вообще без сателлайта или с сателлайтом, но с типом зависимости "Фильтрация"
                    if (!$gridField->satellite || $gridField->dependency != 'e') {
                        $entity = Entity::getInstance()->getModelById($gridField->relation);
                        $relativeValues = $entity->fetchAll('`title` LIKE "%' . $this->params['keyword'] . '%"');
                        $set = array(); foreach ($relativeValues as $relativeValue) $set[] = $relativeValue->id;
                        if (count($set)) {
                            $keywordCondition[] = 'FIND_IN_SET(`' . $gridField->alias . '`, "' . implode(',', $set) . '")';
                        }
                    // Если тип зависимости - переменная сущность
                    } else {

                    }
                }
            }
        }
        if (count($keywordCondition)) {
            $keywordCondition = '(' . implode(' OR ', $keywordCondition) . ')';
        }
        if ($keywordCondition) $condition[] = $keywordCondition;
        return $condition;
    }
}
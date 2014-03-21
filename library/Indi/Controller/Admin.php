<?php
class Indi_Controller_Admin extends Indi_Controller_Admin_Beautiful {

    /**
     * Init all general cms features
     */
    public function preDispatch() {

        // Set current language
        @include_once(DOC . STD . '/core/application/lang/admin/' . Indi::ini('view')->lang . '.php');
        @include_once(DOC . STD . '/www/application/lang/admin/' . Indi::ini('view')->lang . '.php');

        // Perform authentication
        $this->auth();

        // set up current section and foreign rows, assotiated with
        $this->section = $this->trail->getItem()->section;

        if ($this->section) {

            // set up row of entity, assotiated with current section, if action != 'index'
            $this->row = $this->trail->getItem()->row;

            // set up rowset
            if ($this->trail->getItem()->model) {

                if ($this->action == 'index') {

                    $primaryWHERE = $this->primaryWHERE();

                    $this->setScopeUpper($primaryWHERE);

                    if ($this->params['json'] || $this->params['excel']) {

                        // Get final WHERE clause, that will implode primaryWHERE, filterWHERE and keywordWHERE
                        $finalWHERE = $this->finalWHERE($primaryWHERE);

                        // Get final ORDER clause, built regarding column name and sorting direction
                        $finalORDER = $this->finalORDER($finalWHERE, $this->get['sort']);

                        // Get the rowset, fetched using WHERE and ORDER clauses, and with built LIMIT clause,
                        // constructed with usage of $this->get('limit') and $this->get('page') params
                        $this->rowset = $this->trail->getItem()->model->{
                            'fetch'. ($this->trail->getItem()->model->treeColumn() ? 'Tree' : 'All')
                        }($finalWHERE, $finalORDER,
                            $this->params['excel'] ? null : (int) Indi::get('limit'),
                            $this->params['excel'] ? null : (int) Indi::get('page'));

                        // Save rowset properties, to be able to use them later in Sibling-navigation feature, and be
                        // able to restore the state of panel, that is representing the rowset at cms interface.
                        // State of the panel includes: filtering and search params, sorting params
                        $this->setScope($primaryWHERE, $this->get['search'], $this->params['keyword'], $this->get['sort'],
                            $this->get['page'], $this->rowset->found(), $finalWHERE, $finalORDER);
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
    public function toggleAction() {
        $this->row->toggle();
        $this->redirectToIndex();
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

        $fields = $this->trail->getItem()->fields->foreign('elementId');
        $model = $this->trail->getItem()->model;
        $table = $model->name();
        $sql = array();
        $set = array();
        $data = array();
        $sql[] = ($this->identifier ? 'UPDATE' : 'INSERT INTO') . ' `' . $table . '` SET';
        $treeColumn = $this->trail->getItem()->model->treeColumn();
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
                        } else if ($value != '0000-00-00') {
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
                        unset($this->post->{$field->alias}['date']);
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
                        $value = $model->getNextMove();
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
                    $disabledField = Indi::model('DisabledField')->fetchRow('`sectionId` = "' . $sectionId . '" AND `fieldId` = "' . $fieldId . '"');
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
                if ($_SESSION['admin']['alternate'] && $field->alias == $_SESSION['admin']['alternate'] . 'Id') {
                    $value = $_SESSION['admin']['id'];
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

    public function postSave(){
    }
    public function preSave(){
    }
    public function postDelete(){
    }
    public function preDelete(){
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
        if (Indi::uri('section') == 'index' && Indi::uri('action') == 'index') {
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
    public function assign() {
        if (Indi::uri()->section == 'index') {
            $this->view->assign('menu', Section::menu());
            $this->view->assign('admin', $_SESSION['admin']['title'] . ' [' . $_SESSION['admin']['profileTitle']  . ']');
        }
        $this->view->assign('trail', $this->trail);
        $this->view->assign('section', $this->section);
        $this->view->assign('action', $this->action);
        $this->view->assign('entity', $this->section ? $this->section->foreign('entityId') : null);
        if ($this->row) $this->view->row = $this->row;
    }

    public function updateCacheIfNeed(){
        if (!Indi_Cache::$useCache) return;
        // Get table name
        $table = $this->trail->getItem()->model->name();

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
        if (Indi::model('Entity')->fetchRow('`table` = "' . $table .'" AND `system` = "y"')->useCache) {
            Indi_Cache::update(get_class($this->trail->getItem()->model));
        }
    }
}
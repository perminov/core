<?php
class Indi_Controller_Admin extends Indi_Controller_Admin_Beautiful {

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

        $fields = Indi::trail()->fields->foreign('elementId');
        $model = Indi::trail()->model;
        $table = $model->name();
        $sql = array();
        $set = array();
        $data = array();
        $sql[] = ($this->identifier ? 'UPDATE' : 'INSERT INTO') . ' `' . $table . '` SET';
        $treeColumn = Indi::trail()->model->treeColumn();
        foreach ($fields as $field) {
            if (isset($this->post[$field->alias])) {
                $value = $this->post[$field->alias];
            } else {
                $value = '';
            }
            if (!in_array($field->alias, Indi::trail()->disabledFields['save']))
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
                        $value = Indi::trail()->row->move;
                    }
                case 'hidden':
                    if ($field->alias == 'move') {
                        if (!$this->identifier) {
                            $value = $model->getLastPosition();
                        } else {
                            $value = Indi::trail()->row->move;
                        }
                    }
                    break;
                default:
                    break;
            }
            if ($field['columnTypeId'] != 0) {
                // prevent self parentness for row
                if ($this->identifier && $field->alias == $treeColumn && $value == $this->identifier){
                    $value = Indi::trail()->row->$treeColumn;
                }
                // ���������, �� ��������� �� ���� � ������ �����������
                if (!in_array($field->alias, Indi::trail()->disabledFields['save'])) {
                    $set[] = $field->alias . ' = "' . $value . '"';
                    $data[$field->alias] = $value;
                } else if (!Indi::trail()->row->{$field->alias}){
                    $fieldId = Indi::trail()->model->fields($field->alias)->id;
                    $sectionId = Indi::trail()->section->id;
                    $disabledField = Indi::model('DisabledField')->fetchRow('`sectionId` = "' . $sectionId . '" AND `fieldId` = "' . $fieldId . '"');
                    if (strlen($disabledField->defaultValue)) {
                        $value = $disabledField->defaultValue;
						Indi::$cmpTpl = $value; eval(Indi::$cmpRun); $value = Indi::$cmpOut;
                        $set[] = $field->alias . ' = "' . $value . '"';
                        $data[$field->alias] = $value;
                    }
                } else {
                    $value = Indi::trail()->row->{$field->alias};
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
//		d(Indi::trail()->disabledFields);
        try {
            if ($this->identifier) {
                $row = Indi::trail()->model->fetchRow('`id` = "' . $this->identifier . '"');
                foreach ($data as $f => $v) $row->$f = $v;
                $row->save();
                //Indi::trail()->model->update($data, '`id` = "' . $this->identifier . '"');
            } else {
                $row = Indi::trail()->model->createRow($data);
                $row->save();
                $this->identifier = $row->id;
                //$this->identifier = Indi::trail()->model->insert($data);
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
            if (Indi::trail(1)->row) {
                $id = Indi::trail(1)->row->id;
            }
            if ($this->post['redirect-url']) {
                $url = $this->post['redirect-url'];
                if (preg_match('/\/ph\/([0-9a-f]+)\//', $url, $matches)) {
                    $_SESSION['indi']['admin'][Indi::uri()->section][$matches[1]]['toggledSave'] = true;
                    if (!Indi::uri()->id) {
						$_SESSION['indi']['admin'][Indi::uri()->section][$matches[1]]['found']++;
                        $this->post['redirect-url'] = str_replace('null', $this->identifier, $this->post['redirect-url']);
					}
                } else {
                    if (!Indi::uri()->id) $this->post['redirect-url'] = str_replace('null', $this->identifier, $this->post['redirect-url']);
                }
				die('<script>top.window.Indi.load("' . $this->post['redirect-url'] . '")</script>');
            } else {
                $url = STD . (COM ? '' : '/' . $this->module) . '/' . Indi::trail()->section->alias . '/'
                    . ($id ? 'index/id/' . $id . '/' : '');
            }
            $this->_redirect($url);
        }
    }

    public function updateCacheIfNeed(){
        if (!Indi_Cache::$useCache) return;
        // Get table name
        $table = Indi::trail()->model->name();

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
            Indi_Cache::update(get_class(Indi::trail()->model));
        }
    }
}
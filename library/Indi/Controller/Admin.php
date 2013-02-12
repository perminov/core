<?php
class Indi_Controller_Admin extends Indi_Controller{
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
     * Set up session's specialSectionCondition variable as object
     * of stdClass as Indi_Session_Namespace does not work with arrays
     *
     */
    public function init()
    {
        parent::init();
        if (!is_object($this->session->specialSectionCondition)) {
            $this->session->specialSectionCondition = new stdClass();
        }
    }
    
    /**
     * Init all general cms features
     *
     */
    public function preDispatch()
    {
		// set up configuration as key->value from 'config' table
        // config is used in different times
        $this->config = Config::getInstance()->asObject();

		//$this->getFrontController()->unregisterPlugin('Indi_Controller_Plugin_ErrorHandler');

        // set up auth session storage for cms
//        $this->auth = Indi_Auth::getInstance()->setStorage(new Indi_Auth_Storage_Session('admin', 'admin'));
		
        // perform authentication
        Indi_Auth::getInstance()->auth($this);

        
        // set up all trail info
        $sectionAlias = $this->controller;
		if ($this->session->specialSectionCondition->$sectionAlias) $condition[] = $this->session->specialSectionCondition->$sectionAlias;

//        $condition = $this->session->specialSectionCondition->$sectionAlias;
//        $condition = $condition ? ' AND ' . $condition : '';
        
		// set up info for pagination
		if (isset($this->post['limit'])) {
			$this->limit = $this->post['limit'];
			$this->start = $this->post['start'];
		}

		$section = Misc::loadModel('Section');
        // set up configuration as key->value from 'config' table
        // config is used in different times
        $this->config = Config::getInstance()->asObject();
                    
        // set up all trail info
        $sectionAlias = $this->controller;

        $this->trail = new Indi_Trail_Admin($this->controller, $this->identifier, $this->action, null, $this->params, $this->authComponent);

        // set up current section and foreign rows, assotiated with
        $this->section = $section->fetchRow('`alias` = "' . $this->controller . '"');// . $condition);

        if ($this->section) {
                
            $this->section->setForeignRowsByForeignKeysOld();
            
            // set up row of entity, assotiated with current section, if action != 'index'
            $this->row = $this->trail->getItem()->row;

			// determine if current entity has a tree structure
			if ($this->trail->getItem()->model) {
				$fields = $this->trail->getItem()->fields->toArray();
				foreach ($fields as $field) {
					if (!$entityTableName) {
						$entityRow = Entity::getInstance()->fetchRow('`id`="' . $field['entityId'] . '"');
					}
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
/*                if ($this->trail->getItem(1)->row) {
                    if ($this->specialParentCondition) {
                        $condition = $this->specialParentCondition;
                    } else {
                        $condition = '`' . $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id` = "' . $this->trail->getItem(1)->row->id . '"';
                    }
                } else {
                    $condition = null;
                }*/
                // if this section have parent section, we should fetch only records, related to parent row
                // for example if we want to see cities, we must define in WHAT country these cities are located
                if ($this->trail->getItem(1)->row) {
                    if ($this->specialParentCondition) {
                        $condition[] = $this->specialParentCondition;
                    } else {
                        $condition[] = '`' . $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id` = "' . $this->trail->getItem(1)->row->id . '"';
                    }
                }
				
/*				$this->rowsetCondition = $this->trail->getItem()->section->filter;
                if ($this->rowsetCondition) {
                    if ($condition) {
                        $condition .= ' AND ' . $this->rowsetCondition;
                    } else {
                        $condition = $this->rowsetCondition;
                    }
                }
                $condition .= $this->rsc;*/
                if ($this->rowsetCondition) $condition[] = $this->rowsetCondition;
                if ($this->trail->getItem()->section->filter) $condition[] = $this->trail->getItem()->section->filter;
				if ($this->rsc) $condition[] = $this->rsc;
				
				// owner control
				if($this->admin['alternate'] && $this->trail->getItem()->model->fieldExists($this->admin['alternate'] . 'Id')) $condition[] =  '`' . $this->admin['alternate'] . 'Id` = "' . $this->admin['id'] . '"';

				$this->limit = $this->trail->getItem()->section->rowsOnPage;
				// set up sorting depend on ExtJS grid column click
				$condition = count($condition) ? implode(' AND ', $condition) : null;
                $condition = $this->modifyRowsetCondition($condition);
				$order = $this->getOrderForJsonRowset($condition, true);
				if($this->params['json']) {
					$this->preIndexJson();
					if ($this->trail->getItem()->treeColumn) {
						$this->rowset = $this->trail->getItem()->model->fetchTree($this->trail->getItem()->treeColumn, 0, false, true, 0, $order, $condition);
					} else {
						$order = $this->getOrderForJsonRowset($condition);
						$this->rowset = $this->trail->getItem()->model->fetchAll($condition, $order, $this->limit, ($this->start/$this->limit)+1);
					}
				}
            }
        }
		parent::preDispatch();
    }

    /**
     * Provide default downAction (Move down) for Admin Sections controllers
     *
     * @param string $condition
     */
    public function downAction($condition = null)
    {
		$condition = $condition ? array($condition) : array();
		if ($this->trail->getItem()->treeColumn) {
			$condition[] = '`' . $this->trail->getItem()->treeColumn . '`="' . $this->row->{$this->trail->getItem()->treeColumn} . '"';
		}
        if ($this->trail->getItem(1)->row && $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')) {
//      if ($this->trail->getItem(1)->row) {
            $id = $this->trail->getItem(1)->row->id;
//          $condition = $condition . ($this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId') ? ' AND ' . $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id="' . $id . '"' : '');
            $condition[] = $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id="' . $id . '"';
        }
		if ($this->trail->getItem()->section->filter) {
			$condition[] = $this->trail->getItem()->section->filter;
		}
		$steps = $this->params['steps'];
		if (!$steps) $steps = 1;
//		for ($i = 0; $i < $steps; $i++) {
			$this->row->move('down', implode(' AND ', $condition));
			$this->postMove();
//		}
        $this->_redirect(($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));

    }
    
    /**
     * Provide default upAction (Move up) for Admin Sections controllers
     *
     * @param string $condition
     */
    public function upAction($condition = null)
    {
		$condition = $condition ? array($condition) : array();
		if ($this->trail->getItem()->treeColumn) {
			$condition[] = '`' . $this->trail->getItem()->treeColumn . '`="' . $this->row->{$this->trail->getItem()->treeColumn} . '"';
		}
        if ($this->trail->getItem(1)->row && $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')) {
//      if ($this->trail->getItem(1)->row) {
            $id = $this->trail->getItem(1)->row->id;
//          $condition = $condition . ($this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId') ? ' AND ' . $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id="' . $id . '"' : '');
            $condition[] = $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id="' . $id . '"';
        }
		if ($this->trail->getItem()->section->filter) {
			$condition[] = ' AND ' . $this->trail->getItem()->section->filter;
		}
		$steps = $this->params['steps'];
		if (!$steps) $steps = 1;
//		for ($i = 0; $i < $steps; $i++) {
			$this->row->move('up', implode(' AND ', $condition));
			$this->postMove();
//		}
        $this->_redirect(($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));
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
     * Provide delete action
     *
     */
    public function deleteAction()
    {
		$this->preDelete();
        $this->trail->getItem()->row->delete();
        if ($this->trail->getItem(1)->row) {
            $id = $this->trail->getItem(1)->row->id;
        }
		$this->postDelete();
        $this->_redirect(($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));
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
				case 'upload':
					break;
				case 'price':
					$value = $this->post[$field['alias']]['integer'] . '.' . $this->post[$field['alias']]['decimal'];
					break;
				case 'color':
					$value = preg_match('/^#[a-fA-F0-9]{6}$/', trim($value)) ? trim($value) : '#ffffff';
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
					$value = preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', trim($this->post[$field['alias']]['date'])) ? trim($this->post[$field['alias']]['date']) : '0000-00-00';
					unset($this->post[$field['alias']]['date']);
					foreach ($this->post[$field['alias']] as $p => $v) {
						$this->post[$field['alias']][$p] = preg_match('/^[0-9]{2}$/', trim($v)) ? trim($v) : '00';
					}
					$value .= ' ' . implode(':', array_values($this->post[$field['alias']]));
					break;
				case 'check':
				case 'multicheck':
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
//		d($data);
//		die();
		try {
            if ($this->identifier) {
                $this->trail->getItem()->model->update($data, '`id` = "' . $this->identifier . '"');
            } else {
                $this->identifier = $this->trail->getItem()->model->insert($data);
            }
            
            Indi_Image::deleteEntityImagesIfChecked();
            Indi_Image::uploadEntityImagesIfBrowsed();
            
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
            $this->_redirect(($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));
        }
	}
	
    /**
     * Provide default save action
     * Saving data is got from $this->post
     *
     */
    public function saveAction1($redirect = true, $incorrectMessage = '')
    {
		$this->preSave();
        if ($incorrectMessage) {
            $session = new Indi_Session_Namespace('incorrect');
            $exaction = $this->controller;
            $session->$exaction = new stdClass();
            $session->$exaction->message = $incorrectMessage;
            $session->$exaction->request = $this->post;
            $location = ($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . 
                        '/form/' . ($this->identifier ? 'id/' . $this->identifier . '/' : '');
            $this->_redirect($location);
            die();
        }
        if ($this->trail->getItem(1)->row) {
            // foreign key that aim to parent entity row 
            // is to be added to POST automativally while saving row of current entity
            // That feature is to prevent lost children
            $foreignKey = $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->foreignKeyName;
            if (!isset($this->post[$foreignKey])) {
                $this->post[$foreignKey] = $this->trail->getItem(1)->row->id;
            }
        }
		if ($this->admin['alternate']) foreach ($this->post as $key => $value) if ($key == $this->admin['alternate'] . 'Id') $this->post[$key] = $this->admin['id'];
//		d($this->post);
        // search for array-variables in POST
		$imploders = array('integer' => '.', 'hours' => ':', 'ids'=>',');
		foreach ($this->post  as $key => $value) {
            // make string from array variables by imploding
            // this affects only post variable-array, which keys are incremented from zero to array length-1
            // so it is not an assotiative array with sense key names
            // feature is nesessary for multiple checkboxes and multiple selects
			if (is_array($value) && count($value)) {
				if (implode('', array_values($value)) == '') {
					$this->post[$key] = '';
                } else if (implode('', array_keys($value)) == '-1') {
					echo 'asd';
					$this->post[$key] = '0';
				} else {
                    // unset empty values
                    for ($i = -1; $i < count ($value); $i++) if (!$value[$i]) unset($value[$i]);

					$imploderIndex = in_array(key($this->post[$key]), array_keys($imploders)) ? key($this->post[$key]) : 'ids';
					if (key($this->post[$key]) == 'date') {
						$this->post[$key] = $this->post[$key]['date'] . ' ' . $this->post[$key]['hours'] . ':' .$this->post[$key]['minutes'] . ':' .$this->post[$key]['seconds'];
					} else {
						$this->post[$key] = implode($imploders[$imploderIndex], $value);
					}
                }
            }
        }
		// prevent self parentness for row
		if ($treeColumn = $this->trail->getItem()->treeKeyName){
		  if ($this->post[$treeColumn] == $this->trail->getItem()->row->id) {
			  $this->post[$treeColumn] = $this->trail->getItem()->row->$treeColumn;
		  }
		}
//		d($this->post);
//		die();
		try {
            if ($this->identifier) {
                $this->trail->getItem()->model->update($this->post, '`id` = "' . $this->identifier . '"');                    
            } else {
                // auto completing `sorting` field when creating a row if table
                // structure have `sorting` field that mean that section have move up/move down actions
                if ($this->trail->getItem()->model->fieldExists('move')) {
                    $this->post['move'] = $this->trail->getItem()->model->getLastPosition();
                }
                $this->identifier = $this->trail->getItem()->model->insert($this->post);
            }
            
            // entity images actions
//            if ($this->files['image']['name'][0] !== '') {
//                Indi_Registry::set('post', Array('image' => 1));
//            }
            Indi_Image::deleteEntityImagesIfChecked();
            Indi_Image::uploadEntityImagesIfBrowsed();
            
        } catch (Exception $e) {
            d($e);
            die();
            throw new Exception('Cannot save into "' . $this->section->foreignRows->entityId->table . '" table');
        }
		$this->postSave();
//		die('ss');
        if ($redirect) {
            if ($this->trail->getItem(1)->row) {
                $id = $this->trail->getItem(1)->row->id;
            }
//            $this->_redirect('/' . $this->module . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : 'form/'));
            $this->_redirect(($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));
        }
    }
    
    /**
     * Provide default index action
     *
     */
    public function indexAction()
    {
		if ($this->params['json']) {
			$this->view->jsonData = $this->prepareJsonDataForIndexAction();
		} else {
//			session_destroy();
//			d($_SESSION);
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
								$this->trail->items[count($this->trail->items)-1]->dropdownWhere[$fields[$i]->alias] = 'CONCAT("\'",REPLACE(`' . ($fields[$j]->satellitealias ? $fields[$j]->satellitealias : $fields[$j]->alias) . '`,",","\',\'"),"\'") LIKE "%\'' . $this->post['satellite'] . '\'%"';
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
		} else {
			//d($this->row->toArray());
		}
    }
    
    public function viewAction()
    {
        
    }
	public function prepareJsonDataForIndexAction(){
		// set up raw grid data
		$data = $this->rowset->toArray();
		$this->doSomethingCustom();
		
		// set up indent in case of tree structure of entity
		for($i = 0; $i < count($data); $i++) {
			$data[$i]['title'] = $data[$i]['indent'] . $data[$i]['title'];
		}

		// get info about columns that wiil be presented in grid
		$gridFields = $this->trail->getItem()->gridFields->toArray();
		$gridFieldsAliases = array('id'); for ($i = 0; $i < count ($gridFields); $i++) $gridFieldsAliases[] = $gridFields[$i]['alias'];

		// get info about all columns that are exists at the present moment in $data
		$columns = count($data) ? array_keys($data[0]) : array();

		// unset columns in $data that will not be used in grid
		for ($i = 0; $i < count($data); $i++) {
			foreach ($columns as $column) {
				if (!in_array($column, $gridFieldsAliases)) {
					unset($data[$i][$column]); 
				}
			}
		}

		// get info about grid columns, that store relations
		for ($i = 0; $i < count ($gridFields); $i++) {
			if ($gridFields[$i]['relation']) $gridFieldsThatStoreRelation[$gridFields[$i]['alias']] = $gridFields[$i]['relation'];
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
						foreach ($foreignRowset as $foreignRow) $titles[$fieldAlias][$foreignRow->id] = $foreignRow->getTitle();
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

        // get custom titles for values in grid that are foreign keys, but each can relate to different entity
		// at first we should  find such a columns and their satellites
		$satellitedFields = $this->trail->getItem()->model->getSatellitedFields();
		foreach ($satellitedFields as $field) {
			$fieldAlias = $field->alias;
			$satelliteAlias = $field->getForeignRowByForeignKey('satellite')->alias;
			$i = 0;
			foreach ($this->rowset as $row) {
				if ($fr = $row->getForeignRowByForeignKey($fieldAlias))	$data[$i][$fieldAlias] = $fr->getTitle();
				$i++;
			}
		}

		// Set up grid fields that are not stored in db, such as fileupload field
		$nonDbGridFieldAliases = array();
		foreach ($gridFields as $gridField) {
			if ($gridField['columnTypeId'] == 0) {
				if ($gridField['elementId'] == 14) {
					$nonDbGridFieldAliases[] = $gridField['alias'];
				}
			}
		}

		$uploadPath = Indi_Image::getUploadPath();
		$entity = $this->trail->getItem()->model->info('name');
		for ($j = 0; $j < count($nonDbGridFieldAliases); $j++) {
			for($i = 0; $i < count($data); $i++) {
				$tileGridMode = true;
				$data[$i][$nonDbGridFieldAliases[$j]] = Indi_View_Helper_Admin_IndexFile::indexFile($nonDbGridFieldAliases[$j], null, true, $entity, $data[$i]['id']);
			}
		}
//		d($nonDbGridFieldAliases);

		$jsonData = '({"totalCount":"'.$this->rowset->foundRows.'","blocks":'.json_encode($data) . '})';
		return $jsonData;
	}

	function setGridTitlesByCustomLogic(&$data){}

	public function getOrderForJsonRowset($condition = null){
//		$this->post['sort'] = 'identifier';
//		$this->post['dir'] = 'ASC';
		// get info about columns that will be presented in grid
		$gridFields = $this->trail->getItem()->gridFields->toArray();
/*					$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/www' . "/tmp.txt","w");
					ob_start();
					d($order);
					d($this->post);
					$out = ob_get_clean();
					fwrite($fp, $out);
					fclose($fp);*/
		
		// check if field (that is to be sorted by) store relation
		$entityId = false;		
		for ($i = 0; $i < count($gridFields); $i++) {
			if($gridFields[$i]['alias'] == $this->post['sort'] && ($gridFields[$i]['relation'] || $gridFields[$i]['satellite'])) {
				$entityId = $gridFields[$i]['relation'];
				$fieldId  = $gridFields[$i]['id'];
                $satellite = $gridFields[$i]['satellite'];
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

				$query = 'CREATE TEMPORARY TABLE `' . $tmpTable . '` (`id` VARCHAR(255) NOT NULL, `title` VARCHAR(255) NOT NULL);';
				$this->db->query($query);

				$query = ' INSERT INTO `' . $tmpTable . '` ';
				if ($entityId == 6) {
					$condition  = '`alias` IN ("' . implode('","', $ids) . '")';
					$condition .= ' AND `fieldId` = "' . $fieldId . '"';
					$query .= 'VALUES ';
					$foreignRowset = Entity::getInstance()->getModelById($entityId)->fetchAll($condition);
					foreach ($foreignRowset as $foreignRow) {
						$values[] = '("' . $foreignRow->alias . '","' . $foreignRow->getTitle() . '")';
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
					$values[] = '(' . $tmp[$i]['id'] . ', "' . $tmp[$i]['title'] . '")';
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
		} else {
			$order = $this->post['sort'] . ' ' . $this->post['dir'];
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
        $this->_redirect(($GLOBALS['cmsOnlyMode'] ? '' : '/' . $this->module) . '/' . $this->section->alias . '/' . ($id ? 'index/id/' . $id . '/' : ''));
	}
	public function postDispatch(){
        if ($this->action == 'form') {
            $session = Indi_Session::namespaceGet('incorrect', $this->controller);
            Indi_Session::namespaceUnset('incorrect', $this->controller);
            $this->view->incorrectMessage = $session->message;
            if ($this->row && is_array($session->request)) {
                foreach ($session->request as $key => $value) $this->row->$key = $value;
            }            
        }
        // assign general template data
        $this->assign();
        $out = $this->view->render('index.php');           
		if ($GLOBALS['cmsOnlyMode']) {
			$out = preg_replace('/("|\')\/admin/', '$1', $out);
		};
		
		// perform hrefs adjustments in case if system used only as admin area
		$config = Indi_Registry::get('config');
		if($config['general']->standalone == 'true') {
			$out = preg_replace('/(src|href|background)=("|\')/', '$1=$2/admin', $out);
			$out = preg_replace('/\/admin\/admin\//', '/admin/', $out);
			$out = preg_replace('/\/adminjavascript/', 'javascript', $out);
		}
        die($out);
	}
    /**
	  * Assigns admin name, date, menu, trail and all
	  * that used in admin area in general
	  *
	  */
    public function assign()
    {
        $section = new Section();
        $this->view->assign('admin', $this->admin['title']);
        $this->view->assign('date', date('<b>l</b>, d.m.Y [H:i]'));
        $this->view->assign('menu', Indi_Auth::getInstance()->getMenu());
        $title = $this->config->project;
        $this->view->assign('config', $this->config);
		
        $section = new Section();
        $this->view->assign('trail', $this->trail);
        if ($windowTitle = $this->view->trail(true)) {
            $title .= ' &raquo; ' . strip_tags(implode(' &raquo; ', $windowTitle));
        }
        $this->view->assign('titleAdmin', $title);
        $this->view->assign('module', $this->module);
        $this->view->assign('section', $this->section);
        $this->view->assign('action', $this->action);
        $this->view->assign('entity', $this->section->foreignRows->entityId);
        $title = $this->config->project;
        if ($windowTitle = $this->trail->getWindowTitleSite()) {
            $title .= ' &raquo; ' . $windowTitle;
        }
        $this->view->assign('title', $title);
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
                if (!is_array($this->row->$field)) {
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
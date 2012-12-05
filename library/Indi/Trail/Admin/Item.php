<?php
class Indi_Trail_Admin_Item extends Indi_Trail_Item
{
	/**
	 * Store number of fields that associated with a ExtJs grid, in case if
	 * there is an entity attached to section, and the current action is 'index'
	 *
	 * @var Indi_Db_Table_Rowset
	 */
	public $gridFields = null;

	/**
	 * Store number of fields that are unaccessible in grid and form
	 *
	 * @var array
	 */
	public $disabledFields = array();

    /**
     * Set up all internal variables
     *
     * @param int $sectionId
     * @param int $rowIdentifier
     * @param string $actionAlias
     */
    public function __construct($sectionId, $rowIdentifier, $actionAlias, &$trail, $_sectionId)
    {
        $session = $_SESSION;
        
        // set up section row
        $section = new Section();
        $this->section = $section->fetchRow('`id` = "' . $sectionId . '"');
        
        if ($this->section) {
            // set up actions of section
            $this->actions = $trail->authComponent->getActions($sectionId, $session['admin']['profileId']);

            // set up subsections of section
            $this->sections = $trail->authComponent->getSections($sectionId, $session['admin']['profileId']);
        }
        
        if ($this->section->sectionId) {
            // set up action row
            $action = new Action();
            $this->action = $action->fetchRow('`alias` = "' . $actionAlias . '"');
            
            // set up row
            $entityTitle = $this->section->getForeignRowByForeignKey('entityId')->table;
            if ($entityTitle) {

				// set up model
                $className = ucfirst($entityTitle);

				// if file in which model is declared doesn't exists, so there will be performed emulation of it initialization
				$systemModelsDir = $_SERVER['DOCUMENT_ROOT'] . '/www' . '/application/models/';
				$modelClassName = $className;
				$modelFileName = $className . '.php';
				$modelFilePath = $systemModelsDir . $modelFileName;
				
				if (!in_array($modelClassName, get_declared_classes())){
					if (file_exists($modelFilePath)) {
						if (!class_exists($modelClassName))
							require($modelFilePath);  
					} else {
						$entityRow = Entity::getInstance()->fetchRow('`table` = "' . strtolower($modelClassName) . '"');
						if ($entityRow) {
							$extends = $entityRow->extends ? $entityRow->extends : 'Indi_Db_Table';
							eval('class ' . $modelClassName . ' extends ' . $extends . '{}');
						} else {
							throw new Exception('Model is not in entities table');
						}
					}
				}
				
				$this->model = new $className();
                
                // set up row
                if ($rowIdentifier) {
					$where = array('`id` = "' . $rowIdentifier . '"');
					if ($this->section->filter) $where[] = $this->section->filter;
                    $this->row = $this->model->fetchRow($where);
					if (!$this->row) die('Нет доступа к этой записи');
					if ($_sectionId) $this->row->_sectionId = $_sectionId;
                } else if ($this->action->alias == 'form') {
                    // set up empty row if no row identifier
                    $this->row = $this->model->createRow();
                    
                    // if current section have parent section 
                    $parentSection = $this->section->getForeignRowByForeignKey('sectionId');
					do{
                        // and parent section is not a group
                        if ($parentSection->sectionId != '0') {
                            // determining parent key name
                            $parentSectionId = $parentSection->id;
                            $parentEntity = $parentSection->getForeignRowByForeignKey('entityId');
                            $parentEntityForeignKeyName = $parentEntity->table . 'Id';
                            
                            // determining parent key value
                            $session = Indi_Session::namespaceGet('trail');

							$info = $session['parentId'];
                            $parentId = $info->$parentSectionId;
							
							if (!$parentId) {
								$info = (array) $session['parentId'];
								$parentId = $info[$parentSectionId];
							}
                            // set up key name with value
                            $this->row->$parentEntityForeignKeyName = $parentId;
                        }
                    } while($parentSection = $parentSection->getForeignRowByForeignKey('sectionId'));

                }
				// set up row fields
				$field = new Field();
				$this->fields = $field->getFieldsByEntityId($this->section->entityId);

				// set up ExtJs grid fields definitions in case if current action is 'index'
				if ($actionAlias == 'index') $this->gridFields = $field->getGridFieldsBySectionId($sectionId);

				// set up disabled (unreachable for view, edit and save) fields
				$this->disabledFields = $field->getDisabledFieldsBySectionId($sectionId);
            }
        }
    }
    /**
     * Get array version of internal variables
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        if ($this->section) $array['section'] = $this->section->toArray();
        if ($this->action) $array['action'] = $this->action->toArray();
        if ($this->actions) $array['actions'] = $this->actions->toArray();
        if ($this->row) $array['row'] = $this->row->toArray();
        if ($this->model) $array['model'] = $this->model->toArray();
        if ($this->fields) $array['fields'] = $this->fields->toArray();
        if ($this->gridFields) $array['gridFields'] = $this->gridFields->toArray();
        if ($this->dropdownWhere) $array['dropdownWhere'] = $this->dropdownWhere;
        return $array;
    }
}
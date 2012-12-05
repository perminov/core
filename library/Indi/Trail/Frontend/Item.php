<?php
class Indi_Trail_Frontend_Item extends Indi_Trail_Item
{
    /**
     * Set up all internal variables
     *
     * @param int $sectionId
     * @param int $rowIdentifier
     * @param string $actionAlias
     */
    public function __construct($sectionId, $rowIdentifier, $actionAlias, &$trail, $_sectionId)
    {
        $session = Indi_Session::namespaceGet('admin');
        
        // set up section row
        $section = new Fsection();
        $this->section = $section->fetchRow('`id` = "' . $sectionId . '"');
        
        if ($this->section) {
            // set up actions of section
//			$ids = Misc::loadModel('Fsection2faction')->getImplodedIds('`fsectionId` = "' . $sectionId . '"');
			$fsection2factions = Misc::loadModel('Fsection2faction')->fetchAll('`fsectionId` = "' . $sectionId . '"');
			foreach ($fsection2factions as $fsection2faction) $ids[] = $fsection2faction->factionId; $ids = implode('","', $ids);
            $this->actions = Misc::loadModel('Faction')->fetchAll('`id` IN ("' . $ids . '")');
			
            // set up subsections of section
            $this->sections = Misc::loadModel('Fsection')->fetchAll('`fsectionId` = "' . $sectionId . '"');
        }
        if ($this->section) {
            // set up action row
            $action = new Faction();
            $this->action = $action->fetchRow('`alias` = "' . $actionAlias . '"');

            // set up row
            $entityTitle = $this->section->getForeignRowByForeignKey('entityId')->table;
            if ($entityTitle) {

				// set up model
                $className = ucfirst($entityTitle);

				$this->model = Misc::loadModel($className);
                
                // set up row
                if ($rowIdentifier) {
                    $this->row = $this->model->fetchRow('`id` = "' . $rowIdentifier . '"');
					if ($_sectionId) $this->row->_sectionId = $_sectionId;
                } else if ($this->action->alias == 'form') {
                    // set up empty row if no row identifier
                    $this->row = $this->model->createRow();

                    $parentSection = $this->section->getForeignRowByForeignKey('fsectionId');
					do{
						// determining parent key name
						$parentSectionId = $parentSection->id;
						$parentEntity = $parentSection->getForeignRowByForeignKey('entityId');
						$parentEntityForeignKeyName = $parentEntity->table . 'Id';
						
						// determining parent key value
						$session = Indi_Session::namespaceGet('trail');
						$info = (array) $session['parentId'];
						$parentId = $info[$parentSectionId];
						// set up key name with value
						$this->row->$parentEntityForeignKeyName = $parentId;
                    } while($parentSection = $parentSection->getForeignRowByForeignKey('fsectionId'));

                }
				// set up row fields
				$field = new Field();
				$this->fields = $field->getFieldsByEntityId($this->section->entityId);
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
        if ($this->dropdownWhere) $array['dropdownWhere'] = $this->dropdownWhere;
        return $array;
    }
}
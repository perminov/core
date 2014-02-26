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
    public function __construct($sectionId, $rowIdentifier, $actionAlias, &$trail)
    {
        $session = Indi_Session::namespaceGet('admin');
        
        // set up section row
        $section = new Fsection();
        $this->section = $section->fetchRow('`id` = "' . $sectionId . '"');
        
        if ($this->section) {
            // set up actions of section
			$fsection2factions = Indi::model('Fsection2faction')->fetchAll('`fsectionId` = "' . $sectionId . '"');
			foreach ($fsection2factions as $fsection2faction) $ids[] = $fsection2faction->factionId; $ids = implode('","', $ids);
            $this->actions = Indi::model('Faction')->fetchAll('`id` IN ("' . $ids . '")');
			
            // set up subsections of section
            $this->sections = Indi::model('Fsection')->fetchAll('`fsectionId` = "' . $sectionId . '"');
        }
        if ($this->section) {
            // set up action row
            $action = new Faction();
            $this->action = $action->fetchRow('`alias` = "' . $actionAlias . '"');

            // set up row
            $entityTitle = $this->section->foreign('entityId')->table;
            if ($entityTitle) {

				// set up model
                $className = ucfirst($entityTitle);

				$this->model = Indi::model($className);

                // set up row
                if ($rowIdentifier) {
                    $this->row = $this->model->fetchRow('`id` = "' . $rowIdentifier . '"');
                    if ($this->row && $this->section->fsectionId) {
                        $parentSection = $this->section->foreign('fsectionId');
                        $parentEntity = $parentSection->foreign('entityId');
                        $parentEntityForeignKeyName = $parentEntity->table . 'Id';
                        if (!in_array($parentEntityForeignKeyName, array_keys($this->row->toArray()))) {
                            $session = Indi_Session::namespaceGet('trailfront');
                            $info = $session['parentId'];
                            $parentId = $info->{$parentSection->id};
                            if (!$parentId) {
                                $info = (array) $session['parentId'];
                                $parentId = $info[$parentSection->id];
                            }
                            $this->row->$parentEntityForeignKeyName = $parentId;
                        }
                    }

                } else if ($this->action->alias == 'form') {
                    // set up empty row if no row identifier
                    $this->row = $this->model->createRow();

                    if ($parentSection = $this->section->foreign('fsectionId'))
					do{
						// determining parent key name
						$parentSectionId = $parentSection->id;
						$parentEntity = $parentSection->foreign('entityId');
						$parentEntityForeignKeyName = $parentEntity->table . 'Id';
						
						// determining parent key value
						$session = Indi_Session::namespaceGet('trail');
						$info = (array) $session['parentId'];
						$parentId = $info[$parentSectionId];
						// set up key name with value
						$this->row->$parentEntityForeignKeyName = $parentId;
                    } while($parentSection = $parentSection->foreign('fsectionId'));

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
<?php
class Indi_Trail_Frontend extends Indi_Trail
{
    /**
     * Instantiate a new Trail object and set up
     * call path (trail) in $this->items variable as array of
     * Indi_Trail_Item objects
     *
     * @param string $sectionAlias
     * @param int $rowIdentifier
     * @param int $actionAlias
     * @uses Indi_Session_Namespace, self::addItem(), Indi_Trail_Item::setTrail()
     */
    public function __construct($sectionAlias, $rowIdentifier = null, $actionAlias = 'index', $specialSectionCondition = '', $requestParams = array())
    {
		$initialId = $rowIdentifier;
        // Get session
        $session = new Indi_Session_Namespace('trailfront');

        // Set up self::items array
        do {
            if ($lastItem) {
                $sectionId = $lastItem->section->fsectionId;
                $parentSection = $lastItem->section->getForeignRowByForeignKey('fsectionId');
				if ($parentSection->type == 'r') {
					$actionAlias = 'index';
				} else {
					$actionAlias = $parentSection->index;
				}
				if (!$initialId) $info = (array) $session->parentId;
                if ($info[$sectionId] || $parentRowId) {
                    $rowId = $parentRowId ? $parentRowId : $info[$sectionId];
                    $parentRowId = null;
                } else {
                    $parentSection = $lastItem->section->getForeignRowByForeignKey('fsectionId');
                    $parentEntityForeignKeyName = $parentSection->getForeignRowByForeignKey('entityId')->table;
					if ($parentSection->type == 'r') {
						$rowId = $lastItem->row->{$parentEntityForeignKeyName . 'Id'};
					} else {
						eval('$rowId = ' . $parentSection->where . ';');
					}
                }
				unset($_sectionId);
            } else {
                $section = new Fsection();
                // section id
                $sectionRow = $section->fetchRow('`alias` = "' . $sectionAlias . '"' . $specialSectionCondition);
                $sectionId = $sectionRow->id;
                if ($actionAlias != 'index') {
                    $rowId = $rowIdentifier;
					$_sectionId = $sectionId;
                } else {
                    if (!$session->parentId) $session->parentId = new stdClass();
                    if ($key = $sectionRow->fsectionId) {
                        $session->parentId->$key = $rowIdentifier;
                    }
                    $parentRowId = $rowIdentifier;
                }
            }
			$lastItem = $this->addItem($sectionId, $rowId, $actionAlias, $this, $_sectionId);
        } while ($lastItem->section->fsectionId);
        
        // Reverse array to work with it from the start by the end, not from the end to the start.
        $this->items = $this->items[0]->section ? array_reverse($this->items) : array();
        end($this->items);

		// set up request params
		$this->requestParams = $requestParams;

		// set up tree key name for the last item
        $lastItemIndex = count($this->items) - 1;
		if ($model = $this->items[$lastItemIndex]->model) {
            if ($treeColumnName = $model->getTreeColumnName()) {
                $this->items[$lastItemIndex]->treeKeyName = $treeColumnName;
            }
        }

        // set up dropdownWhere for the last item
		if ($model = $this->items[$lastItemIndex]->model) {
            $parentItem = $this->items[$lastItemIndex - 1];
            if ($parentItem->row) {
                $parentColumn = $parentItem->model->info('name') . 'Id';
                if ($treeColumnName = $this->items[$lastItemIndex]->treeKeyName) {
                    $this->items[$lastItemIndex]->dropdownWhere[$treeColumnName] = '`' . $parentColumn  . '` = "' . $parentItem->row->id . '"';
                }
            }
        }

        // set up dropdownWhere for the previous of last item
		if ($model = $this->items[$lastItemIndex - 1]->model) {
            $levelUpParentItem = $this->items[$lastItemIndex - 2];
            if ($levelUpParentItem->row) {
                $levelUpParentColumn = $levelUpParentItem->model->info('name') . 'Id';
                $this->items[$lastItemIndex]->dropdownWhere[$parentColumn] = '`' . $levelUpParentColumn  . '` = "' . $levelUpParentItem->row->id . '"';
            }
        }
    }
    
    /**
     * Push a new Indi_Trail_Item object into a $this->items
     * array of these objects and return last pushed item
     *
     * @param int $sectionId
     * @param int $rowIdentifier = null
     * @param string $actionAlias = 'index'
     * @return Indi_Trail_Item object
     */
    public function addItem($sectionId, $rowIdentifier = null, $actionAlias = null, $trail = null, $_sectionId = null)
    {
        $this->items[] = new Indi_Trail_Frontend_Item($sectionId, $rowIdentifier, $actionAlias, $trail, $_sectionId);
        return end($this->items);
    }
}
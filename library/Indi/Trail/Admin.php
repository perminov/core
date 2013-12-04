<?php
class Indi_Trail_Admin extends Indi_Trail
{
	/**
	 * Store Indi_Auth instance
	 *
	 * @var object
	 */
	public $authComponent;

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
    public function __construct($sectionAlias, $rowIdentifier = null, $actionAlias = 'index', $specialSectionCondition = '', $requestParams = array(), $authComponent)
    {
        // Get session
        $session = new Indi_Session_Namespace('trail');
        // Set up auth component
        $this->authComponent = $authComponent;

        // Set up self::items array
        do {
            if ($lastItem) {
                $sectionId = $lastItem->section->sectionId;
                $actionAlias = 'index';
				$info = (array) $session->parentId;
				foreach ($info as $sId => $rId) if ($sId == $sectionId) $info[$sId] = $rId;
                if ($info[$sectionId] || $parentRowId) {
                    $rowId = $parentRowId ? $parentRowId : $info[$sectionId];
                    $parentRowId = null;
                } else {
                    $parentSection = $lastItem->section->getForeignRowByForeignKey('sectionId');
                    $parentEntityForeignKeyName = $parentSection->getForeignRowByForeignKey('entityId')->table;
                    $rowId = $lastItem->row->{$parentEntityForeignKeyName . 'Id'};
                }
            } else {
                $section = new Section();
                // section id
                $sectionRow = $section->fetchRow('`alias` = "' . $sectionAlias . '"' . $specialSectionCondition);
                $sectionId = $sectionRow->id;
                if ($actionAlias != 'index') {
                    $rowId = $rowIdentifier;
                } else {
                    if (!$session->parentId) $session->parentId = new stdClass();
                    if ($key = $sectionRow->sectionId) {
                        $session->parentId->$key = $rowIdentifier;
                    }
                    $parentRowId = $rowIdentifier;
                }
            }
			$lastItem = $this->addItem($sectionId, $rowId, $actionAlias, $this);
        } while ($lastItem->section->sectionId);
        
        // Reverse array to work with it from the start by the end, not from the end to the start.
        $this->items = $this->items[0]->section ? array_reverse($this->items) : array();
        end($this->items);

		// set up request params
		$this->requestParams = $requestParams;
//		d($this->getItem(1)->section);

	 // set up tree key name for the last item
        $lastItemIndex = count($this->items) - 1;
	 if ($model = $this->items[$lastItemIndex]->model) {
            if ($treeColumnName = $model->treeColumn) {
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
    public function addItem($sectionId, $rowIdentifier = null, $actionAlias = null, $trail = null)
    {

        $this->items[] = new Indi_Trail_Admin_Item($sectionId, $rowIdentifier, $actionAlias, $trail);
        return end($this->items);
    }
}
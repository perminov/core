<?php
class Indi_Trail_Frontend
{
    /**
     * Store array of Indi_Trail_Item objects
     *
     * @var array
     */
    public $items = array();

    /**
     * Store request params
     *
     * @var array
     */
    public $request = array();

    /**
     * Get item from $this->items array, that have index
     * calculated as maximum index decremented by $stepsUp argument
     *
     * @return Indi_Trail_Item object
     */
    public function getItem($stepsUp = null)
    {

        $index = $this->count() - 1 - ($stepsUp ? (int) $stepsUp : 0);
        if ($index >= 0 && $index < $this->count()) {
            return $this->items[$index];
        } else {
            return null;
        }
    }

    /**
     * Return count of elements in $this->items
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Get an array version of trail
     *
     * @uses Indi_Trail_Item::toArray()
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach($this->items as $item) {
            $array[] = $item->toArray();
        }
        end($this->items);
        return $array;
    }

    /**
     * Instantiate a new Trail object and set up
     * call path (trail) in $this->items variable as array of
     * Indi_Trail_Item objects
     *
     * @param string $sectionAlias
     * @param int $rowIdentifier
     * @param int $actionAlias
     */
    public function __construct($sectionAlias, $rowIdentifier = null, $actionAlias = 'index', $specialSectionCondition = '', $requestParams = array())
    {
		$initialId = $rowIdentifier;
        // Set up self::items array
        do {
            if ($lastItem) {
                $sectionId = $lastItem->section->fsectionId;
                $parentSection = $lastItem->section->foreign('fsectionId');
				if ($parentSection->type == 'r') {
					$actionAlias = 'index';
				} else {
					$actionAlias = $parentSection->index;
				}
				if (!$initialId) $info = $_SESSION['indi']['front']['trail']['parentId'];
                if ($info[$sectionId] || $parentRowId) {
                    $rowId = $parentRowId ? $parentRowId : $info[$sectionId];
                    $parentRowId = null;
                } else {
                    $parentSection = $lastItem->section->foreign('fsectionId');
                    $parentEntityForeignKeyName = $parentSection->foreign('entityId')->table;
					if ($parentSection->type == 'r') {
						$rowId = $lastItem->row->{$parentEntityForeignKeyName . 'Id'};
					} else {
						eval('$rowId = ' . $parentSection->where . ';');
					}
                }
            } else {
                $section = new Fsection();
                // section id
                $sectionRow = $section->fetchRow('`alias` = "' . $sectionAlias . '"' . $specialSectionCondition);
                $sectionId = $sectionRow->id;
                if ($actionAlias != 'index') {
                    $rowId = $rowIdentifier;
                } else {
                    if (!is_array($_SESSION['indi']['front']['trail']['parentId']))
                        $_SESSION['indi']['front']['trail']['parentId'] = array();
                    if ($key = $sectionRow->fsectionId) {
                        $_SESSION['indi']['front']['trail']['parentId'][$key] = $rowIdentifier;
                    }
                    $parentRowId = $rowIdentifier;
                }
            }
			$lastItem = $this->addItem($sectionId, $rowId, $actionAlias, $this);
        } while ($lastItem->section->fsectionId);
        
        // Reverse array to work with it from the start by the end, not from the end to the start.
        $this->items = $this->items[0]->section ? array_reverse($this->items) : array();
        end($this->items);

		// set up request params
		$this->requestParams = $requestParams;

		// set up tree key name for the last item
        $lastItemIndex = count($this->items) - 1;
		if ($model = $this->items[$lastItemIndex]->model) {
            if ($treeColumnName = $model->treeColumn()) {
                $this->items[$lastItemIndex]->treeKeyName = $treeColumnName;
            }
        }

        // set up dropdownWhere for the last item
		if ($model = $this->items[$lastItemIndex]->model) {
            $parentItem = $this->items[$lastItemIndex - 1];
            if ($parentItem->row) {
                $parentColumn = $parentItem->model->name() . 'Id';
                if ($treeColumnName = $this->items[$lastItemIndex]->treeKeyName) {
                    $this->items[$lastItemIndex]->dropdownWhere[$treeColumnName] = '`' . $parentColumn  . '` = "' . $parentItem->row->id . '"';
                }
            }
        }

        // set up dropdownWhere for the previous of last item
		if ($model = $this->items[$lastItemIndex - 1]->model) {
            $levelUpParentItem = $this->items[$lastItemIndex - 2];
            if ($levelUpParentItem->row) {
                $levelUpParentColumn = $levelUpParentItem->model->name() . 'Id';
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
        $this->items[] = new Indi_Trail_Frontend_Item($sectionId, $rowIdentifier, $actionAlias, $trail);
        return end($this->items);
    }
}
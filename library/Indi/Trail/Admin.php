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
                    $parentSection = $lastItem->section->foreign('sectionId');
                    $parentEntityForeignKeyName = $parentSection->foreign('entityId')->table;
                    $rowId = $lastItem->row->{$parentEntityForeignKeyName . 'Id'};
                }
                if (count($this->items) == 1) {
                    $primaryHash = $requestParams['ph'];
                } else {
                    $primaryHash = '';
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
			$lastItem = $this->addItem($sectionId, $rowId, $actionAlias, $this, $primaryHash);
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
    public function addItem($sectionId, $rowIdentifier = null, $actionAlias = null, $trail = null, $primaryHash = null)
    {

        $this->items[] = new Indi_Trail_Admin_Item($sectionId, $rowIdentifier, $actionAlias, $trail, $primaryHash);
        return end($this->items);
    }

    public function setItemScopeHashes($hash, $aix, $index) {
        $i = -1 + ($index ? 1 : 0);
        do {
            $i++;
            $this->items[count($this->items) - 1 - $i]->section->primaryHash = $hash;
            $this->items[count($this->items) - 1 - $i]->section->rowIndex = $aix;
        } while (($hash = $_SESSION
            ['indi']
            ['admin']
            [$this->items[count($this->items) - 1 - $i]->section->alias]
            [$this->items[count($this->items) - 1 - $i]->section->primaryHash]
            ['upperHash'])
            &&
            (($aix = $_SESSION
            ['indi']
            ['admin']
            [$this->items[count($this->items) - 1 - $i]->section->alias]
            [$this->items[count($this->items) - 1 - $i]->section->primaryHash]
            ['upperAix']) || true)
        );
    }

    public function toString($imploded = true) {

         // Declare crumbs array and push the first item - section group
        $crumbA = array($this->items[0]->section->title);

        // For each remaining trail items
        for ($i = 1; $i < count($this->items); $i++) {

            // Define a shortcut for current trail item
            $item = $this->items[$i];

            // Append a current item section title
            $crumbA[] = $item->section->title;

            // If current trail item has a row
            if ($item->row) {

                // If that row has an id
                if ($item->row->id) {

                    // At first, we strip newline characters, html '<br>' tags
                    $title = preg_replace('<br(|\/)>', '', preg_replace('/[\n\r]/' , '', $item->row->title));

                    // Detect color
                    preg_match('/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i', $title, $color);

                    // Strip the html tags from title, and extract first 50 characters
                    $title = mb_substr(strip_tags($title), 0, 50, 'utf-8');

                    // Append current trail item row title, with color definition
                    $crumbA[] = '<i' . ($color ? ' style="color: ' . $color[1] . ';"' : '') . '>' . $title . '</i>';

                    // If current trail item is a last item, append current trail item action title
                    if ($i == count($this->items) - 1) $crumbA[] = $item->action->title;

                // Else if current trail item row does not have and id, and current action alias is 'form'
                } else if ($item->action->alias == 'form') {

                    // We append 'form' action title, but it' version for case then new row is going to be
                    // created, hovewer, got from localization object, instead of actual action title
                    $crumbA[] = ACTION_CREATE;
                }
            }
        }

        return $imploded ? implode(' » ', $crumbA) : $crumbA;
    }
}
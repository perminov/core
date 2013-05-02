<?php
class Field extends Indi_Db_Table{
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Field_Row'; 

	public function getFieldsByEntityId($entityId){
		return $this->fetchAll('`entityId` = "' . $entityId . '"', 'move');
	}

	public function getGridFieldsBySectionId($sectionId){

		$grid = new Grid();
	    $gridArray = $grid->fetchAll('`sectionId` = "' . $sectionId . '"', 'move')->toArray();

		$fieldIds = array();
		for($i = 0; $i < count($gridArray); $i++) $fieldIds[] = $gridArray[$i]['fieldId'];
		$where = count($fieldIds) ? '`id` IN (' . implode(',', $fieldIds) . ')' : '`id` IN ("")';
		$order = count($fieldIds) ? 'POSITION(CONCAT("\'", `id`, "\'") IN "\'' . implode("','", $fieldIds) . '\'")' : null;

	    return $this->fetchAll($where, $order);
	}

	public function getFiltersCountBySectionId($sectionId) {
        return $this->_db->query('SELECT COUNT(*) FROM `search` WHERE `sectionId` = "' . $sectionId . '"')->fetchColumn();
	}

	public function getFiltersBySectionId($sectionId){
        return  Misc::loadModel('Search')->fetchAll('`sectionId` = "' . $sectionId . '" AND `toggle`="y"', '`move`');
	}

	public function getDisabledFieldsBySectionId($sectionId){

		$disabled = Misc::loadModel('DisabledField');
	    $disabledArray = $disabled->fetchAll('`sectionId` = "' . $sectionId . '"')->toArray();

		$fieldIds = array();
		for($i = 0; $i < count($disabledArray); $i++) $fieldIds[] = $disabledArray[$i]['fieldId'];
		$where = count($fieldIds) ? '`id` IN (' . implode(',', $fieldIds) . ')' : '`id` IN ("")';
		
		$aliases = array();
		
		$rs = $this->fetchAll($where)->toArray();
		foreach ($rs as $r) $aliases[] = $r['alias'];
		
	    return $aliases;
	}
}
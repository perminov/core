<?php
class Fsection_Row extends Indi_Db_Table_Row{
	public function getFilter($alias){
		return Misc::loadModel('Filter')->fetchRow('`fsectionId` = "' . $this->id . '" AND `alias` = "' . $alias . '"');
	}
	public function getFilters(){
		return Misc::loadModel('Filter')->fetchAll('`fsectionId` = "' . $this->id . '"');
	}
	public function getOrder(){
		$orderByRs = Misc::loadModel('OrderBy')->fetchAll('`fsectionId` = "' . $this->id . '"', 'move');
		$options = array();
		foreach($orderByRs as $orderByRow) $options[$orderByRow->id] = $orderByRow->title;
		return $options;
	}
}
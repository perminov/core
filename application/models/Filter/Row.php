<?php
class Filter_Row extends Indi_Db_Table_Row{
	public function getOptions(){
		$field = Misc::loadModel('Field')->fetchRow('`id` = "' . $this->fieldId . '"');
		$columnType = $field->getForeignRowByForeignKey('columnTypeId')->type;
		$entityId = $this->getForeignRowByForeignKey('fsectionId')->entityId;
		$table = Entity::getInstance()->getModelById($entityId)->info('name');
		if (strpos($columnType, 'ENUM') !== false || strpos($columnType, 'SET') !== false) {
			$options = Misc::loadModel('Enumset')->getOptions($table, $this->fieldId, $this->displayOptions == 'u' ? true : false);
		}
		return $options;
	}	
}
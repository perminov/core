<?php
class Grid_Row extends Indi_Db_Table_Row{
	public function getTitle(){
		if ($fieldRow = $this->foreign('fieldId')) {
			return $fieldRow->getTitle();
		} else {
			return parent::getTitle();
		}
	}
}
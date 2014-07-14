<?php
class Staticblock_Row extends Indi_Db_Table_Row {
	/**
	  * Get the appropriate value, depending on block type
	  */
	public function value() {
		$valueField = 'details' . ucfirst($this->type);
		$value = $this->$valueField;
		if ($this->type == 'textarea') $value = nl2br($value);
		return $value;
	}
}
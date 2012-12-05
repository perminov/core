<?php
class Indi_Trail_Item
{
    /**
     * Store item section row object
     *
     * @var Section_Row object
     */
    public $section;
    
    /**
     * Store actions assotiated to section
     *
     * @var Indi_Db_Table_Rowset
     */
    public $actions;
    
    /**
     * Store sections that are subsections to self::$section
     *
     * @var Indi_Db_Table_Rowset object
     */
    public $sections;
    
    /**
     * Store trail action row
     *
     * @var Indi_Db_Table_Row object
     */
    public $action;
    
    /**
     * Store table class
     *
     * @var Indi_Db_Table object
     */
    public $model;
    
    /**
     * Store trail item row
     *
     * @var Indi_Db_Table_Row object
     */
    public $row;
    
	/**
	 * Store number of fields associated with a row, in case if
	 * there is an entity attached to section
	 *
	 * @var Indi_Db_Table_Rowset
	 */
	public $fields;

    /**
     * Store dropdown conditions for different fields
     * 
     * @var array
     */
    public $dropdownWhere = array();


	public function getFieldByAlias($alias){
		foreach ($this->fields as $field) {
			if($field->alias == $alias) return $field;
		}
	}
}
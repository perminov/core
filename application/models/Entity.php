<?php
class Entity extends Indi_Db_Table
{
    /**
     * Store singleton instance
     *
     * @var Entity object
     */
    protected static $_instance;
    
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Entity_Row'; 
	
	/**
     * Set up singleton instance
     * 
     * @return Entity object
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Get model object by table name
     *
     * @param string $table
     * @return object
     * 
     */
    public function getModelByTable($table)
    {
        try {
            $row = Entity::getInstance()->fetchRow('`table` = "' . $table . '"');
			if ($row) {
	            $className = ucfirst($row->table);
    	        return Misc::loadModel($className);
			}
        } catch (Exception $e) {
            throw new Exception($e->__toString());
        }
    }

    /**
     * Get model object by entityId
     *
     * @param int $id
     * @return object
     * 
     */
    public function getModelById($id)
    {
        try {
            $row = Entity::getInstance()->fetchRow('`id` = "' . $id . '"');
			if ($row) {
	            $className = ucfirst($row->table);
				return Misc::loadModel($className);
			}
        } catch (Exception $e) {
            throw new Exception($e->__toString());
        }
    }
}
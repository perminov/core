<?php
class Config extends Indi_Db_Table
{
    /**
     * Store singleton instance
     *
     * @var Config
     */
    protected static $_instance = null;

    /**
     * Singleton instance
     * 
     * @return Config object
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    /**
     * Returns object of stdClass with variables assigned as keys as aliases
     * from 'config' table and values as corresponding values from config table
     *
     * @return stdClass object
     */
    public function asObject()
    {
        $config = $this->fetchAll();
        $object = new stdClass();
        foreach ($config as $row) {
            $alias = $row->alias;
            $object->$alias = $row->value;
        }
        return $object;
    }
}
<?php
class Indi_Db_Table_Row_Noeval extends Indi_Db_Table_Row {

    /**
     * Constructor. Is redeclared for system use, at model fields initialization stage.
     *
     * @param array $config
     */
    public function __construct(array $config = array()) {

        // Setup initial properties
        $this->_table = $config['table'];
        $this->_original = $config['original'];
        $this->_modified = is_array($config['modified']) ? $config['modified'] : array();
        $this->_system = is_array($config['system']) ? $config['system'] : array();
        $this->_temporary = is_array($config['temporary']) ? $config['temporary'] : array();
        $this->_foreign = is_array($config['foreign']) ? $config['foreign'] : array();
        $this->_nested = is_array($config['nested']) ? $config['nested'] : array();
    }
}
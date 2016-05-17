<?php
class Indi_Db_Table_Row_Noeval extends Indi_Db_Table_Row {

    /**
     * Constructor. Is redeclared for system use, at model fields initialization stage.
     *
     * @param array $config
     */
    public function __construct(array $config = array()) {

        // Setup initial properties
        $this->_init($config);
    }

    /**
     * Here we override this method withan empty body, to prevent compiling of default values
     *
     * @param $prop
     */
    public function compileDefaultValue($prop) {

    }
}
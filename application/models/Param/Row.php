<?php
class Param_Row extends Indi_Db_Table_Row_Noeval {

    /**
     * Build a string, that will be used in Param_Row->export()
     *
     * @return string
     */
    protected function _ctor() {

        // Use original data as initial ctor
        $ctor = $this->_original;

        // Exclude `id` and `title` as those will be set automatically by MySQL and Indi Engine, respectively
        unset($ctor['id'], $ctor['title']);

        // Exclude props that will be already represented by shorthand-fn args
        foreach (ar('fieldId,possibleParamId') as $arg) unset($ctor[$arg]);

        // Stringify
        return _var_export($ctor);
    }

    /**
     * Build an expression for creating the current `param` entry in another project, running on Indi Engine
     *
     * @return string
     */
    public function export() {

        // Return
        return "param('" .
            $this->foreign('fieldId')->foreign('entityId')->table . "', '" .
            $this->foreign('fieldId')->alias . "', '" .
            $this->foreign('possibleParamId')->alias . "', " . $this->_ctor() . ");";
    }

    /**
     * Setter method for `title` prop
     */
    public function setTitle() {
        $this->_setTitle();
    }
}
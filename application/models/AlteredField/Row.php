<?php
class AlteredField_Row extends Indi_Db_Table_Row_Noeval {

    /**
     * This method was redefined to provide ability for some altered field
     * props to be set using aliases rather than ids
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     */
    public function __set($columnName, $value) {

        // Provide ability for some grid col props to be set using aliases rather than ids
        if (is_string($value) && !Indi::rexm('int11', $value)) {
            if ($columnName == 'sectionId') $value = section($value)->id;
            else if ($columnName == 'fieldId') $value = field(section($this->sectionId)->entityId, $value)->id;
        }

        // Call parent
        parent::__set($columnName, $value);
    }

    /**
     * Build a string, that will be used in AlteredField_Row->export()
     *
     * @return string
     */
    protected function _ctor() {

        // Use original data as initial ctor
        $ctor = $this->_original;

        // Exclude `id` as it will be set automatically by MySQL
        unset($ctor['id']);

        // Exclude props that are already represented by one of shorthand-fn args
        foreach (ar('sectionId,fieldId') as $arg) unset($ctor[$arg]);

        // Foreach $ctor prop
        foreach ($ctor as $prop => &$value) {

            // Get field
            $fieldR = $this->model()->fields($prop);

            // Exclude prop, if it has value equal to default value
            if ($fieldR->defaultValue == $value) unset($ctor[$prop]);

            // Exclude `title` prop, if it was auto-created
            else if ($prop == 'title' && ($tf = $this->model()->titleField()) && $tf->storeRelationAbility != 'none')
                unset($ctor[$prop]);

            // Else if prop contains keys - use aliases instead
            else if ($fieldR->storeRelationAbility != 'none') {
                // Empty for now
            }
        }

        // Stringify
        $ctorS = var_export($ctor, true);

        // Minify
        if (count($ctor) == 1) $ctorS = preg_replace('~^array \(\s+(.*),\s+\)$~', 'array($1)', $ctorS);

        // Return
        return $ctorS;
    }

    /**
     * Build an expression for creating the current `alteredField` entry in another project, running on Indi Engine
     *
     * @return string
     */
    public function export() {

        // Build and return `alteredField` entry creation expression
        return "alteredField('" .
            $this->foreign('sectionId')->alias . "', '" .
            $this->foreign('fieldId')->alias . "', " .
            $this->_ctor() . ");";
    }
}
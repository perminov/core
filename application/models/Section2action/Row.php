<?php
class Section2action_Row extends Indi_Db_Table_Row {

    /**
     * This method was redefined to provide ability for some
     * props to be set using aliases rather than ids
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     */
    public function __set($columnName, $value) {

        // Provide ability for some props to be set using aliases rather than ids
        if (is_string($value) && !Indi::rexm('int11', $value)) {
            if ($columnName == 'sectionId') $value = section($value)->id;
            else if ($columnName == 'actionId') $value = action($value)->id;
        }

        // Standard __set()
        parent::__set($columnName, $value);
    }

    /**
     * Build a string, that will be used in Section2action_Row->export()
     *
     * @return string
     */
    protected function _ctor() {

        // Use original data as initial ctor
        $ctor = $this->_original;

        // Exclude `id` and `move` as they will be set automatically by MySQL and Indi Engine, respectively
        unset($ctor['id'], $ctor['move']);

        // Exclude props that are already represented by one of shorthand-fn args
        foreach (ar('sectionId,actionId') as $arg) unset($ctor[$arg]);

        // Foreach $ctor prop
        foreach ($ctor as $prop => &$value) {

            // Get field
            $field = Indi::model('Section2action')->fields($prop);

            // Exclude prop, if it has value equal to default value
            if ($field->defaultValue == $value) unset($ctor[$prop]);

            // Else if prop contains keys - use aliases instead
            else if ($field->storeRelationAbility != 'none') {
                // Empty for now
            }
        }

        // Stringify and return $ctor
        return var_export($ctor, true);
    }

    /**
     * Build an expression for creating the current `section2action` entry in another project, running on Indi Engine
     *
     * @return string
     */
    public function export() {

        // Return creation expression
        return "section2action('" .
            $this->foreign('sectionId')->alias . "','" .
            $this->foreign('actionId')->alias . "', " .
            $this->_ctor() . ");";
    }
}
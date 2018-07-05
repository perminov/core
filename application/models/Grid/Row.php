<?php
class Grid_Row extends Indi_Db_Table_Row {

    /**
     * This method was redefined to provide ability for some grid col
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
            else if ($columnName == 'gridId') $value = grid($this->sectionId, $value)->id;
        }

        // Call parent
        parent::__set($columnName, $value);
    }

    /**
     * @return int
     */
    public function save(){

        // If no field chosen as a grid column basis - setup title same as `alterTitle`
        // if (!$this->fieldId || $this->alterTitle) $this->title = $this->alterTitle;

        // If there is no access limitation, empty `profileIds` prop
        if ($this->access == 'all') $this->profileIds = '';

        // Standard save
        return parent::save();
    }

    /**
     * This method is redefined to setup default value for $within arg,
     * for current `grid` entry to be moved within the `section` it belongs to
     *
     * @param string $direction
     * @param string $within
     * @return bool
     */
    public function move($direction = 'up', $within = '') {

        // If $within arg is not given - move grid column within the section it belongs to
        if (func_num_args() < 2) $within = '`sectionId` = "' . $this->sectionId . '" AND `gridId` = "' . $this->gridId . '"';

        // Call parent
        return parent::move($direction, $within);
    }

    /**
     * Build a string, that will be used in Grid_Row->export()
     *
     * @return string
     */
    protected function _ctor() {

        // Use original data as initial ctor
        $ctor = $this->_original;

        // Exclude `id` and `move` as they will be set automatically by MySQL and Indi Engine, respectively
        unset($ctor['id'], $ctor['move']);

        // Exclude props that are already represented by one of shorthand-fn args
        foreach (ar('sectionId,fieldId,alias') as $arg) unset($ctor[$arg]);

        // Foreach $ctor prop
        foreach ($ctor as $prop => &$value) {

            // Get field
            $field = Indi::model('Grid')->fields($prop);

            // Exclude prop, if it has value equal to default value
            if ($field->defaultValue == $value) unset($ctor[$prop]);

            // Exclude `title` prop, if it was auto-created
            else if ($prop == 'title' && ($tf = $this->model()->titleField()) && $tf->storeRelationAbility != 'none')
                unset($ctor[$prop]);

            // Else if prop contains keys - use aliases instead
            else if ($field->storeRelationAbility != 'none') {
                if ($prop == 'gridId') {
                    $value = ($_ = $this->foreign('gridId')) && $_->fieldId ? $_->foreign('fieldId')->alias : $_->alias;
                }
            }
        }

        // Stringify
        $ctorS = var_export($ctor, true);

        // Minify
        if (count($ctor) == 1) $ctorS = preg_replace('~^array \(\s+(.*),\s+\)$~', 'array($1)', $ctorS);
        else if (count($ctor) == 0) $ctorS = 'true';

        // Return
        return $ctorS;
    }

    /**
     * Build an expression for creating the current grid column in another project, running on Indi Engine
     *
     * @return string
     */
    public function export() {

        // Return creation expression
        return "grid('" .
            $this->foreign('sectionId')->alias . "','" .
            ($this->foreign('fieldId')->alias ?: $this->alias) . "', " .
            $this->_ctor() . ");";
    }
}
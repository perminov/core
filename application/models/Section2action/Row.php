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
            $fieldR = Indi::model('Section2action')->fields($prop);

            // Exclude prop, if it has value equal to default value (unless it's `profileIds`)
            if ($fieldR->defaultValue == $value && $prop != 'profileIds') unset($ctor[$prop]);

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

    /**
     * Add roles into linked `section` entry's `roleIds` prop, if need
     */
    public function onInsert() {

        // Foreach added role
        foreach ($this->adelta('profileIds', 'ins') as $ins)

            // Mention that role in section's `roleIds` prop
            $this->foreign('sectionId')->push('roleIds', $ins);

        // Save section
        $this->foreign('sectionId')->save();
    }

    /**
     * Add/remove roles from linked `section` entry, if need
     */
    public function onUpdate() {

        // Foreach added role
        foreach ($this->adelta('profileIds', 'ins') as $ins)

            // Mention that role in section's `roleIds` prop
            $this->foreign('sectionId')->push('roleIds', $ins);

        // Foreach removed role
        foreach ($this->adelta('profileIds', 'del') as $del)

            // If section have no more actions accessible for removed role
            if (!Indi::db()->query('
                SELECT COUNT(*) FROM `section2action`
                WHERE 1
                  AND `sectionId` = "'. $this->sectionId . '"
                  AND FIND_IN_SET("'. $del.'", `profileIds`)
            ')->fetchColumn())

                // Remove that role from section entry's `roleIds` prop
                $this->foreign('sectionId')->drop('roleIds', $del);

        // Save section
        $this->foreign('sectionId')->save();
    }

    /**
     * Setter for `title` prop
     */
    public function setTitle() {
        $this->title = $this->foreign('actionId')->title;
    }
}
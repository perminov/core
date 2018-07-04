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
}
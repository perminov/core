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
            if ($columnName == 'fieldId') $value = field(section($this->sectionId)->entityId, $value)->id;
        }

        // Call parent
        parent::__set($columnName, $value);
    }

    /**
     * @return int
     */
    public function save(){

        // If no field chosen as a grid column basis - setup title same as `alterTitle`
        if (!$this->fieldId || $this->alterTitle) $this->title = $this->alterTitle;

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
        if (func_num_args() < 2) $within = '`sectionId` = "' . $this->sectionId . '"';

        // Call parent
        return parent::move($direction, $within);
    }
}
<?php
class Consider_Row extends Indi_Db_Table_Row {

    /**
     * Small hack, for spoofing prop name
     *
     * @param $where
     * @param $fieldR
     * @param $satelliteR
     * @param $satellite
     * @param null $noSatellite
     * @return mixed|void
     */
    protected function _comboDataSatelliteWHERE(&$where, $fieldR, $satelliteR, $satellite, $noSatellite = null) {

        //
        if ($satelliteR->alias == 'relation') $satelliteR->alias = 'entityId';

        // Call parent
        return $this->callParent();
    }

    /**
     * This method was redefined to provide ability for some `consider` entry's
     * props to be set using aliases rather than ids
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     */
    public function __set($columnName, $value) {

        // Provide ability for some `consider` entry's props to be set using aliases rather than ids
        if (is_string($value) && !Indi::rexm('int11', $value)) {
            if ($columnName == 'entityId') $value = entity($value)->id;
            else if (in($columnName, 'fieldId,consider')) $value = field($this->entityId, $value)->id;
            else if ($columnName == 'foreign') $value = field($this->foreign('consider')->relation, $value)->id;
            else if ($columnName == 'connector') $value = field($this->foreign('fieldId')->relation, $value)->id;
        }

        // Call parent
        parent::__set($columnName, $value);
    }

    /**
     * Force `required` prop to be 'y' if dependent field's `relation` prop is zero
     */
    public function onBeforeSave() {
        if ($this->foreign('fieldId')->zero('relation')) $this->required = 'y';
    }
}
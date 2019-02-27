<?php
class Consider_Row extends Indi_Db_Table_Row {

    /**
     * Small hack, for spoofing name of column to be involved in sql query
     *
     * @param $where
     * @param Field_Row $field
     * @param Field_Row $cField
     * @param $cValue
     * @return mixed|void
     */
    protected function _comboDataConsiderWHERE(&$where, Field_Row $field, Field_Row $cField, $cValue) {

        // Spoof column name
        if ($cField->alias == 'relation') $cField->alias = 'entityId';

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
     * @inheritdocs
     */
    public function onBeforeSave() {

        // If dependent field's `relation` prop is not zero - return
        if (!$this->foreign('fieldId')->zero('relation')) return;

        // Else:
        // 1. Set `required` to be 'y',
        $this->required = 'y';

        // 2. Assign zero-values to `foreign` and `connector` props, as they're applicable
        // only in cases when dependent field's `relation` prop is not zero
        $this->zero('foreign,connector', true);
    }
}
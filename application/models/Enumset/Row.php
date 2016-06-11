<?php
class Enumset_Row extends Indi_Db_Table_Row_Noeval {

    /**
     * Save possible value
     *
     * @return int
     */
    public function save() {

        // If `alias` property was not modified - do a standard save
        if (!array_key_exists('alias', $this->_modified)) return parent::save();

        // Get the field row
        $fieldR = $this->foreign('fieldId');

        // Get the existing possible values
        $enumsetA = $fieldR->nested('enumset', array('order' => 'move'))->column('alias');

        // Get the database table name
        $table = $fieldR->foreign('entityId')->table;

        // Get the current default value
        $defaultValue = Indi::db()->query('SHOW COLUMNS FROM `' . $table . '` LIKE "' . $fieldR->alias . '"')
            ->fetch(PDO::FETCH_OBJ)->Default;

        // If this is an existing enumset row
        if ($this->id) {

            // If modified version of value is already exists within list of possible value - throw an error message
            if (in_array($this->alias, ar(im($enumsetA)))) iexit(sprintf(I_ENUMSET_ERROR_VALUE_ALREADY_EXISTS, $this->alias));

            // Convert $defaultValue to an array, for handling case if column type is SET
            $defaultValue = explode(',', $defaultValue);

            // If original version of value - is a default value
            if (in_array($this->_original['alias'], $defaultValue)) {

                // Setup sql default value as modified version of value
                $defaultValue[array_search($this->_original['alias'], $defaultValue)] = $this->alias;

                // If field's default value does not contain php expressions, setup $updateFieldDefaultValue flag
                // to true, because in this case we need to update field default value bit later, too
                if (!preg_match(Indi::rex('php'), $fieldR->defaultValue)) $updateFieldDefaultValue = true;
            }

            // Convert $defaultValue back from array to string
            $defaultValue = implode(',', $defaultValue);

            // Replace original value with modified value within list of possible values
            $enumsetA[array_search($this->_original['alias'], $enumsetA)] = $this->alias;

        // Else if it is a new enumset row
        } else {

            // If value that is going to be appended - is already exists within list of possible value - throw an error message
            if (in_array($this->alias, ar(im($enumsetA)))) iexit(sprintf(I_ENUMSET_ERROR_VALUE_ALREADY_EXISTS, $this->alias));

            // Append a new value to the list of allowed values
            $enumsetA[] = $this->alias;
        }

        // Build the ALTER query
        $sql[] = 'ALTER TABLE `' . $table . '` CHANGE COLUMN `' . $fieldR->alias . '` `' . $fieldR->alias . '`';
        $sql[] = $fieldR->foreign('columnTypeId')->type . '("' . implode('","', $enumsetA) . '")';
        $sql[] = 'CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
        $sql[] = 'DEFAULT "' . $defaultValue . '"';

        // Run that query
        Indi::db()->query(implode(' ', $sql));

        // If $updateFieldDefaultValue flag is set to true
        if ($updateFieldDefaultValue)
            Indi::db()->query('
                UPDATE `field`
                SET `defaultValue` = "' . $defaultValue . '"
                WHERE `id` = "' . $fieldR->id . '"
                LIMIT 1
            ');

        // Standard save
        return parent::save();
    }

    /**
     * Delete
     *
     * @return int
     */
    public function delete() {

        // Get the field row
        $fieldR = $this->foreign('fieldId');

        // Get the existing possible values
        $enumsetA = $fieldR->nested('enumset', array('order' => 'move'))->column('alias');

        // Get the database table name
        $table = $fieldR->foreign('entityId')->table;

        // Get the current default value
        $defaultValue = Indi::db()->query('SHOW COLUMNS FROM `' . $table . '` LIKE "' . $fieldR->alias . '"')
            ->fetch(PDO::FETCH_OBJ)->Default;

        // If current row is the last enumset row, related to current field - throw an error message
        if (count($enumsetA) == 1) iexit(sprintf(I_ENUMSET_ERROR_VALUE_LAST, $this->alias));

        // Remove current item from the list of possible values
        unset($enumsetA[array_search($this->alias, $enumsetA)]);

        // Convert $defaultValue to an array, for handling case if column type is SET
        $defaultValue = explode(',', $defaultValue);

        // If original version of value - is a default value
        if (in_array($this->alias, $defaultValue)) {

            // Unset current item from the list of default values
            unset($defaultValue[array_search($this->alias, $defaultValue)]);

            // If field's default value does not contain php expressions, setup $updateFieldDefaultValue flag
            // to true, because in this case we need to update field default value bit later, too
            if (!preg_match(Indi::rex('php'), $fieldR->defaultValue)) $updateFieldDefaultValue = true;

            // If after unset there is no more sql default values left, we should set at least one, so we pick first
            // item from $enumsetA and set it as $defaultValue
            if (count($defaultValue) == 0) $defaultValue = array(current($enumsetA));
        }

        // Convert $defaultValue back from array to string
        $defaultValue = implode(',', $defaultValue);

        // Build the ALTER query
        $sql[] = 'ALTER TABLE `' . $table . '` CHANGE COLUMN `' . $fieldR->alias . '` `' . $fieldR->alias . '`';
        $sql[] = $fieldR->foreign('columnTypeId')->type . '("' . implode('","', $enumsetA) . '")';
        $sql[] = 'CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL';
        $sql[] = 'DEFAULT "' . $defaultValue . '"';

        // Run that query
        Indi::db()->query(implode(' ', $sql));

        // If $updateFieldDefaultValue flag is set to true
        if ($updateFieldDefaultValue)
            Indi::db()->query('
                UPDATE `field`
                SET `defaultValue` = "' . $defaultValue . '"
                WHERE `id` = "' . $fieldR->id . '"
                LIMIT 1
            ');

        // Standard save
        return parent::delete();
    }

    public function deleteForeignKeysUsages(){
        // Empty method
    }
}
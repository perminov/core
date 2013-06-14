<?php
class Enumset_Row extends Indi_Db_Table_Row{
    public function getTitle(){
        if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $this->alias, $matches)) {
            return '<span class="color-box" style="background: #' . $matches[1] . ';"></span> '. parent::getTitle();
        } else {
            return parent::getTitle();
        }
    }

    public function save($parentSave = false) {
        // Simple save
        if ($parentSave) return parent::save();

        // Get Field row
        $fieldR = $this->getForeignRowByForeignKey('fieldId');

        // Get ColumnType row
        $columnTypeR = $fieldR->getForeignRowByForeignKey('columnTypeId');

        // Check that $columnTypeR->type is ENUM or SET
        if (in_array($columnTypeR->type, array('ENUM', 'SET'))) {

            if (preg_match('/^#([0-9a-fA-F]{6})$/', $this->_modified['alias'])) {
                $this->alias = Misc::rgbPrependHue($this->alias);
            }
            parent::save();

            // Get existing values
            $values = $this->getTable()->fetchAll('`fieldId` = "' . $fieldR->id . '"', 'move')->toArray();
            for ($i = 0; $i < count($values); $i++) {

                // Set up viewValues. Difference between viewValues and rawValues is that if viewValues
                // are #RRGGBB colors with prepended hue number, hue number will be stripped from viewValues
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $values[$i]['alias'], $matches)) {
                    $viewValues[] = '#' . $matches[1];
                } else {
                    $viewValues[] = $values[$i]['alias'];
                }

                // Set up rawValues
                $rawValues[] = $values[$i]['alias'];
            }

            if ($columnTypeR->type == 'ENUM') {
                // Check if default value is a rgb color with a prepended hue number
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $fieldR->defaultValue, $matches)) {
                    $viewDefaultValue = '#' . $matches[1];
                } else {
                    $viewDefaultValue = $fieldR->defaultValue;
                }
                // If field default value is not presented (for some reasons) in $viewValues array, we create corresponding value
                if (!in_array($viewDefaultValue, $viewValues) && ($columnTypeR->type == 'ENUM' || ($columnTypeR->type == 'SET' && $fieldR->defaultValue != ''))) {
                    $rawValues[] = $fieldR->defaultValue;
                    $new = $this->getTable()->createRow();
                    $new->fieldId = $fieldR->id;
                    $new->alias = $fieldR->defaultValue;
                    $new->title = 'Укажите наименование для значения по умолчанию - "' . $viewDefaultValue . '"';
                    $new->save(true);
                }
            } else if ($columnTypeR->type == 'SET') {
                $defaultValues = explode(',', $fieldR->defaultValue);
                for ($i = 0; $i < count($defaultValues); $i++) {
                    // Check if any of default value items is a rgb colors with a prepended hue number
                    if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $defaultValues[$i], $matches)) {
                        $viewDefaultValue = '#' . $matches[1];
                    } else {
                        $viewDefaultValue = $defaultValues[$i];
                    }
                    // If field default value is not presented (for some reasons) in $viewValues array, we create corresponding value
                    if (!in_array($viewDefaultValue, $viewValues) && ($columnTypeR->type == 'ENUM' || ($columnTypeR->type == 'SET' && $defaultValues[$i] != ''))) {
                        $rawValues[] = $defaultValues[$i];
                        $new = $this->getTable()->createRow();
                        $new->fieldId = $fieldR->id;
                        $new->alias = $defaultValues[$i];
                        $new->title = 'Укажите наименование для значения по умолчанию - "' . $viewDefaultValue . '"';
                        $new->save(true);
                    }
                }
            }

            // Construction and execution of ALTER sql query
            $query  = 'ALTER TABLE `' . Misc::loadModel('Entity')->fetchRow('`id` = "' . $fieldR->entityId . '"')->table . '` ';
            $query .= 'CHANGE `' . $fieldR->alias . '` `' . $fieldR->alias . '` ';
            $query .=  $columnTypeR->type . '(' . "'" . implode("','", $rawValues) . "'" . ')';
            $query .= ' CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ';
            $query .= 'DEFAULT ' . "'" . $fieldR->defaultValue . "'";
            // different default setup for SET ??
            $this->getTable()->getAdapter()->query($query);
        }
    }

    /**
     * Check if value to be deleted is last value or default value, and displays correcponding error messages.
     * Otherwise, will construct and execute an ALTER sql query to adjust field structure, and after that
     * perform standard deletion
     *
     * @return int|void
     */
    public function delete($parentDelete = false) {
        // Force deletion, used in case if there is a need to just remove rows from `enumset` table without
        // executing any ALTER queries
        if ($parentDelete || $GLOBALS['enumsetForceDelete']) return parent::delete();

        // Get Field row
        $fieldR = $this->getForeignRowByForeignKey('fieldId');

        // Get ColumnType row
        $columnTypeR = $fieldR->getForeignRowByForeignKey('columnTypeId');

        // Check that $columnTypeR->type is ENUM or SET
        if (in_array($columnTypeR->type, array('ENUM', 'SET'))) {

            // Get values
            $values = $this->getTable()->fetchAll('`fieldId` = "' . $fieldR->id . '"', 'move')->toArray();
            $rawValues = array(); for ($i = 0; $i < count($values); $i++) $rawValues[] = $values[$i]['alias'];

            // Checks if deletion is not allowed
            if (count($rawValues) == 1) {
                die(ENUMSET_DELETE_DENIED_LASTVALUE);
            } else if (count($rawValues) > 1) {
                if ($fieldR->defaultValue == $this->alias) {
                    if ($columnTypeR->type == 'ENUM' || ($columnTypeR->type == 'SET' && $fieldR->defaultValue != '')) {
                        die(ENUMSET_DELETE_DENIED_DEFAULTVALUE);
                    }
                }
            }

            // Unset value that will be deleted for the correct construction of ALTER query
            unset($rawValues[array_search($this->alias, $rawValues)]);

            // Construction and execution of ALTER sql query
            $query  = 'ALTER TABLE `' . Misc::loadModel('Entity')->fetchRow('`id` = "' . $fieldR->entityId . '"')->table . '` ';
            $query .= 'CHANGE `' . $fieldR->alias . '` `' . $fieldR->alias . '` ';
            $query .= $columnTypeR->type . "('" . implode("','", $rawValues) . "') ";
            $query .= 'CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ';
            $query .= 'DEFAULT ' . "'" . $fieldR->defaultValue . "'";
            $this->getTable()->getAdapter()->query($query);
        }
        return parent::delete();
    }
}
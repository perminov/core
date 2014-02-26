<?php
class Indi_View_Helper_FormCombo extends Indi_View_Helper_Admin_FormCombo{
    public function getField($name, $tableName) {
        $entityId = Indi::model('Entity')->fetchRow('`table` = "' . $tableName . '"')->id;
        return Indi::model('Field')->fetchRow('`alias` = "' . $name . '" AND `entityId` = "' . $entityId . '"');
    }
}
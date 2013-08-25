<?php
class Indi_View_Helper_FormCombo extends Indi_View_Helper_Admin_FormCombo{
    public function getField($name, $tableName) {
        $entityId = Misc::loadModel('Entity')->fetchRow('`table` = "' . $tableName . '"')->id;
        return Misc::loadModel('Field')->fetchRow('`alias` = "' . $name . '" AND `entityId` = "' . $entityId . '"');
    }
}
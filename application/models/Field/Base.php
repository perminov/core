<?php
class Field_Base extends Indi_Db_Table{
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Field_Row';

    public function getFieldsByEntityId($entityId){
        return $this->fetchAll('`entityId` = "' . $entityId . '"', 'move');
    }

    public function getGridFieldsBySectionId($sectionId){
        $grid = new Grid();
        $gridArray = $grid->fetchAll('`sectionId` = "' . $sectionId . '" AND `toggle` = "y"', 'move')->toArray();

        $fieldIds = array();
        for($i = 0; $i < count($gridArray); $i++) $fieldIds[] = $gridArray[$i]['fieldId'];
        $where = count($fieldIds) ? '`id` IN (' . implode(',', $fieldIds) . ')' : '`id` IN ("")';
        $order = count($fieldIds) ? 'POSITION(CONCAT("\'", `id`, "\'") IN "\'' . implode("','", $fieldIds) . '\'")' : null;
        $fieldsA = $this->fetchAll($where, $order)->toArray();
        for ($i = 0; $i < count($fieldsA); $i++) {
            for ($j = 0; $j < count($gridArray); $j++) {
                if ($gridArray[$j]['fieldId'] == $fieldsA[$i]['id'] && $gridArray[$j]['alterTitle']) {
                    $fieldsA[$i]['title'] = $gridArray[$j]['alterTitle'];
                }
            }
        }
        return $this->createRowset(array('data' => $fieldsA));
    }

    public function getFiltersCountBySectionId($sectionId) {
        return $this->_db->query('SELECT COUNT(*) FROM `search` WHERE `sectionId` = "' . $sectionId . '"')->fetchColumn();
    }

    public function getFiltersBySectionId($sectionId){
        return  Misc::loadModel('Search')->fetchAll('`sectionId` = "' . $sectionId . '" AND `toggle`="y"', '`move`');
    }

    public function getDisabledFieldsBySectionId($sectionId){

        $disabled = Misc::loadModel('DisabledField');
        $disabledArray = $disabled->fetchAll('`sectionId` = "' . $sectionId . '"')->toArray();

        $fieldIds = array();
        for($i = 0; $i < count($disabledArray); $i++) {
            $fieldIds[] = $disabledArray[$i]['fieldId'];
            $tmp[$disabledArray[$i]['fieldId']] = $disabledArray[$i];
        }
        $where = count($fieldIds) ? '`id` IN (' . implode(',', $fieldIds) . ')' : '`id` IN ("")';
        $rs = $this->fetchAll($where)->toArray();

        $disabled = array('form' => array(), 'save' => array());
        foreach ($rs as $r) {
            $disabled['save'][] = $r['alias'];
            if ($tmp[$r['id']]['displayInForm'] != 1) $disabled['form'][] = $r['alias'];
        }

        return $disabled;
    }
}
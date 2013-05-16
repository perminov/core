<?php
class Search_Row extends Indi_Db_Table_Row{
    public function combo() {
        $store = array();
        $max = 0;
        $store[] = array('id' => '%', 'title' => 'Неважно');
        if ($this->foreign['fieldId']->foreign['elementId']['alias'] == 'check') {
            $store = array_merge($store, array(
                array('id' => '1', 'title' => 'Да'),
                array('id' => '0', 'title' => 'Нет')
            ));
        } else if ($this->foreign['fieldId']->relation == 6) {
            $a = Misc::loadModel('Enumset')->fetchAll('`fieldId` = "' . $this->fieldId . '"', 'move')->toArray();
            foreach ($a as $i) $store[] = array('id' => $i['alias'], 'title' => strip_tags($i['title']));
        } else if ($this->foreign['fieldId']->satellite) {

        } else {
            $where = $this->foreign['fieldId']->filter;
            if (preg_match('/(\$|::)/', $where)) eval('$where = \'' . $where . '\';');
            $a = Entity::getModelById($this->foreign['fieldId']->relation)->fetchAll($where)->toArray();
            foreach ($a as $i) $store[] = array('id' => $i['id'], 'title' => strip_tags($i['title']));
        }
        foreach ($store as $item) if (mb_strlen($item['title']) > $max) $max = mb_strlen(strip_tags($item['title']), 'utf-8');
        return array('width' => $max*7 + 23 + 5,'store' => json_encode($store));
    }
}
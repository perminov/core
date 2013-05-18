<?php
class Search_Row extends Indi_Db_Table_Row{
    public function combo() {
        $store = array();
        $max = 0;
        if ($this->foreign['fieldId']->storeRelationAbility != 'many') $store[] = array('id' => '%', 'title' => 'Неважно');
        if ($this->foreign['fieldId']->foreign['elementId']['alias'] == 'check') {
            $store = array_merge($store, array(
                array('id' => '1', 'title' => 'Да'),
                array('id' => '0', 'title' => 'Нет')
            ));
        } else if ($this->foreign['fieldId']->relation == 6) {
            $a = Misc::loadModel('Enumset')->fetchAll('`fieldId` = "' . $this->fieldId . '"', 'move');
            foreach ($a as $i) {
                if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $i->alias, $matches)) {
                    $color = true;
                    break;
                }
            }
            if ($color) {
                foreach ($a as $i) {
                    if(preg_match('/^[0-9]{3}#[0-9a-fA-F]{6}$/',$i->alias)) {
                        $color = substr($i->alias, 4);
                        $i->title = '<span class="color-box" style="background: #' . $color . ';"></span> '. $i->title;
                        $store[] = array('id' => $i->alias, 'title' => $i->title);
                    } else {
                        $store[] = array('id' => $i->alias, 'title' => strip_tags($i->title));
                    }
                }
            } else {
                foreach ($a as $i) $store[] = array('id' => $i->alias, 'title' => strip_tags($i->title));
            }
        } else if ($this->foreign['fieldId']->satellite) {

        } else {
            $where = $this->foreign['fieldId']->filter;
            if (preg_match('/(\$|::)/', $where)) eval('$where = \'' . $where . '\';');
            $model = Entity::getModelById($this->foreign['fieldId']->relation);
            $fields = $model->getDbFields();
            if (array_key_exists('move', $fields)) {
                $order = '`move`';
            } else if (array_key_exists('title', $fields)) {
                $order = '`title`';
                if ($fields['title']->elementId == 11) $color = true;
            }
            $a = $model->fetchAll($where, $order);
            if ($color) {
                foreach ($a as $i) {
                    if(preg_match('/^[0-9]{3}#[0-9a-fA-F]{6}$/',$i->title)) {
                        $color = substr($i->title, 4);
                        $i->title = '<span class="color-box" style="background: #' . $color . ';"></span><span>#'. $color.'</span>';
                        $store[] = array('id' => $i->id, 'title' => $i->title);
                    } else {
                        $store[] = array('id' => $i->id, 'title' => strip_tags($i->title));
                    }
                }
            } else {
                foreach ($a as $i) $store[] = array('id' => $i->id, 'title' => strip_tags($i->title));
            }
        }
        if ($color && $this->foreign['fieldId']->relation != 6) {
            $max = 10;
        } else {
            foreach ($store as $item) if (mb_strlen($item['title']) > $max) $max = mb_strlen(strip_tags($item['title']), 'utf-8');
        }
        return array('width' => $max*7 + 23 + 5,'store' => json_encode($store), 'color' => $color ? 1 : 0);
    }
}
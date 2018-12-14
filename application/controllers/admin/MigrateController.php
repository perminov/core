<?php
class Admin_MigrateController extends Indi_Controller {

    public function lockedAction () {
        field('grid', 'group', array (
            'title' => 'Группа',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'normal',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ))->move(2);
        enumset('grid', 'group', 'normal', array('title' => 'Обычные'));
        enumset('grid', 'group', 'locked', array('title' => 'Зафиксированные'));
        section('grid', array('groupBy' => 'group'));
        grid('grid', 'group', true);
        die('ok');
    }
}
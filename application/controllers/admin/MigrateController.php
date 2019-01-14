<?php
class Admin_MigrateController extends Indi_Controller {

    public function lockedAction() {
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
        param('grid', 'gridId', 'groupBy', 'group');
        die('ok');
    }

    public function summaryTypeAction() {
        field('grid', 'summaryType', array (
            'title' => 'Внизу',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'none',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ))->move(2);
        enumset('grid', 'summaryType', 'none', array('title' => 'Пусто'));
        enumset('grid', 'summaryType', 'sum', array('title' => 'Сумма'));
        enumset('grid', 'summaryType', 'average', array('title' => 'Среднее'));
        enumset('grid', 'summaryType', 'min', array('title' => 'Минимум'));
        enumset('grid', 'summaryType', 'max', array('title' => 'Максимум'));
        enumset('grid', 'summaryType', 'text', array('title' => 'Текст'));
        field('grid', 'summaryText', array (
            'title' => 'Текст',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
        ))->move(2);
        die('ok');
    }
}
<?php
class Indi_Controller_Migrate extends Indi_Controller {
    public function filterTipAction() {
        field('search', 'tooltip', array (
            'title' => 'Подсказка',
            'columnTypeId' => 'TEXT',
            'elementId' => 'textarea',
        ));
        die('ok');
    }
    public function sectiontogglehAction() {
        enumset('section', 'toggle', 'h', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Скрыт'));
        die('ok');
    }
    public function noticegettertoggleAction() {
        field('noticeGetter', 'toggle', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'y',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ))->move(14);
        enumset('noticeGetter', 'toggle', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Включен'));
        enumset('noticeGetter', 'toggle', 'n', array('title' => '<span class="i-color-box" style="background: red;"></span>Выключен'));
        grid('noticeGetters', 'toggle', true)->move(5);
        die('ok');
    }
    public function rowexpanderAction() {
        enumset('grid', 'toggle', 'e', array('title' => '<span class="i-color-box" style="background: lightgray; border: 1px solid blue;"></span>Скрыт, но показан в развороте'));
        die('ok');
    }
    public function fieldsmodeAction() {
        action('activate', array('title' => 'Активировать', 'rowRequired' => 'y', 'type' => 's'));
        section2action('fields','activate', array ('profileIds' => '1', 'rename' => 'Выбрать режим'))->move(1);
        die('ok');
    }

    public function gridcolWidthUsageAction() {
        field('grid', 'width', array (
            'title' => 'Ширина',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
        ))->move(6);
        param('grid', 'width', 'measure', 'px');
        grid('grid', 'width', array('editor' => '1'));
        action('rwu', array('rowRequired' => 'n', 'type' => 's', 'display' => 0));
        die('ok');
    }

    public function foreignFilterAction() {
        field('search', 'further', array (
            'title' => 'Поле по ключу',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'relation' => 'field',
            'storeRelationAbility' => 'one',
        ))->move(12);
        consider('search', 'further', 'fieldId', array (
            'foreign' => 'relation',
            'required' => 'y',
            'connector' => 'entityId',
        ));
        die('ok');
    }

    public function foreignGridAction(){
        field('grid', 'further', array (
            'title' => 'Поле по ключу',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'relation' => 'field',
            'storeRelationAbility' => 'one',
        ))->move(13);
        consider('grid', 'further', 'fieldId', array (
            'foreign' => 'relation',
            'required' => 'y',
            'connector' => 'entityId',
        ));
        die('ok');
    }

    public function last4Action() {
        foreach (ar('sectionancestor,rownumberer,filterallowclear,sectionmultiselect') as $_)
            $this->{$_ . 'Action'}();
        foreach (ar('sectionActions,grid,alteredFields,search') as $s)
            section($s, array('extendsPhp' => 'Indi_Controller_Admin_Multinew'));
        die('ok');
    }

    public function sectionmultiselectAction() {
        field('section', 'multiSelect', array(
            'title' => 'Выделение более одной записи',
            'columnTypeId' => 'BOOLEAN',
            'elementId' => 'check'
        ));
        //die('ok');
    }

    public function filterallowclearAction() {
        field('search', 'allowClear', array (
            'title' => 'Разрешить сброс',
            'columnTypeId' => 'BOOLEAN',
            'elementId' => 'check',
            'defaultValue' => '1',
        ))->move(6);
        //die('ok');
    }

    public function rownumbererAction() {
        field('section', 'rownumberer', array (
            'title' => 'Включить нумерацию строк',
            'columnTypeId' => 'BOOLEAN',
            'elementId' => 'check',
            'defaultValue' => '0',
        ));
        //die('ok');
    }

    public function sectionancestorAction() {
        field('section', 'extends', array('title' => 'Родительский класс PHP', 'alias' => 'extendsPhp'));
        field('section', 'extendsJs', array (
            'title' => 'Родительский класс JS',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'defaultValue' => 'Indi.lib.controller.Controller',
        ))->move(12);
        grid('sections','rowsOnPage', array (
            'alterTitle' => 'СНС',
            'tooltip' => 'Строк на странице',
        ));
        grid('sections','extendsPhp', array('editor' => 1));
        grid('sections','extendsJs', array('editor' => 1));
        //die('ok');
    }

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

    public function convertSatelliteAction() {
        if (entity('user'))
            foreach (ar('color,testIds,storeRelationAbility,test,entityId') as $prop)
                if ($_ = field('user', $prop))
                    $_->delete();
        entity('columnType', array('titleFieldId' => 'type'));
        enumset('field', 'storeRelationAbility', 'none', array('title' => '<span class="i-color-box" style="background: white;"></span>Нет'));
        enumset('field', 'storeRelationAbility', 'one', array('title' => '<span class="i-color-box" style="background: url(/i/admin/btn-icon-login.png);"></span>Да, но для только одного значения ключа'));
        enumset('field', 'storeRelationAbility', 'many', array('title' => '<span class="i-color-box" style="background: url(/i/admin/btn-icon-multikey.png);"></span>Да, для энного количества значений ключей'));
        if ($_ = section('fieldsAll')) $_->delete();
        section('fieldsAll', array (
            'sectionId' => 'configuration',
            'entityId' => 'field',
            'title' => 'Все поля',
            'disableAdd' => '1',
            'type' => 's',
            'groupBy' => 'entityId',
        ))->nested('grid')->delete();
        section2action('fieldsAll','index', array('profileIds' => '1'));
        section2action('fieldsAll','form', array('profileIds' => '1'));
        section2action('fieldsAll','save', array('profileIds' => '1'));
        grid('fieldsAll','entityId', array (
            'alterTitle' => 'Сущность',
            'tooltip' => 'Сущность, в структуру которой входит это поле',
        ));
        grid('fieldsAll','title', array (
            'alterTitle' => 'Наименование',
            'editor' => 1,
        ));
        grid('fieldsAll','view', array('alterTitle' => 'Отображение'));
        grid('fieldsAll','mode', array('gridId' => 'view'));
        grid('fieldsAll','elementId', array (
            'alterTitle' => 'UI',
            'tooltip' => 'Элемент управления',
            'gridId' => 'view',
        ));
        grid('fieldsAll','tooltip', array('gridId' => 'view'));
        grid('fieldsAll','mysql', array('alterTitle' => 'MySQL'));
        grid('fieldsAll','alias', array (
            'alterTitle' => 'Имя',
            'gridId' => 'mysql',
        ));
        grid('fieldsAll','columnTypeId', array (
            'alterTitle' => 'Тип',
            'tooltip' => 'Тип столбца MySQL',
            'gridId' => 'mysql',
        ));
        grid('fieldsAll','defaultValue', array (
            'alterTitle' => 'По умолчанию',
            'tooltip' => 'Значение по умолчанию',
            'gridId' => 'mysql',
        ));
        grid('fieldsAll','fk', array('alterTitle' => 'Ключи'));
        grid('fieldsAll','storeRelationAbility', array (
            'alterTitle' => 'Режим',
            'tooltip' => 'Предназначено для хранения ключей',
            'gridId' => 'fk',
        ));
        grid('fieldsAll','relation', array (
            'alterTitle' => 'Сущность',
            'tooltip' => 'Ключи какой сущности будут храниться в этом поле',
            'gridId' => 'fk',
        ));
        grid('fieldsAll','filter', array (
            'alterTitle' => 'Фильтрация',
            'tooltip' => 'Статическая фильтрация',
            'gridId' => 'fk',
        ));
        grid('fieldsAll','l10n', array (
            'alterTitle' => 'l10n',
            'tooltip' => 'Мультиязычность',
        ));
        grid('fieldsAll','move', true);
        grid('fieldsAll','satellitealias', array('toggle' => 'n'));
        grid('fieldsAll','span', array('toggle' => 'n'));
        grid('fieldsAll','dependency', array('gridId' => 'span'));
        grid('fieldsAll','satellite', array('gridId' => 'span'));
        grid('fieldsAll','alternative', array('gridId' => 'span'));
        filter('fieldsAll', 'entityId', array('alt' => 'Сущность'));
        filter('fieldsAll', 'mode', true);
        filter('fieldsAll', 'relation', array('alt' => 'Ключи'));
        filter('fieldsAll', 'elementId', array('alt' => 'Элемент'));
        filter('fieldsAll', 'dependency', true);

        // Convert satellite-cfg into consider-cfg
        foreach (Indi::model('Field')->fetchAll('`dependency` != "u"') as $fieldR) {
            $ctor = array();
            if ($fieldR->alternative) $ctor['foreign'] = $fieldR->alternative;
            if (!Indi::model($fieldR->entityId)->fields($fieldR->alias)->param('allowZeroSatellite'))
                $ctor['required'] = 'y';
            if ($_ = $fieldR->foreign('satellite')->satellitealias) $ctor['connector'] = $_;
            if (!$ctor) $ctor = true;
            consider($fieldR->foreign('entityId')->table, $fieldR->alias, $fieldR->foreign('satellite')->alias, $ctor);
        }
        field('section', 'parentSectionConnector', array('filter' => '`storeRelationAbility`!="none"'));

        // Erase satellite-cfg and hide fields, responsible for satellite-functionaity
        //Indi::db()->query('UPDATE `field` SET `dependency` = "u", `satellitealias` = "", `satellite` = "0", `alternative` = ""');
        foreach (ar('span,dependency,satellitealias,satellite,alternative') as $field)
            field('field', $field, array('mode' => 'readonly'));
        die('ok');
    }
    public function admindemoAction() {
        field('admin', 'demo', array (
            'title' => 'Демо-режим',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('admin', 'demo', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Нет'));
        enumset('admin', 'demo', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Да'));
        grid('admins', 'demo', array('alterTitle' => 'Демо', 'tooltip' => 'Демо-режим'));
        field('profile', 'demo', array (
            'title' => 'Демо-режим',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('profile', 'demo', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Нет'));
        enumset('profile', 'demo', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Да'));
        grid('profiles', 'demo', array('alterTitle' => 'Демо', 'tooltip' => 'Демо-режим'));
        die('ok');
    }
    public function filesGroupByAction() {
        field('entity', 'filesGroupBy', array (
            'title' => 'Группировать файлы',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'relation' => 'field',
            'storeRelationAbility' => 'one',
            'filter' => '`entityId` = "<?=$this->id?>" AND `storeRelationAbility` = "one"',
        ));
        grid('entities', 'filesGroupBy', array('editor' => 1));
        die('ok');
    }
}
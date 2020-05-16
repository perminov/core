<?php
class Indi_Controller_Migrate extends Indi_Controller {
    public function titleField2considerAction() {
        foreach (m('Entity')->fetchAll() as $entityR) {
            if (!$entityR->titleFieldId) continue;
            if ($entityR->foreign('titleFieldId')->storeRelationAbility == 'none') continue;
            if (!$fieldR_title = Indi::model($entityR->id)->fields('title')) continue;
            $existing = consider($entityR->table, 'title', $entityR->foreign('titleFieldId')->alias);
            d($entityR->table . ':' . $entityR->foreign('titleFieldId')->alias);
            if (Indi::get()->do) $newupdated = consider($entityR->table, 'title', $entityR->foreign('titleFieldId')->alias, ['foreign' => 'title']);
        }
        die('xx');
    }
    public function l10n2Action() {
        consider('grid', 'alterTitle', 'title', array ('required' => 'y'));
        consider('search', 'alt', 'title', array ('required' => 'y'));
        consider('section2action', 'rename', 'title', array ('required' => 'y'));
        consider('alteredField', 'rename', 'title', array ('required' => 'y'));
        field('queueItem', 'result', array (
            'title' => 'Результат',
            'columnTypeId' => 'TEXT',
            'elementId' => 'html',
        ));
        field('queueChunk', 'queueChunkId', array (
            'title' => 'Родительский сегмент',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'relation' => 'queueChunk',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ))->move(14);
        entity('queueChunk', array('titleFieldId' => 'location'));
        field('queueChunk', 'fraction', array (
            'title' => 'Фракция',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'none',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ))->move(14);
        enumset('queueChunk', 'fraction', 'none', array('title' => 'Не указана'));
        enumset('queueChunk', 'fraction', 'adminSystemUi', array('title' => 'AdminSystemUi'));
        enumset('queueChunk', 'fraction', 'adminCustomUi', array('title' => 'AdminCustomUi'));
        enumset('queueChunk', 'fraction', 'adminCustomData', array('title' => 'AdminCustomData'));
        section('queueChunk', array ('groupBy' => 'fraction'));

        field('enumset', 'title', array('columnTypeId' => 'TEXT'));
        if (action('login')) action('login', ['type' => 's']);
        foreach (ar('grid,alteredField,search') as $table) Indi::db()->query('
            UPDATE `' . $table . '` `g`, `field` `f` SET `g`.`title` = `f`.`title` WHERE `g`.`fieldId` = `f`.`id`
        ');
        field('queueTask', 'stageState', array (
            'title' => 'Этап - Статус',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'mode' => 'hidden',
        ))->move(14);
        consider('queueTask', 'stageState', 'stage', array (
            'required' => 'y',
        ));
        consider('queueTask', 'stageState', 'state', array (
            'required' => 'y',
        ));
        section('queueTask', ['groupBy' => 'stageState']);
        m('QueueTask')->batch(function($r){$r->setStageState(); $r->save();});
        die('xx');
    }
    public function l10nAction() {
        enumset('field', 'l10n', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Выключена'));
        enumset('field', 'l10n', 'qy', array('title' => '<span class="i-color-box" style="background: lightgray; border: 3px solid blue;"></span>В очереди на включение'))->move(1);
        enumset('field', 'l10n', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Включена'));
        enumset('field', 'l10n', 'qn', array('title' => '<span class="i-color-box" style="background: blue; border: 3px solid lightgray;"></span>В очереди на выключение'));

        entity('lang', array (
            'title' => 'Язык',
            'system' => 'y',
        ));
        field('lang', 'title', array (
            'title' => 'Наименование',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'mode' => 'required',
        ));
        field('lang', 'alias', array (
            'title' => 'Ключ',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'mode' => 'required',
        ));
        field('lang', 'toggle', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'y',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('lang', 'toggle', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Включен'));
        enumset('lang', 'toggle', 'n', array('title' => '<span class="i-color-box" style="background: red;"></span>Выключен'));
        field('lang', 'state', array (
            'title' => 'Состояние',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'noth',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('lang', 'state', 'noth', array('title' => 'Ничего'));
        enumset('lang', 'state', 'smth', array('title' => 'Чтото'));
        field('lang', 'admin', array (
            'title' => 'Админка',
            'elementId' => 'span',
        ));
        field('lang', 'adminSystem', array (
            'title' => 'Система',
            'elementId' => 'span',
        ));
        field('lang', 'adminSystemUi', array (
            'title' => 'Интерфейс',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('lang', 'adminSystemUi', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Выключен'));
        enumset('lang', 'adminSystemUi', 'qy', array('title' => '<span class="i-color-box" style="background: lightgray; border: 3px solid blue;"></span>В очереди на включение'));
        enumset('lang', 'adminSystemUi', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Включен'));
        enumset('lang', 'adminSystemUi', 'qn', array('title' => '<span class="i-color-box" style="background: blue; border: 3px solid lightgray;"></span>В очереди на выключение'));
        field('lang', 'adminSystemConst', array (
            'title' => 'Константы',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('lang', 'adminSystemConst', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Выключен'));
        enumset('lang', 'adminSystemConst', 'qy', array('title' => '<span class="i-color-box" style="background: lightgray; border: 3px solid blue;"></span>В очереди на включение'));
        enumset('lang', 'adminSystemConst', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Включен'));
        enumset('lang', 'adminSystemConst', 'qn', array('title' => '<span class="i-color-box" style="background: blue; border: 3px solid lightgray;"></span>В очереди на выключение'));
        field('lang', 'adminCustom', array (
            'title' => 'Проект',
            'elementId' => 'span',
        ));
        field('lang', 'adminCustomUi', array (
            'title' => 'Интерфейс',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('lang', 'adminCustomUi', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Выключен'));
        enumset('lang', 'adminCustomUi', 'qy', array('title' => '<span class="i-color-box" style="background: lightgray; border: 3px solid blue;"></span>В очереди на включение'));
        enumset('lang', 'adminCustomUi', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Включен'));
        enumset('lang', 'adminCustomUi', 'qn', array('title' => '<span class="i-color-box" style="background: blue; border: 3px solid lightgray;"></span>В очереди на выключение'));
        field('lang', 'adminCustomConst', array (
            'title' => 'Константы',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('lang', 'adminCustomConst', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Выключен'));
        enumset('lang', 'adminCustomConst', 'qy', array('title' => '<span class="i-color-box" style="background: lightgray; border: 3px solid blue;"></span>В очереди на включение'));
        enumset('lang', 'adminCustomConst', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Включен'));
        enumset('lang', 'adminCustomConst', 'qn', array('title' => '<span class="i-color-box" style="background: blue; border: 3px solid lightgray;"></span>В очереди на выключение'));
        field('lang', 'adminCustomData', array (
            'title' => 'Данные',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('lang', 'adminCustomData', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Выключен'));
        enumset('lang', 'adminCustomData', 'qy', array('title' => '<span class="i-color-box" style="background: lightgray; border: 3px solid blue;"></span>В очереди на включение'));
        enumset('lang', 'adminCustomData', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Включен'));
        enumset('lang', 'adminCustomData', 'qn', array('title' => '<span class="i-color-box" style="background: blue; border: 3px solid lightgray;"></span>В очереди на выключение'));
        field('lang', 'adminCustomTmpl', array (
            'title' => 'Шаблоны',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('lang', 'adminCustomTmpl', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Выключен'));
        enumset('lang', 'adminCustomTmpl', 'qy', array('title' => '<span class="i-color-box" style="background: lightgray; border: 3px solid blue;"></span>В очереди на включение'));
        enumset('lang', 'adminCustomTmpl', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Включен'));
        enumset('lang', 'adminCustomTmpl', 'qn', array('title' => '<span class="i-color-box" style="background: blue; border: 3px solid lightgray;"></span>В очереди на выключение'));
        field('lang', 'move', array (
            'title' => 'Порядок',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'move',
            'defaultValue' => '0',
        ));
        entity('lang', array('titleFieldId' => 'title'));
        section('lang', array (
            'sectionId' => 'configuration',
            'entityId' => 'lang',
            'title' => 'Языки',
            'type' => 's',
            'groupBy' => 'state',
            'roleIds' => '1',
            'multiSelect' => '1',
        ))->nested('grid')->delete();
        section2action('lang','index', array('profileIds' => '1'));
        section2action('lang','form', array('profileIds' => '1'));
        section2action('lang','save', array('profileIds' => '1'));
        section2action('lang','delete', array('profileIds' => '1'));
        section2action('lang','up', array('profileIds' => '1'));
        section2action('lang','down', array('profileIds' => '1'));
        action('dict', array('title' => 'Доступные языки', 'rowRequired' => 'n', 'type' => 's'));
        action('run', array('title' => 'Запустить', 'rowRequired' => 'y', 'type' => 's'));
        section2action('lang','dict', array('profileIds' => '1'));
        grid('lang', 'title', true);
        grid('lang', 'alias', true);
        grid('lang', 'admin', true);
        grid('lang', 'toggle', array('gridId' => 'admin'));
        grid('lang', 'adminSystem', array('gridId' => 'admin'));
        grid('lang', 'adminSystemUi', array('gridId' => 'adminSystem'));
        grid('lang', 'adminSystemConst', array('gridId' => 'adminSystem'));
        grid('lang', 'adminCustom', array('gridId' => 'admin'));
        grid('lang', 'adminCustomUi', array('gridId' => 'adminCustom'));
        grid('lang', 'adminCustomConst', array('gridId' => 'adminCustom'));
        grid('lang', 'adminCustomData', array('gridId' => 'adminCustom'));
        grid('lang', 'adminCustomTmpl', array('gridId' => 'adminCustom'));
        grid('lang', 'move', true);
        filter('lang', 'state', true);
        filter('lang', 'toggle', true);
        entity('queueTask', array (
            'title' => 'Очередь задач',
            'system' => 'y',
        ));
        field('queueTask', 'title', array (
            'title' => 'Задача',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'mode' => 'required',
        ));
        field('queueTask', 'datetime', array (
            'title' => 'Создана',
            'columnTypeId' => 'DATETIME',
            'elementId' => 'datetime',
            'defaultValue' => '<?=date(\'Y-m-d H:i:s\')?>',
            'mode' => 'readonly',
        ));
        field('queueTask', 'params', array (
            'title' => 'Параметры',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
        ));
        field('queueTask', 'proc', array (
            'title' => 'Процесс',
            'elementId' => 'span',
        ));
        field('queueTask', 'procSince', array (
            'title' => 'Начат',
            'columnTypeId' => 'DATETIME',
            'elementId' => 'datetime',
            'defaultValue' => '0000-00-00 00:00:00',
        ));
        field('queueTask', 'procID', array (
            'title' => 'PID',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'mode' => 'readonly',
        ));
        field('queueTask', 'stage', array (
            'title' => 'Этап',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'count',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('queueTask', 'stage', 'count', array('title' => 'Оценка масштабов'));
        enumset('queueTask', 'stage', 'items', array('title' => 'Создание очереди'));
        enumset('queueTask', 'stage', 'queue', array('title' => 'Процессинг очереди'));
        enumset('queueTask', 'stage', 'apply', array('title' => 'Применение результатов'));
        field('queueTask', 'state', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('queueTask', 'state', 'waiting', array('title' => 'Ожидание'));
        enumset('queueTask', 'state', 'progress', array('title' => 'В работе'));
        enumset('queueTask', 'state', 'finished', array('title' => 'Завершено'));
        field('queueTask', 'chunk', array (
            'title' => 'Сегменты',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
        ));
        field('queueTask', 'count', array (
            'title' => 'Оценка',
            'elementId' => 'span',
        ));
        field('queueTask', 'countState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueTask', 'countState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueTask', 'countState', 'progress', array('title' => 'В работе'));
        enumset('queueTask', 'countState', 'finished', array('title' => 'Завершено'));
        field('queueTask', 'countSize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'mode' => 'readonly',
        ));
        field('queueTask', 'items', array (
            'title' => 'Создание',
            'elementId' => 'span',
        ));
        field('queueTask', 'itemsState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueTask', 'itemsState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueTask', 'itemsState', 'progress', array('title' => 'В работе'));
        enumset('queueTask', 'itemsState', 'finished', array('title' => 'Завершено'));
        field('queueTask', 'itemsSize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'mode' => 'readonly',
        ));
        field('queueTask', 'itemsBytes', array (
            'title' => 'Байт',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
        ));
        field('queueTask', 'queue', array (
            'title' => 'Процессинг',
            'elementId' => 'span',
        ));
        field('queueTask', 'queueState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueTask', 'queueState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueTask', 'queueState', 'progress', array('title' => 'В работе'));
        enumset('queueTask', 'queueState', 'finished', array('title' => 'Завершено'));
        enumset('queueTask', 'queueState', 'noneed', array('title' => 'Не требуется'));
        field('queueTask', 'queueSize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
        ));
        field('queueTask', 'apply', array (
            'title' => 'Применение',
            'elementId' => 'span',
        ));
        field('queueTask', 'applyState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueTask', 'applyState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueTask', 'applyState', 'progress', array('title' => 'В работе'));
        enumset('queueTask', 'applyState', 'finished', array('title' => 'Завершено'));
        field('queueTask', 'applySize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'mode' => 'readonly',
        ));
        entity('queueTask', array('titleFieldId' => 'title'));
        entity('queueChunk', array (
            'title' => 'Сегмент очереди',
            'system' => 'y',
        ));
        field('queueChunk', 'queueTaskId', array (
            'title' => 'Очередь задач',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'relation' => 'queueTask',
            'storeRelationAbility' => 'one',
        ));
        field('queueChunk', 'location', array (
            'title' => 'Расположение',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
        ));
        field('queueChunk', 'where', array (
            'title' => 'Условие выборки',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
        ));
        field('queueChunk', 'count', array (
            'title' => 'Оценка',
            'elementId' => 'span',
        ));
        field('queueChunk', 'countState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueChunk', 'countState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueChunk', 'countState', 'progress', array('title' => 'В работе'));
        enumset('queueChunk', 'countState', 'finished', array('title' => 'Завершено'));
        field('queueChunk', 'countSize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'mode' => 'readonly',
        ));
        field('queueChunk', 'items', array (
            'title' => 'Создание',
            'elementId' => 'span',
        ));
        field('queueChunk', 'itemsState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueChunk', 'itemsState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueChunk', 'itemsState', 'progress', array('title' => 'В работе'));
        enumset('queueChunk', 'itemsState', 'finished', array('title' => 'Завершено'));
        field('queueChunk', 'itemsSize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'mode' => 'readonly',
        ));
        field('queueChunk', 'queue', array (
            'title' => 'Процессинг',
            'elementId' => 'span',
        ));
        field('queueChunk', 'queueState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueChunk', 'queueState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueChunk', 'queueState', 'progress', array('title' => 'В работе'));
        enumset('queueChunk', 'queueState', 'finished', array('title' => 'Завершено'));
        enumset('queueChunk', 'queueState', 'noneed', array('title' => 'Не требуется'));
        field('queueChunk', 'queueSize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
        ));
        field('queueChunk', 'apply', array (
            'title' => 'Применение',
            'elementId' => 'span',
        ));
        field('queueChunk', 'applyState', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'waiting',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        enumset('queueChunk', 'applyState', 'waiting', array('title' => 'Ожидание'));
        enumset('queueChunk', 'applyState', 'progress', array('title' => 'В работе'));
        enumset('queueChunk', 'applyState', 'finished', array('title' => 'Завершено'));
        field('queueChunk', 'applySize', array (
            'title' => 'Размер',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'mode' => 'readonly',
        ));
        entity('queueChunk', array (
        ));
        entity('queueItem', array (
            'title' => 'Элемент очереди',
            'system' => 'y',
        ));
        field('queueItem', 'queueTaskId', array (
            'title' => 'Очередь',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'number',
            'defaultValue' => '0',
            'relation' => 'queueTask',
            'storeRelationAbility' => 'one',
            'mode' => 'readonly',
        ));
        field('queueItem', 'queueChunkId', array (
            'title' => 'Сегмент',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'relation' => 'queueChunk',
            'storeRelationAbility' => 'one',
        ));
        field('queueItem', 'target', array (
            'title' => 'Таргет',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'string',
            'mode' => 'readonly',
        ));
        field('queueItem', 'value', array (
            'title' => 'Значение',
            'columnTypeId' => 'TEXT',
            'elementId' => 'string',
            'mode' => 'readonly',
        ));
        field('queueItem', 'result', array (
            'title' => 'Результат',
            'columnTypeId' => 'TEXT',
            'elementId' => 'textarea',
        ));
        field('queueItem', 'stage', array (
            'title' => 'Статус',
            'columnTypeId' => 'ENUM',
            'elementId' => 'radio',
            'defaultValue' => 'items',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('queueItem', 'stage', 'items', array('title' => 'Добавлен'));
        enumset('queueItem', 'stage', 'queue', array('title' => 'Обработан'));
        enumset('queueItem', 'stage', 'apply', array('title' => 'Применен'));
        section('queueTask', array (
            'sectionId' => 'configuration',
            'entityId' => 'queueTask',
            'title' => 'Очереди задач',
            'defaultSortField' => 'datetime',
            'defaultSortDirection' => 'DESC',
            'disableAdd' => '1',
            'type' => 's',
            'roleIds' => '1',
        ))->nested('grid')->delete();
        section2action('queueTask','index', array('profileIds' => '1'));
        section2action('queueTask','form', array('profileIds' => '1'));
        section2action('queueTask','delete', array('profileIds' => '1'));
        section2action('queueTask','run', array('profileIds' => '1'));
        grid('queueTask', 'datetime', true);
        grid('queueTask', 'title', true);
        grid('queueTask', 'params', true);
        grid('queueTask', 'stage', array('toggle' => 'h'));
        grid('queueTask', 'state', array('toggle' => 'h'));
        grid('queueTask', 'chunk', true);
        grid('queueTask', 'proc', true);
        grid('queueTask', 'procID', array('gridId' => 'proc'));
        grid('queueTask', 'procSince', array('gridId' => 'proc'));
        grid('queueTask', 'count', true);
        grid('queueTask', 'countState', array('gridId' => 'count'));
        grid('queueTask', 'countSize', array('gridId' => 'count'));
        grid('queueTask', 'items', true);
        grid('queueTask', 'itemsState', array('gridId' => 'items'));
        grid('queueTask', 'itemsSize', array('gridId' => 'items'));
        grid('queueTask', 'itemsBytes', array('gridId' => 'items'));
        grid('queueTask', 'queue', true);
        grid('queueTask', 'queueState', array('gridId' => 'queue'));
        grid('queueTask', 'queueSize', array('gridId' => 'queue'));
        grid('queueTask', 'apply', true);
        grid('queueTask', 'applyState', array('gridId' => 'apply'));
        grid('queueTask', 'applySize', array('gridId' => 'apply'));
        section('queueChunk', array (
            'sectionId' => 'queueTask',
            'entityId' => 'queueChunk',
            'title' => 'Сегменты очереди',
            'disableAdd' => '1',
            'type' => 's',
            'rowsetSeparate' => 'no',
            'roleIds' => '1',
            'rownumberer' => '1',
        ))->nested('grid')->delete();
        section2action('queueChunk','index', array('profileIds' => '1'));
        section2action('queueChunk','form', array('profileIds' => '1'));
        grid('queueChunk', 'entityId', array('toggle' => 'n'));
        grid('queueChunk', 'fieldId', array('toggle' => 'n'));
        grid('queueChunk', 'location', true);
        grid('queueChunk', 'where', true);
        grid('queueChunk', 'count', true);
        grid('queueChunk', 'countState', array('gridId' => 'count'));
        grid('queueChunk', 'countSize', array('gridId' => 'count', 'summaryType' => 'sum'));
        grid('queueChunk', 'items', true);
        grid('queueChunk', 'itemsState', array('gridId' => 'items'));
        grid('queueChunk', 'itemsSize', array('gridId' => 'items', 'summaryType' => 'sum'));
        grid('queueChunk', 'queue', true);
        grid('queueChunk', 'queueState', array('gridId' => 'queue'));
        grid('queueChunk', 'queueSize', array('gridId' => 'queue', 'summaryType' => 'sum'));
        grid('queueChunk', 'apply', true);
        grid('queueChunk', 'applyState', array('gridId' => 'apply'));
        grid('queueChunk', 'applySize', array('gridId' => 'apply', 'summaryType' => 'sum'));
        section('queueItem', array (
            'sectionId' => 'queueChunk',
            'entityId' => 'queueItem',
            'title' => 'Элементы очереди',
            'disableAdd' => '1',
            'type' => 's',
            'roleIds' => '1',
        ))->nested('grid')->delete();
        section2action('queueItem','index', array('profileIds' => '1'));
        section2action('queueItem','save', array('profileIds' => '1'));
        grid('queueItem', 'target', true);
        grid('queueItem', 'value', true);
        grid('queueItem', 'result', array(  'editor' => 1,
        ));
        grid('queueItem', 'stage', true);
        filter('queueItem', 'stage', true);
        die('xx');
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
}
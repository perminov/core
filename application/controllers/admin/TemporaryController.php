<?php
/**
 * This is a temporary controller, that used for adjusting database-stored system features of Indi Engine.
 * Methods declared within this class, will be deleted once all database-stored system features will be adjusted
 * on all projects, that run on Indi Engine
 */
class Admin_TemporaryController extends Indi_Controller {

    public function satelliteAction() {

        field('consider', 'required', array (
            'title' => 'Обязательное',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'n',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('consider', 'required', 'y', array('title' => '<span class="i-color-box" style="background: blue;"></span>Да'));
        enumset('consider', 'required', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Нет'));
        grid('consider','required', array (
            'alterTitle' => '[ ! ]',
            'tooltip' => 'Обязательное',
        ));
        $connector = field('consider', 'connector', array (
            'title' => 'Коннектор',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'defaultValue' => '0',
            'relation' => 'field',
            'storeRelationAbility' => 'one',
        ));

        grid('consider', 'connector', true);
        if (!Indi::model('Consider')->fetchRow(['`fieldId` = "' . $connector->id . '"']))
            Indi::model('Consider')->createRow([
                'entityId' => entity('consider')->id,
                'fieldId' => $connector->id,
                'consider' => field('consider', 'fieldId')->id,
                'foreign' => field('field', 'relation')->id,
                'required' => 'y'
            ], true)->save();
        die('ok');
    }

    /**
     * Convert disabledFields-feature to alteredFields-feature
     */
    public function alteredFieldsAction() {
        die('disabled');
        // If 'disabledFields' entity exists, and 'displayInForm' field exists in it
        if (field('disabledField', 'displayInForm')) {

            // Update `showInForm` field's inner props
            field('disabledField', 'displayInForm', array(
                'title' => 'Режим', 'alias' => 'mode', 'storeRelationAbility' => 'one',
                'elementId' => 'combo', 'columnTypeId' => 'ENUM',
            ));

            // Update existing possible values
            enumset('disabledField', 'mode', '0', array('alias' => 'hidden', 'title' => 'Скрытое', 'color' => 'url(/i/admin/field/hidden.png)'));
            enumset('disabledField', 'mode', '1', array('alias' => 'readonly', 'title' => 'Только чтение', 'color' => 'url(/i/admin/field/readonly.png)'));

            // Append new possible values
            foreach (array('inherit' => 'Без изменений', 'regular' => 'Обычное', 'required' => 'Обязательное') as $alias => $title)
                enumset('disabledField', 'mode', $alias, array('title' => $title, 'color' => 'url(/i/admin/field/' . $alias . '.png)'))
                    ->move(2);

            // Other things
            enumset('disabledField', 'mode', 'hidden')->move(-1);
            field('disabledField', 'mode', array('defaultValue' => 'inherit'));
            field('disabledField', 'fieldId', array('title' => 'Поле'));
            field('disabledField', 'mode')->move(1);
            entity('disabledField')->assign(array('title' => 'Поле, измененное в рамках раздела'))->save();
            section('disabledFields', array('title' => 'Измененные поля', 'alias' => 'alteredFields'));
            field('disabledField', 'alter', array('title' => 'Изменить свойства поля', 'elementId' => 'span'));
            field('disabledField', 'rename', array('title' => 'Наименование', 'elementId' => 'string', 'columnTypeId' => 'VARCHAR(255)'));
            field('disabledField', 'defaultValue')->move(-5);
            field('disabledField', 'mode')->move(-5);
            grid('alteredFields', 'rename', array('editor' => 1))->move(2);
            grid('alteredFields', 'mode')->move(1);
            grid('alteredFields', 'impact', array('editor' => 1));
            grid('alteredFields', 'profileIds', array('editor' => 1));
        }

        // If entity, having table 'disabledField' exists - rename it
        if (entity('disabledField')) entity('disabledField', array('table' => 'alteredField'));

        //
        die('ok');
    }

    public function noticesAction() {
        die('disabled');
        // If notices system is already is in it's last version - return
        if (!(!Indi::model('NoticeGetter', true) || !Indi::model('NoticeGetter')->fields('criteriaRelyOn'))) die('already ok');

        // Remove previous version of notices, if exists
        if (entity('noticeGetter')) entity('noticeGetter')->delete();
        if (entity('notice')) entity('notice')->delete();

        // Create `notice` entity
        if (true) {
            entity('notice', array (
                'title' => 'Уведомление',
                'system' => 'y',
            ));
            field('notice', 'title', array (
                'title' => 'Наименование',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
                'mode' => 'required',
            ));
            field('notice', 'entityId', array (
                'title' => 'Сущность',
                'columnTypeId' => 'INT(11)',
                'elementId' => 'combo',
                'relation' => 'entity',
                'storeRelationAbility' => 'one',
                'mode' => 'required',
            ));
            field('notice', 'event', array (
                'title' => 'Событие / PHP',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
            ));
            field('notice', 'profileId', array (
                'title' => 'Получатели',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'combo',
                'relation' => 'profile',
                'storeRelationAbility' => 'many',
                'mode' => 'required',
            ));
            field('notice', 'toggle', array (
                'title' => 'Статус',
                'columnTypeId' => 'ENUM',
                'elementId' => 'combo',
                'defaultValue' => 'y',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
            ));
            enumset('notice', 'toggle', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Включено'));
            enumset('notice', 'toggle', 'n', array('title' => '<span class="i-color-box" style="background: red;"></span>Выключено'));
            field('notice', 'qty', array (
                'title' => 'Счетчик',
                'elementId' => 'span',
            ));
            field('notice', 'qtySql', array (
                'title' => 'Отображение / SQL',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
                'mode' => 'required',
            ));
            field('notice', 'qtyDiffRelyOn', array (
                'title' => 'Направление изменения',
                'columnTypeId' => 'ENUM',
                'elementId' => 'combo',
                'defaultValue' => 'event',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
            ));
            enumset('notice', 'qtyDiffRelyOn', 'event', array('title' => 'Одинаковое для всех получателей'));
            enumset('notice', 'qtyDiffRelyOn', 'getter', array('title' => 'Неодинаковое, зависит от получателя'));
            field('notice', 'sectionId', array (
                'title' => 'Пункты меню',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'combo',
                'relation' => 'section',
                'satellite' => 'entityId',
                'dependency' => 'с',
                'storeRelationAbility' => 'many',
                'filter' => 'FIND_IN_SET(`sectionId`, "<?=Indi::model(\'Section\')->fetchAll(\'`sectionId` = "0"\')->column(\'id\', true)?>")',
            ));
            field('notice', 'bg', array (
                'title' => 'Цвет фона',
                'columnTypeId' => 'VARCHAR(10)',
                'elementId' => 'color',
                'defaultValue' => '212#d9e5f3',
            ));
            field('notice', 'fg', array (
                'title' => 'Цвет текста',
                'columnTypeId' => 'VARCHAR(10)',
                'elementId' => 'color',
                'defaultValue' => '216#044099',
            ));
            field('notice', 'tooltip', array (
                'title' => 'Подсказка',
                'columnTypeId' => 'TEXT',
                'elementId' => 'textarea',
            ));
            field('notice', 'tpl', array (
                'title' => 'Сообщение',
                'elementId' => 'span',
            ));
            field('notice', 'tplFor', array (
                'title' => 'Назначение',
                'columnTypeId' => 'ENUM',
                'elementId' => 'combo',
                'defaultValue' => 'inc',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
            ));
            enumset('notice', 'tplFor', 'inc', array('title' => 'Увеличение'));
            enumset('notice', 'tplFor', 'dec', array('title' => 'Уменьшение'));
            enumset('notice', 'tplFor', 'evt', array('title' => 'Изменение'));
            field('notice', 'tplIncSubj', array (
                'title' => 'Заголовок',
                'columnTypeId' => 'TEXT',
                'elementId' => 'string',
            ));
            field('notice', 'tplIncBody', array (
                'title' => 'Текст',
                'columnTypeId' => 'TEXT',
                'elementId' => 'textarea',
            ));
            field('notice', 'tplDecSubj', array (
                'title' => 'Заголовок',
                'columnTypeId' => 'TEXT',
                'elementId' => 'string',
            ));
            field('notice', 'tplDecBody', array (
                'title' => 'Текст',
                'columnTypeId' => 'TEXT',
                'elementId' => 'textarea',
            ));
            field('notice', 'tplEvtSubj', array (
                'title' => 'Заголовок',
                'columnTypeId' => 'TEXT',
                'elementId' => 'string',
            ));
            field('notice', 'tplEvtBody', array (
                'title' => 'Сообщение',
                'columnTypeId' => 'TEXT',
                'elementId' => 'textarea',
            ));
            entity('notice', array('titleFieldId' => 'title'));
        }

        // Create `noticeGetter` entity
        if (true) {
            entity('noticeGetter', array (
                'title' => 'Получатель уведомлений',
                'system' => 'y',
            ));
            field('noticeGetter', 'noticeId', array (
                'title' => 'Уведомление',
                'columnTypeId' => 'INT(11)',
                'elementId' => 'combo',
                'relation' => 'notice',
                'storeRelationAbility' => 'one',
                'mode' => 'readonly',
            ));
            field('noticeGetter', 'profileId', array (
                'title' => 'Роль',
                'columnTypeId' => 'INT(11)',
                'elementId' => 'combo',
                'relation' => 'profile',
                'storeRelationAbility' => 'one',
                'mode' => 'readonly',
            ));
            field('noticeGetter', 'criteriaRelyOn', array (
                'title' => 'Критерий',
                'columnTypeId' => 'ENUM',
                'elementId' => 'radio',
                'defaultValue' => 'event',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
            ));
            enumset('noticeGetter', 'criteriaRelyOn', 'event', array('title' => 'Общий'));
            enumset('noticeGetter', 'criteriaRelyOn', 'getter', array('title' => 'Раздельный'));
            field('noticeGetter', 'criteriaEvt', array (
                'title' => 'Общий',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
            ));
            field('noticeGetter', 'criteriaInc', array (
                'title' => 'Для увеличения',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
            ));
            field('noticeGetter', 'criteriaDec', array (
                'title' => 'Для уменьшения',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
            ));
            field('noticeGetter', 'title', array (
                'title' => 'Ауто титле',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
                'mode' => 'hidden',
            ));
            field('noticeGetter', 'email', array (
                'title' => 'Дублирование на почту',
                'columnTypeId' => 'ENUM',
                'elementId' => 'combo',
                'defaultValue' => 'n',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
            ));
            enumset('noticeGetter', 'email', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Нет'));
            enumset('noticeGetter', 'email', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Да'));
            field('noticeGetter', 'vk', array (
                'title' => 'Дублирование в ВК',
                'columnTypeId' => 'ENUM',
                'elementId' => 'combo',
                'defaultValue' => 'n',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
            ));
            enumset('noticeGetter', 'vk', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Нет'));
            enumset('noticeGetter', 'vk', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Да'));
            field('noticeGetter', 'sms', array (
                'title' => 'Дублирование по SMS',
                'columnTypeId' => 'ENUM',
                'elementId' => 'combo',
                'defaultValue' => 'n',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
            ));
            enumset('noticeGetter', 'sms', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Нет'));
            enumset('noticeGetter', 'sms', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Да'));
            field('noticeGetter', 'criteria', array (
                'title' => 'Критерий',
                'columnTypeId' => 'VARCHAR(255)',
                'elementId' => 'string',
                'mode' => 'hidden',
            ));
            field('noticeGetter', 'mail', array (
                'title' => 'Дублирование на почту',
                'columnTypeId' => 'ENUM',
                'elementId' => 'combo',
                'defaultValue' => 'n',
                'relation' => 'enumset',
                'storeRelationAbility' => 'one',
                'mode' => 'hidden',
            ));
            enumset('noticeGetter', 'mail', 'n', array('title' => '<span class="i-color-box" style="background: lightgray;"></span>Нет'));
            enumset('noticeGetter', 'mail', 'y', array('title' => '<span class="i-color-box" style="background: lime;"></span>Да'));
            entity('noticeGetter', array('titleFieldId' => 'profileId'));
        }

        // Create `notices` section
        if (true) {
            section('notices', array (
                'sectionId' => 'configuration',
                'entityId' => 'notice',
                'title' => 'Уведомления',
                'defaultSortField' => 'title',
                'type' => 's',
            ))->nested('grid')->delete();
            section2action('notices','index', array('profileIds' => 1));
            section2action('notices','form', array('profileIds' => 1));
            section2action('notices','save', array('profileIds' => 1));
            section2action('notices','delete', array('profileIds' => 1));
            section2action('notices','toggle', array('profileIds' => 1));
            grid('notices','title', true);
            grid('notices','entityId', true);
            grid('notices','profileId', true);
            grid('notices','toggle', true);
            grid('notices','qty', true);
            grid('notices','qtySql', array('gridId' => 'qty'));
            grid('notices','event', array('gridId' => 'qty'));
            grid('notices','sectionId', array('gridId' => 'qty'));
            grid('notices','bg', array('gridId' => 'qty'));
            grid('notices','fg', array('gridId' => 'qty'));
        }

        // Create `noticeGetters` action
        if (true) {
            section('noticeGetters', array (
                'sectionId' => 'notices',
                'entityId' => 'noticeGetter',
                'title' => 'Получатели',
                'defaultSortField' => 'profileId',
                'type' => 's',
            ))->nested('grid')->delete();
            section2action('noticeGetters','index', array('profileIds' => 1));
            section2action('noticeGetters','form', array('profileIds' => 1));
            section2action('noticeGetters','save', array('profileIds' => 1));
            section2action('noticeGetters','delete', array('profileIds' => 1));
            grid('noticeGetters','profileId', true);
            grid('noticeGetters','criteriaEvt', true);
            grid('noticeGetters','email', array (
                'alterTitle' => 'Email',
                'tooltip' => 'Дублирование на почту',
            ));
            grid('noticeGetters','vk', array (
                'alterTitle' => 'VK',
                'tooltip' => 'Дублирование во ВКонтакте',
            ));
            grid('noticeGetters','sms', array (
                'alterTitle' => 'SMS',
                'tooltip' => 'Дублирование по SMS',
            ));
        }

        die('ok');
    }

    public function changelogAction() {

        // Create/update `year` entity
        entity('year', array('title' => 'Год', 'system' => 'y'));
        field('year', 'title', array('title' => 'Наименование', 'columnTypeId' => 'VARCHAR(255)', 'elementId' => 'string', 'mode' => 'required'));
        entity('year', array('titleFieldId' => 'title'));

        // Create/update `month` entity
        entity('month', array('title' => 'Месяц', 'system' => 'y'));
        field('month', 'yearId', array('title' => 'Год', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo', 'relation' => 'year', 'storeRelationAbility' => 'one', 'mode' => 'required'));
        field('month', 'month', array ('title' => 'Месяц', 'columnTypeId' => 'ENUM', 'elementId' => 'combo', 'defaultValue' => '01', 'relation' => 'enumset', 'storeRelationAbility' => 'one'));
        enumset('month', 'month', '01', array('title' => 'Январь'));
        enumset('month', 'month', '02', array('title' => 'Февраль'));
        enumset('month', 'month', '03', array('title' => 'Март'));
        enumset('month', 'month', '04', array('title' => 'Апрель'));
        enumset('month', 'month', '05', array('title' => 'Май'));
        enumset('month', 'month', '06', array('title' => 'Июнь'));
        enumset('month', 'month', '07', array('title' => 'Июль'));
        enumset('month', 'month', '08', array('title' => 'Август'));
        enumset('month', 'month', '09', array('title' => 'Сентябрь'));
        enumset('month', 'month', '10', array('title' => 'Октябрь'));
        enumset('month', 'month', '11', array('title' => 'Ноябрь'));
        enumset('month', 'month', '12', array('title' => 'Декабрь'));
        field('month', 'title', array('title' => 'Наименование', 'columnTypeId' => 'VARCHAR(255)', 'elementId' => 'string'));
        field('month', 'move', array('title' => 'Порядок', 'columnTypeId' => 'INT(11)', 'elementId' => 'move'));
        entity('month', array('titleFieldId' => 'title'));

        // Create/update `changeLog` entity
        entity('changeLog', array('title' => 'Корректировка', 'system' => 'y'));
        field('changeLog', 'entityId', array('title' => 'Сущность', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'entity', 'storeRelationAbility' => 'one', 'mode' => 'readonly'));
        field('changeLog', 'key', array('title' => 'Объект', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'satellite' => 'entityId', 'dependency' => 'e', 'storeRelationAbility' => 'one', 'mode' => 'readonly'));
        field('changeLog', 'fieldId', array('title' => 'Что изменено', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'field', 'satellite' => 'entityId', 'dependency' => 'с', 'storeRelationAbility' => 'one',
            'filter' => '`columnTypeId` != "0"', 'mode' => 'readonly'));
        field('changeLog', 'was', array('title' => 'Было', 'columnTypeId' => 'TEXT', 'elementId' => 'html', 'mode' => 'readonly'));
        field('changeLog', 'now', array('title' => 'Стало', 'columnTypeId' => 'TEXT', 'elementId' => 'html', 'mode' => 'readonly'));
        field('changeLog', 'datetime', array('title' => 'Когда', 'columnTypeId' => 'DATETIME', 'elementId' => 'datetime',
            'defaultValue' => '0000-00-00 00:00:00', 'mode' => 'readonly'));
        field('changeLog', 'monthId', array('title' => 'Месяц', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'month', 'storeRelationAbility' => 'one', 'mode' => 'readonly'));
        field('changeLog', 'changerType', array('title' => 'Тип автора', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'entity', 'storeRelationAbility' => 'one', 'mode' => 'readonly'));
        field('changeLog', 'changerId', array('title' => 'Автор', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'satellite' => 'changerType', 'dependency' => 'e', 'storeRelationAbility' => 'one', 'mode' => 'readonly'));
        field('changeLog', 'profileId', array('title' => 'Роль', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'profile', 'storeRelationAbility' => 'one', 'mode' => 'readonly'));
        entity('changeLog', array('titleFieldId' => 'datetime'));

        /**
         * Setup `monthId` for existing `changelog` entries (nasled, vkenguru)
         */

        // Create `year` entries
        foreach(Indi::db()->query('
            SELECT DISTINCT YEAR(`datetime`) FROM `changeLog`
        ')->fetchAll(PDO::FETCH_COLUMN) as $year)
            Year::o($year);

        // Create `month` entries
        foreach (Indi::db()->query('
            SELECT DISTINCT DATE_FORMAT(`datetime`, "%Y-%m") AS `Ym` FROM `changeLog` ORDER BY `Ym`
        ')->fetchAll(PDO::FETCH_COLUMN) as $Ym)
            $monthA[$Ym] = Month::o($Ym)->id;

        // Setup `monthId` for `changeLog` entries with zero-value in `monthId` col
        foreach (Indi::db()->query('
            SELECT `id`, DATE_FORMAT(`datetime`, "%Y-%m") AS `Ym` FROM `changeLog` WHERE `monthId` = "0"
        ')->fetchAll() as $_) Indi::db()->query('
            UPDATE `changeLog` SET `monthId` = "' . $monthA[$_['Ym']] . '" WHERE `id` = "' . $_['id'] . '"
        ');

        // Exit
        die('ok');
    }

    public function othersAction() {

        // Add `spaceScheme` and `spaceFields` fields into `entity` entity
        field('entity', 'spaceScheme', array (
            'title' => 'Паттерн комплекта календарных полей',
            'columnTypeId' => 'ENUM',
            'elementId' => 'combo',
            'defaultValue' => 'none',
            'relation' => 'enumset',
            'storeRelationAbility' => 'one',
        ));
        enumset('entity', 'spaceScheme', 'none', array('title' => 'Нет'));
        enumset('entity', 'spaceScheme', 'date', array('title' => 'DATE'));
        enumset('entity', 'spaceScheme', 'datetime', array('title' => 'DATETIME'));
        enumset('entity', 'spaceScheme', 'date-time', array('title' => 'DATE, TIME'));
        enumset('entity', 'spaceScheme', 'date-timeId', array('title' => 'DATE, timeId'));
        enumset('entity', 'spaceScheme', 'date-dayQty', array('title' => 'DATE, dayQty'));
        enumset('entity', 'spaceScheme', 'datetime-minuteQty', array('title' => 'DATETIME, minuteQty'));
        enumset('entity', 'spaceScheme', 'date-time-minuteQty', array('title' => 'DATE, TIME, minuteQty'));
        enumset('entity', 'spaceScheme', 'date-timeId-minuteQty', array('title' => 'DATE, timeId, minuteQty'));
        enumset('entity', 'spaceScheme', 'date-timespan', array('title' => 'DATE, hh:mm-hh:mm'));
        field('entity', 'spaceFields', array (
            'title' => 'Комплект календарных полей',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'combo',
            'relation' => 'field',
            'storeRelationAbility' => 'many',
            'filter' => '`entityId` = "<?=$this->id?>"',
        ));

        // Add `consider` entity
        entity('consider', array('title' => 'Зависимость', 'system' => 'y'));
        field('consider', 'entityId', array('title' => 'Сущность', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'entity', 'storeRelationAbility' => 'one', 'mode' => 'hidden'));
        field('consider', 'fieldId', array('title' => 'Поле', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'field', 'storeRelationAbility' => 'one', 'mode' => 'readonly'));
        field('consider', 'consider', array('title' => 'От какого поля зависит', 'columnTypeId' => 'INT(11)',
            'elementId' => 'combo', 'relation' => 'field', 'satellite' => 'fieldId', 'dependency' => 'с',
            'storeRelationAbility' => 'one', 'alternative' => 'entityId', 'filter' => '`id` != "<?=$this->fieldId?>" AND `columnTypeId` != "0"',
            'satellitealias' => 'entityId', 'mode' => 'required'));
        field('consider', 'foreign', array('title' => 'Поле по ключу', 'columnTypeId' => 'INT(11)', 'elementId' => 'combo',
            'relation' => 'field', 'satellite' => 'consider', 'dependency' => 'с', 'storeRelationAbility' => 'one', 'alternative' => 'relation'));
        field('consider', 'title', array('title' => 'Auto title', 'columnTypeId' => 'VARCHAR(255)', 'elementId' => 'string', 'mode' => 'hidden'));
        entity('consider', array('titleFieldId' => 'consider'));

        // Add 'consider' section
        $_ = section('consider', array('sectionId' => 'fields', 'entityId' => 'consider', 'title' => 'Зависимости', 'type' => 's'));
        if ($_->affected('id')) $_->nested('grid')->delete();
        section2action('consider','index', array('profileIds' => 1));
        section2action('consider','form', array('profileIds' => 1));
        section2action('consider','save', array('profileIds' => 1));
        section2action('consider','delete', array('profileIds' => 1));
        grid('consider','consider', true);
        grid('consider','foreign', true);

        // Add menu-expand fields
        $_ = field('section', 'expand', array('title' => 'Разворачивать пункт меню', 'columnTypeId' => 'ENUM', 'elementId' => 'radio',
            'defaultValue' => 'all', 'relation' => 'enumset', 'storeRelationAbility' => 'one'));
        if ($_->affected('id')) $_->move(19)->move(-2);
        enumset('section', 'expand', 'all', array('title' => 'Всем пользователям'));
        enumset('section', 'expand', 'only', array('title' => 'Только выбранным'));
        enumset('section', 'expand', 'except', array('title' => 'Всем кроме выбранных'));
        enumset('section', 'expand', 'none', array('title' => 'Никому'));
        $_ = field('section', 'expandRoles', array('title' => 'Выбранные', 'columnTypeId' => 'VARCHAR(255)', 'elementId' => 'combo',
            'relation' => 'profile', 'storeRelationAbility' => 'many'));
        if ($_->affected('id')) $_->move(19)->move(-3);

        // Exit
        die('ok');
    }

    public function sectionRolesAction() {

        field('section', 'roleIds', array (
            'title' => 'Доступ',
            'columnTypeId' => 'VARCHAR(255)',
            'elementId' => 'combo',
            'relation' => 'profile',
            'storeRelationAbility' => 'many',
            'mode' => 'hidden',
        ));
        field('section', 'entityId', array ('title' => 'Сущность'));
        filter('sections', 'roleIds', true);

        $sectionRs = Indi::model('Section')->fetchAll();
        $sectionRs->nested('section2action');
        foreach ($sectionRs as $sectionR) {
            $sectionR->roleIds = '';
            foreach ($sectionR->nested('section2action') as $section2actionR)
                foreach (ar($section2actionR->profileIds) as $roleId)
                    $sectionR->push('roleIds', $roleId);
            $sectionR->save();
        }
        enumset('grid', 'toggle', 'h', array('title' => 'Скрыт', 'color' => 'lightgray'));
        action('goto', array('title' => 'Перейти', 'type' => 's'));
        die('ok');
    }
}
<?php
/**
 * This is a temporary controller, that used for adjusting database-stored system features of Indi Engine.
 * Methods declared within this class, will be deleted once all database-stored system features will be adjusted
 * on all projects, that run on Indi Engine
 */
class Admin_TemporaryController extends Indi_Controller {

    /**
     * Convert disabledFields-feature to alteredFields-feature
     */
    public function alteredFieldsAction() {

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
}
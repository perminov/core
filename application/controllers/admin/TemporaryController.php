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
        //die('disabled');
        Indi::db()->query('DROP TABLE IF EXISTS `notice`');
        Indi::db()->query('DROP TABLE IF EXISTS `noticeGetter`');

        $entityR_notice = Indi::model('Entity')->createRow(array(
            'title' => 'Уведомление',
            'table' => 'notice',
            'system' => 'y'
        ), true);
        $entityR_notice->save();

        $elementRs = Indi::model('Element')->fetchAll();
        $columnTypeRs = Indi::model('ColumnType')->fetchAll();

        $fieldR_title = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Наименование',
            'alias' => 'title',
            'mode' => 'required',
            'elementId' => $elementRs->gb('string', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(255)', 'type')->id,
        ), true);
        $fieldR_title->save();

        $fieldR_entityId = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Сущность',
            'alias' => 'entityId',
            'mode' => 'required',
            'storeRelationAbility' => 'one',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('INT(11)', 'type')->id,
            'relation' => Indi::model('Entity')->id()
        ), true);
        $fieldR_entityId->save();

        $fieldR_profileId = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Получатели',
            'alias' => 'profileId',
            'mode' => 'required',
            'storeRelationAbility' => 'many',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(255)', 'type')->id,
            'relation' => Indi::model('Profile')->id()
        ), true);
        $fieldR_profileId->save();

        // Create field
        $fieldR_toggle = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Статус',
            'alias' => 'toggle',
            'storeRelationAbility' => 'one',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('ENUM', 'type')->id,
            'defaultValue' => 'y'
        ), true);

        // Save field
        $fieldR_toggle->save();

        // Get first enumset option (that was created automatically)
        $y = $fieldR_toggle->nested('enumset')->at(0);
        $y->title = '<span class="i-color-box" style="background: lime;"></span>Включено';
        $y->save();

        // Create one more enumset option within this field
        Indi::model('Enumset')->createRow(array(
            'fieldId' => $y->fieldId,
            'title' => '<span class="i-color-box" style="background: red;"></span>Выключено',
            'alias' => 'n'
        ), true)->save();

        $fieldR_match = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Счетчик',
            'alias' => 'match',
            'elementId' => $elementRs->gb('span', 'alias')->id,
        ), true);
        $fieldR_match->save();

        $fieldR_matchSql = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Отображение / SQL',
            'alias' => 'matchSql',
            'mode' => 'required',
            'elementId' => $elementRs->gb('string', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(255)', 'type')->id,
        ), true);
        $fieldR_matchSql->save();

        $fieldR_matchPhp = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Изменение / PHP',
            'alias' => 'matchPhp',
            'elementId' => $elementRs->gb('string', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(255)', 'type')->id,
        ), true);
        $fieldR_matchPhp->save();

        $fieldR_sectionId = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Пункты меню',
            'alias' => 'sectionId',
            'storeRelationAbility' => 'many',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(255)', 'type')->id,
            'relation' => Indi::model('Section')->id(),
            'filter' => 'FIND_IN_SET(`sectionId`, "<?=Indi::model(\'Section\')->fetchAll(\'`sectionId` = "0"\')->column(\'id\', true)?>")',
            'dependency' => 'с',
            'satellite' => $fieldR_entityId->id
        ), true);
        $fieldR_sectionId->save();

        $fieldR_bg = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Цвет фона',
            'alias' => 'bg',
            'elementId' => $elementRs->gb('color', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(10)', 'type')->id,
            'defaultValue' => '#d9e5f3'
        ), true);
        $fieldR_bg->save();

        $fieldR_bg = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Цвет текста',
            'alias' => 'fg',
            'elementId' => $elementRs->gb('color', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(10)', 'type')->id,
            'defaultValue' => '#044099'
        ), true);
        $fieldR_bg->save();

        $fieldR_tplUp = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Сообщение при увеличении счетчика',
            'alias' => 'tplUp',
            'elementId' => $elementRs->gb('span', 'alias')->id,
        ), true);
        $fieldR_tplUp->save();

        $fieldR_tplUpHeader = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Заголовок',
            'alias' => 'tplUpHeader',
            'elementId' => $elementRs->gb('string', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(255)', 'type')->id,
        ), true);
        $fieldR_tplUpHeader->save();

        $fieldR_tplUpBody = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'Текст',
            'alias' => 'tplUpBody',
            'elementId' => $elementRs->gb('textarea', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('TEXT', 'type')->id,
        ), true);
        $fieldR_tplUpBody->save();

        $fieldR_tplDown = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_notice->id,
            'title' => 'При уменьшении счетчика',
            'alias' => 'tplDown',
            'mode' => 'hidden',
            'elementId' => $elementRs->gb('textarea', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('TEXT', 'type')->id,
        ), true);
        $fieldR_tplDown->save();

        $sectionR_notices = Indi::model('Section')->createRow(array(
            'title' => 'Уведомления',
            'sectionId' => Indi::model('Section')->fetchRow('`alias` = "sections"')->sectionId,
            'alias' => 'notices',
            'type' => 's',
            'entityId' => $entityR_notice->id,
            'defaultSortField' => $fieldR_title->id
        ), true);
        $sectionR_notices->save();

        $entityR_noticeGetter = Indi::model('Entity')->createRow(array(
            'title' => 'Получатель уведомлений',
            'table' => 'noticeGetter',
            'system' => 'y'
        ), true);
        $entityR_noticeGetter->save();

        $fieldR_noticeId = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_noticeGetter->id,
            'title' => 'Уведомление',
            'alias' => 'noticeId',
            'mode' => 'readonly',
            'storeRelationAbility' => 'one',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('INT(11)', 'type')->id,
            'relation' => $entityR_notice->id
        ), true);
        $fieldR_noticeId->save();

        $fieldR_profileId = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_noticeGetter->id,
            'title' => 'Роль',
            'alias' => 'profileId',
            'mode' => 'readonly',
            'storeRelationAbility' => 'one',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('INT(11)', 'type')->id,
            'relation' => Indi::model('Profile')->id()
        ), true);
        $fieldR_profileId->save();

        $fieldR_criteria = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_noticeGetter->id,
            'title' => 'Критерий',
            'alias' => 'criteria',
            'elementId' => $elementRs->gb('string', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('VARCHAR(255)', 'type')->id,
        ), true);
        $fieldR_criteria->save();

        $entityR_noticeGetter->titleFieldId = $fieldR_profileId->id;
        $entityR_noticeGetter->save();

        // Create field
        $fieldR_mail = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_noticeGetter->id,
            'title' => 'Дублирование на почту',
            'alias' => 'mail',
            'storeRelationAbility' => 'one',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('ENUM', 'type')->id,
            'defaultValue' => 'n'
        ), true);

        // Save field
        $fieldR_mail->save();

        // Get first enumset option (that was created automatically)
        $n = $fieldR_mail->nested('enumset')->at(0);
        $n->title = '<span class="i-color-box" style="background: lightgray;"></span>Нет';
        $n->save();

        // Create one more enumset option within this field
        Indi::model('Enumset')->createRow(array(
            'fieldId' => $n->fieldId,
            'title' => '<span class="i-color-box" style="background: lime;"></span>Да',
            'alias' => 'y'
        ), true)->save();

        // Create field
        $fieldR_vk = Indi::model('Field')->createRow(array(
            'entityId' => $entityR_noticeGetter->id,
            'title' => 'Дублирование в ВК',
            'alias' => 'vk',
            'storeRelationAbility' => 'one',
            'elementId' => $elementRs->gb('combo', 'alias')->id,
            'columnTypeId' => $columnTypeRs->gb('ENUM', 'type')->id,
            'defaultValue' => 'n'
        ), true);

        // Save field
        $fieldR_vk->save();

        // Get first enumset option (that was created automatically)
        $n = $fieldR_vk->nested('enumset')->at(0);
        $n->title = '<span class="i-color-box" style="background: lightgray;"></span>Нет';
        $n->save();

        // Create one more enumset option within this field
        Indi::model('Enumset')->createRow(array(
            'fieldId' => $n->fieldId,
            'title' => '<span class="i-color-box" style="background: lime;"></span>Да',
            'alias' => 'y'
        ), true)->save();

        $sectionR_noticeGetters = Indi::model('Section')->createRow(array(
            'title' => 'Получатели',
            'sectionId' => $sectionR_notices->id,
            'alias' => 'noticeGetters',
            'type' => 's',
            'entityId' => $entityR_noticeGetter->id,
            'defaultSortField' => $fieldR_profileId->id
        ), true);
        $sectionR_noticeGetters->save();

        die('ok');
    }
}
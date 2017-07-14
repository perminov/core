<?php
/**
 * This is a temporary controller, that used for adjusting database-stored system features of Indi Engine.
 * Methods declared within this class, will be deleted once all database-stored system features will be adjusted
 * on all projects, that run on Indi Engine
 */
class Admin_TemporaryController extends Indi_Controller {
    public function titlesAction($project, $die = true) {

        // Add `titleFieldId` field within 'entity' entity, if there is no such a field yet
        if (!Indi::model('Entity')->fields('titleFieldId')) {

            $fieldR = Indi::model('Field')->createRow();
            $fieldR->entityId = Indi::model('Entity')->id();
            $fieldR->title = 'Заголовочное поле';
            $fieldR->alias = 'titleFieldId';
            $fieldR->storeRelationAbility = 'one';
            $fieldR->elementId = Indi::model('Element')->fetchRow('`alias` = "combo"')->id;
            $fieldR->columnTypeId = Indi::model('ColumnType')->fetchRow('`type` = "INT(11)"')->id;
            $fieldR->relation = Indi::model('Field')->id();
            $fieldR->filter = '`entityId` = "<?=$this->id?>" AND `columnTypeId` != "0"';
            $fieldR->save();
        }

        $titleFieldAliasA = array(
            'section2action' => 'actionId',
            'fsection2faction' => 'factionId',
            'disabledField' => 'fieldId',
            'param' => 'possibleParamId',
            'grid' => 'fieldId',
            'search' => 'fieldId'
        );

        $projectTitleFieldAliasA = array(
            'picneon' => array(
                'studiedCourse' => 'courseId',
                'indexCourse' => 'courseId',
                'lessonPractice' => 'taskId',
                'recommended' => 'recommended',
                'courseLesson' => 'lessonId'
            ),
            'ota' => array(
                'bannerShow' => 'datetime',
                'courseClick' => 'datetime',
                'courseUser' => 'userId',
                'pollAnswerVote' => 'datetime'
            ),
            'vkenguru' => array(
                'eventAnimator' => 'animatorId',
                'adjustment' => 'datetime'
            ),
            'profpole' => array(
                'rating' => 'datetime',
                'lpProduct' => 'productId'
            )
        );

        $entityRs = Indi::model('Entity')->fetchAll();
        foreach ($entityRs as $entityR) {

            // Если сущность системная или типовая
            if ($entityR->system == 'y' || $entityR->system == 'o') {

                // Если для нее есть hardcoded заголовочное поле - назначаем его
                if ($titleFieldAliasA[$entityR->table]) {
                    $entityR->titleFieldId = Indi::model($entityR->id)->fields($titleFieldAliasA[$entityR->table])->id;
                    $entityR->save();

                // Если его нет, но есть поле title - то назначаем его
                } else if ($titleFieldId = Indi::model($entityR->id)->fields('title')->id) {
                    $entityR->titleFieldId = $titleFieldId;
                    $entityR->save();
                }

            // Если сущность проектная
            } else if ($entityR->system == 'n') {

                // Если для нее есть hardcoded заголовочное поле - назначаем его
                if ($projectTitleFieldAliasA[$project][$entityR->table]) {
                    $entityR->titleFieldId = Indi::model($entityR->id)->fields($projectTitleFieldAliasA[$project][$entityR->table])->id;
                    $entityR->save();

                // Если его нет, но есть поле title - то назначаем его
                } else if ($titleFieldId = Indi::model($entityR->id)->fields('title')->id) {
                    $entityR->titleFieldId = $titleFieldId;
                    $entityR->save();
                }
            }

            // Удаляем старые _title
            if (Indi::model($entityR->id)->fields('_title')) Indi::model($entityR->id)->fields('_title')->delete();
        }

        if ($die) die('ok');
    }

    public function deprecatedAction($die = true){

        if ($rppIdFieldR = Indi::model('Fsection')->fields('rppId')) $rppIdFieldR->delete();
        $tableA = array(
            'joinFk', 'joinFkForDependentRowset', 'joinFkForIndependentRowset', 'dependentCount',
            'dependentCountForDependentRowset', 'dependentRowset', 'metaExclusion', 'rpp', 'seoDescription', 'seoTitle',
            'seoKeyword', 'fconfig', 'independentRowset', 'config', 'orderBy', 'subdomain', 'filter');
        $entityRs = Indi::model('Entity')->fetchAll('FIND_IN_SET(`table`, "' . implode(',', $tableA) . '")');
        foreach ($entityRs as $entityR) $entityR->delete();

        if ($die) die('ok');
    }

	public function emptyAction(){
		die('empty');
	}

    public function trimckestdAction() {
        $ckeElementId = Indi::model('Element')->fetchRow('`alias` = "html"')->id;
        $ckeEntityIdA = array_unique(Indi::model('Field')->fetchAll('`elementId` = "' . $ckeElementId . '"')->column('entityId'));
        foreach ($ckeEntityIdA as $ckeEntityIdI) {
            foreach (Indi::model($ckeEntityIdI)->fetchAll() as $r)
                $r->trimSTDfromCKEvalues()->save();
        }
    }
    
    public function title2aliasAction() {
        $materialRs = Indi::model('Material')->fetchAll();
        foreach ($materialRs as $materialR) {
            $materialR->alias = alias($materialR->title);
            $materialR->save();
            d($materialR->alias);
        }
        die('ok');
    }
    
    /*public function accessAction() {
        Indi::db()->query('
            UPDATE `section2action`
            SET `profileIds` = CONCAT(`profileIds`, ",18")
            WHERE FIND_IN_SET("12", `profileIds`) AND CONCAT(",", `profileIds`, ",") NOT LIKE ",18,"
        ');
        die('ok');
    }*/

    /*public function wrapcssAction() {
        Indi::wrapCss('/library/extjs4/resources/css/ext-neptune.css');
        Indi::wrapCss('/css/admin/indi.all.neptune.css');
        die('ok');
    }*/
    public function toggleAction() {
        $fieldRs = Indi::model('Field')->fetchAll('`alias` = "toggle"');
        $fieldRs->foreign('entityId');
        $fieldRs->nested('enumset');
        foreach ($fieldRs as $fieldR) {
            foreach ($fieldR->nested('enumset') as $enumsetR) {
                if (preg_match('/i-color-box/', $enumsetR->title)) continue;
                echo $fieldR->foreign('entityId')->title . ': ' . strip_tags($enumsetR->title);
                $color = preg_match('/color:\s*([^"\'; ]+)/', $enumsetR->title, $m) ? $m[1] : 'lime';
                d($color);
                $enumsetR->title = '<span class="i-color-box" style="background: ' . $color . ';"></span>' . strip_tags($enumsetR->title);
                $enumsetR->save();
                echo "\n";
            }
        }
        die('zxc');
    }

    public function noticesAction() {

        Indi::db()->query('DROP TABLE IF EXISTS `notice`');
        Indi::db()->query('DROP TABLE IF EXISTS `noticeGetter`');

        $entityR_notice = Indi::model('Entity')->fetchRow('`table` = "notice"')
            ?: Indi::model('Entity')->createRow(array(
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
            'sectionId' => Indi::model('Section')->fetchRow('`title` = "Конфигурация"')->id,
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
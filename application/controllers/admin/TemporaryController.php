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
                'autorespondMessage' => 'autorespondEventId',
                'bannerShow' => 'datetime',
                'capturedDataGridColumn' => 'capturedDataFieldId',
                'capturedDataGridFilter' => 'capturedDataFieldId',
                'capturedDataLog' => 'datetime',
                'capturedDataUser' => 'userId',
                'courseClick' => 'datetime',
                'courseUser' => 'userId',
                'micrositeClick' => 'datetime',
                'newsletterDelivery' => 'date',
                'pollAnswerVote' => 'datetime',
                'userReward' => 'datetime',
                'rewardedActionReward' => 'reward'
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
}
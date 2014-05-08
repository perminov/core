<?php
/**
 * This is a temporary controller, that used for adjusting database-stored system features of Indi Engine.
 * Methods declared within this class, will be deleted once all database-stored system features will be adjusted
 * on all projects, that run on Indi Engine
 */
class Admin_TemporaryController extends Indi_Controller {
    public function titlesAction() {

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

        $project = 'picneon';

        $projectTitleFieldAliasA = array(
            'picneon' => array(
                'studiedCourse' => 'courseId',
                'indexCourse' => 'courseId',
                'lessonPractice' => 'taskId',
                'recommended' => 'recommended',
                'courseLesson' => 'lessonId'
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
            } else {

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

        die('ok');
    }
}
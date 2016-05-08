<?php
/**
 * Special controller for deal with changelog entries
 */
class Indi_Controller_Admin_ChangeLog extends Indi_Controller_Admin {

    /**
     * Replace view type for 'index' action from 'grid' to 'changeLog'
     */
    public function adjustActionCfg() {
        $this->actionCfg['view']['index'] = 'changeLog';
    }

    /**
     * Here is the temporary solution for filters consistency
     */
    public function adjustTrail() {

        // Here we setup `filter` prop for `entityId` field, to ensure that filter-combo,
        // linked to `entityId` field won't use extra width
        if ($entityIdA = Indi::db()
            ->query('SELECT DISTINCT `entityId` FROM `changeLog`')
            ->fetchAll(PDO::FETCH_COLUMN, 0))
            Indi::trail()->model->fields('entityId')->filter = '`id` IN (' . im($entityIdA) . ') ';

        // Here we setup `filter` prop for `changerType` field, to ensure that filter-combo,
        // linked to `changerType` field won't use extra width
        if ($changerTypeA = Indi::db()
            ->query('SELECT DISTINCT `changerType` FROM `changeLog`')
            ->fetchAll(PDO::FETCH_COLUMN, 0))
            Indi::trail()->model->fields('changerType')->filter = '`id` IN (' . im($changerTypeA) . ') ';

        // Here we setup `filter` prop for `changerType` field, to ensure that filter-combo,
        // linked to `changerId` field won't use extra width
        if ($changerIdA = Indi::db()
            ->query('SELECT DISTINCT `changerId` FROM `changeLog`')
            ->fetchAll(PDO::FETCH_COLUMN, 0))
            Indi::trail()->model->fields('changerId')->filter = '`id` IN (' . im($changerIdA) . ') ';
    }

    /**
     * Implement special parentWHERE logic, that involves two db-columns instead of one
     *
     * @return null|string
     */
    public function parentWHERE() {

        // If current section does not have a parent section, or have, but is a root section - return
        if (!Indi::trail(1)->section->sectionId) return;

        // Setup connector alias, which is always is 'key'
        $connectorAlias = 'key';

        // Get the connector value
        $connectorValue = Indi::uri('action') == 'index'
            ? Indi::uri('id')
            : $_SESSION['indi']['admin']['trail']['parentId'][Indi::trail(1)->section->id];

        // Return clause
        return '`entityId` = "' . Indi::trail(1)->section->entityId . '" AND `' . $connectorAlias . '` = "' . $connectorValue . '"';
    }

    /**
     * Adjust values, for 'changerId' and 'key' props
     *
     * @param array $data
     */
    public function adjustGridData(&$data) {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['changerId'] = $data[$i]['datetime'] . ' - ' . $data[$i]['changerId'];
            $data[$i]['key'] = $data[$i]['entityId'] . ' » ' . $data[$i]['key'];
        }
    }
}
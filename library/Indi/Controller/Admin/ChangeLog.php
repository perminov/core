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
        i('`entityId` = "' . Indi::trail(1)->section->entityId . '" AND `' . $connectorAlias . '` = "' . $connectorValue . '"');
        return '`entityId` = "' . Indi::trail(1)->section->entityId . '" AND `' . $connectorAlias . '` = "' . $connectorValue . '"';
    }
}
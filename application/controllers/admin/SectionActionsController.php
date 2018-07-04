<?php
class Admin_SectionActionsController extends Indi_Controller_Admin {

    /**
     * Append ability to choose multiple actions for being added
     */
    public function adjustTrail() {
        if (!$this->row->id) t()->fields->field('actionId')->assign(array('storeRelationAbility' => 'many'));
    }

    /**
     * Handle cases when comma-separated list is given within $_POST['fieldId']
     */
    public function saveAction() {

        // If we operate on existing entry - call parent
        if ($this->row->id) return $this->callParent();

        // Shortcut to $_POST['actionId']
        $_ = Indi::post('actionId');

        // If $_POST['actionId'] is not a comma-separated list having at least 2 values - call parent
        if (!Indi::rexm('int11list', $_) || count($actionIdA = ar($_)) < 2) return $this->callParent();

        // Unset $_POST['rename']
        unset(Indi::post()->rename);

        // Foreach actionId make clone of $this->row
        foreach ($actionIdA as $actionId) $cloneA[$actionId] = clone $this->row;

        // Foreach clone
        foreach ($cloneA as $actionId => $cloned) {

            // Set $_POST['actionId']
            Indi::post('actionId', $actionId);

            // Spoof $this->row with clone
            $this->row = $cloned;

            // Call parent
            $response = parent::saveAction(true, true);
        }

        // Flush response
        jflush($response);
    }
}
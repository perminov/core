<?php
class Admin_AlteredFieldsController extends Indi_Controller_Admin {

    /**
     * Append ability to choose multiple fields for being altered
     */
    public function adjustTrail() {
        if (!$this->row->id) t()->fields->field('fieldId')->assign(array('storeRelationAbility' => 'many'));
    }

    /**
     * Handle cases when comma-separated list is given within $_POST['fieldId']
     */
    public function saveAction() {

        // If we operate on existing entry - call parent
        if ($this->row->id) return $this->callParent();

        // Shortcut to $_POST['fieldId']
        $_ = Indi::post('fieldId');

        // If $_POST['fieldId'] is not a comma-separated list having at least 2 values - call parent
        if (!Indi::rexm('int11list', $_) || count($fieldIdA = ar($_)) < 2) return $this->callParent();

        // Unset $_POST['rename'] and $_POST['defaultValue']
        unset(Indi::post()->rename, Indi::post()->defaultValue);

        // Foreach fieldId make clone of $this->row
        foreach ($fieldIdA as $fieldId) $cloneA[$fieldId] = clone $this->row;

        // Foreach clone
        foreach ($cloneA as $fieldId => $cloned) {

            // Set $_POST['fieldId']
            Indi::post('fieldId', $fieldId);

            // Spoof $this->row with clone
            $this->row = $cloned;

            // Call parent
            $response = parent::saveAction(true, true);
        }

        // Flush response
        jflush($response);
    }
}
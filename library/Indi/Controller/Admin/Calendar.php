<?php
class Indi_Controller_Admin_Calendar extends Indi_Controller_Admin {

    /**
     * Here we provide calendar panel to be used
     */
    public function adjustActionCfg() {
        $this->actionCfg['view']['index'] = 'calendar';
    }

    /**
     * Append special filter, linked to date-column
     */
    public function adjustTrail() {

        // If no date column - return
        if (!$dateColumn = Indi::trail()->model->dateColumn()) return;

        // If date column is not a field - return
        if (!$dateFieldR = Indi::trail()->model->fields($dateColumn)) return;

        // Append filter
        Indi::trail()->filters->append(array(
            'sectionId' => Indi::trail()->section->id,
            'fieldId' => $dateFieldR->id,
            'title' => $dateFieldR->title
        ));
    }
}
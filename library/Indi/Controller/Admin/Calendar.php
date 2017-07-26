<?php
class Indi_Controller_Admin_Calendar extends Indi_Controller_Admin {

    /**
     * @var string
     */
    public $type = 'month';

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

    /**
     * We set ORDER as 'id', as we do not need any other order type
     * @return string
     */
    public function finalORDER() {
        return 'spaceSince';
    }

    /**
     *
     */
    public function adjustGridDataRowset() {

        // Pick bounds
        list ($since, $until) = ar(im($this->_excelA['date']['value']));

        // Detect type of calendar
        if (strtotime($until) - strtotime($since) == 3600 * 24 * 6) $this->type = 'week';
        else if ($since == $until) $this->type = 'day';

        // Adjust event
        foreach ($this->rowset as $r) $this->{'adjustEventFor' . ucfirst($this->type)}($r);

        // Call parent
        $this->callParent();
    }

    /**
     * @param Indi_Db_Table_Row $r
     */
    public function adjustEventForMonth($r) {

    }

    /**
     * @param Indi_Db_Table_Row $r
     */
    public function adjustEventForWeek($r) {
        $this->adjustEventForMonth($r);
    }

    /**
     * @param Indi_Db_Table_Row $r
     */
    public function adjustEventForDay($r) {
        $this->adjustEventForMonth($r);
    }
}
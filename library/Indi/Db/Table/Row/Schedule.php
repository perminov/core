<?php
class Indi_Db_Table_Row_Schedule extends Indi_Db_Table_Row {

    /**
     * Ensure that fields, related to current entry's space within the schedule - will be set up
     *
     * @return int|mixed
     */
    public function save() {

        // Set value for schedule-space fields
        $this->setSpaceSince();
        $this->setSpaceUntil();
        $this->setSpaceFrame();

        // Call parent
        return $this->callParent();
    }

    /**
     * Empty method, to be redeclared in child classes
     */
    public function setSpaceSince() {

    }

    /**
     * Empty method, to be redeclared in child classes
     */
    public function setSpaceUntil() {

    }

    /**
     * Calculate current entry's space size within the schedule, in seconds
     */
    public function setSpaceFrame() {

        // Get names of fields, related to current entry's space within the schedule
        $sinceField = $this->model()->sinceField();
        $untilField = $this->model()->untilField();
        $frameField = $this->model()->frameField();

        // Set frame field value
        $this->$frameField = strtotime($this->$untilField) - strtotime($this->$sinceField);
    }
}
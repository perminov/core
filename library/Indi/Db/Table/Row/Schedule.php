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

        // Set frame field value
        $this->spaceFrame = strtotime($this->spaceUntil) - strtotime($this->spaceSince);
    }

    /**
     * Get daily working hours to be involved in certain entry validation.
     * This function was implemented as a workaround for cases when we have
     * some events, that are not fully/partially fit within the daily working hours.
     * This may happen for example, then there was old settings for daily working hours,
     * and certian entry was in those hours, but then those hours were changed, so now
     * that entry is already NOT within new daily working hours, and this will prevent
     * any modifications, that user/system will be trying to apply, as validation will flush
     * failure. That is why this function will disable daily working hours settings for events
     * that are not match that settings
     *
     * @return array
     */
    public function daily() {

        // Get daily working hours
        $daily = $this->model()->daily();

        // If this is a not-yet-existing entry - return daily working hours, declared for the model
        if (!$this->id) return $daily;

        // Disable any daily bound in case if current event is overlapping that bound
        foreach ($daily as $type => $time)
            if ($time && $_ = Indi::schedule($this->_original['spaceSince'], $this->_original['spaceUntil']))
                if ($_ = $_->{$type == 'since' ? 'early' : 'late'}($time)->spaces())
                    if (count($_) > 1 || $_[0]->avail != 'free') $daily[$type] = false;

        // Return
        return $daily;
    }
}
<?php
class Section2action_Row extends Indi_Db_Table_Row
{
    /**
     * Get title for section2action row
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->foreign('actionId')->title;
    }
}
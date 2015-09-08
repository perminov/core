<?php
class Indi_Db_Table_Row_Foto extends Indi_Db_Table_Row {

    /**
     * @param bool $adjustFotoTitles
     * @return int|mixed
     */
    public function save($adjustFotoTitles = true) {
    
        // Call parent
        $return = $this->callParent();
        
        // Adjust foto titles
        if ($adjustFotoTitles) $this->model()->adjustFotoTitles($this->{$this->model()->withinField()});

        // Return
        return $return;
    }

    /**
     * @param bool $adjustFotoTitles
     * @return int|mixed
     */
    public function delete($adjustFotoTitles = true) {

        // Call parent
        $return = $this->callParent();
        
        // Adjust foto titles
        if ($adjustFotoTitles) $this->model()->adjustFotoTitles($this->{$this->model()->withinField()});
        
        // Return
        return $return;
    }
}
<?php
class NoticeGetter_Row extends Indi_Db_Table_Row {

    /**
     * @var null
     */
    public $row = null;

    /**
     * Get array of recepients ids
     */
    public function ar($row) {

        // If criteria, that recipients should match - is not specified
        if (!strlen($this->criteria)) return true;

        // Assign `row` prop, that will be visible in compiling context
        $this->row = $row;

        // Unset previously compiled criteria
        unset($this->_compiled['criteria']);

        // Find the name of database table, where recipients should be found
        foreach (Indi_Db::role() as $profileIds => $entityId)
            if (in($this->profileId, $profileIds))
                if ($table = Indi::model($entityId)->table())
                    break;

        // Recompile criteria, use it for finding recipients ids
        $return = Indi::db()->query($sql = '
            SELECT `id` FROM `' . $table . '` WHERE ' . $this->compiled('criteria')
        )->fetchAll(PDO::FETCH_COLUMN, 0);

        // Return array of found recipients ids
        return $return;
    }
}
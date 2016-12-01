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
        if (!strlen($this->criteria) && $this->mail == 'n' && $this->vk == 'n') return true;

        // Start building WHERE clauses array
        $where = array('`toggle` = "y"');

        // Find the name of database table, where recipients should be found within
        foreach (Indi_Db::role() as $profileIds => $entityId)
            if (in($this->profileId, $profileIds))
                if ($table = Indi::model($entityId)->table())
                    break;

        // Prevent recipients duplication
        if ($table == 'admin') $where[] = '`profileId` = "' . $this->profileId . '"';

        // If criteria specified
        if (strlen($this->criteria)) {

            // Assign `row` prop, that will be visible in compiling context
            $this->row = $row;

            // Unset previously compiled criteria
            unset($this->_compiled['criteria']);

            // Append compiled criteria to WHERE clauses array
            if (strlen($criteria = $this->compiled('criteria'))) $where[] = '(' . $criteria . ')';
        }

        // If VK-notifications are toggled On, and current recipient's entity has 'vk' field
        $vk = $this->vk == 'y' && Indi::model($table)->fields('vk')->columnTypeId ? ', `vk`, `title`' : '';

        // Recompile criteria, use it for finding recipients ids
        $return = Indi::db()->query($sql = '
            SELECT `id`, `email`' . $vk . ' FROM `' . $table . '` WHERE ' . im($where, ' AND ')
        )->fetchAll();

        // Return array of found recipients ids
        return $return;
    }
}
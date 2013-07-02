<?php
/**
 * This is a temporary class used to separate good-looking php-code from bad-looking in Indi/Db/Table/Row.php
 * Good looking mean that it have proper coding style, doc blocks and other stuff
 * This class will be renamed to Indi/Db/Table/Row.php after all methods in current Indi/Db/Table/Row.php will become
 * good looking
 */
class Indi_Db_Table_Row_Beautiful extends Indi_Db_Table_Row_Abstract{

    /**
     * Store regular expression for checks of email addresses validity
     *
     * @var string
     */
    public $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
	
    /**
     * Saves row into database table. But.
     * Preliminary checks if row has a `move` field in it's structure and if row is not an existing row yet
     * (but is going to be inserted), and if so - autoset value for `move` column after row save
     *
     * @return int affected rows|last_insert_id
     */
    public function save() {
        if (!$this->_original['id'] && array_key_exists('move', $this->_original) && !$this->move) $orderAutoSet = true;
        $return = parent::save();
        if ($orderAutoSet) {
            $this->move = $this->id;
            parent::save();
        }
        return $return;
    }

    /**
     * Provide Move up/Move down actions for row within the needed area of rows
     *
     * @param string $direction (up|down)
     * @param string $within
     * @param string $condition
     */
    public function move($direction = 'up', $within = '', $condition = null) {
        // Check direction validity
        if (in_array($direction, array('up', 'down'))) {

            // Array of WHERE clause items
            $where = array();

            // Adding conditions required to match the needed scope, there changeRow should be searched
            if (!is_array($within) && $within) $within = explode(',', $within);
            // Append tree-column to $within array, if such column exists
            if (array_key_exists($this->getTable()->info('name') . 'Id', $this->_original)) $within[] = $this->getTable()->info('name') . 'Id';

            for ($i = 0; $i < count($within); $i++) $where[] = '`' . trim($within[$i]) . '` = "' . $this->{trim($within[$i])} . '"';

            // Adding custom condition
            if (is_array($condition) && count($condition)) $where = array_merge($where, $condition);
            else if ($condition) $where[] = $condition;

            // Nearest neighbour clauses
            $where[] = '`move` ' . ($direction == 'up' ? '<' : '>') . ' "' . $this->move . '"';
            $order = 'move ' . ($direction == 'up' ? 'DE' : 'A') . 'SC';

            // Find
            if ($changeRow = $this->getTable()->fetchRow($where, $order)) {
                // Backup `move` of current row
                $backup = $this->move;

                // We exchange values of `move` fields
                $this->move = $changeRow->move;
                $this->save(true);
                $changeRow->move = $backup;
                $changeRow->save(true);
            }
        }
    }

    /**
     * Fully deletion - including attached files and foreign key usages, if will be found
     *
     * @return int Number of deleted rows (1|0)
     */
    public function delete(){
        // Delete all files and images that have been attached to row
        $this->deleteUploadedFiles();

        // Delete other rows of entities, that have fields, related to entity of current row
        // This function also covers other situations, such as if entity of current row has a tree structure,
        // or row has dependent rowsets
        $this->deleteForeignKeysUsages();

        // Standard Indi_Db_Table_Row deletion
        return parent::delete();
    }

    public function getComboData($field, $page = 1){
        $entityM = Misc::loadModel('Entity');
        $fieldM = Misc::loadModel('Field');
        $entityR = $entityM->fetchRow('`table` = "' . $this->_table->_name . '"');
        $fieldR = $fieldM->fetchRow('`entityId` = "' . $entityR->id . '" AND `alias` = "' . $field . '"');
        $relatedM = Entity::getInstance()->getModelById($fieldR->relation);

        // Set ORDER clause for combo data
        if ($relatedM->fieldExists('move')) {
            $order = 'move';
        } else if ($relatedM->fieldExists('title')) {
            $order = 'title';
        } else {
            $order = null;
        }

        if ($relatedM->treeColumn) {
            return $relatedM->fetchTree(null, $order, 20, $page);
        }
    }
}
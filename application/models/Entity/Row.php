<?php
class Entity_Row extends Indi_Db_Table_Row {

    /**
     * Delete current entity
     *
     * @return int|void
     */
    public function delete(){

		// Delete all uploaded files of entity rows and folder they were stored in
		$this->deleteAllUploadedFilesAndUploadFolder();

        // Standard deletion
        parent::delete();

        // Delete database table
		Indi::db()->query('DROP TABLE `' . $this->table . '`');
	}

    /**
     * Delete the whole directory, containing files, related to current entity. If all was successful - return true,
     * else if all files were deleted, but directory was not - return false, else if some of files were not deleted
     *  - return their count
     *
     * @return bool|int
     */
    public function deleteAllUploadedFilesAndUploadFolder() {

        // Get the directory name
        $dir = DOC . STD . '/' . Indi::ini()->upload->path . '/' . $this->table . '/';

        // If directory does not exist - return
        if (!is_dir($dir)) return;

        // Get all files
        $fileA = glob($dir . '*');

        // Delete them
        $deleted = 0; foreach ($fileA as $fileI) $deleted += @unlink($fileI);

        // If all files were deleted - try to delete empty directory and return it's success,
        // else return count of files that were not deleted for some reason
        return $deleted == count($fileA) ? rmdir($dir) : count($fileA) - $deleted;
	}

    /**
     * Create/rename database table, refresh/remove cache file, rename upload folder if need
     *
     * @return int
     */
    public function save() {

        // If this is a new entity
        if (!$this->id) {

            // Run the CREATE TABLE sql query
            Indi::db()->query('CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
                `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM');

        // Else if it is an existing entity, but it's database table name is to be modified
        } else if ($this->_modified['table']) {

            // Run the RENAME TABLE sql query
            Indi::db()->query('RENAME TABLE  `' . $this->_original['table'] . '` TO  `' . $this->_modified['table'] . '`');

            // Get the original upload directory name
            $old = DOC . STD . '/' . Indi::ini()->upload->path . '/' . $this->_original['table'] . '/';

            // If directory exists - rename it
            if (is_dir($old)) {

                // Get the new directory name
                $new = DOC . STD . '/' . Indi::ini()->upload->path . '/' . $this->_modified['table'] . '/';

                // Do rename
                rename($old, $new);
            }
        }

        // If useCache property was changed
        if (isset($this->_modified['useCache'])) {

            // If it was switched on
            if ($this->_modified['useCache']) {

                // Refresh the cache file
                Indi_Cache::update($this->table);

            // Else if it was switched off
            } else {

                // Remove the cache file
                Indi_Cache::remove($this->table);
            }
        }

        // Standard save
        return parent::save();
    }
}
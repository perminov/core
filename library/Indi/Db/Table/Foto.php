<?php
class Indi_Db_Table_Foto extends Indi_Db_Table {

    /**
     * The alias of field, that will determine the scope of fotos to be auto-titled.
     * This property should be non-null in child classes
     *
     * @var null
     */
    protected $_withinField = null;

    /**
     * Foto's title template
     *
     * @var string
     */
    protected $_titlePref = I_FORM_UPLOAD_FPREF;

    /**
     * Read-only access to $_withinField property
     *
     * @return null
     */
    public function withinField() {
        return $this->_withinField;
    }

    /**
     * Update foto titles
     *
     * @param mixed $_withinFieldValue
     */
    public function adjustFotoTitles($_withinFieldValue) {

        // If no `_withinField` set, - return
        if (!$this->_withinField) return;

        // Get fotos array
        $_fotoA = Indi::db()->query('
            SELECT `id`
            FROM `' . $this->_table . '`
            WHERE `' . $this->_withinField . '` = "' . $_withinFieldValue . '"
            ORDER BY  `move` ASC
        ')->fetchAll();

        // For each foto update `title` prop
        for ($i = 0; $i < count($_fotoA); $i++)
            Indi::db()->query('
                UPDATE `' . $this->_table . '`
                SET `title` = "' . sprintf($this->_titlePref, $i+1) . '"
                WHERE 1
                  AND `id` = "' . $_fotoA[$i]['id'] .'"
                  AND (`title` RLIKE "^(' . sprintf($this->_titlePref, '[0-9]+') . ')?$" OR TRIM(`title`) = "")
                  AND `toggle` = "y"
                LIMIT 1'
            );
    }
}
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

        // Setup colors
        $this->colors();
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

    /**
     * Try to find enumset-field having most quantity of color-boxes,
     * and if found - prepare object containing colors info, to be passed to view
     *
     * @return mixed
     */
    public function colors() {

        // Get id of ENUM column type
        $ENUM_columnTypeId = Indi::model('ColumnType')->fetchRow('`type` = "ENUM"')->id;

        // Get ENUM fields
        $ENUM_fieldRs = Indi::trail()->model->fields()->select($ENUM_columnTypeId, 'columnTypeId');

        // Try to find color definitions
        $found = array();
        foreach ($ENUM_fieldRs as $ENUM_fieldR)
            foreach ($ENUM_fieldR->nested('enumset') as $enumsetR)
                if ($color = Indi::rexm('/(background|color):([^;]+);?/', $enumsetR->title, 2))
                    $found[$ENUM_fieldR->alias][$enumsetR->alias] = trim($color);

        // If nothing found - return
        if (!$found) return;

        // Get field having bigger qty of colored enum values
        $info = array('field' => '', 'colors' => array());
        foreach ($found as $field => $colors)
            if (count($colors) > count($info['colors']) && $info['field'] = $field)
                $info['colors'] = $colors;

        // Prepare colors
        foreach ($info['colors'] as $option => $color) {

            // If $color is a color in format #rrggbb
            if (Indi::rexm('rgb', $color)) $hex = $color;

            // Else $color is a name of one of html-colors
            else if (Indi::$colorNameA[$color]) $hex = Indi::$colorNameA[$color];

            // Convert red, green and blue values from hex to decimals
            $background = ($hex = preg_replace('/^#/', '', $hex))
                ? sprintf('rgba(%d, %d, %d, 0.2)', hexdec(substr($hex, 0, 2)),
                  hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))) : '';

            $info['colors'][$option] = array(
                'color' => sprintf('rgb(%d, %d, %d)', hexdec(substr($hex, 0, 2)) - 50,
                    hexdec(substr($hex, 2, 2)) - 50, hexdec(substr($hex, 4, 2)) - 50),
                'border-color' => $color,
                'background-color' => $background
            );
        }

        // Assign colors info
        Indi::trail()->section->colors = $info;
    }
}
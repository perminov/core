<?php
class Indi_Controller_Admin_Calendar extends Indi_Controller_Admin {

    /**
     * Calendar period
     *
     * @var string
     */
    public $type = 'month';

    /**
     * Color definition for calendar events
     *
     * @var bool/array
     */
    public $colors = false;

    /**
     * Flag, indicating whether or not current trail item's model has space-fields,
     * e.g. whether or not it is possible to apply all calendar-related things
     *
     * @var bool
     */
    public $spaceFields = false;

    /**
     * Here we provide calendar panel to be used
     */
    public function adjustActionCfg() {

        // If calendar can't be used - return
        if (!$this->spaceFields) return;

        // Apply calendar view
        $this->actionCfg['view']['index'] = 'calendar';
    }

    /**
     * Append special filter, linked to `spaceSince` column
     */
    public function adjustTrail() {

        // If `spaceSince` field does not exists - return
        if (!$fieldR_spaceSince = Indi::trail()->model->fields('spaceSince')) return;

        // Set `spaceField` flag to `true`
        $this->spaceFields = true;

        // For each of the below space-fields
        foreach (ar('spaceSince,spaceUntil') as $field) {

            // Set format of time to include seconds
            Indi::trail()->model->fields($field)->param('displayTimeFormat', 'H:i:s');

            // Append to gridFields
            if (Indi::trail()->gridFields) Indi::trail()->gridFields->append(Indi::trail()->model->fields($field));
        }

        // Append filter
        Indi::trail()->filters->append(array(
            'sectionId' => Indi::trail()->section->id,
            'fieldId' => $fieldR_spaceSince->id,
            'title' => $fieldR_spaceSince->title,
            'toolbar' => 'master'
        ));

        // Define colors
        Indi::trail()->section->colors = $this->defineColors();

        // Check whether 'since' uri-param is given, and if yes - prefill current entry's
        // certain space-fields with values according to clicked timestamp ('since' uri-param)
        // or according to selected datetime-range (both 'since' and 'until' uri-params)
        $this->applySpace();
    }

    /**
     * Check whether 'since' uri-param is given, and if yes - prefill current entry's
     * certain space-fields with values according to clicked timestamp ('since' uri-param)
     * or according to selected datetime-range (both 'since' and 'until' uri-params)
     *
     * @return mixed
     */
    public function applySpace() {

        // If we're not dealing with a row, or we are, but with already existing row - return
        if (!t()->row || t()->row->id) return;

        // If clicked timestamp is not given as an uri-param - return
        if (!$since = Indi::uri('since')) return;

        // Get 'until' uri-param, if given
        $until = Indi::uri('until');

        // Setup `extraUri`, for 'since' and 'until' uri-params being kept even if entry's form will be reloaded
        t()->action->extraUri = '/since/' . $since . ($until ? '/until/' . $until : '');

        // Get space scheme and fields
        $space = t()->model->space();

        // Prepare array of values, that space-start fields should be prefilled with
        foreach (explode('-', $space['scheme']) as $coord) switch ($coord) {
            case 'date': $prefill[$space['coords'][$coord]] = date('Y-m-d', $since); break;
            case 'datetime': $prefill[$space['coords'][$coord]] = date('Y-m-d H:i:s', $since); break;
            case 'time': $prefill[$space['coords'][$coord]] = date('H:i:s', $since); break;
            case 'timeId': $prefill[$space['coords'][$coord]] = timeId(date('H:i', $since)) ?: '0'; break;
        }

        // Prepare array of values, that space-duration fields should be prefilled with
        if ($until) foreach (explode('-', $space['scheme']) as $coord) switch ($coord) {
            case 'dayQty': $prefill[$space['coords'][$coord]] = ($until - $since) / 86400; break;
            case 'minuteQty': $prefill[$space['coords'][$coord]] = ($until - $since) / 60; break;
            case 'timespan': $prefill[$space['coords'][$coord]] = date('H:i', $since) . '-' . date('H:i', $since); break;
        }

        // Assign prepared values
        $this->row->assign($prefill);
    }

    /**
     * This method is redefined to append calendar type detection
     *
     * @return array|mixed
     */
    public function filtersWHERE() {

        // Call parent
        $return = $this->callParent();

        // Detect current calendar type
        if ($this->_excelA) {

            // Pick bounds
            list ($since, $until) = ar(im($this->_excelA['spaceSince']['value']));

            // Detect type of calendar
            $diff = strtotime($until) - strtotime($since);
            if ($diff == 3600 * 24 * 7) $this->type = 'week';
            else if ($diff == 3600 * 24 * 1) $this->type = 'day';
        }

        // Return
        return $return;
    }

    /**
     * We set ORDER as 'id', as we do not need any other order type
     * @return string
     */
    public function finalORDER() {
        return $this->spaceFields ? 'spaceSince' : $this->callParent();
    }

    /**
     * Adjust events' *_Row instances depending on calendar type, and apply colors to events
     */
    public function adjustGridDataRowset() {

        // If calendar can't be used - return
        if (!$this->spaceFields) return;

        // Adjust events according to current calendar type
        if ($this->_excelA)
            foreach ($this->rowset as $r)
                $this->{'adjustEventFor' . ucfirst($this->type)}($r);

        // Apply colors
        $this->applyColors();
    }

    /**
     * Adjust events' props-arrays depending on calendar type
     */
    public function adjustGridData(&$data) {

        // If calendar can't be used - return
        if (!$this->spaceFields) return;

        // Adjust events data according to current calendar type
        if ($this->_excelA)
            foreach ($data as &$item)
                $this->{'adjustEventDataFor' . ucfirst($this->type)}($item);
    }

    /**
     * Apply colors
     */
    public function applyColors() {
        foreach ($this->rowset as $r) $r->system('color', $this->detectEventColor($r));
    }

    /**
     * Detect color for a certain event, given by $r arg.
     */
    public function detectEventColor($r) {
        return $this->colors['field'] ? $r->{$this->colors['field']} : null;
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
        $this->adjustEventForWeek($r);
    }

    /**
     * @param array $data
     */
    public function adjustEventDataForMonth(&$data) {

    }

    /**
     * @param array $data
     */
    public function adjustEventDataForWeek(&$data) {
        $this->adjustEventDataForMonth($data);
    }

    /**
     * @param array $data
     */
    public function adjustEventDataForDay(&$data) {
        $this->adjustEventDataForWeek($data);
    }

    /**
     * Try to find enumset-field having most quantity of color-boxes,
     * and if found - prepare object containing colors info, to be used further
     *
     * @return mixed
     */
    public function detectColors() {

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
        if (!$found) return false;

        // Get field having bigger qty of colored enum values
        $info = array('field' => '', 'colors' => array());
        foreach ($found as $field => $colors)
            if (count($colors) > count($info['colors']) && $info['field'] = $field)
                $info['colors'] = $colors;

        // Return info about automatically-detected colors
        return $info;
    }

    /**
     * Define colors
     *
     * @return mixed
     */
    public function defineColors() {

        // Try to detected colors automatically
        $info = $this->detectColors();

        // Adjust colors, even if automatic colors detection had no success
        $this->adjustColors($info);

        // If still no colors - return
        if (!$info) return false;

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

            // Build css
            $css = array(
                'color' => sprintf('rgb(%d, %d, %d)', hexdec(substr($hex, 0, 2)) - 50,
                    hexdec(substr($hex, 2, 2)) - 50, hexdec(substr($hex, 4, 2)) - 50),
                'border-color' => $color,
                'background-color' => $background
            );

            // Adjust it for custom needs
            $this->adjustColorsCss($option, $color, $css);

            // Assign css
            $info['colors'][$option] = $css;
        }

        // Assign colors info into $this->colors, and return it
        return $this->colors = $info;
    }

    /**
     * Override this method in child classes for defining custom colors, if need
     *
     * @param $info
     */
    public function adjustColors(&$info) {

        // Set empty info
        if (!$info) $info = array('field' => '', 'colors' => array());

        // Append one more color definition
        $info['colors']['default'] = 'lime';
    }

    /**
     * Adjust certain color's css-definition
     *
     * @param $option
     * @param $color
     * @param $css
     */
    public function adjustColorsCss($option, $color, &$css) {

    }

    /**
     * Exclude `spaceSince` and `spaceUntil` fields from the list of disabled fields,
     * as those fields wil be got from $_POST as a result of event move or resize
     *
     * @param bool $redirect
     * @param bool $return
     * @return array|mixed
     */
    public function saveAction($redirect = true, $return = false) {

        // If calendar can be used - exclude calendar-fields fields from the list of disabled fields
        if ($this->spaceFields) $this->excludeDisabledFields('spaceSince,spaceUntil');

        // Detect calendar type
        $this->filtersWHERE();

        // Call parent
        return $this->callParent();
    }
}
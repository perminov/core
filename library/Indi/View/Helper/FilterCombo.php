<?php
class Indi_View_Helper_FilterCombo extends Indi_View_Helper_FormCombo {

    public $filter = null;

    /**
     * This var is used for html elements css class names building
     * @var string
     */
    public $type = 'filter';

    /**
     * Detect whether or not filter should have ability to deal with multiple values
     *
     * @return bool
     */
    public function isMultiSelect() {
        return $this->filter->any();
    }

    /**
     * Builds the combo for grid filter
     *
     * @param Search_Row $filter
     * @return string
     */
    public function filterCombo($filter) {
        // Here we create a shared *_Row object, that will be used by all filters, that are presented in current grid.
        // We need it bacause of a satellites. If we define a default value for some combo, and that combo is a satellite
        // for another combo - another combo's initial data will depend on satellite value, so the shared row is the place
        // there dependent combo can get that value.
        $this->filter = $filter;

        // Get field's alias
        $alias = $this->getField()->alias;

        // Reset filterSharedRow's property value, representing current filter
        $this->getRow()->$alias = null;

        // Declare WHERE array
        $this->where = array();

        // Append statiс WHERE, defined for filter
        if (strlen($this->filter->filter)) $this->where[] = $this->filter->filter;

        // Setup ignoreTemplate property
        $this->ignoreTemplate = $this->filter->ignoreTemplate;

        // Do stuff
        return parent::formCombo($filter->foreign('fieldId')->alias);
    }

    /**
     * It's purpose is to define an explicit definition of a number of combo data items, that should ONLY be displayed.
     * ONLY here  mean combo items will be exact as in $consistence array, not less and not greater. This feature is used
     * for rowset filters, and is a part of a number of tricks, that provide the availability of filter-combo
     * data-options only for data-options, that will have at least one matching row within rowset, in case of
     * their selection as a part of a rowset search criteria. The $consistence array is being taken into
     * consideration even if it constains no elements ( - zero-length array), in this case filter-combo will contain
     * no options, and therefore will be disabled
     */
    public function getConsistence() {

        // Check if consistency is not toggled Off for current filter
        if ($this->filter->model()->fields('consistence') && !$this->filter->consistence) return;

        // If filter is non-boolean
        if ((($relation = $this->getField()->relation) || $this->getField()->columnTypeId == 12) && Indi::uri('format')) {

            // Get field's alias
            $alias = $this->getField()->alias;

            // Get table name
            $tbl = Indi::trail()->model->table();

            // Get primary WHERE
            $primaryWHERE = $this->primaryWHERE();

            // Get finalWHERE as array
            $sw = $this->getController()->finalWHERE($primaryWHERE, null, false);

            // Exclude WHERE clause part, related to current filter
            unset($sw['filters'][$alias]); if (!count($sw['filters'])) unset($sw['filters']);

            // Force $finalWHERE to be single-dimension array
            foreach ($sw as $p => $w) if (is_array($w)) $sw[$p] = im($w, ' AND '); $sw = implode(' AND ', $sw);

            // Get the distinct list of possibilities
            $in = Indi::db()->query('
                SELECT DISTINCT `'. $alias . '` FROM `' . $tbl .'`' .  (strlen($sw) ? 'WHERE ' . $sw : '')
            )->fetchAll(PDO::FETCH_COLUMN);

            // Unset zero-length values
            foreach ($in as $i => $inI) if (!strlen($inI)) unset($in[$i]);

            // Return
            return in($relation, '0,6') ? $in : '`id` IN (' . ($in ? implode(',', $in) : '0') . ')';
        }
    }

    public function getSelected() {
        return $this->getDefaultValue();
    }

    public function getField() {
        return Indi::trail()->fields->select($this->filter->fieldId)->at(0);
    }

    /**
     * Detect whether or not WHERE clause part, related especially to current field's satellite value
     * should be involved in the process of fetching data for current filter-combo
     *
     * @return bool
     */
    public function noSatellite() {

        // If current field has a satellite, we'll try to find satellite's value in several places
        if ($satelliteFieldId = $this->getField()->satellite) {

            // Clone filters rowset, as iterating through same rowset will give an error
            $filters = clone Indi::trail()->filters;

            // Lookup satellite within the available filters. If found - use it
            $availableFilterA = $filters->toArray();
            foreach ($availableFilterA as $availableFilterI)
                if ($availableFilterI['fieldId'] == $satelliteFieldId)
                    return false;

            // Lookup satellite within the filterSharedRow's props, that might hav been set up by
            // trail items connections logic. If found - use it
            $satelliteFieldAlias = $this->getField()->foreign('satellite')->alias;
            if (array_key_exists($satelliteFieldAlias, $this->getRow()->modified())) return false;

            // If current cms user is an alternate, and if there is corresponding column-field within current entity structure. If found - use it
            if (Indi::admin()->alternate && in($aid = Indi::admin()->alternate . 'Id', Indi::trail()->model->fields(null, 'columns')))
                return false;

            // No satellite should be used
            return true;

            // No satellite should be used
        } else return true;
    }

    /**
     * Setup row object for combo
     *
     * @return Indi_Db_Table_Row
     */
    public function getRow(){
        return Indi::trail()->filtersSharedRow;
    }

    /**
     * Filter combos have different behaviour, related to deal with default values
     *
     * @return mixed|string
     */
    public function getDefaultValue() {
        $gotFromScope = Indi::trail()->scope->filter($this->field->alias);

        if ($gotFromScope || ($this->field->columnTypeId == 12 && $gotFromScope != '')) {
            if ($this->isMultiSelect())
                if(is_array($gotFromScope))
                    $gotFromScope = implode(',', $gotFromScope);
            $this->filter->defaultValue = $this->getRow()->{$this->field->alias} = $gotFromScope;
            return $gotFromScope;
        }
        if (strlen($this->filter->defaultValue) || ($this->field->columnTypeId == 12 && $this->filter->defaultValue != '')) {
            Indi::$cmpTpl = $this->filter->defaultValue; eval(Indi::$cmpRun); $this->filter->defaultValue = Indi::cmpOut();
            $this->getRow()->{$this->field->alias} = $this->filter->defaultValue;
            return $this->filter->defaultValue;
        } else {
            return '';
        }
    }
}
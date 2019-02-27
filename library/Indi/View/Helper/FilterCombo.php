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

        // Filter entry
        $this->filter = $filter;

        // Get field's alias
        $alias = $this->getField()->alias;

        // Reset filterSharedRow's property value, representing current filter
        $this->getRow()->$alias = null;

        // Declare WHERE array
        $this->where = array();

        // Append statiс WHERE, defined for filter
        if (strlen($this->filter->filter)) $this->where []= $this->filter->filter;

        // Setup ignoreTemplate property
        $this->ignoreTemplate = $this->filter->ignoreTemplate;

        // Foreach `consider` entry, nested under filter's underlying `field` entry
        foreach ($this->getField()->nested('consider') as $considerR) {

            // Get consider-field's alias
            $cField = $consider = t()->model->fields($considerR->consider)->alias;

            // Pick consider-field's value from scope and if it's not zero-length - assign to filters shared row
            if (($cValue = t()->scope->filter($cField)) && strlen($cValue)) t()->filtersSharedRow->$cField = $cValue;

            // Else if there is no such filter - set consider field to be not required
            else if (!t()->filters->gb($considerR->consider, 'fieldId')) $considerR->required = 'n';
        }

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
                SELECT DISTINCT `'. $alias . '` FROM `' . $this->distinctFrom($alias, $tbl) .'`' .  (strlen($sw) ? 'WHERE ' . $sw : '')
            )->fetchAll(PDO::FETCH_COLUMN);

            // Unset zero-length values
            foreach ($in as $i => $inI) if (!strlen($inI)) unset($in[$i]);

            // Return
            return in($relation, '0,6') ? $in : '`id` IN (' . ($in ? implode(',', $in) : '0') . ')';
        }
    }

    /**
     * This function may be useful in cases when $defaultTable - is a name
     * of the MySQL VIEW rather than MySQL TABLE, but using VIEW takes much
     * time for getting distinct values from it. So, you may override this
     * function in child classes for it to return custom table names depending
     * on column, what we need to fetch distinct value from
     *
     * @param $column
     * @param $defaultTable
     * @return mixed
     */
    public function distinctFrom($column, $defaultTable) {
        return $defaultTable;
    }

    public function getSelected() {
        return $this->getDefaultValue();
    }

    public function getField() {
        return t()->model->fields($this->filter->fieldId);
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

        if ($gotFromScope || ($this->field->columnTypeId == 12 && $gotFromScope != '' && $gotFromScope !== array())) {
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
<?php
class Indi_Trail_Item {

    /**
     * Store current trail item index/level
     *
     * @var int
     */
    public $level = null;

    /**
     * Store number of fields that associated with a ExtJs grid, in case if
     * there is an entity attached to section, and the current action is 'index'
     *
     * @var Indi_Db_Table_Rowset
     */
    public $gridFields = null;

    /**
     * Store Indi_Trail_Admin_Item_Scope object, related to current trail item
     *
     * @var Indi_Trail_Admin_Item_Scope
     */
    public $scope = null;

    /**
     * Store trail item row
     *
     * @var Indi_Db_Table_Row object
     */
    public $row;

    /**
     * Getter. Currently declared only for getting 'model' and 'fields' property
     *
     * @param $property
     * @return Indi_Db_Table
     */
    public function __get($property) {
        if ($this->section->entityId)
            if ($property == 'model') return Indi::model($this->section->entityId);
            else if ($property == 'fields') return Indi::model($this->section->entityId)->fields();
    }

    /**
     * Setup shared row object, that filters will be deal with
     * (same as usual row object, that form's combos are dealing with)
     *
     * @param $start
     * @return null
     */
    public function filtersSharedRow($start) {

        // Setup filters shared row
        $this->filtersSharedRow = $this->model->createRow();

        // If current cms user is an alternate, and if there is corresponding column-field within current entity structure
        if (Indi::admin()->alternate && in($aid = Indi::admin()->alternate . 'Id', $this->model->fields(null, 'columns')))

            // Force setup of that field value as id of current cms user, within filters shared row
            $this->filtersSharedRow->$aid = Indi::admin()->id;

        // Setup several temporary properties within the existing row, as these may be involved in the
        // process of parent trail items rows retrieving
        for ($i = $start + 1; $i < count(Indi_Trail_Admin::$items) - 1; $i++) {

            // Determine the connector field between, for example 'country' and 'city'. Usually it is
            // '<parent-table-name>Id' but in some custom cases, this may differ. We do custom connector
            // field autosetup only if it was set and only in case of one-level-up parent section. This
            // mean that if we have 'Continents' as upper level, and we are creating city, city's property
            // name will be determined as `continentId` mean parentSectionConnector logic won't be used for that
            $connector = $i == 1 && Indi::trail($i-1)->section->parentSectionConnector
                ? Indi::trail($i-1)->section->foreign('parentSectionConnector')->alias
                : Indi::trail($i)->model->table() . 'Id';


            // Get the connector value from session special place and assign it to current row, but only
            // in case if that connector is not a one of existing fields
            if ($this->model->fields($connector))
                $this->filtersSharedRow->$connector = $_SESSION['indi']['admin']['trail']['parentId']
                [Indi::trail($i)->section->id];
        }
    }

    /**
     * Get array version of internal variables
     *
     * @return array
     */
    public function toArray() {
        $array = array();
        if ($this->section) {
            $array['section'] = $this->section->toArray();
            if ($this->section->defaultSortField)
                $array['section']['defaultSortFieldAlias'] = $this->section->foreign('defaultSortField')->alias;
        }
        if ($this->sections) $array['sections'] = $this->sections->toArray();
        if ($this->action) $array['action'] = $this->action->toArray();
        if ($this->actions) $array['actions'] = $this->actions->toArray();
        if ($this->row) {
            $array['row'] = $this->row->toArray('current', true, $this->action->alias);
            $array['row']['_system']['title'] = $this->row->title();
            //$array['row']['title'] = $this->row->title();

            // Collect aliases of all CKEditor-fields
            $ckeFieldA = array();
            foreach ($this->fields as $fieldR)
                if ($fieldR->foreign('elementId')->alias == 'html')
                    $ckeFieldA[] = $fieldR->alias;

            // Get the aliases of fields, that are CKEditor-fields
            $ckeDataA = array_intersect(array_keys($array['row']), $ckeFieldA);

            // Left-trim the {STD . '/www'} from the values of 'href' and 'src' attributes
            foreach ($ckeDataA as $ckeDataI) $array['row'][$ckeDataI]
                = preg_replace(':(\s*(src|href)\s*=\s*[\'"])(/[^/]):', '$1' . STD . '$3', $array['row'][$ckeDataI]);

        }
        if ($this->model) $array['model'] = $this->model->toArray();
        if ($this->fields) $array['fields'] = $this->fields->toArray(true);
        if ($this->gridFields) $array['gridFields'] = $this->gridFields->toArray();
        if ($this->grid) $array['grid'] = $this->grid->toNestingTree()->toArray(true);
        if ($this->disabledFields) $array['disabledFields'] = $this->disabledFields->toArray();
        if ($this->filters) $array['filters'] = $this->filters->toArray();
        if ($this->filtersSharedRow) $array['filtersSharedRow'] = $this->filtersSharedRow->toArray('current', true, true);
        if ($this->scope) $array['scope'] = $this->scope->toArray();
        $array['level'] = $this->level;
        return $array;
    }
}
<?php
class Indi_Trail_Admin_Item {

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
     * Store current trail item index
     *
     * @var int
     */
    public $level = null;

    public $filtersSharedRow = null;

    /**
     * Getter. Currently declared only for getting 'model' property
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
     * Set up all internal properties
     *
     * @param $sectionR
     * @param $level
     */
    public function __construct($sectionR, $level) {
        // Setup $this->section
        $config = array();
        $dataTypeA = array('original', 'temporary', 'compiled', 'foreign');
        foreach ($dataTypeA as $dataTypeI) $config[$dataTypeI] = $sectionR->$dataTypeI();
        $this->section = Indi::model('Section')->createRow($config);

        // Setup index
        $this->level = $level;

        // Setup section href
        $this->section->href = (COM ? '' : '/admin') . '/' . $this->section->alias;

        // Setup $this->actions
        foreach ($sectionR->nested('section2action') as $section2actionR)
            $actionA[] = $section2actionR->foreign('actionId');
        $this->actions = Indi::model('Action')->createRowset(array('rows' => $actionA));

        // Setup $this->sections
        $this->sections = $sectionR->nested('section');

        // If current trail item will be a first item
        if (count(Indi_Trail_Admin::$items) == 0) {

            // Setup filters
            $this->filters = $sectionR->nested('search');

            // Setup filters shared row
            $this->filtersSharedRow = $this->model->createRow();

            // Setup action
            foreach ($this->actions as $actionR)
                if ($actionR->alias == Indi::uri('action'))
                    $this->action = $actionR;

            // Set fields, that will be used as grid columns in case if current action is 'index'
            if (Indi::uri('action') == 'index') {
                $gridFieldA = array();
                foreach ($sectionR->nested('grid') as $gridR) {
                    foreach ($this->fields as $fieldR) {
                        if ($gridR->fieldId == $fieldR->id) {
                            $gridFieldI = $fieldR;
                            if ($gridR->alterTitle) $gridFieldI->title = $gridR->alterTitle;
                            $gridFieldA[] = $gridFieldI;
                        }
                    }
                }
                $this->gridFields = Indi::model('Field')->createRowset(array('rows' => $gridFieldA));
            }

            $this->disabledFields = $sectionR->nested('disabledField');

        } else {

            // Setup action as 'index'
            foreach ($this->actions as $actionR) if ($actionR->alias == 'index') $this->action = $actionR;
        }
    }

    /**
     * Setup rows for each trail item, if possible
     *
     * @param $index
     * @return string
     */
    public function row($index){

        // If current trail item relates to current section
        if ($index == 0) {

            // If there is an id
            if (Indi::uri('id')) {

                // If action is not 'index', so it mean that we are dealing with not rowset, but certain row
                if (Indi::uri('action') != 'index') {

                    // Get primary WHERE clause
                    $where = Indi_Trail_Admin::$controller->primaryWHERE();

                    // Prepend an additional part to WHERE clause array, so if row would be found,
                    // it will mean that that row match all necessary requirements
                    array_unshift($where, '`id` = "' . Indi::uri('id') . '"');
                    //d($where);

                    // Try to find a row by given id, that, hovewer, also match all requirements,
                    // mentioned in all other WHERE clause parts
                    if (!($this->row = $this->model->fetchRow($where)))

                        // If row was not found, return an error
                        return I_ACCESS_ERROR_ROW_DOESNT_EXIST;

                    // Else
                    else

                        // Setup several temporary properties within the existing row, as these may be involved in the
                        // process of parent trail items rows retrieving
                        for ($i = 1; $i < count(Indi_Trail_Admin::$items) - 1; $i++) {

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
                            if (!$this->model->fields($connector))
                                $this->row->$connector = $_SESSION['indi']['admin']['trail']['parentId']
                                [Indi::trail($i)->section->id];
                        }
                }

            // Else there was no id passed within uri, and action is 'form' or 'save', so we assume that
            // user it trying to add a new row within current section
            } else if (Indi::uri('action') == 'form' || Indi::uri('action') == 'save') {

                // Create an empty row object
                $this->row = $this->model->createRow();

                // Setup several properties within the empty row, e.g if we are trying to create a 'City' row, and
                // a moment ago we were browsing cities list within Canada - we should autosetup a proper `countryId`
                // property for that empty 'City' row, for ability to save it as one of Canada's cities
                for ($i = 1; $i < count(Indi_Trail_Admin::$items) - 1; $i++) {

                    // Determine the connector field between 'country' and 'city'. Usually it is '<parent-table-name>Id'
                    // but in some custom cases, this may differ. We do custom connector field autosetup only if it was
                    // set and only in case of one-level-up parent section. This mean that if we have 'Continents' as
                    // upper level, and we are creating city, city's property name will be determined as `continentId`
                    // mean parentSectionConnector logic won't be used for that
                    $connector = $i == 1 && Indi::trail($i-1)->section->parentSectionConnector
                        ? Indi::trail($i-1)->section->foreign('parentSectionConnector')->alias
                        : Indi::trail($i)->model->table() . 'Id';

                    // Get the connector value from session special place
                    //if ($this->model->fields($connector))
                        $this->row->$connector = $_SESSION['indi']['admin']['trail']['parentId']
                        [Indi::trail($i)->section->id];
                }
            }

        // Else if current trail item relates to one of parent sections
        } else {

            // Declare array for WHERE clause
            $where = array();

            // Determine the connector field
            $connector = Indi::trail($index-1)->section->parentSectionConnector
                ? Indi::trail($index-1)->section->foreign('parentSectionConnector')->alias
                : Indi::trail($index)->model->table() . 'Id';

            // Get the id
            $id = Indi::uri('action') == 'index' && $index == 1
                ? Indi::uri('id')
                : Indi::trail($index-1)->row->$connector;

            // Add main item to WHERE clause stack
            $where[] = '`id` = "' . $id . '"';

            // If a special section's primary filter was defined add it to WHERE clauses stack
            if (strlen(Indi::trail($index)->section->compiled('filter')))
                $where[] = Indi::trail($index)->section->compiled('filter');

            // Owner control
            if ($alternateWHERE = Indi_Trail_Admin::$controller->alternateWHERE($index))
                $where[] =  $alternateWHERE;

            // Try to find a row by given id, that, hovewer, also match all requirements,
            // mentioned in all other WHERE clause parts
            if (!($this->row = $this->model->fetchRow($where)))

                // If row was not found, return an error
                return false;//I_ACCESS_ERROR_ROW_DOESNT_EXIST;
        }
    }

    /**
     * Setup scope properties for trail item at $index index within Indi_Trail_Admin::$items
     *
     * @param $index
     */
    public function scope($index) {
        $this->scope = new Indi_Trail_Admin_Item_Scope($index);
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
            $array['section']['defaultSortFieldAlias'] = $this->section->foreign('defaultSortField')->alias;
        }
        if ($this->sections) $array['sections'] = $this->sections->toArray();
        if ($this->action) $array['action'] = $this->action->toArray();
        if ($this->actions) $array['actions'] = $this->actions->toArray();
        if ($this->row) {
            $array['row'] = $this->row->toArray();
            $array['row']['title'] = $this->row->title();
        }
        if ($this->model) $array['model'] = $this->model->toArray();
        if ($this->fields) $array['fields'] = $this->fields->toArray(true);
        if ($this->gridFields) $array['gridFields'] = $this->gridFields->toArray();
        if ($this->filters) $array['filters'] = $this->filters->toArray();
        if ($this->filtersSharedRow) $array['filtersSharedRow'] = $this->filtersSharedRow->toArray();
        if ($this->scope) $array['scope'] = $this->scope->toArray();
        $array['level'] = $this->level;
        return $array;
    }

    /**
     * Return the trail item, that is parent for current trail item
     *
     * @param int $step
     * @return Indi_Trail_Admin_Item object
     */
    public function parent($step = 1) {
        return Indi_Trail_Admin::$items[$this->level - $step];
    }

    /**
     * Return base id for current trail item. It is used for building unique 'id' attributes of html elements,
     * and for giving direct access to those of javascript objects, who can be got by their id
     *
     * @return string
     */
    public function bid() {

        // Basement if base id - include section alias and action alias
        $bid = 'i-section-' . $this->section->alias . '-action-' . $this->action->alias;

        // If current trail item has a row - append it's id
        if ($this->row)
            $bid .= '-row-' . (int) $this->row->id;

        // Else if current trail item doesn't have a row, but parent trail item do - append it's id
        else if ($this->parent()->row)
            $bid .= '-parentrow-' + (int) $this->parent()->row->id;

        // Return base id
        return $bid;
    }
}
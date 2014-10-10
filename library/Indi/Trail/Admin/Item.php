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

    /**
     * @var Indi_Db_Table_Row
     */
    public $filtersSharedRow = null;

    /**
     * @var Indi_View_Action_Admin
     */
    public $view;

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

        // Setup subsections
        $this->sections = $sectionR->nested('section');

        // Setup nested section2action-s for subsections
        $sectionR->nested('section')->nested('section2action', array(
            'where' => array(
                '`toggle` = "y"',
                'FIND_IN_SET("' . $_SESSION['admin']['profileId'] . '", `profileIds`)',
                'FIND_IN_SET(`actionId`, "' . implode(',', Indi_Trail_Admin::$toggledActionIdA) . '")',
                '`actionId` = "1"'
            ),
            'order' => 'move',
            'foreign' => 'actionId'
        ));

        // Exclude inaccessbile subsections from subsections list
        foreach ($sectionR->nested('section') as $subsection)
            if (!$subsection->nested('section2action')->count())
                $this->sections->exclude($subsection->id);

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

            // Setup view. This call will create an action-view object instance, especially for current trail item
            $this->view();

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
        if ($this->disabledFields) $array['disabledFields'] = $this->disabledFields->toArray();
        if ($this->filters) $array['filters'] = $this->filters->toArray();
        if ($this->filtersSharedRow) $array['filtersSharedRow'] = $this->filtersSharedRow->toArray('current', true, true);
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

    /**
     * Setup and/or return action-view object instance for trail item's action
     *
     * @param bool $render
     * @return Indi_View_Action_Admin|string
     */
    public function view($render = false) {

        // If action-view object is already set up - return it
        if ($this->view instanceof Indi_View_Action_Admin && !$render) return $this->view;

        // Setup shortcuts
        $action = $this->action->alias;
        $section = $this->section->alias;

        // Construct filename of the template, that should be rendered by default
        $script = $section . '/' . $action . '.php';

        // Build action-view class name
        $actionClass = 'Admin_' . ucfirst($section) . 'Controller' . ucfirst($action) . 'ActionView';

        // Setup $actionClassDefined flag as `false`, initially
        $actionClassDefinition = false;

        // If template with such filename exists, render the template
        if ($actionClassFile = Indi::view()->exists($script)) {

            // If $render argument is set to `true`
            if ($render) {

                // If action-view class file is in the list of already included files - this will mean
                // that we are sure about action-view class file definitely contains class declaration,
                // and this fact assumes that there is no sense of this file to be rendered, as rendering
                // won't and shouldn't give any plain output, so, instead of rendering, we return existing
                // action-view class instance
                if (in(str_replace('/', DIRECTORY_SEPARATOR, $actionClassFile), get_included_files()))
                    return $this->view;

                // Else, get the plain result of the rendered script, assignt it to `plain` property of
                // already existing action-view object instance, and return that `plain` property value individually
                else return $this->view->plain = Indi::view()->render($script);

            // Else
            } else {

                // Get the action-view-file source
                $lines = file($actionClassFile);

                // If first line consists of '<?php' string
                if (trim($lines[0]) == '<?php') {

                    // Foreach remaining lines
                    for ($i = 1; $i < count($lines); $i++) {

                        // If no enclosing php-tag found yet
                        if (!preg_match('/\?>/', trim($lines[$i]))) {

                            // If current line contains action-view class definition signature
                            if (preg_match('/class\s+' . $actionClass . '(\s|\{)/', trim($lines[$i]))) {

                                // Setup $actionClassDefinition flag as `true`
                                $actionClassDefinition = true;
                                break;
                            }

                        // Else stop searching action-view class definition signature
                        } else break;
                    }
                }

                // If action-view class definition signature was found in action-view class file - include that file
                if ($actionClassDefinition) include_once($actionClassFile);
            }

        // Else if action-view class instance already exists and $render argument is `true` - return instance
        } else if ($this->view instanceof Indi_View_Action_Admin && $render) return $this->view;


        // If such action class does not exist
        if (!class_exists($actionClass, false)) {

            // Setup default action-view parent class
            $actionParentClass = 'Project_View_Action_Admin';

            // Get modes and views config for actions
            $actionCfg = Indi_Trail_Admin::$controller->actionCfg;

            // Detect mode (rowset or row) and save into trail
            if ($mode = ucfirst($actionCfg['mode'][$action])) {
                $actionParentClass .= '_' . $mode;
                $this->action->mode = $mode;
            }

            // Detect view and save into trail
            if ($view = ucfirst($actionCfg['view'][$action])) {
                $actionParentClass .= '_' . $view;
                $this->action->view = $view;
            }

            // If such action parent class does not exist - replace 'Project' with 'Indi' within it's name
            if (!class_exists($actionParentClass)) $actionParentClass = preg_replace('/^Project/', 'Indi', $actionParentClass);

            // Auto-declare action-view class
            eval('class ' . ucfirst($actionClass) . ' extends ' . $actionParentClass . '{}');

        // Else
        } else {

            // Get action-view parent class name
            $actionParentClass = get_parent_class($actionClass);

            // If action-view parent class name contains mode definition
            if (preg_match('/(Indi|Project)_View_Action_Admin_(Row|Rowset)/', $actionParentClass, $mode)) {

                // Pick that mode and assing it as a property of trail item's action object
                $this->action->mode = $mode[2];

                // If action-view parent class name also contains view definition
                if (preg_match('/' . $mode[0]. '_([A-Z][A-Za-z0-9_]*)/', $actionParentClass, $view))

                    // Pick that view and also assign it as a property of trail item's action object
                    $this->action->view = $view[1];
            }
        }

        // Get the action-view instance
        return $this->view = new $actionClass();
    }
}
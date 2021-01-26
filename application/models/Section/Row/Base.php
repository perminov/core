<?php
class Section_Row_Base extends Indi_Db_Table_Row {

    /**
     * Function is redeclared to provide an ability for `href` pseudo property to be got
     *
     * @param string $property
     * @return string
     */
    public function __get($property) {
        return $property == 'href' ? PRE . '/' . $this->alias : parent::__get($property);
    }

    /**
     * This method was redefined to provide ability for some field
     * props to be set using aliases rather than ids
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     */
    public function __set($columnName, $value) {

        // Provide ability for some field props to be set using aliases rather than ids
        if (is_string($value) && !Indi::rexm('int11', $value)) {
            if (in($columnName, 'parentSectionConnector,groupBy,defaultSortField,tileField')) $value = field($this->entityId, $value)->id;
            else if ($columnName == 'entityId') $value = entity($value)->id;
            else if ($columnName == 'sectionId') $value = section($value)->id;
            else if ($columnName == 'tileThumb') $value = thumb($this->entityId, $this->tileField, $value)->id;
        }

        // Standard __set()
        parent::__set($columnName, $value);
    }

    /**
     * Setup grid
     *
     * @return int
     */
    public function save() {

        // Clear value for `expandRoles` prop, if need
        if (in($this->expand, 'all,none')) $this->expandRoles = '';

        // If entity was changed
        if (isset($this->_modified['entityId'])) {

            // Grid model
            $gridM = Indi::model('Grid');

            // Delete old grid info when assotiated entity has changed
            $gridM->fetchAll('`sectionId` = "' . $this->id . '"')->delete();

            // Set up new grid, if assotiated entity remains not null, event after change
            if ($this->_modified['entityId']) {

                // Get entity fields as grid columns candidates
                $fields = Indi::model('Field')->fetchAll('`entityId` = "' . $this->_modified['entityId'] . '"', '`move`')->toArray();
                if (count($fields)) {

                    // Declare exclusions array, because not each entity field will have corresponding column in grid
                    $exclusions = array();

                    // Exclude tree column, if exists
                    if ($model = Indi::model($this->_modified['entityId'])) {
                        if ($model->treeColumn()) {
                            $exclusions[] = $model->treeColumn();
                        }
                    }

                    // Exclude columns that have controls of several types, listed below
                    for ($i = 0; $i < count($fields); $i++) {
                        // 13 - html-editor
                        if (in_array($fields[$i]['elementId'], array(13))) {
                            if ($fields[$i]['elementId'] == 6 && $fields[$i]['alias'] == 'title') {} else {
                                $exclusions[] = $fields[$i]['alias'];
                            }
                        }
                    }

                    // Exclude columns that are links to parent sections
                    $parentSectionId = $this->sectionId;
                    do {
                        $parentSection = $this->model()->fetchRow('`id` = "' . $parentSectionId . '"');
                        if ($parentSection && $parentEntity = $parentSection->foreign('entityId')){
                            for ($i = 0; $i < count($fields); $i++) {
                                if ($fields[$i]['alias'] == $parentEntity->table . 'Id' && $fields[$i]['relation'] == $parentEntity->id) {
                                    $exclusions[] = $fields[$i]['alias'];
                                }
                            }
                            $parentSectionId = $parentSection->sectionId;
                        }
                    } while ($parentEntity);

                    // We need to call parent::save() function, because it will set $this->id, which will be used in
                    // a process of grid columns creation
                    parent::save();

                    // Create grid, stripping exclusions from final grid column list
                    $lastPosition = $gridM->getNextMove();
                    $j = 0; $gridId = 0;
                    for ($i = 0; $i < count($fields); $i++) {
                        if (!in_array($fields[$i]['alias'], $exclusions)) {
                            $gridR = $gridM->createRow();
                            $gridR->gridId = $fields[$i]['elementId'] == 16 ? 0 : $gridId;
                            $gridR->sectionId = $this->id;
                            $gridR->fieldId = $fields[$i]['id'];
                            $gridR->move = $lastPosition + $j - 1;
                            $gridR->save();
                            $j++;
                            if ($fields[$i]['elementId'] == 16) $gridId = $gridR->id;
                        }
                    }
                } else return parent::save();
            } else return parent::save();
        } else return parent::save();
    }

    /**
     * Convert values of `defaultSortField` and `defaultSortDirection`
     * into a special kind of json-encoded array, suitable for usage
     * with ExtJS
     *
     * Also, before json-encoding, prepend additional item into that array,
     * responsible for grouping, if `groupBy` prop is set
     *
     * @return string
     */
    public function jsonSort() {

        // Blank array
        $json = array();

        // Mind grouping, as it also should be involved while building ORDER clause
        if ($this->groupBy && $this->foreign('groupBy'))
            $json[] = array(
                'property' => $this->foreign('groupBy')->alias,
                'direction' => 'ASC'
            );

        // Ming sorting
        if ($this->foreign('defaultSortField'))
            $json[] = array(
                'property' => $this->foreign('defaultSortField')->alias,
                'direction' => $this->defaultSortDirection,
            );

        // Return json-encoded sort params
        return json_encode($json);
    }

    /**
     * Build a string, that will be used in Section_Row_Base->export()
     *
     * @return string
     */
    protected function _ctor() {

        // Use original data as initial ctor
        $ctor = $this->_original;

        // Exclude `id` and `move` as they will be set automatically by MySQL and Indi Engine, respectively
        unset($ctor['id'], $ctor['move']);

        // Exclude props that are already represented by one of shorthand-fn args
        foreach (ar('alias') as $arg) unset($ctor[$arg]);

        // Foreach $ctor prop
        foreach ($ctor as $prop => &$value) {

            // Get field
            $field = Indi::model('Section')->fields($prop);

            // Exclude prop, if it has value equal to default value
            if ($field->defaultValue == $value) unset($ctor[$prop]);

            // Else if prop contains keys - use aliases instead
            else if ($field->storeRelationAbility != 'none') {
                if ($prop == 'sectionId') $value = section($value)->alias;
                else if ($prop == 'entityId') $value = entity($value)->table;
                else if (in($prop, 'parentSectionConnector,groupBy,defaultSortField,tileField')) $value = field($this->entityId, $value)->alias;
                else if ($prop == 'tileThumb') $value = m('Resize')->fetchRow($value)->alias;
            }
        }

        // Stringify and return $ctor
        return _var_export($ctor);
    }

    /**
     * Build an expression for creating the current `section` entry in another project, running on Indi Engine
     *
     * @return string
     */
    public function export() {

        // Build `section` entry creation expression
        $lineA[] = "section('" . $this->alias . "', " . $this->_ctor() . ");";

        // Foreach `section2action` entry, nested within current `section` entry
        // - build `section2action` entry's creation expression
        foreach ($this->nested('section2action', array('order' => 'move')) as $section2actionR)
            $lineA[] = $section2actionR->export();

        // Foreach `grid` entry, nested within current `section` entry
        // - build `grid` entry's creation expression
        foreach ($this->nested('grid', array('order' => 'move')) as $gridR)
            $lineA[] = $gridR->export();

        // Foreach `alteredField` entry, nested within current `section` entry
        // - build `alteredField` entry's creation expression
        foreach ($this->nested('alteredField') as $alteredFieldR)
            $lineA[] = $alteredFieldR->export();

        // Foreach `filter` entry, nested within current `section` entry
        // - build `filter` entry's creation expression
        foreach ($this->nested('search', array('order' => 'move')) as $filterR)
            $lineA[] = $filterR->export();

        // Return newline-separated list of creation expressions
        return im($lineA, "\n");
    }

    /**
     * Prevent `extendsPhp` and `extendsJs` props from being empty
     */
    public function onBeforeSave() {

        // Setup default value instead of empty value
        foreach (ar('extendsPhp,extendsJs') as $prop)
            if ($this->isModified($prop) && !$this->$prop)
                $this->$prop = $this->field($prop)->defaultValue;
    }
}
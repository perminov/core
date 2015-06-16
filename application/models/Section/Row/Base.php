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
     * Setup grid
     *
     * @return int
     */
    public function save() {

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
                        // 6 - text
                        // 13 - html-editor
                        // 14 - file upload
                        // 16 - span (group of fields)
                        if (in_array($fields[$i]['elementId'], array(6, 13, 14))) {
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
     * @return string
     */
    public function jsonSort() {

        // If no default sort - return empty json array
        if (!$this->foreign('defaultSortField')) return '[]';

        // Return json-encoded sort params
        return json_encode(array(array(
            'property' => $this->foreign('defaultSortField')->alias,
            'direction' => $this->defaultSortDirection,
        )));
    }
}
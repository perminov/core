<?php
class Section_Row_Base extends Indi_Db_Table_Row
{
    public function delete(){
        // delete controller class if exists
        //$this->deleteControllerClassIfExists();

        // standart Indi_Db_Table_Row deletion
        parent::delete();
    }

    public function deleteControllerClassIfExists(){
        $controllersDir = rtrim($_SERVER['DOCUMENT_ROOT'] . '/www', '/') . $_SERVER['STD'] . '/application/controllers/admin/';
        $controllerFile = $controllersDir . ucfirst($this->alias) . 'Controller.php';
        if (file_exists($controllerFile)) {
            unlink($controllerFile);
        }
    }

    public function getFilters() {
        $filterM = Misc::loadModel('Search');
        $filterRs = $filterM->fetchAll('`sectionId` = "' . $this->id . '" AND `toggle` = "y"', 'move');
        $data['foundRows'] = $filterRs->foundRows;
        $filterA = $filterRs->toArray();
        $fieldIds = array(); foreach ($filterA as $filterI) $fieldIds[] = $filterI['fieldId'];
        $fieldRs = Misc::loadModel('Field')->fetchAll('FIND_IN_SET(`id`, "' . implode(',', $fieldIds) . '")');
        $fieldRs->setForeignRowsByForeignKeys('columnTypeId,elementId');
        for ($i = 0; $i < count($filterA); $i++) {
            foreach ($fieldRs as $fieldR) {
                if ($filterA[$i]['fieldId'] == $fieldR->id) {
                    $filterA[$i]['foreign']['fieldId'] = $fieldR;
                    $data['data'][] = $filterA[$i];
                }
            }
        }
        return $filterM->createRowset($data);
    }

    public function save(){
        // If entity was changed
        if (isset($this->_modified['entityId'])) {

            // Grid model
            $gridM = Misc::loadModel('Grid');

            // Delete old grid info when assotiated entity has changed
            $gridM->fetchAll('`sectionId` = "' . $this->id . '"')->delete();

            // Set up new grid, if assotiated entity remains not null, event after change
            if ($this->_modified['entityId']) {

                // Get entity fields as grid columns candidates
                $fields = Misc::loadModel('Field')->fetchAll('`entityId` = "' . $this->_modified['entityId'] . '"', 'move')->toArray();
                if (count($fields)) {

                    // Declare exclusions array, because not each entity field will have corresponding column in grid
                    $exclusions = array();

                    // Exclude tree column, if exists
                    if ($model = Misc::loadModel('Entity')->getModelById($this->_modified['entityId'])) {
                        if ($treeColumnName = $model->getTreeColumnName()) {
                            $exclusions[] = $treeColumnName;
                        }
                    }

                    // Exclude columns that have controls of several types, listed below
                    for ($i = 0; $i < count($fields); $i++) {
                        // 6 - text
                        // 13 - html-editor
                        // 14 - file upload
                        // 16 - span (group of fields)
                        if (in_array($fields[$i]['elementId'], array(6, 13, 14, 16))) {
                            if ($fields[$i]['elementId'] == 6 && $fields[$i]['alias'] == 'title') {} else {
                                $exclusions[] = $fields[$i]['alias'];
                            }
                        }
                    }

                    // Exclude columns that are links to parent sections
                    $parentSectionId = $this->sectionId;
                    do {
                        $parentSection = $this->_table->fetchRow('`id` = "' . $parentSectionId . '"');
                        if ($parentSection && $parentEntity = $parentSection->getForeignRowByForeignKey('entityId')){
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

                    // create grid, stripping exclusions from final grid column list
                    $lastPosition = $gridM->getLastPosition();
                    $j = 0;
                    for ($i = 0; $i < count($fields); $i++) {
                        if (!in_array($fields[$i]['alias'], $exclusions)) {
                            $gridR = $gridM->createRow();
                            $gridR->sectionId = $this->id;
                            $gridR->fieldId = $fields[$i]['id'];
                            $gridR->move = $lastPosition + $j - 1;
                            $gridR->save();
                            $j++;
                        }
                    }
                } else return parent::save();
            } else return parent::save();
        } else return parent::save();
    }
}
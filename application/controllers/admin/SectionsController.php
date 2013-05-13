<?php
class Admin_SectionsController extends Indi_Controller_Admin
{
    public function postSave(){
        // delete old grid info when assotiated entity has changed
        if ($this->post['entityId'] != $this->trail->getItem()->row->entityId) {
            $this->db->query('DELETE FROM `grid` WHERE `sectionId` = "' . $this->identifier . '"');
        }
        // set up new grid
        if ($this->post['entityId']) {
            if (!$this->trail->getItem()->row || ($this->trail->getItem()->row && !$this->trail->getItem()->row->entityId)) {
                $field = new Field();
                $grid = new Grid();
                $fields = $field->fetchAll('`entityId`="' . $this->post['entityId'] . '"')->toArray();
                if (count($fields)) {
                    $exclusions = array();
                    if ($model = Misc::loadModel('Entity')->getModelById($this->post['entityId'])) {
                        if ($treeColumnName = $model->getTreeColumnName()) {
                           $exclusions[] = $treeColumnName;
                        }
                    }
                    for ($i = 0; $i < count($fields); $i++) {
                        if (in_array($fields[$i]['elementId'], array(6, 13, 14, 16))) {
                            if ($fields[$i]['elementId'] == 6 && $fields[$i]['alias'] == 'title') {} else {
                                $exclusions[] = $fields[$i]['alias'];
                            }
                        }
                        do {
                            $parentSection = $this->trail->getItem()->model->fetchRow('`id` = "' . $this->post['sectionId'] . '"');
                            if ($parentSection && $parentEntity = $parentSection->getForeignRowByForeignKey('entityId')){
                                if ($fields[$i]['alias'] == $parentEntity->table . 'Id' && $fields[$i]['relation'] == $parentEntity->id) {
                                    $exclusions[] = $fields[$i]['alias'];
                                }
                                $this->post['sectionId'] = $parentSection->sectionId;
                            }
                        } while ($parentEntity);
                    }
                    $query = 'INSERT INTO `grid` VALUES ';
                    for ($i = 0; $i < count($fields); $i++) {
                        if (!in_array($fields[$i]['alias'], $exclusions)) {
                            $values[] = '(NULL, ' . $this->identifier. ',' . $fields[$i]['id'] . ',' . ($grid->getLastPosition() + $i - 1) . ')';
                        }
                    }
                    $query .= implode(',', $values) . ';';
                    $this->db->query($query);
                }
            }
        }
    }
}
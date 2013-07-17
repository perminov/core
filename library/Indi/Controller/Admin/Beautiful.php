<?php
class Indi_Controller_Admin_Beautiful extends Indi_Controller{

    /**
     * Provide default downAction (Move down) for Admin Sections controllers
     *
     * @param string $condition
     */
    public function downAction($condition = null)
    {
        $this->move('down', $condition);
    }

    /**
     * Provide default upAction (Move up) for Admin Sections controllers
     *
     * @param string $condition
     */
    public function upAction($condition = null)
    {
        $this->move('up', $condition);
    }

    /**
     * Gets $within param and call $row->move() method with that param.
     * This was created just for use in in $controller->downAction() and $controller->upAction()
     *
     * @param $direction
     * @param null $condition
     */
    public function move($direction, $condition = null) {
        // Get the scope of rows to move within
        if ($this->trail->getItem()->section->parentSectionConnector) {
            $within = $this->trail->getItem()->section->getForeignRowByForeignKey('parentSectionConnector')->alias;
        } else if ($this->trail->getItem(1)->row){
            $within = $this->trail->getItem(1)->section->getForeignRowByForeignKey('entityId')->table . 'Id';
        }
        // Move
        $this->row->move($direction, $within, $this->trail->getItem()->section->filter);
        $this->postMove();
        $this->redirectToIndex();
    }

    /**
     * Provide delete action
     *
     */
    public function deleteAction()
    {
        $this->preDelete();
        $this->row->delete();
        $this->postDelete();
        $this->redirectToIndex();
    }

    public function formAction()
    {
        if ($this->params['combo']) {
            // Get options
            if ($this->post['keyword']) {
                $comboDataRs = $this->row->getComboData($this->post['field'], $this->post['page'], $this->post['keyword'], true);
            } else {
                $comboDataRs = $this->row->getComboData($this->post['field'], $this->post['page'], $this->row->{$this->post['field']});
            }

            // Prepare options
            $options = array();
            foreach ($comboDataRs as $comboDataR) {
                $options[$comboDataR->id] = array('title' => $comboDataR->title, 'system' => $comboDataR->system());
            }
            $options = array('ids' => array_keys($options), 'data' => array_values($options));

            // Setup number of found rows
            if ($comboDataRs->foundRows) $options['found'] = $comboDataRs->foundRows;

            // Setup tree flag
            if ($comboDataRs->getTable()->treeColumn) $options['tree'] = true;

            // Output
            die(json_encode($options));
        }
        /*if ($this->params['json'] && false) {
            $fields = $this->trail->getItem()->fields;
            for($i = 0; $i < $fields->count(); $i++) {
                if ($fields[$i]->alias == $this->post['field']) {
                    $elementAlias = Entity::getModelByTable('element')->fetchRow('`id` = "' . $fields[$i]->elementId . '"')->alias;
                    if ($fields[$i]->dependency == 'e') {
                        $entityId = $this->row->getEntityIdForVariableForeignKey($this->post['field'], $this->post['satellite']);
                        $this->trail->items[count($this->trail->items)-1]->fields[$i]->relation = $entityId;
                    } else if ($fields[$i]->dependency != 'u') {
                        for($j = 0; $j < $fields->count(); $j++) {
                            if ($fields[$i]->satellite == $fields[$j]->id) {
                                if ($fields[$i]->alternative) {
                                    $satelliteRow = Entity::getInstance()->getModelById($fields[$j]->relation)->fetchRow('`id` = "' .$this->post['satellite'] . '"');
                                    $fields[$j]->alias = $fields[$i]->alternative;
                                    $this->post['satellite'] = $satelliteRow->{$fields[$i]->alternative};
                                }
                                $this->trail->items[count($this->trail->items)-1]->dropdownWhere[$fields[$i]->alias] = 'FIND_IN_SET("' . $this->post['satellite'] . '", `' . ($fields[$j]->satellitealias ? $fields[$j]->satellitealias : $fields[$j]->alias) . '`)';
                            }
                        }
                    } else if ($fields[$i]->dependency == 'u') {
                    }
                }
            }
            $this->view->trail = $this->trail;
            $this->view->row = $this->row;
            switch ($elementAlias) {
                case 'select':
                    $element = $this->view->formSelect($this->post['field'], null, array('optionsOnly' => true));
                    break;
                case 'dselect':
                    $attribs = array(
                        'optionsOnly' => true,
                        'find' => str_replace('"','\"', $this->post['find']),
                        'value' => $this->post['value'],
                        'more' => $this->post['more'] == 'true' ? true : false,
                        'element' => 'dselect'
                    );
                    if (isset($this->post['noempty'])) $attribs['noempty'] = $this->post['noempty'];
                    if (isset($this->post['page'])) $attribs['page'] = $this->post['page'];
                    if (isset($this->post['up'])) $attribs['up'] = $this->post['up'];

                    $element = $this->view->formDselect($this->post['field'], null, $attribs);
                    break;
                case 'multicheck':
                    $element = $this->view->formMulticheck($this->post['field'], 1, array('optionsOnly' => true));
                    break;
            }
            die($element);
        }*/
    }


}
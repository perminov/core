<?php
class Indi_Controller_Admin_Beautiful extends Indi_Controller{

    /**
     * Method for using custom part of WHERE clause, especially related to rowset filtering by parent
     * Return null by default, so in usual conditions it won't be used. But if redeclared - wil be used.
     *
     * @return null
     */
    public function specialParentCondition() {
        return null;
    }

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
                $comboDataRs = $this->row->getComboData($this->post['field'], $this->post['page'], $this->post['keyword'], true, $this->post['satellite']);
            } else {
                $comboDataRs = $this->row->getComboData($this->post['field'], $this->post['page'], $this->row->{$this->post['field']}, false, $this->post['satellite']);
            }

            $options = array();

            // If 'optgroup' param is used
            if ($comboDataRs->optgroup) {
                $by = $comboDataRs->optgroup['by'];
            }

            // Detect key property for options
            $keyProperty = $comboDataRs->enumset ? 'alias' : 'id';

            foreach ($comboDataRs as $o) {
                $system = $o->system();
                if ($by) $system = array_merge($system, array('group' => $o->$by));
                $options[$o->$keyProperty] = array('title' => Misc::usubstr($o->title, 50), 'system' => $system);

                // Deal with optionAttrs, if specified.
                if ($comboDataRs->optionAttrs) {
                    for ($i = 0; $i < count($comboDataRs->optionAttrs); $i++) {
                        $options[$o->$keyProperty]['attrs'][$comboDataRs->optionAttrs[$i]] = $o->{$comboDataRs->optionAttrs[$i]};
                    }
                }
            }
            $options = array('ids' => array_keys($options), 'data' => array_values($options));

            // Setup number of found rows
            if ($comboDataRs->foundRows) $options['found'] = $comboDataRs->foundRows;

            // Setup tree flag
            if ($comboDataRs->getTable()->treeColumn) $options['tree'] = true;

            // Setup groups for options
            if ($comboDataRs->optgroup) $options['optgroup'] = $comboDataRs->optgroup;

            // Setup additional attributes names list
            if ($comboDataRs->optionAttrs) $options['attrs'] = $comboDataRs->optionAttrs;

            // Output
            die(json_encode($options));
        }
    }


}
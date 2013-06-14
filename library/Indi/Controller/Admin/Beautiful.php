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

}
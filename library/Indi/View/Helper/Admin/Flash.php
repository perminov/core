<?php
class Indi_View_Helper_Admin_Flash extends Indi_View_Helper_Abstract
{
    public function flash($entity = null, $id = null, $name = null, $silence = true)
    {
        static $index = null;

        $entity = $entity ? $entity : $this->view->entity->table;
        $id = $id ? $id : $this->view->row->id;

        if ($name === null) {
            if ($index !== null) {
                $index++;
                $name = $index;
            } else {
                $index = 1;
            }
        }

        $xhtml = Indi_Image::flash($entity, $id, $name, $copy, $silence);
        
        return $xhtml;
    }
}
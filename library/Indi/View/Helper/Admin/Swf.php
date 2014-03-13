<?php
class Indi_View_Helper_Admin_Swf extends Indi_View_Helper_Abstract
{
    public function swf($entity = null, $id = null, $name = null, $silence = true, $width = null, $height = null)
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

        $xhtml = Indi_Image::flash($entity, $id, $name, $silence, $width, $height);
        
        return $xhtml;
    }
}
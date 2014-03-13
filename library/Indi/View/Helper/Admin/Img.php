<?php
class Indi_View_Helper_Admin_Img extends Indi_View_Helper_Abstract
{
    public function img($entity = null, $id = null, $name = null, $copy = null, $silence = true, $width = null, $height = null)
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

        $xhtml = Indi_Image::image($entity, $id, $name, $copy, $silence, $width, $height);
        
        return $xhtml;
    }
}
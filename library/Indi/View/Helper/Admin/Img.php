<?php
class Indi_View_Helper_Admin_Img {
    public function img($entity = null, $id = null, $name = null, $copy = null, $silence = true, $width = null, $height = null)
    {
        static $index = null;

        $entity = $entity ? $entity : Indi::trail()->model->table();
        $id = $id ? $id : Indi::view()->row->id;

        if ($name === null) {
            if ($index !== null) {
                $index++;
                $name = $index;
            } else {
                $index = 1;
            }
        }

        $xhtml = Indi::img($entity, $id, $name, $copy, array('width' => $width, 'height' => $height));
        
        return $xhtml;
    }
}
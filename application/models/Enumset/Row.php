<?php
class Enumset_Row extends Indi_Db_Table_Row{
    public function getTitle(){
        if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $this->alias, $matches)) {
            return '<span class="color-box" style="background: #' . $matches[1] . ';"></span> '. parent::getTitle();
        } else {
            return parent::getTitle();
        }
    }
}
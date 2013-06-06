<?php
class Admin_EnumsetController extends Indi_Controller_Admin{
    public function formAction() {
        if (preg_match('/^[0-9]{3}#([0-9a-fA-F]{6})$/', $this->row->alias, $matches)) {
            $this->row->modified('alias', '#' . $matches[1]);
        }
        parent::formAction();
    }
}
<?php
class Admin_FieldsController extends Indi_Controller_Admin {

    /**
     * Action function is redeclared to provide a strip hue part from $this->row->defaultValue
     */
    public function formAction() {

        // If $this->row->defaultValue is a color in format 'hue#rrggbb'
        if (preg_match(Indi::rex('hrgb'), $this->row->defaultValue))

            // Strip hue part from that color, for it to be displayed in form without hue
            $this->row->modified('defaultValue', substr($this->row->defaultValue, 3));

        // Default form action
        parent::formAction();
    }
}
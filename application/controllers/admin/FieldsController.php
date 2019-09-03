<?php
class Admin_FieldsController extends Indi_Controller_Admin_Exportable {

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

    /**
     * Change mode for selected fields
     */
    public function activateAction() {

        // Build combo config for that field
        $combo = ['fieldLabel' => '', 'allowBlank' => 0] + t()->row->combo('mode');

        // Get field title
        $title = mb_strtolower($this->row->field('mode')->title);

        // Show prompt and obtain data
        $prompt = $this->prompt('Пожалуйста, выберите ' . $title, [$combo]);

        // Save new mode
        foreach ($this->selected as $selected) $selected->assign(array('mode' => $prompt['mode']))->save();

        // Flush success
        jflush(true);
    }
}
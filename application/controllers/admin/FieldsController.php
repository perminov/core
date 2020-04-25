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

        // Disable `l10n` field
        $this->appendDisabledField('l10n', true);

        // Default form action
        parent::formAction();
    }

    /**
     * Change mode for selected fields
     */
    public function activateAction() {

        // Build combo config for that field
        $combo = array('fieldLabel' => '', 'allowBlank' => 0) + t()->row->combo('mode');

        // Get field title
        $title = mb_strtolower($this->row->field('mode')->title);

        // Show prompt and obtain data
        $prompt = $this->prompt('Пожалуйста, выберите ' . $title, array($combo));

        // Save new mode
        foreach ($this->selected as $selected) $selected->assign(array('mode' => $prompt['mode']))->save();

        // Flush success
        jflush(true);
    }

    /**
     * Create `queueTask` entry
     *
     * @param $cell
     * @param $value
     */
    public function onBeforeCellSave($cell, $value) {

        // If $cell is not 'l10n' - skip
        if ($cell != 'l10n') return;

        // Ask whether we want to turn l10n On/Off,
        // or want to arrange value of `l10n` for it to match real situation.
        $answer = $this->confirm(sprintf(
            'Если вы хотите включить мультиязычность для поля "%s" нажмите "%s". ' .
            'Если просто нужно привести в соответствие с текущим состоянием - нажмите "%s"',
            t()->row->title, I_YES, I_NO), 'YESNOCANCEL');

        // If we just want to arrange
        if ($answer == 'no') return; else if ($value != 'q') return;

        // Create phantom `langId` field
        $langId_combo = Indi::model('Field')->createRow([
            'alias' => 'langId',
            'columnTypeId' => 'INT(11)',
            'elementId' => 'combo',
            'storeRelationAbility' => 'one',
            'relation' => 'lang',
            'filter' => '"y" IN (`' . im($fraction = ar(t()->row->l10nFraction()), '`, `') . '`)',
            'mode' => 'hidden',
            'defaultValue' => 0
        ], true);

        // Append to fields list
        t()->model->fields()->append($langId_combo);

        // Build config for langId-combo
        $combo = ['fieldLabel' => '', 'allowBlank' => 0] + t()->row->combo('langId');

        // Prompt for source language
        $prompt = $this->prompt(sprintf('Выберите текущий язык поля "%s"', t()->row->title), [$combo]);

        // Check prompt data
        $_ = jcheck(['langId' => ['req' => true, 'rex' => 'int11', 'key' => 'lang']], $prompt);

        // Build queue class name
        $queueClassName = 'Indi_Queue_L10n_FieldToggleL10n' . ucfirst($value == 'q' ? 'y' : $value);

        // Check that class exists
        if (!class_exists($queueClassName)) jflush(false, sprintf('Не найден класс %s', $queueClassName));

        // Create queue class instance
        $queue = new $queueClassName();

        // Get target langs
        $target = [];
        foreach ($fraction as $fractionI) $target[$fractionI] = Indi::model('Lang')->fetchAll([
            '`' . $fractionI . '` = "y"',
            '`alias` != "' . $_['langId']->alias . '"'
        ])->column('alias', true);

        // Run first stage
        $queue->chunk(array(
            'field' => Indi::model(t()->row->entityId)->table() . ':' . t()->row->alias,
            'source' => $_['langId']->alias,
            'target' => count($target) > 1 ? $target : current($target)
        ));
    }
}
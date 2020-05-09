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

        // If we're going to create queue task for turning selected language either On or Off
        if (in($value, 'qy,qn')) {

            // Ask whether we want to turn l10n On/Off,
            // or want to arrange value of `l10n` for it to match real situation.
            if ('no' == $this->confirm(sprintf(
                'Если вы хотите %s мультиязычность для поля "%s" нажмите "%s". ' .
                'Если просто нужно привести в соответствие с текущим состоянием - нажмите "%s"',
                $value == 'qy' ? 'включить' : 'выключить', t()->row->title, I_YES, I_NO), 'YESNOCANCEL'))
                return;

        // Else if we're going to setup fraction-status directly
        } else if ('ok' == $this->confirm(sprintf(
            'Для поля "%s" мультиязычность будет вручную указана как "%s". Продолжить?',
            t()->row->title, t()->row->enumset($cell, $value)
        ), 'OKCANCEL'))
            return;

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
        $prompt = $this->prompt(sprintf(
            $value == 'qy' ? 'Выберите текущий язык поля "%s"' : 'Выберите язык который должен остаться в поле "%s"',
            t()->row->title
        ), [$combo]);

        // Check prompt data
        $_ = jcheck(['langId' => ['req' => true, 'rex' => 'int11', 'key' => 'lang']], $prompt);

        // Build queue class name
        $queueClassName = 'Indi_Queue_L10n_FieldToggleL10n';

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

        // Prepare params
        $params = [
            'field' => Indi::model(t()->row->entityId)->table() . ':' . t()->row->alias,
            'source' => $_['langId']->alias
        ];

        // If we're dealing with `action` entity's `title` field
        if ($params['field'] == 'action:title' && !$_ = []) {

            // Collect all target languages
            foreach ($target as $targets) $_ = array_unique(array_merge($_, ar($targets)));

            // Pass separately, to be used for root-level `queueChunk` entry ('action:title')
            $params['rootTarget'] = im($_);
        }

        // Prepare params
        $params['target'] = $target;

        // If we're going to turn l10n On for this field - specify target languages,
        // else setup 'toggle' param as 'n', indicating that l10n will be turned On for this field
        if ($value != 'qy') $params['toggle'] = 'n';

        // Run first stage
        $queue->chunk($params);
    }
}
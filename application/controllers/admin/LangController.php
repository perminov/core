<?php
use Google\Cloud\Translate\V2\TranslateClient;

class Admin_LangController extends Indi_Controller_Admin {

    /**
     * @return mixed
     */
    public function dictAction() {

        // Require Google Cloud Translation PHP API
        require_once('google-cloud-php-translate-1.6.0/vendor/autoload.php');

        // Get languages, already existing as `lang` entries
        $langA = Indi::db()->query('SELECT `alias`, `title` FROM `lang`')->fetchAll(PDO::FETCH_KEY_PAIR);

        // Create Google Cloud Translation PHP API
        $gapi = new TranslateClient(['key' => Indi::ini('lang')->gapi->key]);

        // New languages counter
        $l = 0;

        // Get languages, available from google
        foreach ($gapi->localizedLanguages() as $langI) {

            // If `lang` entry is already created - skip
            if ($langA[$langI['code']]) continue;

            // Increment new languages counter
            $l++;

            // Create `lang` entry
            Indi::model('Lang')->createRow(array(
                'title' => $langI['name'],
                'alias' => $langI['code'],
                'toggle' => 'n'
            ), true)->save();
        }

        // Flush result
        jflush(true, 'Новых языков: ' .  $l);
    }

    /**
     * Create `queueTask` entry
     *
     * @param $cell
     * @param $value
     */
    public function onBeforeCellSave($cell, $value) {

        // Get field
        $fieldR = t()->model->fields($cell);

        // Skip if $cell is not a l10n-fraction field
        if ($fieldR->rel()->table() != 'enumset' || $cell == 'toggle') return;

        // Get fraction
        $fraction = $fieldR->nested('grid')->column('title', ' - ');

        // If we're going to create queue task for turning selected language either On or Off
        if (in($value, 'qy,qn')) {

            // Ask what we're going to do
            if ('no' == $this->confirm(sprintf(
                'Если вы хотите %s язык "%s" для фракции "%s" нажмите "%s". ' .
                'Если просто нужно привести в соответствие с текущим состоянием - нажмите "%s"',
                $value == 'qy' ? 'добавить' : 'удалить', t()->row->title, $fraction, I_YES, I_NO), 'YESNOCANCEL'))
                return;

        // Else if we're going to setup fraction-status directly
        } else if ('ok' == $this->confirm(sprintf(
            'Для фракции "%s" язык "%s" будет вручную помечен как "%s". Продолжить?',
            $fraction, t()->row->title, t()->row->enumset($cell, $value)
        ), 'OKCANCEL'))
            return;

        // If we're going to add new translation
        if ($value == 'qy') {

            // Create phantom `langId` field
            $langId_combo = Indi::model('Field')->createRow([
                'title' => 'asdasd',
                'alias' => 'langId',
                'columnTypeId' => 'INT(11)',
                'elementId' => 'combo',
                'storeRelationAbility' => 'one',
                'relation' => 'lang',
                'filter' => '`id` != "' . t()->row->id . '" AND `' . $cell . '` = "y"',
                'mode' => 'hidden',
                'defaultValue' => 0
            ], true);

            // Append to fields list
            t()->model->fields()->append($langId_combo);

            // Build config for langId-combo
            $combo = ['fieldLabel' => '', 'allowBlank' => 0] + t()->row->combo('langId');

            // Prompt for source language
            $prompt = $this->prompt('Выберите исходный язык', [$combo]);

            // Check prompt data
            $_ = jcheck(['langId' => ['req' => true, 'rex' => 'int11', 'key' => 'lang']], $prompt);

            // Prepare params
            $params = ['source' => $_['langId']->alias, 'target' => t()->row->alias];

        // Else
        } else {

            // Prepare params
            $params = ['source' => t()->row->alias, 'toggle' => 'n'];
        }

        // Build queue class name
        $queueClassName = 'Indi_Queue_L10n_' . ucfirst($cell);

        // Check that class exists
        if (!class_exists($queueClassName)) jflush(false, sprintf('Не найден класс %s', $queueClassName));

        // Create queue class instance
        $queue = new $queueClassName();

        // Run first stage
        $queueTaskR = $queue->chunk($params);

        // Auto-start queue as a background process, for now only for *Const-fractions
        if (preg_match('~Const$~', $cell)) Indi::cmd('queue', array('queueTaskId' => $queueTaskR->id));
    }
}
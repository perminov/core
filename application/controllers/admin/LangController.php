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
     *
     */
    public function constantsAction() {

        // Translate constants for core- and www- levels
        $this->constants('core,www', $this->row->alias);

        // Flush success
        jflush(true, 'OK');
    }

    /**
     * Translate constants from current language to target language
     *
     * @param $levels
     * @param $target
     */
    public function constants($levels, $target) {

        // Required Google Cloud Translation PHP API
        require_once('google-cloud-php-translate-1.6.0/vendor/autoload.php');

        // Get available languages
        $langA = Indi::db()->query('SELECT `alias`, `title`, `toggle` FROM `lang` WHERE `toggle` = "y"')->fetchAll();

        // Get default/current language
        $source = in($_COOKIE['lang'], array_column($langA, 'alias')) ? $_COOKIE['lang'] : Indi::ini('lang')->admin;

        // If source and target languages are same - flush error
        if ($source === $target) jflush(false, 'Current and selected languages are same');

        // Create Google Cloud Translation PHP API
        $gapi = new TranslateClient(array('key' => Indi::ini('lang')->gapi->key));

        // Foreach level
        foreach (ar($levels) as $dir) {

            // Build filename of a php-file, containing l10n constants for source language
            $l10n_source_abs = DOC . STD . '/' . $dir . '/application/lang/admin/' . $source . '.php';

            // If no file - skip
            if (!file_exists($l10n_source_abs)) jflush(false, 'File ' . $l10n_source_abs . ' - not found');

            // If emtpy file - skip
            if (!$l10n_source_raw = file_get_contents($l10n_source_abs))  jflush(false, 'File ' . $l10n_source_abs . ' - is empty');

            // Parse constants-file contents to pick name and value for each constant
            $const = Indi::rexma('~define\(\'(.*?)\', ?\'(.*?)\'\);~', $l10n_source_raw);
            foreach ($const[2] as &$value) $value = stripslashes($value);

            //
            $l10n_const_names = $const[1]; $l10n_source_values = $const[2];

            // Get target values
            $l10n_target_values = array();
            foreach (array_chunk($l10n_source_values, 50) as $chunk)
                $l10n_target_values = array_merge($l10n_target_values,
                    array_column($gapi->translateBatch($chunk, [
                        'target' => 'th',
                        'source' => 'en'
                    ]), 'text')
                );

            // Prepare raw contents for target-language constants-file
            $l10n_target_raw = '<?php' . "\r\n";

            // Foreach target-language constant value
            for ($i = 0; $i < count($l10n_target_values); $i++) {

                // Make amendments
                $l10n_target_values[$i] = str_replace(
                    array('&quot;', '% s', '&#39;'),
                    array(     '"',  '%s',    "\'"),
                    $l10n_target_values[$i]
                );

                // Append to raw contents
                $l10n_target_raw .= "define('" . $l10n_const_names[$i] . "', '" . $l10n_target_values[$i] . "');\r\n";
            }

            // Build filename of a php-file, containing l10n constants for target language
            $l10n_target_abs = DOC . STD . '/' . $dir . '/application/lang/admin/' . $target . '.php';

            // Write raw contents into constants-file
            file_put_contents($l10n_target_abs, $l10n_target_raw);
        }
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

        // Ask what we're going to do
        $answer = $this->confirm(sprintf(
            'Если вы хотите добавить новый язык для категории "%s" нажмите "%s". ' .
            'Если просто нужно привести в соответствие с текущим состоянием - нажмите "%s"',
            $fieldR->nested('grid')->column('title', ' - '), I_YES, I_NO), 'YESNOCANCEL');

        // Remember
        if ($answer != 'yes' || !preg_match('~^admin~', $cell) || $value != 'q') return;

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

        // Build queue class name
        $queueClassName = 'Indi_Queue_L10n_' . ucfirst($cell);

        // Check that class exists
        if (!class_exists($queueClassName)) jflush(false, sprintf('Не найден класс %s', $queueClassName));

        // Create queue class instance
        $queue = new $queueClassName();

        // Run first stage
        $queue->chunk(array(
            'source' => $_['langId']->alias,
            'target' => t()->row->alias
        ));
    }
}
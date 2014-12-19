<?php
class Admin_EntitiesController extends Indi_Controller_Admin {

    public function phpAction() {

        // PHP class files for sections of type 'system' - will be created in '/core',                                                          //$repositoryDirA = array('s' => 'core', 'o' => 'coref', 'p' => 'www');
        // 'often' - in '/coref', 'project' - in '/www'
        $repositoryDirA = array('y' => 'core', 'o' => 'coref', 'n' => 'www');

        // If current section has a type, that is (for some reason) not in the list of known types
        if (!in($this->row->system, array_keys($repositoryDirA)))

            // Flush an error
            jflush(false, 'Can\'t detect the alias of repository, associated with a type of the chosen entity');

        // Build the dir name, that model's php-file will be created in
        $dir = Indi::dir(DOC . STD . '/' . $repositoryDirA[$this->row->system] . '/application/models/');

        // If that dir doesn't exist and can't be created - flush an error
        if (!preg_match(Indi::rex('dir'), $dir)) jflush(false, $dir);

        // Get the model name with first letter upper-cased
        $model = ucfirst($this->row->table);

        // If model file is not yet exist
        if (!is_file($modelFn = $dir . '/' . $model . '.php')) {

            // Build template model file name
            $tplModelFn = DOC. STD . '/core/application/models/{Model}.php';

            // If it is not exists - flush an error, as we have no template for creating a model file
            if (!is_file($tplModelFn)) jflush(false, 'No template-model file found');

            // Get the template contents (source code)
            $emptyModelSc = file_get_contents($tplModelFn);

            // Replace {Model} keyword with an actual model name
            $modelSc = preg_replace(':\{Model\}:', $model, $emptyModelSc);

            // Put the contents to a model file
            file_put_contents($modelFn, $modelSc);
        }

        // Build the model's own dir name, and try to create it, if it not yet exist
        $modelDir = Indi::dir($dir . '/' . $model . '/');

        // If model's own dir doesn't exist and can't be created - flush an error
        if (!preg_match(Indi::rex('dir'), $modelDir)) jflush(false, $modelDir);

        // If model's row-class file is not yet exist
        if (!is_file($modelRowFn = $dir . '/' . $model . '/Row.php')) {

            // Build template model's rowClass file name
            $tplModelRowFn = DOC. STD . '/core/application/models/{Model}/Row.php';

            // If it is not exists - flush an error, as we have no template for creating a model's rowClass file
            if (!is_file($tplModelRowFn)) jflush(false, 'No template file for model\'s rowClass found');

            // Get the template contents (source code)
            $tplModelRowSc = file_get_contents($tplModelRowFn);

            // Replace {Model} keyword with an actual model name
            $modelRowSc = preg_replace(':\{Model\}:', $model, $tplModelRowSc);

            // Put the contents to a model's rowClass file
            file_put_contents($modelRowFn, $modelRowSc);
        }

        // Flush success
        jflush(true);
    }
}
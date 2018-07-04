<?php
class Admin_SectionsController extends Indi_Controller_Admin {

    public function jsAction() {

        // JS-controller files for sections of type 'system' - will be created in '/core',                                                          //$repositoryDirA = array('s' => 'core', 'o' => 'coref', 'p' => 'www');
        // 'often' - in '/coref', 'project' - in '/www'
        $repoDirA = array('s' => 'core', 'o' => 'coref', 'p' => 'www');

        // If current section has a type, that is (for some reason) not in the list of known types
        if (!in($this->row->type, array_keys($repoDirA)))

            // Flush an error
            jflush(false, 'Can\'t detect the alias of repository, associated with a type of the chosen section');

        // Build the dir name, that controller's js-file should be created in
        $dir = Indi::dir(DOC . STD . '/' . $repoDirA[$this->row->type] . '/js/admin/app/controller/');

        // If that dir doesn't exist and can't be created - flush an error
        if (!preg_match(Indi::rex('dir'), $dir)) jflush(false, $dir);

        // Get the controller name
        $ctrl = $this->row->alias;

        // If controller file is not yet exist
        if (!is_file($ctrlAbs = $dir . '/' . $ctrl . '.js')) {

            // Build template model absolute file name
            $tplAbs = DOC. STD . '/core/js/admin/app/controller/{controller}.js';

            // If it is not exists - flush an error, as we have no template for creating a model file
            if (!is_file($tplAbs)) jflush(false, 'No template-controller file found');

            // Get the template contents (source code)
            $tplRaw = file_get_contents($tplAbs);

            // Replace {controller} keyword with an actual section name
            $ctrlRaw = preg_replace(':\{controller\}:', $ctrl, $tplRaw);

            // Put the contents to a model file
            file_put_contents($ctrlAbs, $ctrlRaw);

            // Chmod
            chmod($ctrlAbs, 0765);
        }

        // Flush success
        jflush(true);
    }

    public function phpAction() {

        // JS-controller files for sections of type 'system' - will be created in '/core',                                                          //$repositoryDirA = array('s' => 'core', 'o' => 'coref', 'p' => 'www');
        // 'often' - in '/coref', 'project' - in '/www'
        $repoDirA = array('s' => 'core', 'o' => 'coref', 'p' => 'www');

        // If current section has a type, that is (for some reason) not in the list of known types
        if (!in($this->row->type, array_keys($repoDirA)))

            // Flush an error
            jflush(false, 'Can\'t detect the alias of repository, associated with a type of the chosen section');

        // Build the dir name, that controller's js-file should be created in
        $dir = Indi::dir(DOC . STD . '/' . $repoDirA[$this->row->type] . '/application/controllers/admin/');

        // If that dir doesn't exist and can't be created - flush an error
        if (!preg_match(Indi::rex('dir'), $dir)) jflush(false, $dir);

        // Get the controller name
        $ctrl = ucfirst($this->row->alias);

        // If controller file is not yet exist
        if (is_file($ctrlAbs = $dir . '/' . $ctrl . 'Controller.php'))
            jflush(false, 'PHP-controller file for that section already exists');

        // Build template model absolute file name
        $tplAbs = DOC. STD . '/core/application/controllers/admin/{controller}.php';

        // If it is not exists - flush an error, as we have no template for creating a model file
        if (!is_file($tplAbs)) jflush(false, 'No template-controller file found');

        // Get the template contents (source code)
        $tplRaw = file_get_contents($tplAbs);

        // Replace {controller} keyword with an actual section name
        $ctrlRaw = preg_replace(':\{controller\}:', $ctrl, $tplRaw);

        // Replace {extends} keyword with an actual parent class name
        $ctrlRaw = preg_replace(':\{extends\}:', $this->row->extends, $ctrlRaw);

        // Put the contents to a model file
        file_put_contents($ctrlAbs, $ctrlRaw);

        // Chmod
        chmod($ctrlAbs, 0765);

        // Flush success
        jflush(true);
    }

    /**
     * Flush section entry's creation expression, to be applied on another project running on Indi Engine
     */
    public function exportAction() {
        jflush(true, '<textarea style="width: 500px; height: 400px;">' . $this->row->export() . '</textarea>');
    }
}
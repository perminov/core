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
     * Flush selected sections entries' creation expression, to be applied on another project running on Indi Engine
     */
    public function exportAction() {

        // Declare array of ids of entries, that should be exported, and push main entry's id as first item
        $toBeExportedIdA[] = $this->row->id;

        // If 'others' param exists in $_POST, and it's not empty
        if ($otherIdA = ar(Indi::post()->others)) {

            // Unset invalid values
            foreach ($otherIdA as $i => $otherIdI) if (!(int) $otherIdI) unset($otherIdA[$i]);

            // If $otherIdA array is still not empty append it's item into $toBeExportedIdA array
            if ($otherIdA) $toBeExportedIdA = array_merge($toBeExportedIdA, $otherIdA);
        }

        // Fetch rows that should be moved
        $toBeExportedRs = Indi::trail()->model->fetchAll(
            array('`id` IN (' . im($toBeExportedIdA) . ')', Indi::trail()->scope->WHERE)
        );

        // For each row get export expression
        $php = []; foreach ($toBeExportedRs as $toBeExportedR) $php []= $toBeExportedR->export();

        // Apply new index
        $this->setScopeRow(false, null, $toBeExportedRs->column('id'));

        // Flush
        jtextarea(true, im($php, "\n/*----------------*/\n"));
    }

    /**
     * todo: make 'Inversion' checkbox-field for filters
     */
    public function indexAction() {

        //
        Indi::trail()->model->fields('roleIds')->storeRelationAbility = 'one';

        // Call parent
        $this->callParent();
    }
}
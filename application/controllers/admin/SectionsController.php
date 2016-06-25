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
        }

        // Flush success
        jflush(true);
    }

    public function authorAction() {

        // Get model
        $model = Indi::model($this->row->id);

        // If `author` field exists - flush error
        if ($model->fields('author'))
            jflush(false, 'Группа полей "Автор" уже существует в структуре сущности "' . $this->row->title . '"');

        // Get involded `element` entries
        $elementRs = Indi::model('Element')->fetchAll('FIND_IN_SET(`alias`, "span,combo,datetime")');

        // Prepare fields config
        $fieldA = array(
            'author' => array(
                'title' => 'Создание',
                'elementId' => $elementRs->gb('span', 'alias')->id
            ),
            'authorType' => array(
                'title' => 'Кто создал',
                'storeRelationAbility' => 'one',
                'elementId' => $elementRs->gb('combo', 'alias')->id,
                'columnTypeId' => 3,
                'defaultValue' => '<?=Indi::me(\'mid\')?>',
                'relation' => Indi::model('Entity')->id(),
                'filter' => '`id` IN (' . Indi::db()->query('
                    SELECT GROUP_CONCAT(DISTINCT IF(`entityId`, `entityId`, 11)) FROM `profile` WHERE `toggle` = "y"
                ')->fetchColumn() . ')'
            ),
            'authorId' => array(
                'title' => 'Кто именно',
                'storeRelationAbility' => 'one',
                'elementId' => $elementRs->gb('combo', 'alias')->id,
                'columnTypeId' => 3,
                'defaultValue' => '<?=Indi::me(\'id\')?>',
                'dependency' => 'e',
                'satellite' => 0
            ),
            'authorTs' => array(
                'title' => 'Когда',
                'elementId' => $elementRs->gb('datetime', 'alias')->id,
                'columnTypeId' => 9,
                'defaultValue' => '<?=date(\'Y-m-d H:i:s\')?>'
            )
        );

        // Create fields
        foreach ($fieldA as $alias => $fieldI) {
            $fieldRA[$alias] = Indi::model('Field')->createRow();
            $fieldRA[$alias]->entityId = $this->row->id;
            $fieldRA[$alias]->alias = $alias;
            $fieldRA[$alias]->assign($fieldI);
            if ($alias == 'authorId') $fieldRA[$alias]->satellite = $fieldRA['authorType']->id;
            $fieldRA[$alias]->save();
        }

        // Flush success
        jflush(true, 'Группа полей "Создание" была добавлена в структуру сущности "' . $this->row->title . '"');
    }
}
<?php
class Indi_Controller_Admin extends Indi_Controller_Admin_Beautiful {

    /**
     * Save form data
     *
     * @param bool $redirect
     */
    public function saveAction($redirect = true) {

        // Do pre-save operations
        $this->preSave();

        // Get array of aliases of fields, that are actually represented in database table
        $possibleA = Indi::trail()->model->fields(null, 'columns');

        // Pick values from Indi::post()
        foreach ($possibleA as $possibleI) $data[$possibleI] = Indi::post($possibleI);

        // Unset 'move' key from data, because 'move' is a system field, and it's value will be set up automatically
        unset($data['move']);

        // If current cms user is an alternate, and if there is corresponding field within current entity structure
        if ($_SESSION['admin']['alternate'] && in_array($_SESSION['admin']['alternate'] . 'Id', $possibleA))

            // Force setup of that field value as id of current cms user
            $data[$_SESSION['admin']['alternate'] . 'Id'] = $_SESSION['admin']['id'];

        // If there was disabled fields defined for current section, we check if default value was additionally set up
        // and if so - assign that default value under that disabled field alias in $data array, or, if default value
        // was not set - drop corresponding key from $data array
        foreach (Indi::trail()->disabledFields as $disabledFieldR)
            foreach (Indi::trail()->fields as $fieldR)
                if ($fieldR->id == $disabledFieldR->fieldId)
                    if (!strlen($disabledFieldR->defaultValue)) unset($data[$fieldR->alias]);
                    else $data[$fieldR->alias] = $disabledFieldR->compiled('defaultValue');

        // Update current row properties with values from $data array
        foreach ($data as $field => $value) $this->row->$field = $value;

        // Save the row
        $this->row->save();

        //i($this->row->mismatch());
        //Indi_Image::deleteEntityImagesIfChecked();
        //Indi_Image::uploadEntityImagesIfBrowsed(null, null, $this->requirements);

        // Do post-save operations
        $this->postSave();

        // If 'redirect-url' param exists within post data
        if ($location = Indi::post('redirect-url')) {

            // Chech if $url contains primary hash value
            if (preg_match('#/ph/([0-9a-f]+)/#', $location, $matches)) {

                // Remember the fact that save button was toggled on
                $_SESSION['indi']['admin'][Indi::uri()->section][$matches[1]]['toggledSave'] = true;

                // If it was a new row, that we've just saved
                if (!Indi::uri()->id) {

                    // Increment 'found' scope param
                    $_SESSION['indi']['admin'][Indi::uri()->section][$matches[1]]['found']++;

                    // Replace the null id with id of newly created row
                    $location = str_replace('null', Indi::trail()->row->id, $location);
                }

            // Replace the null id with id of newly created row
            } else if (!Indi::uri()->id)  $location = str_replace('null', Indi::trail()->row->id, $location);
        }

        // Redirect
        if ($redirect) $this->redirect($location);
    }
}
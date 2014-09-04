<?php
/**
 * Controller for auxiliary abilities
 */
class Indi_Controller_Auxiliary extends Indi_Controller {

    /**
     * Provide a file download ability
     */
    public function downloadAction(){

        // If 'id' param is not set or is not an integer
        if (!preg_match('/^[0-9]+$/', Indi::uri('id'))) die(I_DOWNLOAD_ERROR_NO_ID);

        // If 'field' param is not set or is not an integer
        if (!preg_match('/^[0-9]+$/', Indi::uri('field'))) die(I_DOWNLOAD_ERROR_NO_FIELD);

        // Get the field
        $fieldR = Indi::model('Field')->fetchRow('`id` = "' . Indi::uri('field') . '"');

        // If field was not found
        if (!$fieldR) die(I_DOWNLOAD_ERROR_NO_SUCH_FIELD);

        // Get extended info about field
        $fieldR = Indi::model($fieldR->entityId)->fields($fieldR->alias);

        // If field was not found
        if (!$fieldR) die(I_DOWNLOAD_ERROR_NO_SUCH_FIELD);

        // If field is not a file upload field, e.g does not deal with files
        if ($fieldR->foreign('elementId')->alias != 'upload') die(I_DOWNLOAD_ERROR_FIELD_DOESNT_DEAL_WITH_FILES);

        // Get the row
        $r = Indi::model($fieldR->entityId)->fetchRow('`id` = "' . Indi::uri('id') . '"');

        // If row was not found
        if (!$r) die(I_DOWNLOAD_ERROR_NO_SUCH_ROW);

        // Get the directory name
        $dir = DOC . STD . '/' . Indi::ini()->upload->path . '/' . Indi::model($fieldR->entityId)->table() . '/';

        // Get the file
        list($abs) = glob($dir . $r->id . '_' . $fieldR->alias . '.*');

        // If there is no file
        if (!$abs) die(I_DOWNLOAD_ERROR_NO_FILE);

        // Declare an array, for containing downloading file title parts
        $title = array();

        // Append entity title to filename parts array, if needed
        //if ($fieldR->params['prependEntityTitle'] == 'true') $title[] = Indi::model($fieldR->entityId)->title() . ',';

        // Append row title to filename parts array
        $title[] = $r->title;

        // Append entity title to filename parts array, if needed
        if ($fieldR->params['appendFieldTitle'] != 'false') $title[] = '- ' . $fieldR->title;

        // Get the extension of the file
        $ext = preg_replace('/.*\.([^\.]+)$/', '$1', $abs);

        // Get the imploded file title, with extension appended
        $title = implode(' ', $title) . '.' . $ext;

        // If user's browser is Microsoft Internet Explorer - do a filename encoding conversion
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) $title = iconv('utf-8', 'windows-1251', $title);

        // Create a file_info resource
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // Get the mime-type
        $type = finfo_file($finfo, $abs);

        // If there was an error while getting info about file
        if (!$type) die(I_DOWNLOAD_ERROR_FILEINFO_FAILED);

        // Close the fileinfo resource
        finfo_close($finfo);

        // Start donwload
        header('Content-Type: ' . $type);
        header('Content-Disposition: attachment; filename="' . $title . '";');
        readfile($abs);
        die();
    }
}
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
        if ($fieldR->params['rowTitle'] != 'false') $title[] = $r->dftitle($fieldR->alias);

        // Append entity title to filename parts array, if needed
        if ($fieldR->params['appendFieldTitle'] != 'false') $title[] = '- ' . $fieldR->title;

        // Append entity title to filename parts array, if needed
        if (strlen($fieldR->params['postfix'])) {
            Indi::$cmpTpl = $fieldR->params['postfix']; eval(Indi::$cmpRun); $title[] = Indi::cmpOut();
        }

        // Get the extension of the file
        $ext = preg_replace('/.*\.([^\.]+)$/', '$1', $abs);

        // Get the imploded file title, with extension appended
        $title = implode(' ', $title) . '.' . $ext;

        // If user's browser is Microsoft Internet Explorer - do a filename encoding conversion
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) $title = iconv('utf-8', 'windows-1251', $title);

        // If finfo-extension enabled
        if (function_exists('finfo_open')) {
        
            // Create a file_info resource
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            // Get the mime-type
            $type = finfo_file($finfo, $abs);

            // If there was an error while getting info about file
            if (!$type) die(I_DOWNLOAD_ERROR_FILEINFO_FAILED);

            // Close the fileinfo resource
            finfo_close($finfo);
        }

        // Replace " with ', as browsers replaces " with _ or - or, maybe, with something else
        $title = str_replace('"', "'", $title);
        
        // Start download
        header('Content-Type: ' . $type);
        header('Content-Disposition: attachment; filename="' . $title . '";');
        header('Content-Length: ' . filesize($abs));
        readfile($abs);
        die();
    }

    /**
     * Generate link to http://colorzilla.com
     */
    public function shadegradAction() {
        $colors = ar('dddddd,ffffff');
        $step = 2;
        for ($i = 0; $i <= 100;) {
            foreach ($colors as $color) {
                $str[] = $color . '+' . $i;
                $str[] = $color . '+' . ($i + $step);
                $i += $step;
            }
        }
        die('<a target="_blank" href="http://colorzilla.com/gradient-editor/#' . im($str) . '">color</a>');
    }

    /**
     * Check whether websocket server is already running, and start it if not
     */
    public function websocketAction() {

        // Close session
        session_write_close();

        // Websocket server process existence check command
        $wsCheck = preg_match('/^WIN/i', PHP_OS)
            ? 'WMIC PROCESS get Commandline,Processid | find "ws.php ' . STD . '" | find /V "wmic" | find /V "find"'
            : 'ps | grep "ws.php ' . STD . '"';

        // If OS is Windows - start new process using 'start' command
        if (!$ps = shell_exec($wsCheck)) {

            // Websocket server process start command
            $wsStart = 'php ../core/application/ws.php ' . STD;

            // Start websocket server
            preg_match('/^WIN/i', PHP_OS)
                ? exec('start /B ' . $wsStart)
                : exec($wsStart . ' > /dev/null &');

            // Flush msg
            jflush(true);
        }

        // Flush msg
        jflush(true, 'Websocket-server is already running');
    }
}
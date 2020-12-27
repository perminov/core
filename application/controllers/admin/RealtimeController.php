<?php
class Admin_RealtimeController extends Indi_Controller_Admin {

    public function yieldAction() {

        // Get lock file
        $wsLock = DOC . STD . '/core/application/ws.pid';
        $wsChl  = DOC . STD . '/core/application/ws.chl';
        $wsErr  = DOC . STD . '/core/application/ws.err';

        // If websocket-server lock-file exists, and contains websocket-server's process ID, and it's an integer
        if (is_file($wsLock) && $wsPid = (int) trim(file_get_contents($wsLock))) {

            // If such process is found - flush msg and exit
            if (checkpid($wsPid)) {

                // Build websocket startup cmd
                $result['cmd'] = preg_match('/^WIN/i', PHP_OS)
                    ? sprintf('taskkill /f /PID  %s 2>&1', $wsPid)
                    : 'asdasdasd';

                // Start websocket server
                wslog('------------------------------');
                wslog('Exec: ' . $result['cmd']);
                exec($result['cmd'], $result['output'], $result['return']);
                wslog('Output: ' . print_r($result['output'], true) . ', return: ' . $result['return']);

                // Unset 'cmd'-key
                unset($result['cmd']);

                $flush = ['success' => true];
                if (is_array($result['output']) && isset($result['output'][0]))
                    if (strlen($flush['msg'] = mb_convert_encoding($result['output'][0], 'utf-8', 'CP-866')));
                        unset($result['output']);

                $flush['result'] = $result;

                // Truncate
                file_put_contents($wsChl, '');
                file_put_contents($wsErr, '');

                // Flush msg
                jflush($flush);
            }
        }

        // Flush response
        jflush(false, 'There is nothing to be restarted');
    }

    /**
     * Append role title to admin title
     *
     * @param $item
     * @param $r
     */
    public function renderGridDataItem(&$item, $r) {
        $item['adminId'] .= ' [' .  $item['profileId'] . ']';
    }
}
<?php
class Admin_RealtimeController extends Indi_Controller_Admin {

    public function restartAction() {

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
                    : sprintf('kill -9 %s', $wsPid);

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
     * Explicitly notify about insertion, because the fact that entry had empty title
     * means that it was created via ws.php, so ->realtime('inserted') was not called
     */
    public function preDispatch() {

        // If $_GET['newtab'] exists
        if (array_key_exists('newtab', Indi::get())) {

            // Check CID
            jcheck(['cid' => ['req' => true, 'rex' => 'wskey']], ['cid' => CID]);

            // Try to found `realtime` entry having such CID and `type` = 'channel'
            if ($r = m('Realtime')->fetchRow(['`token` = "' . CID . '"', '`type` = "channel"'])) {

                // Force `title` to be set
                $r->save();

                // Explicitly notify about insertion, because the fact that entry had empty title
                // means that it was created via ws.php, so ->realtime('inserted') was not called
                $r->realtime('inserted');

                // Flush success
                jflush(true);
            }

            // Flush failure
            jflush(false);

        // Else if $_GET['closetab'] exists
        } else if (array_key_exists('closetab', Indi::get())) {

            // Check CID
            jcheck(['cid' => ['req' => true, 'rex' => 'wskey']], ['cid' => CID]);

            // Try to found `realtime` entry having such CID and `type` = 'channel'
            if ($r = m('Realtime')->fetchRow(['`token` = "' . CID . '"', '`type` = "channel"'])) {

                // Delete
                $r->delete();

                // Flush success
                jflush(true);
            }

            // Flush failure
            jflush(false);
        }

        // Call parent
        parent::preDispatch();
    }

    /**
     * Append role title to admin title, and highlight tokens
     *
     * @param $item
     * @param $r
     */
    public function renderGridDataItem(&$item, $r) {

        // Append role title to admin title
        $item['adminId'] .= ' [' .  $item['profileId'] . ']';

        // Highlight session token
        if ($item['token'] == $_COOKIE['PHPSESSID'])
            $item['_render']['title']
                = preg_replace('~( - )(.*?)(, )~', '$1<span style="color: #35baf6;">$2</span>$3', $item['_render']['title']);

        // Highlight channel/tab token
        else if ($item['token'] == CID)
            $item['_render']['title']
                = preg_replace('~( - )(.*?)$~', '$1<span style="color: #35baf6;">$2</span>', $item['_render']['title']);
    }
}
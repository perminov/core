<?php
class Indi_Controller_Cmd extends Indi_Controller {

    public function preDispatch() {

        // Stop
        if (!CMD) jflush(false); else session_write_close();

        // Call parent
        $this->callParent();
    }

    /**
     * Start running queue
     *
     * @param $queueTaskId
     */
    public function queueAction($queueTaskId) {
        Indi::model('QueueTask')->fetchRow($queueTaskId)->start();
    }

    /**
     * Manage channels
     */
    public function channelAction($arg) {

        // If $arg is an array - create `realtime` entry, that will represent browser tab / websocket channel
        if (is_array($arg)) $r = m('Realtime')->createRow($arg, true); $r->save();

        // Else if $arg is integer - delete such `realtime` entry
        } else if (Indi::rexm('int11', $arg)) m('Realtime')->row($arg)->delete();
    }
}
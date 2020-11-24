<?php
class Indi_View_Action_Admin implements Indi_View_Action_Interface {

    /**
     * Mode. Can be 'auto' and 'view'. This setting affects on type of the value,
     * returned by 'Indi::trail()->view(true)' call. If $mode is 'auto' (by default) - the
     * type of the return value will be detected automatically (possible types are - rendered
     * plain text or instance of Indi_View_Action_Admin), depending on a number of circumstances.
     * If mode is 'view' - instance of Indi_View_Action_Admin will be forced to be returned, and,
     * additionally, plain contents, got by rendering the found script file - will be placed into ::$plain
     * property within that Indi_View_Action_Admin instance.
     *
     * @var string
     */
    public $mode = 'auto';

    /**
     * Plain contents
     *
     * @var string
     */
    public $plain = '';

    /**
     * Constructor
     *
     * @param $plain
     */
    public function __construct($plain = null) {
        $this->plain = $plain;
    }

    /**
     * Render the view
     *
     * @return string
     */
    public function render() {

        // If channel found
        if ($channel = m('realtime')->fetchRow('`token` = "' . CID . '"')) {

            // Get data to be copied
            $data = $channel->original(); unset($data['id'], $data['spaceSince']);

            // Normalize `up` argument
            $up = 0;

            // Get involved fields
            $fields = t($up)->row
                ? t()->fields->select('readonly,ordinary', 'mode')->column('id', ',')
                : t()->gridFields->select(': > 0')->column('id', ',');

            // Create `realtime` entry of `type` = "context"
            m('realtime')->createRow([
                'realtimeId' => $channel->id,
                'type' => 'context',
                'token' => t()->bid(),
                'sectionId' => t()->section->id,
                'entityId' => t()->section->entityId,
                'fields' => $fields
            ] + $data, true)->save();
        }

        // Return json-encoded trail data
        return json_encode(array('route' =>Indi::trail(true)->toArray(), 'plain' => $this->plain), JSON_UNESCAPED_UNICODE);
    }
}
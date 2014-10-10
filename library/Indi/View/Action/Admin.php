<?php
class Indi_View_Action_Admin implements Indi_View_Action_Interface {

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

        // Start output buffering
        ob_start();

        // Push <script> tag containing trail-refresh data into the buffer
        ?><script>Indi.trail(true).apply(<?=json_encode(Indi::trail(true)->toArray())?>);</script><?

        // Get and return buffered contents
        return ob_get_clean();
    }
}
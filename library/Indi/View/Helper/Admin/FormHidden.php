<?php
class Indi_View_Helper_Admin_FormHidden {
    public function formHidden($name)
    {
        return '<script>$(document).ready(function(){hide("tr-' . $name . '")})</script>';
    }
}
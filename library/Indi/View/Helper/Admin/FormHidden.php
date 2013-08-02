<?php
class Indi_View_Helper_Admin_FormHidden extends Indi_View_Helper_Abstract
{
    public function formHidden($name)
    {
        return '<script>$(document).ready(function(){hide("tr-' . $name . '")})</script>';
    }
}
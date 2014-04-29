<?php
class Indi_View_Helper_Admin_FormFooter extends Indi_View_Helper_Abstract
{
    public function formFooter()
    {
        $xhtml  = '</table>';
        $xhtml .= Indi::view()->buttons();
        $xhtml .= '</form>';
        return $xhtml;
    }
}
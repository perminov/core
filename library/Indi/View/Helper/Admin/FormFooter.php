<?php
class Indi_View_Helper_Admin_FormFooter {
    public function formFooter()
    {
        $xhtml  = '</table>';
        $xhtml .= Indi::view()->buttons();
        $xhtml .= '</form>';
        return $xhtml;
    }
}
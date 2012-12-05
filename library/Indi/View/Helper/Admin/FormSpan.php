<?php
class Indi_View_Helper_Admin_FormSpan extends Indi_View_Helper_FormElement
{
    public function formSpan($alias, $attribs = null)
    {
        $field = $this->view->trail->getItem()->getFieldByAlias($alias);
        $xhtml = '<script>$(document).ready(function(){$("#tr-' . $alias . '").attr(\'class\',\'info\');$("#tr-' . $alias . '").html("<td colspan=\'2\' align=\'center\' class=\'table_topics\' id=\'td-' . $field->alias . '\'>' . $field->title . '</td>")});</script>';
//        $xhtml = '<tr '. $this->_htmlAttribs($attribs) . ' class="info" id="tr-' . $field->alias . '"><td colspan="2" class="table_topics" align="center">' . $title . '</td></tr>';
        return $xhtml;
    }    
}
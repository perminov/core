<?php
class Indi_View_Helper_Admin_FormSpan extends Indi_View_Helper_FormElement
{
    public function formSpan($alias, $attribs = null)
    {
        $field = $this->view->trail->getItem()->getFieldByAlias($alias);
        $xhtml = '<script>$("#tr-' . $alias . '").attr("class","info")</script>';
		$xhtml .= '<script>$("#td-left-' . $alias . '").attr({"colspan": "2", "align":"center","class":"table_topics"});</script>';
        return $xhtml;
    }    
}
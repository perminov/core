<?php
class Indi_View_Helper_Admin_FormHeader extends Indi_View_Helper_Abstract
{
    public function formHeader($title = null)
    {
        $title = $title ? $title : $this->view->entity->title;
        $xhtml = '<span class="form_table"><form action="../' . ($this->view->row->id ? '../../' : '') . 'save/' . ($this->view->row->id ? 'id/' . $this->view->row->id . '/' : '') . '" name="' . $this->view->escape($this->view->entity->table) . '" method="post" enctype="multipart/form-data">
<table celpadding="2" cellspacing="1" border="0" width="100%">
'. ($this->view->incorrectMessage ? '<tr><td colspan="2" class="incorrectMessage">' . $this->view->incorrectMessage . '</td></tr>' : '') . '
<tr><td colspan="2" align="center" class="table_topics">' . $title . '</td></tr>
<col width="50%"/><col width="50%"/>
';
        return $xhtml;
    }
}
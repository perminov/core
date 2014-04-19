<?php
class Indi_View_Helper_Admin_FormField extends Indi_View_Helper_Abstract
{
    public function formField($field, $disabled = false)
    {
        $elementRow = $field->foreign('elementId');
        if (isset($this->view->row->{$field->alias . 'Wide'})) {
            $field->params['wide'] = $this->view->row->{$field->alias . 'Wide'};
        }
        if ($field->params['wide']) {
            $xhtml = '<tr class="info" id="tr-' . $field->alias . '">';
            $xhtml .= '<td width="100%" id="td-wide-' . $field->alias . '" colspan="2" align="center">';
            $xhtml .= '<span style="line-height: 21px;">' . $field->title . ':</span><br>';
            $xhtml .= $this->view->{'form' . ucfirst($elementRow->alias)}($field->alias);
            $xhtml .= '</td>';
            $xhtml .= '</tr>';
        } else {
            $xhtml = '<tr class="info' . ($disabled ? ' i-tr-disabled"' : '') . '" id="tr-' . $field->alias . '">';
            $xhtml .= '<td width="50%" id="td-left-' . $field->alias . '">';
            $xhtml .= $field->title . ':';
            $xhtml .= '</td>';
            $xhtml .= '<td width="50%" id="td-right-' . $field->alias . '">';
            $xhtml .= $this->view->{'form' . ucfirst($elementRow->alias)}($field->alias);
            $xhtml .= '</td>';
            $xhtml .= '</tr>';
        }
        return $xhtml;
    }
}

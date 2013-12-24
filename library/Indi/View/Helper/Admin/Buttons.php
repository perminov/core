<?php
class Indi_View_Helper_Admin_Buttons extends Indi_View_Helper_Abstract
{
    public function buttons( $title = null, $action = null, $post = null)
    {
        // set up parents of 1 and 2 levels up
        $parent = $this->view->trail->getItem(1);
        $grandParent = $this->view->trail->getItem(2);

        $accessableActions = $this->view->trail->getItem()->actions->toArray();

        // if buttons are to be displayeв on list screen
        if (($title != null) && ($action != null) && (is_array($title))&&(is_array($action))) {
        } else if ($this->view->action == 'form' || $this->view->alterForm) {
            $title[] = BUTTON_BACK;
            $action[] = "top.window.Ext.getCmp('i-action-form-topbar-button-back').handler();";
            foreach ($accessableActions as $accessableAction) {
                if ($accessableAction['alias'] == 'save') {
                    $title[] = BUTTON_SAVE;
                    $action[] = "top.window.Ext.getCmp('i-action-form-topbar-button-save').handler();";
                    break;
                }
            }

        }


        $xhtml = '<table class="buttons" style="border: 0;margin-top: 6px; width: 100%;" cellpadding="6"><tr style="border: 0;">';
        for ($i = 0; $i < count($title); $i++) {
            $xhtml .= '<td id="td-button-' . $title[$i] . '"' . ($title[$i] == BUTTON_BACK ? 'type="back"':'type="save"') . ' width="'.(100/count($title)).'%" align="' . (count($title)>1?($i?'left':'right'):'center') . '">';
            $xhtml .= $this->view->button($title[$i], $action[$i]);
            $xhtml .= '</td>';
        }
        $xhtml .= '</tr></table>';
        return $xhtml;
    }
}

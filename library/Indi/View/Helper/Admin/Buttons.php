<?php
class Indi_View_Helper_Admin_Buttons {
    public function buttons( $title = null, $action = null, $post = null)
    {
        // set up parents of 1 and 2 levels up
        $parent = Indi::trail(1);
        $grandParent = Indi::trail(2);

        $accessableActions = Indi::trail()->actions->toArray();

        // if buttons are to be displayeв on list screen
        if (($title != null) && ($action != null) && (is_array($title))&&(is_array($action))) {
        } else if (Indi::uri()->action == 'form' || Indi::view()->alterForm) {
            $title[] = I_BACK;
            $action[] = "top.window.Ext.getCmp('i-action-form-topbar-button-back').handler();";
            foreach ($accessableActions as $accessableAction) {
                if ($accessableAction['alias'] == 'save') {
                    $title[] = I_SAVE;
                    $action[] = "top.window.Ext.getCmp('i-action-form-topbar-button-save').handler();";
                    break;
                }
            }

        }


        $xhtml = '<table class="buttons" style="border: 0;margin-top: 6px; width: 100%;" cellpadding="6"><tr style="border: 0;">';
        for ($i = 0; $i < count($title); $i++) {
            $xhtml .= '<td id="td-button-' . $title[$i] . '"' . ($title[$i] == I_BACK ? 'type="back"':'type="save"') . ' width="'.(100/count($title)).'%" align="' . (count($title)>1?($i?'left':'right'):'center') . '">';
            $xhtml .= Indi::view()->button($title[$i], $action[$i]);
            $xhtml .= '</td>';
        }
        $xhtml .= '</tr></table>';
        return $xhtml;
    }
}

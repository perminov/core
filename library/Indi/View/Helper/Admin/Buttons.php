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
        } elseif ($this->view->action == 'index') {
            $title[] = 'Back';

            foreach ($accessableActions as $accessableAction) {
                if ($accessableAction['alias'] == 'form') $title[] = 'Добавить';
            }
            
            $href = '/' . $this->view->module . '/' . $parent->section->alias . '/';
            if ($grandParent->row) {
                $href .= $parent->action->alias . '/id/' . $grandParent->row->id . '/';
            }
            $action[] = "loadContent('" . $href . "')";
            if (is_array($post)) {
                foreach ($post as $key => $value) {
                 $href = "/" . $this->view->module . '/' . $this->view->section->alias . '/' . "form/";
                 $action[] = "javascript: sendAdd('".$key."','".$value."','".$href."')";                   
                }
            } else {
                $action[] = "loadContent('/" . $this->view->module . '/' . $this->view->section->alias . '/' . "form/')";
            }
            
        // iа on edit screen
        } else if ($this->view->action == 'form' || $this->view->alterForm) {
            $title[] = 'Вернуться';
            $action[] = "window.parent.loadContent('/" . $this->view->module . '/' . $this->view->section->alias . '/' . ($parent->row ? 'index/id/' . $parent->row->id . '/' : '') . '\')';
            foreach ($accessableActions as $accessableAction) {
                if ($accessableAction['alias'] == 'save') {
					$title[] = 'Сохранить';
					$action[] = "javascript: document.forms['" . $this->view->entity->table . "'].submit();";
					break;
				}
            }

		}
    
        
        $xhtml = '<table class="buttons" style="border: 0;margin-top: 6px; width: 100%;" cellpadding="6"><tr style="border: 0;">';
        for ($i = 0; $i < count($title); $i++) {
			$xhtml .= '<td id="td-button-' . $title[$i] . '" width="'.(100/count($title)).'%" align="' . ($i?'left':'right') . '">';
            $xhtml .= $this->view->button($title[$i], $action[$i]);
            $xhtml .= '</td>';
        }
        $xhtml .= '</tr></table>';
        return $xhtml;
    }
}

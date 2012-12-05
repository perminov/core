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
                if ($accessableAction['alias'] == 'form') $title[] = 'Add';
            }
            
            $href = '/' . $this->view->module . '/' . $parent->section->alias . '/';
            if ($grandParent->row) {
                $href .= $parent->action->alias . '/id/' . $grandParent->row->id . '/';
            }
            $action[] = "javascript: window.location = '" . $href . "'";            
            if (is_array($post)) {
                //for 'Add'
                foreach ($post as $key => $value) {
                   //@todo : Nowadays is used 1 parameters
                 $href = "/" . $this->view->module . '/' . $this->view->section->alias . '/' . "form/";                   
                 $action[] = "javascript: sendAdd('".$key."','".$value."','".$href."')";                   
                }
            } else {
                $action[] = "javascript: window.location = '/" . $this->view->module . '/' . $this->view->section->alias . '/' . "form/'";
            }
            
        // iа on edit screen
        } else if ($this->view->action == 'form' || $this->view->alterForm) {
            $title[] = 'Back';
            $action[] = "javascript: window.location = '/" . $this->view->module . '/' . $this->view->section->alias . '/' . ($parent->row ? 'index/id/' . $parent->row->id . '/' : '') . '\'';
            foreach ($accessableActions as $accessableAction) {
                if ($accessableAction['alias'] == 'save') {
					$title[] = 'Save';
					$action[] = "javascript: document.forms['" . $this->view->entity->table . "'].submit();";
					break;
				}
            }

		}
    
        
        $xhtml = '<table><tr>';
        for ($i = 0; $i < count($title); $i++) {
            $xhtml .= '<td id="td-button-' . $title[$i] . '">';
            $xhtml .= $this->view->button($title[$i], $action[$i]);
            $xhtml .= '</td>';
        }
        $xhtml .= '</tr></table>';
        return $xhtml;
    }
}

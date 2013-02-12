<?php
class Indi_View_Helper_Admin_RenderIndex extends Indi_View_Helper_Abstract
{
    public function renderIndex()
    {
		$gridFields = $this->view->trail->getItem()->gridFields->toArray();
		foreach ($gridFields as $gridField) {
			if ($gridField['columnTypeId'] == 0) {
				if ($gridField['elementId'] == 14) {
					$nonDbGridFieldAliases[] = $gridField['alias'];
				}
			}
		}
		//ob_start();
		if (count($nonDbGridFieldAliases) == 1 && false) {
			$xhtml = $this->view->renderTile();
		} else {
			$xhtml = $this->view->renderGrid();
		}
        return $xhtml;
    }    
}
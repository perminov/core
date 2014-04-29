<?php
class Indi_View_Helper_Admin_RenderIndex extends Indi_View_Helper_Abstract
{
    public function renderIndex()
    {
		$gridFields = Indi::trail()->gridFields->toArray();
		foreach ($gridFields as $gridField) {
			if ($gridField['columnTypeId'] == 0) {
				if ($gridField['elementId'] == 14) {
					$nonDbGridFieldAliases[] = $gridField['alias'];
				}
			}
		}
		//ob_start();
		if (count($nonDbGridFieldAliases) == 1 && false) {
			$xhtml = Indi::view()->renderTile();
		} else {
			$xhtml = Indi::view()->renderGrid();
		}
        return $xhtml;
    }    
}
<?php
class Indi_View_Helper_Admin_RenderContent extends Indi_View_Helper_Abstract{

    /**
     * Render central cms page content
     *
     */
	public function renderContent()
	{
		if ($this->view->trail->getItem()) {
			try {
				echo $this->view->{'render' . ucfirst($this->view->trail->getItem()->action->alias)}();
			} catch (Exception $e) {
				$oldException = $e;
				try {
					echo $this->view->render('hotels/sync.php');
				} catch (Exception $e) {
					echo '<b>Neigher</b><br><br> ' . $oldException->getMessage() . ' <br><br><b>nor</b><br><br>' . $e->getMessage();
				}
			}
		}
	}

}